<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Models\Izin;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class IzinController extends Controller
{
    /**
     * Display a listing of izin (User)
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $izinList = Izin::where('id_karyawan', $karyawan->id_karyawan)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.izin.index', compact('karyawan', 'izinList'));
    }

    /**
     * Show the form for creating a new izin
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        return view('user.izin.create', compact('karyawan'));
    }

    /**
     * ✅ ENHANCEMENT: Store dengan DB Transaction dan Lock
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe_izin' => 'required|in:izin,sakit',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'tipe_izin.required' => 'Pilih jenis izin (Izin/Sakit)',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai minimal hari ini',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai',
            'keterangan.required' => 'Keterangan/alasan harus diisi',
            'keterangan.min' => 'Keterangan minimal 10 karakter',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'file_pendukung.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG',
            'file_pendukung.max' => 'Ukuran file maksimal 2MB',
        ]);

        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($request->tanggal_selesai);
        
        // Hitung jumlah hari (termasuk weekend)
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // ✅ ENHANCEMENT: DB Transaction untuk atomic operation
        try {
            DB::beginTransaction();

            // ✅ ENHANCEMENT: Lock untuk prevent race condition
            $existingIzin = Izin::where('id_karyawan', $karyawan->id_karyawan)
                ->where(function($query) use ($tanggalMulai, $tanggalSelesai) {
                    $query->whereBetween('tanggal_mulai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                          ->orWhereBetween('tanggal_selesai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                          ->orWhere(function($q) use ($tanggalMulai, $tanggalSelesai) {
                              $q->where('tanggal_mulai', '<=', $tanggalMulai->format('Y-m-d'))
                                ->where('tanggal_selesai', '>=', $tanggalSelesai->format('Y-m-d'));
                          });
                })
                ->whereIn('status_approval', ['pending', 'approved'])
                ->lockForUpdate() // 🔒 Lock untuk prevent concurrent insert
                ->exists();

            if ($existingIzin) {
                throw new \Exception('Anda sudah mengajukan izin pada rentang tanggal tersebut. Silakan pilih tanggal lain.');
            }

            // ✅ ENHANCEMENT: Upload file dengan UUID
            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $filePath = $this->uploadFilePendukungWithUUID($request->file('file_pendukung'), $karyawan);
            }

            // Simpan data izin
            $izin = Izin::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'tipe_izin' => $request->tipe_izin,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                'jumlah_hari' => $jumlahHari,
                'keterangan' => $request->keterangan,
                'file_pendukung' => $filePath,
                'status_approval' => 'pending',
                'tanggal_pengajuan' => Carbon::now(),
            ]);

            // Auto create presensi untuk setiap hari izin
            $this->createPresensiIzin($izin, $karyawan);

            DB::commit();

            return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dikirim! Menunggu persetujuan dari admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating izin: ' . $e->getMessage());
            
            // ✅ Delete uploaded file jika rollback
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified izin
     */
    public function show($id)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $izin = Izin::where('id_karyawan', $karyawan->id_karyawan)
            ->where('id_izin', $id)
            ->with(['karyawan', 'approver'])
            ->firstOrFail();

        return view('user.izin.show', compact('karyawan', 'izin'));
    }

    /**
     * ✅ ENHANCEMENT: Upload file dengan UUID untuk prevent collision
     */
    private function uploadFilePendukungWithUUID($file, $karyawan)
    {
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama_lengkap);
        $namaKaryawan = preg_replace('/[^A-Za-z0-9_]/', '', $namaKaryawan);
        
        $yearMonth = date('Y-m');
        $folderPath = "FilePendukung/{$yearMonth}";
        
        // ✅ Generate unique filename dengan UUID + microtime
        $uuid = Str::uuid()->toString();
        $microtime = microtime(true);
        $extension = $file->getClientOriginalExtension();
        $filename = "{$namaKaryawan}_{$karyawan->nip}_{$uuid}_{$microtime}.{$extension}";
        
        $fullPath = "{$folderPath}/{$filename}";
        
        try {
            Storage::disk('public')->makeDirectory($folderPath);
            Storage::disk('public')->put($fullPath, file_get_contents($file));
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            throw new \Exception('Gagal mengupload file. Silakan coba lagi.');
        }
        
        return $fullPath;
    }

    /**
     * OLD uploadFilePendukung - DEPRECATED
     * ❌ PROBLEM: Filename collision jika upload di detik yang sama
     */
    private function uploadFilePendukung($file, $karyawan)
    {
        return $this->uploadFilePendukungWithUUID($file, $karyawan);
    }

    /**
     * Create presensi records for izin dates
     * ✅ ENHANCEMENT: Wrap dalam transaction (dipanggil dari store yang sudah dalam transaction)
     */
    private function createPresensiIzin($izin, $karyawan)
    {
        $startDate = Carbon::parse($izin->tanggal_mulai);
        $endDate = Carbon::parse($izin->tanggal_selesai);
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Skip weekend (Sabtu & Minggu) - Optional, sesuaikan kebijakan
            if (!$currentDate->isWeekend()) {
                try {
                    // ✅ ENHANCEMENT: Lock untuk prevent race condition
                    $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                        ->where('tanggal_presensi', $currentDate->format('Y-m-d'))
                        ->lockForUpdate()
                        ->first();

                    if ($presensi) {
                        // Update existing
                        $presensi->update([
                            'id_shift' => $shift?->id_shift,
                            'status_kehadiran' => $izin->tipe_izin,
                            'status_verifikasi' => 'pending',
                            'keterlambatan_menit' => 0,
                            'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan}",
                        ]);
                    } else {
                        // Create new dengan handling duplicate
                        try {
                            Presensi::create([
                                'id_karyawan' => $karyawan->id_karyawan,
                                'tanggal_presensi' => $currentDate->format('Y-m-d'),
                                'id_shift' => $shift?->id_shift,
                                'status_kehadiran' => $izin->tipe_izin,
                                'status_verifikasi' => 'pending',
                                'keterlambatan_menit' => 0,
                                'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan}",
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Handle duplicate entry (retry once)
                            if ($e->getCode() === '23000') {
                                $presensi = Presensi::where('id_karyawan', $karyawan->id_karyawan)
                                    ->where('tanggal_presensi', $currentDate->format('Y-m-d'))
                                    ->lockForUpdate()
                                    ->first();
                                    
                                if ($presensi) {
                                    $presensi->update([
                                        'id_shift' => $shift?->id_shift,
                                        'status_kehadiran' => $izin->tipe_izin,
                                        'status_verifikasi' => 'pending',
                                        'keterlambatan_menit' => 0,
                                        'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan}",
                                    ]);
                                }
                            } else {
                                throw $e;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error creating presensi for date {$currentDate->format('Y-m-d')}: " . $e->getMessage());
                    // Continue dengan tanggal berikutnya
                }
            }
            
            $currentDate->addDay();
        }
    }
}