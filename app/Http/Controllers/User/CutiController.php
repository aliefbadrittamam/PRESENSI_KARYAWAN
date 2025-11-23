<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Models\Cuti;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class CutiController extends Controller
{
    // Kuota cuti tahunan (bisa diambil dari setting atau tabel karyawan)
    const KUOTA_CUTI_TAHUNAN = 12; // 12 hari per tahun

    /**
     * Display a listing of cuti (User)
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $cutiList = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung sisa cuti tahunan
        $cutiTerpakai = Cuti::cutiTahunanThisYear($karyawan->id_karyawan)->sum('jumlah_hari');
        $sisaCuti = self::KUOTA_CUTI_TAHUNAN - $cutiTerpakai;

        return view('user.cuti.index', compact('karyawan', 'cutiList', 'sisaCuti', 'cutiTerpakai'));
    }

    /**
     * Show the form for creating a new cuti
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        // Hitung sisa cuti tahunan
        $cutiTerpakai = Cuti::cutiTahunanThisYear($karyawan->id_karyawan)->sum('jumlah_hari');
        $sisaCuti = self::KUOTA_CUTI_TAHUNAN - $cutiTerpakai;

        return view('user.cuti.create', compact('karyawan', 'sisaCuti'));
    }

    /**
     * ✅ ENHANCEMENT: Store dengan DB Transaction dan Pessimistic Locking untuk kuota
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required|in:tahunan,sakit,melahirkan,menikah,khusus',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|min:10|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'jenis_cuti.required' => 'Pilih jenis cuti',
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

            // ✅ ENHANCEMENT: Pessimistic Lock untuk validasi kuota cuti tahunan
            if ($request->jenis_cuti === 'tahunan') {
                // Lock semua cuti tahunan tahun ini untuk karyawan ini
                $cutiTahunIni = Cuti::where('id_karyawan', $karyawan->id_karyawan)
                    ->where('jenis_cuti', 'tahunan')
                    ->where('status_approval', 'approved')
                    ->whereYear('tanggal_mulai', date('Y'))
                    ->lockForUpdate() // 🔒 Lock untuk prevent race condition
                    ->get();

                $cutiTerpakai = $cutiTahunIni->sum('jumlah_hari');
                $sisaCuti = self::KUOTA_CUTI_TAHUNAN - $cutiTerpakai;

                if ($jumlahHari > $sisaCuti) {
                    throw new \Exception("Sisa cuti tahunan Anda hanya {$sisaCuti} hari. Anda mengajukan {$jumlahHari} hari.");
                }
            }

            // ✅ ENHANCEMENT: Lock untuk prevent race condition pada tanggal overlap
            $existingCuti = Cuti::where('id_karyawan', $karyawan->id_karyawan)
                ->where(function($query) use ($tanggalMulai, $tanggalSelesai) {
                    $query->whereBetween('tanggal_mulai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                          ->orWhereBetween('tanggal_selesai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                          ->orWhere(function($q) use ($tanggalMulai, $tanggalSelesai) {
                              $q->where('tanggal_mulai', '<=', $tanggalMulai->format('Y-m-d'))
                                ->where('tanggal_selesai', '>=', $tanggalSelesai->format('Y-m-d'));
                          });
                })
                ->whereIn('status_approval', ['pending', 'approved'])
                ->lockForUpdate() // 🔒 Lock
                ->exists();

            if ($existingCuti) {
                throw new \Exception('Anda sudah mengajukan cuti pada rentang tanggal tersebut. Silakan pilih tanggal lain.');
            }

            // ✅ ENHANCEMENT: Upload file dengan UUID
            $filePath = null;
            if ($request->hasFile('file_pendukung')) {
                $filePath = $this->uploadFilePendukungWithUUID($request->file('file_pendukung'), $karyawan);
            }

            // Hitung sisa cuti tahunan setelah pengajuan
            $sisaCutiSetelahPengajuan = null;
            if ($request->jenis_cuti === 'tahunan') {
                $cutiTerpakai = Cuti::cutiTahunanThisYear($karyawan->id_karyawan)->sum('jumlah_hari');
                $sisaCutiSetelahPengajuan = self::KUOTA_CUTI_TAHUNAN - ($cutiTerpakai + $jumlahHari);
            }

            // Simpan data cuti
            $cuti = Cuti::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'jenis_cuti' => $request->jenis_cuti,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                'jumlah_hari' => $jumlahHari,
                'keterangan' => $request->keterangan,
                'file_pendukung' => $filePath,
                'status_approval' => 'pending',
                'tanggal_pengajuan' => Carbon::now(),
                'sisa_cuti_tahunan' => $sisaCutiSetelahPengajuan,
            ]);

            // Auto create presensi untuk setiap hari cuti
            $this->createPresensiCuti($cuti, $karyawan);

            DB::commit();

            return redirect()->route('user.cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu persetujuan dari admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating cuti: ' . $e->getMessage());
            
            // ✅ Delete uploaded file jika rollback
            if (isset($filePath) && $filePath) {
                Storage::disk('public')->delete($filePath);
            }
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified cuti
     */
    public function show($id)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->firstOrFail();

        $cuti = Cuti::where('id_karyawan', $karyawan->id_karyawan)
            ->where('id_cuti', $id)
            ->with(['karyawan', 'approver'])
            ->firstOrFail();

        return view('user.cuti.show', compact('karyawan', 'cuti'));
    }

    /**
     * ✅ ENHANCEMENT: Upload file dengan UUID untuk prevent collision
     */
    private function uploadFilePendukungWithUUID($file, $karyawan)
    {
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama_lengkap);
        $namaKaryawan = preg_replace('/[^A-Za-z0-9_]/', '', $namaKaryawan);
        
        $yearMonth = date('Y-m');
        $folderPath = "FilePendukungCuti/{$yearMonth}";
        
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
     */
    private function uploadFilePendukung($file, $karyawan)
    {
        return $this->uploadFilePendukungWithUUID($file, $karyawan);
    }

    /**
     * Create presensi records for cuti dates
     * ✅ ENHANCEMENT: Wrap dalam transaction (dipanggil dari store yang sudah dalam transaction)
     */
    private function createPresensiCuti($cuti, $karyawan)
    {
        $startDate = Carbon::parse($cuti->tanggal_mulai);
        $endDate = Carbon::parse($cuti->tanggal_selesai);
        $shift = ShiftKerja::where('status_aktif', 1)->first();

        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Skip weekend (Sabtu & Minggu) - Optional
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
                            'status_kehadiran' => 'cuti',
                            'status_verifikasi' => 'pending',
                            'keterlambatan_menit' => 0,
                            'catatan' => "Pengajuan cuti ({$cuti->jenis_cuti}): {$cuti->keterangan}",
                        ]);
                    } else {
                        // Create new dengan handling duplicate
                        try {
                            Presensi::create([
                                'id_karyawan' => $karyawan->id_karyawan,
                                'tanggal_presensi' => $currentDate->format('Y-m-d'),
                                'id_shift' => $shift?->id_shift,
                                'status_kehadiran' => 'cuti',
                                'status_verifikasi' => 'pending',
                                'keterlambatan_menit' => 0,
                                'catatan' => "Pengajuan cuti ({$cuti->jenis_cuti}): {$cuti->keterangan}",
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
                                        'status_kehadiran' => 'cuti',
                                        'status_verifikasi' => 'pending',
                                        'keterlambatan_menit' => 0,
                                        'catatan' => "Pengajuan cuti ({$cuti->jenis_cuti}): {$cuti->keterangan}",
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