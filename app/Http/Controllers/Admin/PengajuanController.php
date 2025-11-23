<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Izin;
use App\Models\Cuti;
use App\Models\Presensi;
use App\Models\Karyawan;
use Carbon\Carbon;

class PengajuanController extends Controller
{
    /**
     * ✅ FIXED: Display a listing of all pengajuan (izin & cuti) with stats and filters
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filter = $request->get('filter', 'semua'); // semua, izin, cuti
        $status = $request->get('status', 'pending'); // pending, approved, rejected, all

        // Build query for izin
        $izinQuery = Izin::with(['karyawan.jabatan', 'approver']);
        
        // Build query for cuti
        $cutiQuery = Cuti::with(['karyawan.jabatan', 'approver']);

        // Apply status filter
        if ($status !== 'all') {
            $izinQuery->where('status_approval', $status);
            $cutiQuery->where('status_approval', $status);
        }

        // Get data based on filter
        $pengajuan = collect();
        
        if ($filter === 'semua' || $filter === 'izin') {
            $pengajuan = $pengajuan->merge($izinQuery->get());
        }
        
        if ($filter === 'semua' || $filter === 'cuti') {
            $pengajuan = $pengajuan->merge($cutiQuery->get());
        }

        // Sort by created_at descending
        $pengajuan = $pengajuan->sortByDesc('tanggal_pengajuan')->values();

        // ✅ Calculate statistics
        $stats = [
            // Total counts by status (all types)
            'total_pending' => Izin::where('status_approval', 'pending')->count() + 
                               Cuti::where('status_approval', 'pending')->count(),
            'total_approved' => Izin::where('status_approval', 'approved')->count() + 
                                Cuti::where('status_approval', 'approved')->count(),
            'total_rejected' => Izin::where('status_approval', 'rejected')->count() + 
                                Cuti::where('status_approval', 'rejected')->count(),
            
            // Izin counts
            'izin_pending' => Izin::where('status_approval', 'pending')->count(),
            'izin_approved' => Izin::where('status_approval', 'approved')->count(),
            'izin_rejected' => Izin::where('status_approval', 'rejected')->count(),
            'izin_total' => Izin::count(),
            
            // Cuti counts
            'cuti_pending' => Cuti::where('status_approval', 'pending')->count(),
            'cuti_approved' => Cuti::where('status_approval', 'approved')->count(),
            'cuti_rejected' => Cuti::where('status_approval', 'rejected')->count(),
            'cuti_total' => Cuti::count(),
        ];

        return view('admin.pengajuan.index', compact('pengajuan', 'stats', 'filter', 'status'));
    }

    /**
     * ✅ FIXED: Show detail Izin - menggunakan view yang sudah ada
     */
    public function showIzin($id)
    {
        $izin = Izin::with(['karyawan.jabatan', 'karyawan.departemen', 'karyawan.fakultas', 'approver'])->findOrFail($id);
        
        // Get related presensi records
        $presensiList = Presensi::where('id_karyawan', $izin->id_karyawan)
            ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
            ->orderBy('tanggal_presensi', 'asc')
            ->get();

        // ✅ FIXED: Gunakan view yang sudah ada: detail-izin.blade.php
        return view('admin.pengajuan.detail-izin', compact('izin', 'presensiList'));
    }

    /**
     * ✅ FIXED: Show detail Cuti - menggunakan view yang sudah ada
     */
    public function showCuti($id)
    {
        $cuti = Cuti::with(['karyawan.jabatan', 'karyawan.departemen', 'karyawan.fakultas', 'approver'])->findOrFail($id);
        
        // Get related presensi records
        $presensiList = Presensi::where('id_karyawan', $cuti->id_karyawan)
            ->whereBetween('tanggal_presensi', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
            ->orderBy('tanggal_presensi', 'asc')
            ->get();

        // ✅ FIXED: Gunakan view yang sudah ada: detail-cuti.blade.php
        return view('admin.pengajuan.detail-cuti', compact('cuti', 'presensiList'));
    }

    /**
     * ✅ UPDATED: Approve Izin dengan update presensi
     */
    public function approveIzin($id)
    {
        try {
            DB::beginTransaction();

            $izin = Izin::lockForUpdate()->findOrFail($id);

            // Validasi status
            if ($izin->status_approval !== 'pending') {
                throw new \Exception('Izin sudah diproses sebelumnya.');
            }

            // Update status izin
            $izin->update([
                'status_approval' => 'approved',
                'approved_by' => Auth::id(),
                'tanggal_approval' => Carbon::now(),
                'alasan_penolakan' => null,
            ]);

            // ✅ Update status presensi yang terkait menjadi 'verified'
            $updatedCount = Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', $izin->tipe_izin)
                ->where('status_verifikasi', 'pending')
                ->update([
                    'status_verifikasi' => 'verified',
                    'catatan' => DB::raw("CONCAT(COALESCE(catatan, ''), ' - Disetujui oleh Admin')"),
                ]);

            DB::commit();

            Log::info("Izin approved: ID {$id} by user " . Auth::id() . ". Updated {$updatedCount} presensi records.");

            return redirect()->route('admin.pengajuan.index')->with('success', "Izin berhasil disetujui! {$updatedCount} data presensi diverifikasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * ✅ UPDATED: Reject Izin dengan DELETE presensi otomatis
     * FIX BUG #2: Presensi akan dihapus saat izin ditolak
     */
    public function rejectIzin(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter',
        ]);

        try {
            DB::beginTransaction();

            $izin = Izin::lockForUpdate()->findOrFail($id);

            // Validasi status
            if ($izin->status_approval !== 'pending') {
                throw new \Exception('Izin sudah diproses sebelumnya.');
            }

            // Update status izin
            $izin->update([
                'status_approval' => 'rejected',
                'approved_by' => Auth::id(),
                'tanggal_approval' => Carbon::now(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // ✅ FIX BUG 2: HAPUS semua presensi yang terkait dengan izin ini
            $deletedCount = Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', $izin->tipe_izin)
                ->where('status_verifikasi', 'pending')
                ->delete();

            DB::commit();

            Log::info("Izin rejected: ID {$id} by user " . Auth::id() . ". Deleted {$deletedCount} presensi records.");

            return redirect()->route('admin.pengajuan.index')->with('success', "Izin ditolak dan {$deletedCount} data presensi dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Approve Cuti (existing method - no changes needed if already working)
     */
    public function approveCuti($id)
    {
        try {
            DB::beginTransaction();

            $cuti = Cuti::lockForUpdate()->findOrFail($id);

            // Validasi status
            if ($cuti->status_approval !== 'pending') {
                throw new \Exception('Cuti sudah diproses sebelumnya.');
            }

            // Update status cuti
            $cuti->update([
                'status_approval' => 'approved',
                'approved_by' => Auth::id(),
                'tanggal_approval' => Carbon::now(),
                'alasan_penolakan' => null,
            ]);

            // Update status presensi yang terkait
            $updatedCount = Presensi::where('id_karyawan', $cuti->id_karyawan)
                ->whereBetween('tanggal_presensi', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->where('status_kehadiran', 'cuti')
                ->where('status_verifikasi', 'pending')
                ->update([
                    'status_verifikasi' => 'verified',
                    'catatan' => DB::raw("CONCAT(COALESCE(catatan, ''), ' - Disetujui oleh Admin')"),
                ]);

            DB::commit();

            Log::info("Cuti approved: ID {$id} by user " . Auth::id() . ". Updated {$updatedCount} presensi records.");

            return redirect()->route('admin.pengajuan.index')->with('success', "Cuti berhasil disetujui! {$updatedCount} data presensi diverifikasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving cuti: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reject Cuti (existing method - no changes needed if already working)
     */
    public function rejectCuti(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan harus diisi',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter',
        ]);

        try {
            DB::beginTransaction();

            $cuti = Cuti::lockForUpdate()->findOrFail($id);

            // Validasi status
            if ($cuti->status_approval !== 'pending') {
                throw new \Exception('Cuti sudah diproses sebelumnya.');
            }

            // Update status cuti
            $cuti->update([
                'status_approval' => 'rejected',
                'approved_by' => Auth::id(),
                'tanggal_approval' => Carbon::now(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // Hapus presensi yang terkait
            $deletedCount = Presensi::where('id_karyawan', $cuti->id_karyawan)
                ->whereBetween('tanggal_presensi', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->where('status_kehadiran', 'cuti')
                ->where('status_verifikasi', 'pending')
                ->delete();

            DB::commit();

            Log::info("Cuti rejected: ID {$id} by user " . Auth::id() . ". Deleted {$deletedCount} presensi records.");

            return redirect()->route('admin.pengajuan.index')->with('success', "Cuti ditolak dan {$deletedCount} data presensi dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting cuti: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}