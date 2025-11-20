<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\Fakultas;
use App\Models\Departemen;
use App\Models\ShiftKerja;
use Illuminate\Http\Request;

class PresensiMonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Query dengan relasi yang sesuai dengan Model
        $query = Presensi::with(['karyawan.departemen', 'karyawan.fakultas', 'karyawan.jabatan', 'shift'])->whereHas('karyawan'); // ✅ TAMBAHKAN INI - hanya ambil presensi yang punya karyawan

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

        // Search by Nama/NIP
        if ($request->filled('search')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', '%' . $request->search . '%')->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Order dan Paginate
        $presensi = $query->orderBy('tanggal_presensi', 'desc')->orderBy('jam_masuk', 'desc')->paginate(20);

        // Data untuk filter dropdown
        $karyawan = Karyawan::where('status_aktif', 1)->orderBy('nama_lengkap')->get();

        $fakultas = Fakultas::where('status_aktif', 1)->orderBy('nama_fakultas')->get();

        $departemen = Departemen::where('status_aktif', 1)->orderBy('nama_departemen')->get();

        $shift = ShiftKerja::where('status_aktif', 1)->orderBy('nama_shift')->get();

        return view('admin.presensi.monitoring', compact('presensi', 'karyawan', 'fakultas', 'departemen', 'shift'));
    }

    public function show($id)
    {
        // Gunakan primaryKey yang benar dari Model
        $presensi = Presensi::with([
            'karyawan.departemen',
            'karyawan.fakultas',
            'karyawan.jabatan',
            'shift', // Relasi shift sesuai dengan Model
        ])->findOrFail($id);

        return view('admin.presensi.monitoring-detail', compact('presensi'));
    }

    public function exportExcel(Request $request)
    {
        // Query dengan filter yang sama seperti index
        $query = Presensi::with(['karyawan.departemen', 'karyawan.fakultas', 'karyawan.jabatan', 'shift']);

        // Apply semua filter
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_presensi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_presensi', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('id_karyawan', $request->karyawan_id);
        }

        if ($request->filled('fakultas_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            });
        }

        if ($request->filled('departemen_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen_id);
            });
        }

        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('search')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', '%' . $request->search . '%')->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('tanggal_presensi', 'desc')->orderBy('jam_masuk', 'desc')->get();

        // TODO: Implementasi export Excel dengan PhpSpreadsheet atau Maatwebsite/Excel
        return response()->json([
            'message' => 'Export Excel akan segera diimplementasikan',
            'total_records' => $data->count(),
        ]);
    }

    public function exportPDF(Request $request)
    {
        // Query dengan filter yang sama seperti index
        $query = Presensi::with(['karyawan.departemen', 'karyawan.fakultas', 'karyawan.jabatan', 'shift']);

        // Apply semua filter
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_presensi', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_presensi', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('id_karyawan', $request->karyawan_id);
        }

        if ($request->filled('fakultas_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_fakultas', $request->fakultas_id);
            });
        }

        if ($request->filled('departemen_id')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('id_departemen', $request->departemen_id);
            });
        }

        if ($request->filled('shift_id')) {
            $query->where('id_shift', $request->shift_id);
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('search')) {
            $query->whereHas('karyawan', function ($q) use ($request) {
                $q->where('nama_lengkap', 'LIKE', '%' . $request->search . '%')->orWhere('nip', 'LIKE', '%' . $request->search . '%');
            });
        }

        $data = $query->orderBy('tanggal_presensi', 'desc')->orderBy('jam_masuk', 'desc')->get();

        // TODO: Implementasi export PDF dengan DomPDF atau TCPDF
        return response()->json([
            'message' => 'Export PDF akan segera diimplementasikan',
            'total_records' => $data->count(),
        ]);
    }
}
