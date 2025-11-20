<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LokasiPresensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class KaryawanPresensiController extends Controller
{
    /**
     * Display presensi page
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)
            ->with(['fakultas', 'departemen', 'jabatan'])
            ->firstOrFail();

        // Get shift aktif
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        // Get lokasi presensi berdasarkan fakultas karyawan
        $lokasiPresensi = LokasiPresensi::where('id_fakultas', $karyawan->id_fakultas)->where('status_aktif', 1)->first();

        // Jika tidak ada lokasi spesifik fakultas, ambil lokasi umum
        if (!$lokasiPresensi) {
            $lokasiPresensi = LokasiPresensi::whereNull('id_fakultas')->where('status_aktif', 1)->first();
        }

        $today = Carbon::today();
        $presensiHariIni = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereDate('tanggal_presensi', $today)->first();

        return view('user.presensi.index', compact('karyawan', 'shift', 'presensiHariIni', 'lokasiPresensi'));
    }

    /**
     * Store presensi data
     * ✅ ENHANCED: Added DB Transaction, Unique Constraint Handling, Locking
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'alamat' => 'required|string|max:500',
                'foto' => 'required|string',
                'tipe_absen' => 'required|in:masuk,keluar',
                'catatan' => 'nullable|string|max:500',
                'mode_demo' => 'nullable|boolean',
            ],
            [
                'latitude.between' => 'Koordinat latitude tidak valid.',
                'longitude.between' => 'Koordinat longitude tidak valid.',
            ],
        );

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();
        $today = Carbon::today();
        $modeDemo = $request->input('mode_demo', false);

        // ✅ ENHANCEMENT: Wrap dalam DB Transaction untuk atomic operation
        return DB::transaction(function () use ($request, $karyawan, $today, $modeDemo) {
            // ✅ ENHANCEMENT: Lock row untuk prevent race condition
            // Gunakan lockForUpdate() untuk pessimistic locking
            $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_presensi', $today)
                ->lockForUpdate() // 🔒 Lock row saat query
                ->first();

            // ✅ Jika belum ada, create dengan handling duplicate entry
            if (!$presensi) {
                try {
                    $presensi = Presensi::create([
                        'id_karyawan' => $karyawan->id_karyawan,
                        'tanggal_presensi' => $today,
                        'id_shift' => $this->getActiveShift()?->id_shift,
                        'status_kehadiran' => 'alpha',
                        'status_verifikasi' => 'pending',
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // ✅ ENHANCEMENT: Handle unique constraint violation
                    if ($e->getCode() === '23000') {
                        // Duplicate entry
                        // Retry get dengan lock
                        $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)->where('tanggal_presensi', $today)->lockForUpdate()->firstOrFail();
                    } else {
                        throw $e; // Re-throw jika error lain
                    }
                }
            }

            // Validasi lokasi jika BUKAN mode demo
            if (!$modeDemo) {
                $isInRadius = $this->checkLocationRadius($request->latitude, $request->longitude, $karyawan->id_fakultas);

                if (!$isInRadius) {
                    $lokasiInfo = $this->getLokasiInfo($karyawan->id_fakultas);
                    return response()->json(
                        [
                            'success' => false,
                            'message' => "Anda berada di luar radius kantor {$lokasiInfo['nama']}. Radius yang diizinkan: {$lokasiInfo['radius']} meter.",
                            'lokasi_kantor' => $lokasiInfo,
                        ],
                        422,
                    );
                }
            }

            // Process absen masuk/keluar
            if ($request->tipe_absen === 'masuk') {
                return $this->storeAbsenMasuk($presensi, $request, $karyawan, $modeDemo);
            } else {
                return $this->storeAbsenKeluar($presensi, $request, $karyawan, $modeDemo);
            }
        }); // End transaction
    }

    /**
     * Store absen masuk
     * ✅ ENHANCED: File upload dengan unique filename (UUID)
     */
    private function storeAbsenMasuk($presensi, $request, $karyawan, $modeDemo = false)
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

            // --- PERBAIKAN ADA DI SINI ---
            // Tambahkan (int) sebelum mengambil data
            $toleransi = (int) ($shift->toleransi_keterlambatan ?? 15);

            // Sekarang addMinutes menerima integer, bukan string
            $batasWaktu = $jamMulai->copy()->addMinutes($toleransi);

            if ($now->greaterThan($batasWaktu)) {
                $keterlambatan = $now->diffInMinutes($jamMulai);
                $statusKehadiran = 'terlambat';
            }
        }

        // Validasi dan sanitasi accuracy
        $accuracy = floatval($request->accuracy ?? 0);
        if ($accuracy > 1000) {
            $accuracy = 1000;
        }
        if ($accuracy < 0) {
            $accuracy = 0;
        }

        // Simpan foto dengan unique filename
        $fotoPath = $this->savePhotoWithUUID($request->foto, 'masuk', $karyawan->id_karyawan);

        // Tambahkan info mode demo di catatan
        $catatan = $request->catatan;
        if ($modeDemo) {
            $catatan = ($catatan ? $catatan . ' | ' : '') . '[MODE DEMO]';
        }

        $presensi->update([
            'jam_masuk' => $now->format('H:i:s'),
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'alamat_masuk' => $request->alamat,
            'accuracy_masuk' => $accuracy,
            'foto_masuk' => $fotoPath,
            'status_kehadiran' => $statusKehadiran,
            'keterlambatan_menit' => max(0, $keterlambatan),
            'catatan' => $catatan,
            'status_verifikasi' => 'verified',
        ]);

        if ($statusKehadiran === 'terlambat') {
            SendWhatsAppNotification::dispatch($karyawan->nomor_telepon, 'keterlambatan_warning', [
                'nama' => $karyawan->nama_lengkap,
                'tanggal' => $now->format('d F Y'),
                'jam_masuk' => $now->format('H:i'),
                'keterlambatan' => $keterlambatan,
            ]);
        }

        $message = $statusKehadiran === 'terlambat' ? "Absen masuk berhasil. Anda terlambat {$keterlambatan} menit." : 'Absen masuk berhasil!';

        if ($modeDemo) {
            $message .= ' (Mode Demo)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $presensi,
        ]);
    }

    /**
     * Store absen keluar
     * ✅ ENHANCED: File upload dengan unique filename (UUID)
     */
    private function storeAbsenKeluar($presensi, $request, $karyawan, $modeDemo = false)
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
        $accuracy = floatval($request->accuracy ?? 0);
        if ($accuracy > 1000) {
            $accuracy = 1000;
        }
        if ($accuracy < 0) {
            $accuracy = 0;
        }

        // ✅ ENHANCEMENT: Simpan foto dengan unique filename
        $fotoPath = $this->savePhotoWithUUID($request->foto, 'keluar', $karyawan->id_karyawan);

        // Tambahkan info mode demo di catatan
        $catatan = $presensi->catatan . ($request->catatan ? ' | ' . $request->catatan : '');
        if ($modeDemo) {
            $catatan .= ' | [MODE DEMO]';
        }

        $presensi->update([
            'jam_keluar' => $now->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'alamat_keluar' => $request->alamat,
            'accuracy_keluar' => $accuracy,
            'foto_keluar' => $fotoPath,
            'total_jam_kerja' => round($totalJamKerja, 2),
            'catatan' => $catatan,
        ]);

        $message = 'Absen keluar berhasil! Total jam kerja: ' . number_format($totalJamKerja, 1) . ' jam';
        if ($modeDemo) {
            $message .= ' (Mode Demo)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $presensi,
        ]);
    }

    /**
     * Get lokasi info
     */
    private function getLokasiInfo($idFakultas)
    {
        $lokasi = LokasiPresensi::where('id_fakultas', $idFakultas)->where('status_aktif', 1)->first();

        if (!$lokasi) {
            $lokasi = LokasiPresensi::whereNull('id_fakultas')->where('status_aktif', 1)->first();
        }

        if (!$lokasi) {
            return [
                'nama' => 'Tidak ada lokasi terdaftar',
                'radius' => 0,
                'latitude' => 0,
                'longitude' => 0,
            ];
        }

        return [
            'nama' => $lokasi->nama_lokasi,
            'radius' => $lokasi->radius_meter,
            'latitude' => $lokasi->latitude,
            'longitude' => $lokasi->longitude,
        ];
    }

    /**
     * ✅ ENHANCEMENT: Save photo with UUID untuk prevent filename collision
     */
    private function savePhotoWithUUID($base64Image, $tipe, $idKaryawan)
    {
        $karyawan = Karyawan::find($idKaryawan);
        $namaKaryawan = $karyawan ? $karyawan->nama_lengkap : 'Unknown';
        $nip = $karyawan ? $karyawan->nip : $idKaryawan;

        $namaClean = str_replace(' ', '_', $namaKaryawan);
        $namaClean = preg_replace('/[^A-Za-z0-9_]/', '', $namaClean);

        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $yearMonth = date('Y-m');
        $tipeFolderName = ucfirst(strtolower($tipe));
        $folderPath = "FotoPresensi/{$yearMonth}/{$tipeFolderName}";

        // ✅ ENHANCEMENT: Generate unique filename dengan UUID + microtime
        $uuid = Str::uuid()->toString();
        $microtime = microtime(true);
        $filename = "{$namaClean}_{$nip}_{$uuid}_{$microtime}.jpg";

        $fullPath = "{$folderPath}/{$filename}";

        // ✅ ENHANCEMENT: Error handling untuk storage
        try {
            Storage::disk('public')->makeDirectory($folderPath);
            Storage::disk('public')->put($fullPath, $imageData);
        } catch (\Exception $e) {
            Log::error('Photo upload failed: ' . $e->getMessage());
            throw new \Exception('Gagal menyimpan foto. Silakan coba lagi.');
        }

        // Thumbnail creation (optional)
        try {
            $thumbnailFilename = "thumb_{$filename}";
            $thumbnailPath = "{$folderPath}/{$thumbnailFilename}";

            $manager = new ImageManager(new Driver());
            $img = $manager->read(storage_path("app/public/{$fullPath}"));
            $img->scale(width: 300);
            $img->save(storage_path("app/public/{$thumbnailPath}"));
        } catch (\Exception $e) {
            Log::error('Thumbnail creation failed: ' . $e->getMessage());
            // Tidak throw error, karena thumbnail optional
        }

        return $fullPath;
    }

    /**
     * OLD savePhoto - DEPRECATED (kept for reference)
     * ❌ PROBLEM: Filename collision pada detik yang sama
     */
    private function savePhoto($base64Image, $tipe, $idKaryawan)
    {
        return $this->savePhotoWithUUID($base64Image, $tipe, $idKaryawan);
    }

    /**
     * Check if location is within allowed radius
     */
    private function checkLocationRadius($latitude, $longitude, $idFakultas)
    {
        // Cari lokasi berdasarkan fakultas karyawan
        $lokasi = LokasiPresensi::where('id_fakultas', $idFakultas)->where('status_aktif', 1)->first();

        // Jika tidak ada, cari lokasi umum (tanpa fakultas)
        if (!$lokasi) {
            $lokasi = LokasiPresensi::whereNull('id_fakultas')->where('status_aktif', 1)->first();
        }

        // Jika tetap tidak ada lokasi, izinkan (return true)
        if (!$lokasi) {
            return true;
        }

        $earthRadius = 6371000; // meter

        $lat1 = deg2rad((float) $lokasi->latitude);
        $lon1 = deg2rad((float) $lokasi->longitude);
        $lat2 = deg2rad((float) $latitude);
        $lon2 = deg2rad((float) $longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        Log::info('Distance check', [
            'lokasi' => $lokasi->nama_lokasi,
            'fakultas_id' => $idFakultas,
            'radius_allowed' => $lokasi->radius_meter,
            'distance_actual' => $distance,
            'is_valid' => $distance <= (float) $lokasi->radius_meter,
        ]);

        return $distance <= (float) $lokasi->radius_meter;
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
