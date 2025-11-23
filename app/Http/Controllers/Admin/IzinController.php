<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Izin;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class IzinController extends Controller
{
    /**
     * Display a listing of all izin submissions
     */
    public function index(Request $request)
    {
        $query = Izin::with(['karyawan']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_approval', $request->status);
        }

        // Filter by tipe izin
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe_izin', $request->tipe);
        }

        // Search by employee name or NIP
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.izin.index', compact('izinList'));
    }

    /**
     * Display the specified izin detail
     */
    public function show($id)
    {
        $izin = Izin::with(['karyawan', 'approver'])->findOrFail($id);
        
        // Get related presensi records
        $presensiList = Presensi::where('id_karyawan', $izin->id_karyawan)
            ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
            ->orderBy('tanggal_presensi', 'asc')
            ->get();

        return view('admin.izin.show', compact('izin', 'presensiList'));
    }

    /**
     * ✅ Approve izin dan update status presensi
     * Logic konsisten dengan pattern CutiController
     */
    public function approve($id)
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
                'approved_at' => Carbon::now(),
                'alasan_penolakan' => null,
            ]);

            // ✅ Update status presensi yang terkait menjadi 'verified'
            // Gunakan whereBetween seperti di CutiController
            $updatedCount = Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', $izin->tipe_izin)
                ->where('status_verifikasi', 'pending')
                ->update([
                    'status_verifikasi' => 'verified',
                    'catatan' => DB::raw("CONCAT(catatan, ' - Disetujui oleh Admin')"),
                ]);

            DB::commit();

            Log::info("Izin approved: ID {$id} by user " . Auth::id() . ". Updated {$updatedCount} presensi records.");

            return redirect()->back()->with('success', "Izin berhasil disetujui! {$updatedCount} data presensi diverifikasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * ✅ FIXED BUG 2: Reject izin dan HAPUS presensi yang sudah dibuat
     * Logic konsisten dengan pattern CutiController
     */
    public function reject(Request $request, $id)
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
                'approved_at' => Carbon::now(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // ✅ FIX BUG 2: HAPUS semua presensi yang terkait dengan izin ini
            // Gunakan whereBetween seperti di CutiController
            $deletedCount = Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', $izin->tipe_izin)
                ->where('status_verifikasi', 'pending') // Hanya hapus yang masih pending
                ->delete();

            DB::commit();

            Log::info("Izin rejected: ID {$id} by user " . Auth::id() . ". Deleted {$deletedCount} presensi records.");

            return redirect()->back()->with('success', "Izin ditolak dan {$deletedCount} data presensi dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * ✅ ALTERNATIVE: Reject dengan soft delete (set status 'alpha' atau 'tidak_hadir')
     * Gunakan ini jika ingin tetap ada record tapi ditandai sebagai tidak sah
     */
    public function rejectSoftDelete(Request $request, $id)
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
                'approved_at' => Carbon::now(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // ✅ ALTERNATIVE: Update presensi menjadi 'alpha' atau 'tidak_hadir'
            $updatedCount = Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', $izin->tipe_izin)
                ->where('status_verifikasi', 'pending')
                ->update([
                    'status_kehadiran' => 'alpha', // atau 'tidak_hadir'
                    'status_verifikasi' => 'rejected',
                    'catatan' => "Izin ditolak: {$request->alasan_penolakan}",
                ]);

            DB::commit();

            Log::info("Izin rejected (soft): ID {$id} by user " . Auth::id() . ". Updated {$updatedCount} presensi records to alpha.");

            return redirect()->back()->with('success', "Izin ditolak dan {$updatedCount} data presensi diubah menjadi alpha.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * ✅ UTILITY: Reprocess presensi untuk izin yang sudah ada
     * Gunakan ini untuk fix data yang sudah terlanjur bermasalah
     * Logic sama dengan CutiController
     */
    public function reprocessPresensi($id)
    {
        try {
            DB::beginTransaction();

            $izin = Izin::with('karyawan')->findOrFail($id);

            if ($izin->status_approval === 'approved') {
                // Hapus presensi lama
                Presensi::where('id_karyawan', $izin->id_karyawan)
                    ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                    ->where('status_kehadiran', $izin->tipe_izin)
                    ->delete();

                // Buat ulang presensi
                $this->createPresensiIzin($izin, $izin->karyawan);

                DB::commit();

                return redirect()->back()->with('success', 'Presensi berhasil diproses ulang.');
            } else {
                throw new \Exception('Hanya izin yang approved yang bisa diproses ulang.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reprocessing presensi: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Helper function untuk membuat presensi
     * ✅ Logic sama persis dengan createPresensiIzin di User\IzinController
     */
    private function createPresensiIzin($izin, $karyawan)
    {
        $startDate = Carbon::parse($izin->tanggal_mulai);
        $endDate = Carbon::parse($izin->tanggal_selesai);
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        // ✅ Gunakan copy() untuk avoid reference issue
        $currentDate = $startDate->copy();
        
        // ✅ Loop menggunakan <= seperti di CutiController
        while ($currentDate <= $endDate) {
            // Skip weekend (Sabtu & Minggu) - Optional
            if (!$currentDate->isWeekend()) {
                try {
                    // Lock untuk prevent race condition
                    $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                        ->where('tanggal_presensi', $currentDate->format('Y-m-d'))
                        ->lockForUpdate()
                        ->first();

                    if ($presensi) {
                        // Update existing
                        $presensi->update([
                            'id_shift' => $shift?->id_shift,
                            'status_kehadiran' => $izin->tipe_izin,
                            'status_verifikasi' => 'verified', // Langsung verified karena approved
                            'keterlambatan_menit' => 0,
                            'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan} - Disetujui",
                        ]);
                    } else {
                        // Create new
                        try {
                            Presensi::create([
                                'id_karyawan' => $karyawan->id_karyawan,
                                'tanggal_presensi' => $currentDate->format('Y-m-d'),
                                'id_shift' => $shift?->id_shift,
                                'status_kehadiran' => $izin->tipe_izin,
                                'status_verifikasi' => 'verified',
                                'keterlambatan_menit' => 0,
                                'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan} - Disetujui",
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Handle duplicate entry
                            if ($e->getCode() === '23000') {
                                $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                                    ->where('tanggal_presensi', $currentDate->format('Y-m-d'))
                                    ->lockForUpdate()
                                    ->first();
                                    
                                if ($presensi) {
                                    $presensi->update([
                                        'id_shift' => $shift?->id_shift,
                                        'status_kehadiran' => $izin->tipe_izin,
                                        'status_verifikasi' => 'verified',
                                        'keterlambatan_menit' => 0,
                                        'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan} - Disetujui",
                                    ]);
                                }
                            } else {
                                throw $e;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error creating presensi for date {$currentDate->format('Y-m-d')}: " . $e->getMessage());
                }
            }
            
            // ✅ Increment date
            $currentDate->addDay();
        }
    }

    /**
     * Bulk approve multiple izin
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'izin_ids' => 'required|array',
            'izin_ids.*' => 'exists:izin,id_izin',
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $totalPresensi = 0;

            foreach ($request->izin_ids as $id) {
                $izin = Izin::lockForUpdate()->find($id);
                
                if ($izin && $izin->status_approval === 'pending') {
                    $izin->update([
                        'status_approval' => 'approved',
                        'approved_by' => Auth::id(),
                        'approved_at' => Carbon::now(),
                    ]);

                    // Update presensi
                    $count = Presensi::where('id_karyawan', $izin->id_karyawan)
                        ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                        ->where('status_kehadiran', $izin->tipe_izin)
                        ->where('status_verifikasi', 'pending')
                        ->update(['status_verifikasi' => 'verified']);

                    $totalPresensi += $count;
                    $successCount++;
                }
            }

            DB::commit();

            Log::info("Bulk approved {$successCount} izin. Total {$totalPresensi} presensi verified.");

            return redirect()->back()->with('success', "{$successCount} izin berhasil disetujui! {$totalPresensi} presensi diverifikasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk approving izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Bulk reject multiple izin
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'izin_ids' => 'required|array',
            'izin_ids.*' => 'exists:izin,id_izin',
            'alasan_penolakan' => 'required|string|min:10|max:500',
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $totalDeleted = 0;

            foreach ($request->izin_ids as $id) {
                $izin = Izin::lockForUpdate()->find($id);
                
                if ($izin && $izin->status_approval === 'pending') {
                    $izin->update([
                        'status_approval' => 'rejected',
                        'approved_by' => Auth::id(),
                        'approved_at' => Carbon::now(),
                        'alasan_penolakan' => $request->alasan_penolakan,
                    ]);

                    // Delete presensi
                    $count = Presensi::where('id_karyawan', $izin->id_karyawan)
                        ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                        ->where('status_kehadiran', $izin->tipe_izin)
                        ->where('status_verifikasi', 'pending')
                        ->delete();

                    $totalDeleted += $count;
                    $successCount++;
                }
            }

            DB::commit();

            Log::info("Bulk rejected {$successCount} izin. Total {$totalDeleted} presensi deleted.");

            return redirect()->back()->with('success', "{$successCount} izin ditolak! {$totalDeleted} presensi dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk rejecting izin: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}