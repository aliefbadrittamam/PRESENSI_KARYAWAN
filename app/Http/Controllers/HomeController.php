<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\Fakultas;
use App\Models\Departemen;
use App\Models\Jabatan;
use Carbon\Carbon;

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
        $today = today();
        
        // Statistics for dashboard 
        $stats = [
            'total_fakultas' => Fakultas::count(), 
            'total_departemen' => Departemen::count(), 
            'total_jabatan' => Jabatan::count(),
            'total_karyawan' => Karyawan::count(),
            'presensi_hari_ini' => Presensi::where('tanggal_presensi', $today)->count(),
            'karyawan_terlambat' => Presensi::where('tanggal_presensi', $today)
                                        ->where('status_kehadiran', 'terlambat')
                                        ->count(),
        ];

        // Recent presensi 
        $recentPresensi = Presensi::with(['karyawan', 'karyawan.departemen'])
            ->where('tanggal_presensi', $today)
            ->orderBy('jam_masuk', 'desc')
            ->limit(5)
            ->get();

        return view('home', compact('stats', 'recentPresensi'));
    }
}