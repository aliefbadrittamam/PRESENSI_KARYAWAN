<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\ShiftKerja;
use App\Models\LokasiPresensi;
use App\Models\RekapPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with(['karyawan.jabatan', 'shift']);

        // Filter by date
        if ($request->has('tanggal') && $request->tanggal) {
            $query->where('tanggal_presensi', $request->tanggal);
        } else {
            $query->where('tanggal_presensi', today());
        }

        // Filter by karyawan
        if ($request->has('karyawan') && $request->karyawan) {
            $query->where('id_karyawan', $request->karyawan);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status_kehadiran', $request->status);
        }

        $presensi = $query->orderBy('tanggal_presensi', 'desc')
                         ->orderBy('jam_masuk', 'desc')
                         ->paginate(20);

        // Statistics
        $stats = [
            'total' => $presensi->total(),
            'hadir' => $presensi->where('status_kehadiran', 'hadir')->count(),
            'terlambat' => $presensi->where('status_kehadiran', 'terlambat')->count(),
            'izin' => $presensi->where('status_kehadiran', 'izin')->count(),
            'sakit' => $presensi->where('status_kehadiran', 'sakit')->count(),
            'alpha' => $presensi->where('status_kehadiran', 'alpha')->count(),
        ];

        $karyawan = Karyawan::where('status_aktif', true)->get();

        return view('presensi.index', compact('presensi', 'stats', 'karyawan'));
    }

    public function create()
    {
    $karyawan = Karyawan::all();
    $shifts = ShiftKerja::all(); // ✅ model shift kamu
    return view('presensi.create', compact('karyawan', 'shifts'));
    }


    public function createWithCamera()
    {
        $karyawan = Karyawan::where('status_aktif', true)->get();
        $shifts = ShiftKerja::where('status_aktif', true)->get();
        $lokasi = LokasiPresensi::aktif()->get();

        // Get jenis presensi dari session (jika ada)
        $jenisPresensi = session('jenis', 'masuk');

        return view('presensi.create-face', compact('karyawan', 'shifts', 'lokasi', 'jenisPresensi'));
    }
    public function createMasuk()
    {

           $karyawan = Karyawan::with('jabatan')->get();
        $shifts = ShiftKerja::all();

        return view('presensi.create-face', compact('karyawan', 'shifts'));

    }

    public function storeMasuk(Request $request)
    {
        // validasi & simpan data presensi masuk
        // logika sesuai kebutuhan kamu
    }

 public function createKeluar()
{
    $karyawan = Karyawan::with('jabatan')->get();
    $shifts = ShiftKerja::all();
    $lokasi = LokasiPresensi::aktif()->get();

    return view('presensi.create-face', compact('karyawan', 'shifts', 'lokasi'));
}

private function savePresensiImage($request, $folder)
{
    $fotoPath = $request->file('foto')->store($folder, 'public');
    $thumbnailPath = $request->file('foto')->store($folder . '/thumbnails', 'public');

    return [$fotoPath, $thumbnailPath];
}


    public function storeKeluar(Request $request)
    {
        // validasi & simpan data presensi keluar
    }


    // Controller
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'id_shift' => 'required|exists:shift_kerja,id_shift',
            'tanggal_presensi' => 'required|date',
            'waktu_presensi' => 'required',
            'jenis_presensi' => 'required|in:masuk,keluar',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
            'foto_data' => 'required|string',
            'confidence_score' => 'required|numeric',
            'catatan' => 'nullable|string',
            'accuracy' => 'nullable|string'
        ]);

        try {
            // Simpan data presensi
            Presensi::create($validated);
            return redirect()->route('presensi.index')->with('success', 'Presensi berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Presensi $presensi)
    {
        $presensi->load(['karyawan', 'shift', 'karyawan.jabatan', 'karyawan.departemen']);
        return view('presensi.show', compact('presensi'));
    }

    public function edit(Presensi $presensi)
    {
        $karyawan = Karyawan::where('status_aktif', true)->get();
        $shifts = ShiftKerja::where('status_aktif', true)->get();
        $presensi->load(['karyawan', 'shift']);

        return view('presensi.edit', compact('presensi', 'karyawan', 'shifts'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'id_shift' => 'required|exists:shift_kerja,id_shift',
            'tanggal_presensi' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i|after:jam_masuk',
            'status_kehadiran' => 'required|in:hadir,terlambat,izin,sakit,cuti,alpha',
            'latitude_masuk' => 'nullable|numeric|between:-90,90',
            'longitude_masuk' => 'nullable|numeric|between:-180,180',
            'latitude_keluar' => 'nullable|numeric|between:-90,90',
            'longitude_keluar' => 'nullable|numeric|between:-180,180',
            'catatan' => 'nullable|max:500'
        ]);

        // Cek duplikasi presensi (kecuali untuk record ini)
        $existingPresensi = Presensi::where('id_karyawan', $request->id_karyawan)
            ->where('tanggal_presensi', $request->tanggal_presensi)
            ->where('id_presensi', '!=', $presensi->id_presensi)
            ->first();

        if ($existingPresensi) {
            return redirect()->back()
                ->with('error', 'Karyawan sudah memiliki presensi pada tanggal tersebut.')
                ->withInput();
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Hitung keterlambatan jika ada jam masuk
        if ($request->jam_masuk) {
            $shift = ShiftKerja::find($request->id_shift);
            $jamMulai = Carbon::parse($shift->jam_mulai);
            $jamMasuk = Carbon::parse($request->jam_masuk);
            
            if ($jamMasuk > $jamMulai) {
                $keterlambatan = $jamMulai->diffInMinutes($jamMasuk);
                $data['keterlambatan_menit'] = max(0, $keterlambatan - $shift->toleransi_keterlambatan);
                
                // Set status terlambat jika melebihi toleransi
                if ($data['keterlambatan_menit'] > 0 && $request->status_kehadiran == 'hadir') {
                    $data['status_kehadiran'] = 'terlambat';
                }
            } else {
                $data['keterlambatan_menit'] = 0;
            }
        }

        // Hitung total jam kerja jika ada jam masuk dan keluar
        if ($request->jam_masuk && $request->jam_keluar) {
            $masuk = Carbon::parse($request->jam_masuk);
            $keluar = Carbon::parse($request->jam_keluar);
            
            if ($keluar < $masuk) {
                $keluar->addDay();
            }
            
            $data['total_jam_kerja'] = $masuk->diffInMinutes($keluar) / 60;
        } else {
            $data['total_jam_kerja'] = null;
        }

        $presensi->update($data);

        return redirect()->route('presensi.index')
            ->with('success', 'Presensi berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi)
    {
        // Hapus file foto jika ada
        if ($presensi->foto_masuk) {
            Storage::disk('public')->delete($presensi->foto_masuk);
        }
        if ($presensi->foto_keluar) {
            Storage::disk('public')->delete($presensi->foto_keluar);
        }

        $presensi->delete();

        return redirect()->route('presensi.index')
            ->with('success', 'Presensi berhasil dihapus.');
    }

    public function rekap()
    {
    $dataPresensi = Presensi::with('karyawan', 'shift')->orderBy('tanggal_presensi', 'desc')->get();
    return view('presensi.rekap', compact('dataPresensi'));
    }


    public function generateRekap(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $bulan = $request->bulan ?? date('m');
        
        try {
            RekapPresensi::generateRekap($tahun, $bulan);
            
            return redirect()->route('presensi.rekap', ['tahun' => $tahun, 'bulan' => $bulan])
                ->with('success', 'Rekap presensi berhasil digenerate untuk ' . \Carbon\Carbon::create()->month($bulan)->monthName . ' ' . $tahun);
        } catch (\Exception $e) {
            return redirect()->route('presensi.rekap')
                ->with('error', 'Gagal generate rekap: ' . $e->getMessage());
        }
    }

    // API Methods
    public function presensiMasuk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'face_data' => 'required|string',
            'confidence_score' => 'required|numeric|between:0,1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah sudah presensi hari ini
        $presensiHariIni = Presensi::where('id_karyawan', $request->id_karyawan)
            ->where('tanggal_presensi', today())
            ->first();

        if ($presensiHariIni && $presensiHariIni->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi masuk hari ini'
            ], 400);
        }

        // Validasi Face ID (minimal confidence score 0.75)
        if ($request->confidence_score < 0.75) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi wajah gagal. Confidence score terlalu rendah.'
            ], 400);
        }

        // Validasi lokasi
        if (!$this->validateLocation($request->latitude, $request->longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area presensi yang diperbolehkan'
            ], 400);
        }

        try {
            // Simpan foto
            $fotoPath = $request->file('foto')->store('presensi-masuk', 'public');
            $thumbnailPath = $request->file('foto')->store('presensi-masuk/thumbnails', 'public');

            // Get shift karyawan (default shift pertama yang aktif)
            $shift = ShiftKerja::where('status_aktif', true)->first();
            
            if (!$shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada shift aktif yang tersedia'
                ], 400);
            }

            // Hitung keterlambatan
            $jamMasuk = now();
            $jamMulaiShift = Carbon::parse($shift->jam_mulai);
            $keterlambatan = 0;
            $status = 'hadir';

            if ($jamMasuk > $jamMulaiShift) {
                $keterlambatan = $jamMulaiShift->diffInMinutes($jamMasuk);
                $keterlambatan = max(0, $keterlambatan - $shift->toleransi_keterlambatan);
                
                if ($keterlambatan > 0) {
                    $status = 'terlambat';
                }
            }

            // Create or update presensi
            $presensi = Presensi::firstOrNew([
                'id_karyawan' => $request->id_karyawan,
                'tanggal_presensi' => today()
            ]);

            $presensi->fill([
                'id_shift' => $shift->id_shift,
                'jam_masuk' => $jamMasuk->format('H:i:s'),
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'alamat_masuk' => $this->getAddressFromCoordinates($request->latitude, $request->longitude),
                'accuracy_masuk' => $request->accuracy,
                'face_id_data_masuk' => encrypt($request->face_data),
                'confidence_score_masuk' => $request->confidence_score,
                'foto_masuk' => $fotoPath,
                'foto_thumbnail_masuk' => $thumbnailPath,
                'status_kehadiran' => $status,
                'keterlambatan_menit' => $keterlambatan,
                'status_verifikasi' => 'verified'
            ]);

            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi masuk berhasil',
                'data' => [
                    'jam_masuk' => $jamMasuk->format('H:i:s'),
                    'status' => $status,
                    'keterlambatan' => $keterlambatan,
                    'shift' => $shift->nama_shift
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function presensiKeluar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_karyawan' => 'required|exists:karyawan,id_karyawan',
            'face_data' => 'required|string',
            'confidence_score' => 'required|numeric|between:0,1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'required|numeric|min:0',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek presensi hari ini
        $presensi = Presensi::where('id_karyawan', $request->id_karyawan)
            ->where('tanggal_presensi', today())
            ->first();

        if (!$presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan presensi masuk hari ini'
            ], 400);
        }

        if ($presensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi keluar hari ini'
            ], 400);
        }

        // Validasi Face ID
        if ($request->confidence_score < 0.75) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi wajah gagal. Confidence score terlalu rendah.'
            ], 400);
        }

        try {
            // Simpan foto
            $fotoPath = $request->file('foto')->store('presensi-keluar', 'public');
            $thumbnailPath = $request->file('foto')->store('presensi-keluar/thumbnails', 'public');

            $jamKeluar = now();

            // Update presensi
            $presensi->update([
                'jam_keluar' => $jamKeluar->format('H:i:s'),
                'latitude_keluar' => $request->latitude,
                'longitude_keluar' => $request->longitude,
                'alamat_keluar' => $this->getAddressFromCoordinates($request->latitude, $request->longitude),
                'accuracy_keluar' => $request->accuracy,
                'face_id_data_keluar' => encrypt($request->face_data),
                'confidence_score_keluar' => $request->confidence_score,
                'foto_keluar' => $fotoPath,
                'foto_thumbnail_keluar' => $thumbnailPath,
                'status_verifikasi' => 'verified'
            ]);

            // Hitung total jam kerja
            $presensi->hitungTotalJamKerja();

            return response()->json([
                'success' => true,
                'message' => 'Presensi keluar berhasil',
                'data' => [
                    'jam_keluar' => $jamKeluar->format('H:i:s'),
                    'total_jam_kerja' => $presensi->total_jam_kerja
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper Methods
    private function saveBase64Image($base64Data, $jenis)
    {
        try {
            // Extract the base64 data
            $image_parts = explode(";base64,", $base64Data);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            
            // Generate unique filename
            $filename = $jenis . '_' . uniqid() . '.jpg';
            $path = 'presensi/' . date('Y/m/d') . '/' . $filename;
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('presensi/' . date('Y/m/d'));
            
            // Save image
            Storage::disk('public')->put($path, $image_base64);
            
            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan foto: ' . $e->getMessage());
        }
    }

    private function validateLocation($latitude, $longitude)
    {
        $lokasiPresensi = LokasiPresensi::aktif()->get();
        
        foreach ($lokasiPresensi as $lokasi) {
            if ($lokasi->isDalamRadius($latitude, $longitude)) {
                return true;
            }
        }
        
        return false;
    }

    private function getAddressFromCoordinates($latitude, $longitude)
    {
        // In real application, you would use Google Geocoding API
        // For demo, return formatted coordinates
        return "Lat: " . number_format($latitude, 6) . ", Lng: " . number_format($longitude, 6);
    }

    private function getChartData($tahun, $bulan)
    {
        $rekap = RekapPresensi::with('karyawan')
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        $labels = [];
        $kehadiran = [];
        $terlambat = [];
        $tidakHadir = [];

        foreach ($rekap as $item) {
            $labels[] = $item->karyawan->nama_lengkap;
            $kehadiran[] = $item->persentase_kehadiran;
            $terlambat[] = $item->persentase_terlambat;
            $tidakHadir[] = $item->persentase_tidak_hadir;
        }

        return [
            'labels' => $labels,
            'kehadiran' => $kehadiran,
            'terlambat' => $terlambat,
            'tidak_hadir' => $tidakHadir
        ];
    }
    public function publicFacePage()
    {
    return view('presensi.public-face'); // Halaman absensi tanpa AdminLTE
    }

}