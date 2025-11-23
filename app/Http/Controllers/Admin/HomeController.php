<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\Fakultas;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // ==================== STATISTIK UTAMA ====================
        $totalFakultas = Fakultas::count();
        $totalDepartemen = Departemen::count();
        $totalJabatan = Jabatan::count();
        $totalKaryawan = Karyawan::where('status_aktif', 1)->count();

        // ==================== PRESENSI HARI INI ====================
        $presensiHariIni = Presensi::where('tanggal_presensi', $today)->count();
        $terlambatHariIni = Presensi::where('tanggal_presensi', $today)->where('status_kehadiran', 'terlambat')->count();
        $hadirHariIni = Presensi::where('tanggal_presensi', $today)->where('status_kehadiran', 'hadir')->count();
        $izinHariIni = Presensi::where('tanggal_presensi', $today)
            ->whereIn('status_kehadiran', ['izin', 'sakit', 'cuti'])
            ->count();
        $alphaHariIni = $totalKaryawan - $presensiHariIni;

        // Persentase kehadiran hari ini
        $persentaseKehadiranHariIni = $totalKaryawan > 0 ? round(($presensiHariIni / $totalKaryawan) * 100, 1) : 0;

        // ==================== PENGAJUAN PENDING ====================
        $izinPending = Izin::where('status_approval', 'pending')->count();
        $cutiPending = Cuti::where('status_approval', 'pending')->count();
        $totalPengajuanPending = $izinPending + $cutiPending;

        // ==================== PRESENSI BULAN INI ====================
        $presensiTotalBulanIni = Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->count();

        $terlambatBulanIni = Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'terlambat')->count();

        // Rata-rata keterlambatan
        $avgKeterlambatan = Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('keterlambatan_menit', '>', 0)->avg('keterlambatan_menit');
        $avgKeterlambatan = round($avgKeterlambatan ?? 0, 0);

        // ==================== PRESENSI TERBARU HARI INI ====================
        $latestPresensi = Presensi::with(['karyawan.departemen', 'karyawan.jabatan'])
            ->where('tanggal_presensi', $today)
            ->orderBy('jam_masuk', 'desc')
            ->limit(10)
            ->get();

        // ==================== CHART: PRESENSI 7 HARI TERAKHIR ====================
        $last7Days = [];
        $last7DaysData = [
            'hadir' => [],
            'terlambat' => [],
            'izin' => [],
            'alpha' => [],
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = $date->format('d M');

            $hadirCount = Presensi::where('tanggal_presensi', $date)->where('status_kehadiran', 'hadir')->count();
            $terlambatCount = Presensi::where('tanggal_presensi', $date)->where('status_kehadiran', 'terlambat')->count();
            $izinCount = Presensi::where('tanggal_presensi', $date)
                ->whereIn('status_kehadiran', ['izin', 'sakit', 'cuti'])
                ->count();
            $presensiCount = Presensi::where('tanggal_presensi', $date)->count();
            $alphaCount = $totalKaryawan - $presensiCount;

            $last7DaysData['hadir'][] = $hadirCount;
            $last7DaysData['terlambat'][] = $terlambatCount;
            $last7DaysData['izin'][] = $izinCount;
            $last7DaysData['alpha'][] = $alphaCount;
        }

        // ==================== CHART: PRESENSI PER DEPARTEMEN ====================
        $presensiPerDepartemen = Departemen::withCount([
            'karyawan as total_karyawan',
            'karyawan as hadir_hari_ini' => function ($query) use ($today) {
                $query->whereHas('presensi', function ($q) use ($today) {
                    $q->where('tanggal_presensi', $today);
                });
            },
        ])
            ->having('total_karyawan', '>', 0)
            ->get();

        $departemenLabels = $presensiPerDepartemen->pluck('nama_departemen')->toArray();
        $departemenHadir = $presensiPerDepartemen->pluck('hadir_hari_ini')->toArray();
        $departemenTotal = $presensiPerDepartemen->pluck('total_karyawan')->toArray();

        // ==================== TOP 5 KARYAWAN TERLAMBAT BULAN INI ====================
        $topTerlambat = Karyawan::select('karyawan.id_karyawan', 'karyawan.nip', 'karyawan.nama_lengkap', 'karyawan.foto', 'karyawan.status_aktif', DB::raw('COUNT(presensi.id_presensi) as total_terlambat'), DB::raw('SUM(presensi.keterlambatan_menit) as total_menit_terlambat'))->join('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')->whereMonth('presensi.tanggal_presensi', $currentMonth)->whereYear('presensi.tanggal_presensi', $currentYear)->where('presensi.status_kehadiran', 'terlambat')->groupBy('karyawan.id_karyawan', 'karyawan.nip', 'karyawan.nama_lengkap', 'karyawan.foto', 'karyawan.status_aktif')->orderByDesc('total_terlambat')->limit(5)->get();

        // ==================== KARYAWAN DENGAN KEHADIRAN SEMPURNA ====================
        $karyawanPerfect = Karyawan::select('karyawan.id_karyawan', 'karyawan.nip', 'karyawan.nama_lengkap', 'karyawan.foto', 'karyawan.status_aktif', DB::raw('COUNT(presensi.id_presensi) as total_hadir'))
            ->join('presensi', 'karyawan.id_karyawan', '=', 'presensi.id_karyawan')
            ->whereMonth('presensi.tanggal_presensi', $currentMonth)
            ->whereYear('presensi.tanggal_presensi', $currentYear)
            ->where('presensi.status_kehadiran', 'hadir')
            ->where('karyawan.status_aktif', 1)
            ->groupBy('karyawan.id_karyawan', 'karyawan.nip', 'karyawan.nama_lengkap', 'karyawan.foto', 'karyawan.status_aktif')
            ->havingRaw('COUNT(presensi.id_presensi) >= ?', [Carbon::now()->day])
            ->limit(5)
            ->get();

        // ==================== CHART: STATUS KEHADIRAN BULAN INI (PIE) ====================
        $statusBulanIni = [
            'hadir' => Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'hadir')->count(),
            'terlambat' => Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'terlambat')->count(),
            'izin' => Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'izin')->count(),
            'sakit' => Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'sakit')->count(),
            'cuti' => Presensi::whereMonth('tanggal_presensi', $currentMonth)->whereYear('tanggal_presensi', $currentYear)->where('status_kehadiran', 'cuti')->count(),
        ];

        // ==================== TREN PENGAJUAN 6 BULAN TERAKHIR ====================
        $trendPengajuan = [];
        $trendLabels = [];
        $trendIzin = [];
        $trendCuti = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $trendLabels[] = $month->format('M Y');

            $izinCount = Izin::whereMonth('tanggal_pengajuan', $month->month)->whereYear('tanggal_pengajuan', $month->year)->count();
            $cutiCount = Cuti::whereMonth('tanggal_pengajuan', $month->month)->whereYear('tanggal_pengajuan', $month->year)->count();

            $trendIzin[] = $izinCount;
            $trendCuti[] = $cutiCount;
        }

        return view(
            'home',
            compact(
                // Statistik Utama
                'totalFakultas',
                'totalDepartemen',
                'totalJabatan',
                'totalKaryawan',

                // Presensi Hari Ini
                'presensiHariIni',
                'terlambatHariIni',
                'hadirHariIni',
                'izinHariIni',
                'alphaHariIni',
                'persentaseKehadiranHariIni',

                // Pengajuan
                'izinPending',
                'cutiPending',
                'totalPengajuanPending',

                // Statistik Bulan Ini
                'presensiTotalBulanIni',
                'terlambatBulanIni',
                'avgKeterlambatan',

                // Data untuk tampilan
                'latestPresensi',
                'topTerlambat',
                'karyawanPerfect',

                // Chart Data
                'last7Days',
                'last7DaysData',
                'departemenLabels',
                'departemenHadir',
                'departemenTotal',
                'statusBulanIni',
                'trendLabels',
                'trendIzin',
                'trendCuti',
            ),
        );
    }
}
