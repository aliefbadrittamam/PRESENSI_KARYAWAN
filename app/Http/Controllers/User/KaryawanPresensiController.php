<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Models\LokasiPresensi;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // atau Imagick\Driver

class KaryawanPresensiController extends Controller
{
    /**
     * Display presensi page
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $shift = ShiftKerja::where('status_aktif', 1)->first();

        $today = Carbon::today();
        $presensiHariIni = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereDate('tanggal_presensi', $today)->first();

        return view('user.presensi.index', compact('karyawan', 'shift', 'presensiHariIni'));
    }

    /**
     * Store presensi data
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                // 'accuracy' => 'required|numeric|min:0|max:1000', // Maksimal 1000 meter
                'alamat' => 'required|string|max:500',
                'foto' => 'required|string',
                'tipe_absen' => 'required|in:masuk,keluar',
                'catatan' => 'nullable|string|max:500',
            ],
            [
                // 'accuracy.max' => 'Akurasi GPS terlalu rendah. Pastikan Anda berada di area terbuka.',
                'latitude.between' => 'Koordinat latitude tidak valid.',
                'longitude.between' => 'Koordinat longitude tidak valid.',
            ],
        );

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $today = Carbon::today();

        $presensi = Presensi::firstOrCreate(
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'tanggal_presensi' => $today,
            ],
            [
                'id_shift' => $this->getActiveShift()?->id_shift,
                'status_kehadiran' => 'alpha',
                'status_verifikasi' => 'pending',
            ],
        );

        $isInRadius = $this->checkLocationRadius($request->latitude, $request->longitude, $karyawan->id_fakultas);

        if (!$isInRadius) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda berada di luar radius kantor. Presensi tidak dapat dilakukan.',
                ],
                422,
            );
        }

        if ($request->tipe_absen === 'masuk') {
            return $this->storeAbsenMasuk($presensi, $request, $karyawan);
        } else {
            return $this->storeAbsenKeluar($presensi, $request, $karyawan);
        }
    }

    /**
     * Store absen masuk
     */
    private function storeAbsenMasuk($presensi, $request, $karyawan)
    {
        if ($presensi->jam_masuk) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen masuk hari ini.',
                ],
                422,
            );
        }

        $now = Carbon::now();
        $shift = $this->getActiveShift();

        $keterlambatan = 0;
        $statusKehadiran = 'hadir';

        if ($shift) {
            $jamMulai = Carbon::parse($shift->jam_mulai);
            $toleransi = $shift->toleransi_keterlambatan ?? 15;
            $batasWaktu = $jamMulai->copy()->addMinutes($toleransi);

            // Fix: Hitung keterlambatan dengan benar
            if ($now->greaterThan($batasWaktu)) {
                $keterlambatan = $now->diffInMinutes($jamMulai);
                $statusKehadiran = 'terlambat';
            }
        }

        // Validasi dan sanitasi accuracy
        $accuracy = floatval($request->accuracy);

        // Jika accuracy terlalu besar (>1000m atau 1km), set ke nilai maksimum
        if ($accuracy > 1000) {
            $accuracy = 1000;
            \Log::warning("GPS accuracy too large: {$request->accuracy}m, capped to 1000m");
        }

        // Jika accuracy negatif, set ke 0
        if ($accuracy < 0) {
            $accuracy = 0;
        }

        $fotoPath = $this->savePhoto($request->foto, 'masuk', $karyawan->id_karyawan);

        $presensi->update([
            'jam_masuk' => $now->format('H:i:s'),
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'alamat_masuk' => $request->alamat,
            'accuracy_masuk' => $accuracy, // Gunakan nilai yang sudah divalidasi
            'foto_masuk' => $fotoPath,
            'status_kehadiran' => $statusKehadiran,
            'keterlambatan_menit' => max(0, $keterlambatan), // Pastikan tidak negatif
            'catatan' => $request->catatan,
            'status_verifikasi' => 'verified',
        ]);

        return response()->json([
            'success' => true,
            'message' => $statusKehadiran === 'terlambat' ? "Absen masuk berhasil. Anda terlambat {$keterlambatan} menit." : 'Absen masuk berhasil!',
            'data' => $presensi,
        ]);
    }

    /**
     * Store absen keluar
     */
    private function storeAbsenKeluar($presensi, $request, $karyawan)
    {
        if (!$presensi->jam_masuk) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda belum melakukan absen masuk.',
                ],
                422,
            );
        }

        if ($presensi->jam_keluar) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda sudah melakukan absen keluar hari ini.',
                ],
                422,
            );
        }

        $now = Carbon::now();

        $jamMasuk = Carbon::parse($presensi->jam_masuk);
        $totalJamKerja = $jamMasuk->diffInHours($now, true);

        // Validasi dan sanitasi accuracy
        $accuracy = floatval($request->accuracy);
        if ($accuracy > 1000) {
            $accuracy = 1000;
            \Log::warning("GPS accuracy too large: {$request->accuracy}m, capped to 1000m");
        }
        if ($accuracy < 0) {
            $accuracy = 0;
        }

        $fotoPath = $this->savePhoto($request->foto, 'keluar', $karyawan->id_karyawan);

        $presensi->update([
            'jam_keluar' => $now->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'alamat_keluar' => $request->alamat,
            'accuracy_keluar' => $accuracy, // Gunakan nilai yang sudah divalidasi
            'foto_keluar' => $fotoPath,
            'total_jam_kerja' => round($totalJamKerja, 2),
            'catatan' => $presensi->catatan . ($request->catatan ? ' | ' . $request->catatan : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil! Total jam kerja: ' . number_format($totalJamKerja, 1) . ' jam',
            'data' => $presensi,
        ]);
    }

    /**
     * Backward compatibility methods
     */
    public function storeMasuk(Request $request)
    {
        $request->merge(['tipe_absen' => 'masuk']);
        return $this->store($request);
    }

    public function storeKeluar(Request $request)
    {
        $request->merge(['tipe_absen' => 'keluar']);
        return $this->store($request);
    }

    /**
     * Save photo from base64
     */
    /**
     * Save photo from base64
     */
    /**
     * Save photo from base64
     * Struktur folder: storage/app/public/FotoPresensi/NIP_Karyawan/YYYY-MM/
     */
    /**
     * Save photo from base64
     * Struktur folder: FotoPresensi/YYYY-MM/Masuk atau Keluar/TGL_WAKTU_NAMA(NIP).jpg
     * Contoh: FotoPresensi/2025-11/Masuk/02-11-2025_134308_John_Doe(123456).jpg
     */
    private function savePhoto($base64Image, $tipe, $idKaryawan)
    {
        // Ambil data karyawan untuk mendapatkan nama dan NIP
        $karyawan = Karyawan::find($idKaryawan);
        $namaKaryawan = $karyawan ? $karyawan->nama_lengkap : 'Unknown';
        $nip = $karyawan ? $karyawan->nip : $idKaryawan;

        // Bersihkan nama (hapus spasi dan karakter special, ganti dengan underscore)
        $namaClean = str_replace(' ', '_', $namaKaryawan);
        $namaClean = preg_replace('/[^A-Za-z0-9_]/', '', $namaClean);

        // Hapus prefix base64 dan decode data
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Buat struktur folder: FotoPresensi/YYYY-MM/Masuk atau Keluar
        $yearMonth = date('Y-m'); // Format: 2025-11
        $tipeFolderName = ucfirst(strtolower($tipe)); // "Masuk" atau "Keluar"
        $folderPath = "FotoPresensi/{$yearMonth}/{$tipeFolderName}";

        // Buat nama file: TGL_WAKTU_NAMA(NIP).jpg
        // Format: 02-11-2025_134308_John_Doe(123456).jpg
        $tanggal = date('d-m-Y'); // 02-11-2025
        $waktu = date('His'); // 134308
        $filename = "{$tanggal}_{$waktu}_{$namaClean}({$nip}).jpg";

        // Path lengkap file
        $fullPath = "{$folderPath}/{$filename}";

        // Pastikan direktori ada
        Storage::disk('public')->makeDirectory($folderPath);

        // Simpan file utama
        Storage::disk('public')->put($fullPath, $imageData);

        try {
            // Buat thumbnail (versi lebih kecil) - simpan di folder yang sama
            $thumbnailFilename = "thumb_{$filename}";
            $thumbnailPath = "{$folderPath}/{$thumbnailFilename}";

            $manager = new ImageManager(new Driver());
            $img = $manager->read(storage_path("app/public/{$fullPath}"));
            $img->scale(width: 300);
            $img->save(storage_path("app/public/{$thumbnailPath}"));

            \Log::info("Photo saved successfully: {$fullPath}");
            \Log::info("Thumbnail created successfully: {$thumbnailPath}");
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }

        // Return path relatif (untuk disimpan di database)
        return $fullPath;
    }

    /**
     * Check if location is within allowed radius
     */
    private function checkLocationRadius($latitude, $longitude, $idFakultas)
    {
        $lokasi = LokasiPresensi::where('id_fakultas', $idFakultas)->where('status_aktif', 1)->first();

        if (!$lokasi) {
            return true;
        }

        $earthRadius = 6371000;

        $lat1 = deg2rad($lokasi->latitude);
        $lon1 = deg2rad($lokasi->longitude);
        $lat2 = deg2rad($latitude);
        $lon2 = deg2rad($longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance <= $lokasi->radius_meter;
    }

    /**
     * Get active shift
     */
    private function getActiveShift()
    {
        return ShiftKerja::where('status_aktif', 1)->first();
    }

    /**
     * Show presensi history
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$tahun, $bulan_num] = explode('-', $bulan);

        $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereYear('tanggal_presensi', $tahun)->whereMonth('tanggal_presensi', $bulan_num)->orderBy('tanggal_presensi', 'desc')->paginate(20);

        $months = $this->generateMonthOptions($bulan);

        return view('user.presensi.history', compact('karyawan', 'presensi', 'months', 'bulan'));
    }

    /**
     * Show presensi detail
     */
    public function show($id)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)->where('id_presensi', $id)->with('shift')->firstOrFail();

        return view('user.presensi.show', compact('karyawan', 'presensi'));
    }

    /**
     * Show rekap presensi user
     */
    public function rekap(Request $request)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        [$tahun, $bulan_num] = explode('-', $bulan);

        // Get presensi data
        $presensiList = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereYear('tanggal_presensi', $tahun)->whereMonth('tanggal_presensi', $bulan_num)->orderBy('tanggal_presensi', 'desc')->get();

        // Calculate statistics
        $startDate = Carbon::createFromDate($tahun, $bulan_num, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan_num, 1)->endOfMonth();

        // Count working days (exclude weekends)
        $totalHariKerja = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if (!$currentDate->isWeekend()) {
                $totalHariKerja++;
            }
            $currentDate->addDay();
        }

        $jumlahHadir = $presensiList->where('status_kehadiran', 'hadir')->count();
        $jumlahTerlambat = $presensiList->where('status_kehadiran', 'terlambat')->count();
        $jumlahIzin = $presensiList->where('status_kehadiran', 'izin')->count();
        $jumlahSakit = $presensiList->where('status_kehadiran', 'sakit')->count();
        $jumlahCuti = $presensiList->where('status_kehadiran', 'cuti')->count();
        $jumlahAlpha = $totalHariKerja - ($jumlahHadir + $jumlahTerlambat + $jumlahIzin + $jumlahSakit + $jumlahCuti);

        $totalMenitTerlambat = $presensiList->sum('keterlambatan_menit');
        $totalJamKerja = $presensiList->sum('total_jam_kerja');

        $persentaseKehadiran = $totalHariKerja > 0 ? (($jumlahHadir + $jumlahTerlambat) / $totalHariKerja) * 100 : 0;

        $rekap = [
            'total_hari_kerja' => $totalHariKerja,
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_terlambat' => $jumlahTerlambat,
            'jumlah_izin' => $jumlahIzin,
            'jumlah_sakit' => $jumlahSakit,
            'jumlah_cuti' => $jumlahCuti,
            'jumlah_alpha' => max(0, $jumlahAlpha),
            'persentase_kehadiran' => round($persentaseKehadiran, 2),
            'total_menit_terlambat' => $totalMenitTerlambat,
            'rata_rata_terlambat' => $jumlahTerlambat > 0 ? round($totalMenitTerlambat / $jumlahTerlambat, 2) : 0,
            'total_jam_kerja' => round($totalJamKerja, 2),
        ];

        $months = $this->generateMonthOptions($bulan);

        return view('user.presensi.rekap', compact('karyawan', 'presensiList', 'rekap', 'months', 'bulan'));
    }

    /**
     * Generate month options
     */
    private function generateMonthOptions($selectedMonth)
    {
        $months = [];
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $value = $date->format('Y-m');
            $label = $monthNames[$date->month] . ' ' . $date->year;

            $months[] = [
                'value' => $value,
                'label' => $label,
                'selected' => $value === $selectedMonth,
            ];
        }

        return $months;
    }
}
