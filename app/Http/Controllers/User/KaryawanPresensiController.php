<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Models\LokasiPresensi;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class KaryawanPresensiController extends Controller
{
    /**
     * Display presensi page
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        // Get shift kerja
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        // Get presensi hari ini
        $today = Carbon::today();
        $presensiHariIni = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereDate('tanggal_presensi', $today)->first();

        return view('user.presensi.index', compact('karyawan', 'shift', 'presensiHariIni'));
    }

    /**
     * Store presensi data
     */
    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'required|numeric',
            'alamat' => 'required|string',
            'foto' => 'required|string',
            'tipe_absen' => 'required|in:masuk,keluar',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $today = Carbon::today();
        $now = Carbon::now();

        // Get or create presensi record
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

        // Check location radius
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

        // Process based on tipe absen
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
        // Check if already checked in
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

        // Calculate keterlambatan
        $keterlambatan = 0;
        $statusKehadiran = 'hadir';

        if ($shift) {
            $jamMulai = Carbon::parse($shift->jam_mulai);
            $toleransi = $shift->toleransi_keterlambatan ?? 15;

            if ($now->greaterThan($jamMulai->addMinutes($toleransi))) {
                $keterlambatan = $now->diffInMinutes($jamMulai);
                $statusKehadiran = 'terlambat';
            }
        }

        // Save photo
        $fotoPath = $this->savePhoto($request->foto, 'masuk', $karyawan->id_karyawan);

        // Update presensi
        $presensi->update([
            'jam_masuk' => $now->format('H:i:s'),
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'alamat_masuk' => $request->alamat,
            'accuracy_masuk' => $request->accuracy,
            'foto_masuk' => $fotoPath,
            'status_kehadiran' => $statusKehadiran,
            'keterlambatan_menit' => $keterlambatan,
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
        // Check if not checked in yet
        if (!$presensi->jam_masuk) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Anda belum melakukan absen masuk.',
                ],
                422,
            );
        }

        // Check if already checked out
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

        // Calculate total jam kerja
        $jamMasuk = Carbon::parse($presensi->jam_masuk);
        $totalJamKerja = $jamMasuk->diffInHours($now, true);

        // Save photo
        $fotoPath = $this->savePhoto($request->foto, 'keluar', $karyawan->id_karyawan);

        // Update presensi
        $presensi->update([
            'jam_keluar' => $now->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'alamat_keluar' => $request->alamat,
            'accuracy_keluar' => $request->accuracy,
            'foto_keluar' => $fotoPath,
            'total_jam_kerja' => $totalJamKerja,
            'catatan' => $presensi->catatan . ($request->catatan ? ' | ' . $request->catatan : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absen keluar berhasil! Total jam kerja: ' . number_format($totalJamKerja, 1) . ' jam',
            'data' => $presensi,
        ]);
    }

    /**
     * Save photo from base64
     */
    private function savePhoto($base64Image, $tipe, $idKaryawan)
    {
        // Remove data:image/...;base64, prefix
        $image = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Generate filename & path
        $filename = $tipe . '_' . $idKaryawan . '_' . time() . '.jpg';
        $path = 'foto-presensi/' . date('Y/m');
        Storage::makeDirectory('public/' . $path);

        // Full path
        $fullPath = $path . '/' . $filename;
        Storage::put('public/' . $fullPath, $imageData);

        // Buat thumbnail menggunakan Intervention Image v3
        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read(storage_path('app/public/' . $fullPath))->scale(width: 300); // resize lebar 300 px

            $thumbnailPath = $path . '/thumb_' . $filename;
            $img->save(storage_path('app/public/' . $thumbnailPath));
        } catch (\Throwable $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }

        return $fullPath;
    }

    /**
     * Check if location is within allowed radius
     */
    private function checkLocationRadius($latitude, $longitude, $idFakultas)
    {
        $lokasi = LokasiPresensi::where('id_fakultas', $idFakultas)->where('status_aktif', 1)->first();

        if (!$lokasi) {
            // If no location restriction, allow presensi
            return true;
        }

        // Calculate distance using Haversine formula
        $earthRadius = 6371000; // meters

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

        // Generate months for filter
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
