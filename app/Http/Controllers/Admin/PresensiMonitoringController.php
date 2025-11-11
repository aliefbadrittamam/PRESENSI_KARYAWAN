<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\Fakultas;
use App\Models\Departemen;
use App\Models\ShiftKerja;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with(['karyawan.departemen', 'karyawan.fakultas', 'karyawan.jabatan', 'shift']);

        // Filter by Date Range
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_presensi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_presensi', '<=', $request->tanggal_sampai);
        }

        // Filter by Karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('id_karyawan', $request->karyawan_id);
        }

        // Filter by Fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            });
        }

        // Filter by Departemen
        if ($request->filled('departemen_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen_id);
            });
        }

        // Filter by Shift
        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        // Filter by Status Kehadiran
        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        // Filter by Status Verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Search by Nama
        if ($request->filled('search')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', '%' . $request->search . '%')->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }

        $presensi = $query->orderBy('tanggal_presensi', 'desc')->orderBy('jam_masuk', 'desc')->paginate(20);

        // Data untuk filter dropdown
        $karyawan = Karyawan::where('status_aktif', 1)->get();
        $fakultas = Fakultas::where('status_aktif', 1)->get();
        $departemen = Departemen::where('status_aktif', 1)->get();
        $shift = ShiftKerja::where('status_aktif', 1)->get();

        return view('admin.presensi.monitoring', compact('presensi', 'karyawan', 'fakultas', 'departemen', 'shift'));
    }

    public function show($id)
    {
        $presensi = Presensi::with(['karyawan.departemen', 'karyawan.fakultas', 'karyawan.jabatan', 'shift'])->findOrFail($id);

        return view('admin.presensi.detail', compact('presensi'));
    }
}
