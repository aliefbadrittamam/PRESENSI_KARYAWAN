<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Cuti;
use App\Models\Izin;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendWhatsAppNotification;

class PengajuanController extends Controller
{
    /**
     * Display a listing of all izin and cuti requests
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'semua'); // semua, izin, cuti
        $status = $request->get('status', 'all'); // '

        // Query Izin
        $izinQuery = Izin::with(['karyawan.user', 'karyawan.jabatan', 'approvedBy'])->orderBy('tanggal_pengajuan', 'desc');

        // Query Cuti
        $cutiQuery = Cuti::with(['karyawan.user', 'karyawan.jabatan', 'approvedBy'])->orderBy('tanggal_pengajuan', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $izinQuery->where('status_approval', $status);
            $cutiQuery->where('status_approval', $status);
        }

        // Get data based on filter
        if ($filter === 'izin') {
            $pengajuanIzin = $izinQuery->get();
            $pengajuanCuti = collect();
        } elseif ($filter === 'cuti') {
            $pengajuanIzin = collect();
            $pengajuanCuti = $cutiQuery->get();
        } else {
            // PERBAIKAN: pastikan kedua query dieksekusi dengan get()
            $pengajuanIzin = $izinQuery->get();
            $pengajuanCuti = $cutiQuery->get();
        }

        // PERBAIKAN: Merge dengan sortByDesc yang lebih eksplisit
        $pengajuan = $pengajuanIzin->merge($pengajuanCuti)
    ->sortByDesc(function($item) {
        return $item->tanggal_pengajuan;
    })
    ->values(); // Reset array keys

        // Statistics
        $stats = [
            'total_pending' => Izin::where('status_approval', 'pending')->count() + Cuti::where('status_approval', 'pending')->count(),
            'total_approved' => Izin::where('status_approval', 'approved')->count() + Cuti::where('status_approval', 'approved')->count(),
            'total_rejected' => Izin::where('status_approval', 'rejected')->count() + Cuti::where('status_approval', 'rejected')->count(),
            'izin_pending' => Izin::where('status_approval', 'pending')->count(),
            'cuti_pending' => Cuti::where('status_approval', 'pending')->count(),
        ];

        return view('admin.pengajuan.index', compact('pengajuan', 'stats', 'filter', 'status'));
    }

    /**
     * Show detail of izin request
     */
    public function showIzin($id)
    {
        $izin = Izin::with(['karyawan.user', 'karyawan.jabatan', 'karyawan.departemen', 'approvedBy'])->findOrFail($id);

        return view('admin.pengajuan.detail-izin', compact('izin'));
    }

    /**
     * Show detail of cuti request
     */
    public function showCuti($id)
    {
        $cuti = Cuti::with(['karyawan.user', 'karyawan.jabatan', 'karyawan.departemen', 'approvedBy'])->findOrFail($id);

        return view('admin.pengajuan.detail-cuti', compact('cuti'));
    }

    /**
     * Approve izin request
     */
    public function approveIzin(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $izin = Izin::findOrFail($id);

            // Check if already processed
            if ($izin->status_approval !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan izin sudah diproses sebelumnya.');
            }

            // Update izin status
            $izin->update([
                'status_approval' => 'approved',
                'tanggal_approval' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Create presensi records for each day
            $this->createPresensiForIzin($izin);

            DB::commit();
            SendWhatsAppNotification::dispatch($izin->karyawan->nomor_telepon, 'izin_approved', [
                'nama' => $izin->karyawan->nama_lengkap,
                'jenis' => ucfirst($izin->tipe_izin),
                'tanggal_mulai' => Carbon::parse($izin->tanggal_mulai)->format('d F Y'),
                'tanggal_selesai' => Carbon::parse($izin->tanggal_selesai)->format('d F Y'),
                'keterangan' => $izin->keterangan,
            ]);

            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan izin berhasil disetujui dan presensi telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve cuti request
     */
    public function approveCuti(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $cuti = Cuti::findOrFail($id);

            // Check if already processed
            if ($cuti->status_approval !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan cuti sudah diproses sebelumnya.');
            }

            // Update cuti status
            $cuti->update([
                'status_approval' => 'approved',
                'tanggal_approval' => now(),
                'approved_by' => Auth::id(),
            ]);

            // Create presensi records for each day
            $this->createPresensiForCuti($cuti);

            DB::commit();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan cuti berhasil disetujui dan presensi telah dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject izin request
     */
    public function rejectIzin(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            $izin = Izin::findOrFail($id);

            // Check if already processed
            if ($izin->status_approval !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan izin sudah diproses sebelumnya.');
            }

            $izin->update([
                'status_approval' => 'rejected',
                'tanggal_approval' => now(),
                'approved_by' => Auth::id(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            SendWhatsAppNotification::dispatch($izin->karyawan->nomor_telepon, 'izin_rejected', [
                'nama' => $izin->karyawan->nama_lengkap,
                'jenis' => ucfirst($izin->tipe_izin),
                'tanggal_mulai' => Carbon::parse($izin->tanggal_mulai)->format('d F Y'),
                'tanggal_selesai' => Carbon::parse($izin->tanggal_selesai)->format('d F Y'),
                'alasan' => $request->alasan_penolakan,
            ]);

            // Delete related presensi if any
            Presensi::where('id_karyawan', $izin->id_karyawan)
                ->whereBetween('tanggal_presensi', [$izin->tanggal_mulai, $izin->tanggal_selesai])
                ->where('status_kehadiran', 'izin')
                ->delete();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan izin berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject cuti request
     */
    public function rejectCuti(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            $cuti = Cuti::findOrFail($id);

            // Check if already processed
            if ($cuti->status_approval !== 'pending') {
                return redirect()->back()->with('error', 'Pengajuan cuti sudah diproses sebelumnya.');
            }

            $cuti->update([
                'status_approval' => 'rejected',
                'tanggal_approval' => now(),
                'approved_by' => Auth::id(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // Delete related presensi if any
            Presensi::where('id_karyawan', $cuti->id_karyawan)
                ->whereBetween('tanggal_presensi', [$cuti->tanggal_mulai, $cuti->tanggal_selesai])
                ->where('status_kehadiran', 'cuti')
                ->delete();

            return redirect()->route('admin.pengajuan.index')->with('success', 'Pengajuan cuti berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Create presensi records for approved izin
     */
    private function createPresensiForIzin($izin)
    {
        $startDate = Carbon::parse($izin->tanggal_mulai);
        $endDate = Carbon::parse($izin->tanggal_selesai);

        // Get default shift
        $defaultShift = ShiftKerja::where('status_aktif', 1)->first();

        // Create presensi for each day
        while ($startDate <= $endDate) {
            // Skip weekends (optional - comment out if weekends should be included)
            if (!$startDate->isWeekend()) {
                Presensi::updateOrCreate(
                    [
                        'id_karyawan' => $izin->id_karyawan,
                        'tanggal_presensi' => $startDate->format('Y-m-d'),
                    ],
                    [
                        'id_shift' => $defaultShift ? $defaultShift->id_shift : null,
                        'status_kehadiran' => $izin->tipe_izin === 'sakit' ? 'sakit' : 'izin',
                        'status_verifikasi' => 'verified',
                        'catatan' => 'Pengajuan izin: ' . $izin->keterangan,
                        'created_at' => $izin->tanggal_pengajuan,
                        'updated_at' => now(),
                    ],
                );
            }

            $startDate->addDay();
        }
    }

    /**
     * Create presensi records for approved cuti
     */
    private function createPresensiForCuti($cuti)
    {
        $startDate = Carbon::parse($cuti->tanggal_mulai);
        $endDate = Carbon::parse($cuti->tanggal_selesai);

        // Get default shift
        $defaultShift = ShiftKerja::where('status_aktif', 1)->first();

        // Create presensi for each day
        while ($startDate <= $endDate) {
            // Skip weekends (optional - comment out if weekends should be included)
            if (!$startDate->isWeekend()) {
                Presensi::updateOrCreate(
                    [
                        'id_karyawan' => $cuti->id_karyawan,
                        'tanggal_presensi' => $startDate->format('Y-m-d'),
                    ],
                    [
                        'id_shift' => $defaultShift ? $defaultShift->id_shift : null,
                        'status_kehadiran' => 'cuti',
                        'status_verifikasi' => 'verified',
                        'catatan' => 'Pengajuan cuti (' . $cuti->jenis_cuti . '): ' . $cuti->keterangan,
                        'created_at' => $cuti->tanggal_pengajuan,
                        'updated_at' => now(),
                    ],
                );
            }

            $startDate->addDay();
        }
    }
}
