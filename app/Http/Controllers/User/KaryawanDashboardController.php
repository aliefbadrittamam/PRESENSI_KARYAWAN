<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\RekapPresensi;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class KaryawanDashboardController extends Controller
{
    /**
     * Display dashboard karyawan
     */
    public function index(Request $request)
    {
        // Get authenticated user
        $user = Auth::user();

        // Get karyawan data dengan relasi
        $karyawan = Karyawan::with(['jabatan', 'departemen', 'fakultas'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Get greeting berdasarkan waktu
        $greeting = $this->getGreeting();

        // Get shift kerja karyawan (ambil shift pertama atau bisa disesuaikan dengan jadwal)
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        // Get presensi hari ini
        $today = Carbon::today();
        $presensiHariIni = Presensi::where('id_karyawan', $karyawan->id_karyawan)->whereDate('tanggal_presensi', $today)->first();

        // Get month dari request atau default bulan ini
        $selectedMonth = $request->input('month', Carbon::now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);

        // Get rekap presensi bulan ini
        $rekapBulan = RekapPresensi::where('id_karyawan', $karyawan->id_karyawan)->where('tahun', $year)->where('bulan', $month)->first();

        // Jika rekap belum ada, buat data kosong
        if (!$rekapBulan) {
            $rekapBulan = $this->generateRekapBulan($karyawan->id_karyawan, $year, $month);
        }

        // Get presensi 1 minggu terakhir
        $startOfWeek = Carbon::now()->startOfWeek();
        $presensiMingguIni = Presensi::where('id_karyawan', $karyawan->id_karyawan)
            ->whereBetween('tanggal_presensi', [$startOfWeek, Carbon::now()])
            ->orderBy('tanggal_presensi', 'desc')
            ->get();

        // Generate months untuk dropdown (6 bulan terakhir)
        $months = $this->generateMonthOptions($selectedMonth);

        return view('user.dashboard', compact('karyawan', 'greeting', 'shift', 'presensiHariIni', 'rekapBulan', 'presensiMingguIni', 'months'));
    }

    /**
     * Get greeting berdasarkan waktu
     */
    private function getGreeting()
    {
        $hour = Carbon::now()->hour;

        if ($hour >= 5 && $hour < 11) {
            return 'Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Sore';
        } else {
            return 'Malam';
        }
    }

    /**
     * Generate rekap bulan jika belum ada
     */
    private function generateRekapBulan($idKaryawan, $year, $month)
    {
        // Hitung dari data presensi
        $presensi = Presensi::where('id_karyawan', $idKaryawan)->whereYear('tanggal_presensi', $year)->whereMonth('tanggal_presensi', $month)->get();

        $jumlahHadir = $presensi->where('status_kehadiran', 'hadir')->count();
        $jumlahTerlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
        $jumlahIzin = $presensi->where('status_kehadiran', 'izin')->count();
        $jumlahSakit = $presensi->where('status_kehadiran', 'sakit')->count();
        $jumlahCuti = $presensi->where('status_kehadiran', 'cuti')->count();
        $jumlahAlpha = $presensi->where('status_kehadiran', 'alpha')->count();

        // Total hari kerja dalam bulan (weekdays)
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $totalHariKerja = 0;

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $totalHariKerja++;
            }
        }

        // Return object dengan data
        return (object) [
            'id_karyawan' => $idKaryawan,
            'tahun' => $year,
            'bulan' => $month,
            'total_hari_kerja' => $totalHariKerja,
            'jumlah_hadir' => $jumlahHadir,
            'jumlah_terlambat' => $jumlahTerlambat,
            'jumlah_izin' => $jumlahIzin,
            'jumlah_sakit' => $jumlahSakit,
            'jumlah_cuti' => $jumlahCuti,
            'jumlah_alpha' => $jumlahAlpha,
        ];
    }

    /**
     * Generate month options untuk dropdown
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

        // Generate 6 bulan terakhir
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

    /**
     * Show profile karyawan
     */
    public function profile()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)
            ->with(['jabatan', 'departemen', 'fakultas'])
            ->firstOrFail();

        return view('user.profile.index', compact('karyawan', 'user'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ],
            [
                'current_password.required' => 'Password lama harus diisi',
                'new_password.required' => 'Password baru harus diisi',
                'new_password.min' => 'Password baru minimal 8 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            ],
        );

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate(
            [
                'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            ],
            [
                'photo.required' => 'Pilih foto terlebih dahulu',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
                'photo.max' => 'Ukuran foto maksimal 2MB',
            ],
        );

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        // Delete old photo if exists
        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        // Upload new photo
        $file = $request->file('photo');
        $folderPath = 'foto-karyawan';

        $filename = time() . '_' . $karyawan->nip . '.jpg';
        $fullPath = "{$folderPath}/{$filename}";

        Storage::disk('public')->makeDirectory($folderPath);

        // Resize and save
        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read($file);
            $img->scale(width: 500);
            $img->save(storage_path("app/public/{$fullPath}"));
        } catch (\Exception $e) {
            return back()->withErrors(['photo' => 'Gagal mengupload foto: ' . $e->getMessage()]);
        }

        // Update database
        $karyawan->foto = $fullPath;
        $karyawan->save();

        return back()->with('success', 'Foto profil berhasil diubah!');
    }

    /**
     * Regenerate QR Code token
     */
    public function regenerateQRCode()
    {
        $user = Auth::user();

        // Generate new UUID token
        $user->barcode_token = \Illuminate\Support\Str::uuid();
        $user->save();

        return back()->with('success', 'QR Code berhasil di-generate ulang!');
    }
}
