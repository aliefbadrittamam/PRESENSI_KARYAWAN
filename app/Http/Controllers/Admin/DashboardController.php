<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Presensi;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Statistik utama
        $totalKaryawan = Karyawan::count();

        $hadirHariIni = Presensi::whereDate('created_at', $today)->count();

        $tidakHadir = $totalKaryawan - $hadirHariIni;

        $persentaseKehadiran = $totalKaryawan > 0
            ? round(($hadirHariIni / $totalKaryawan) * 100, 2)
            : 0;

        // Presensi terbaru
        $presensiTerbaru = Presensi::with('karyawan')
            ->latest()
            ->take(5)
            ->get();

        // Grafik presensi 7 hari terakhir
        $grafik = Presensi::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        $labels = $grafik->pluck('tanggal')->map(fn($t) => Carbon::parse($t)->format('d M'));
        $values = $grafik->pluck('total');

        return view('dashboard', compact(
            'totalKaryawan',
            'hadirHariIni',
            'tidakHadir',
            'persentaseKehadiran',
            'presensiTerbaru',
            'labels',
            'values'
        ));
    }
}
