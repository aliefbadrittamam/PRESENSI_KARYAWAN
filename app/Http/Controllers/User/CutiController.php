<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     * Store a newly created cuti
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

        // Validasi kuota cuti tahunan
        if ($request->jenis_cuti === 'tahunan') {
            $cutiTerpakai = Cuti::cutiTahunanThisYear($karyawan->id_karyawan)->sum('jumlah_hari');
            $sisaCuti = self::KUOTA_CUTI_TAHUNAN - $cutiTerpakai;

            if ($jumlahHari > $sisaCuti) {
                return back()->withErrors([
                    'error' => "Sisa cuti tahunan Anda hanya {$sisaCuti} hari. Anda mengajukan {$jumlahHari} hari."
                ])->withInput();
            }
        }

        // Check apakah sudah ada cuti di tanggal yang sama
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
            ->exists();

        if ($existingCuti) {
            return back()->withErrors([
                'error' => 'Anda sudah mengajukan cuti pada rentang tanggal tersebut. Silakan pilih tanggal lain.'
            ])->withInput();
        }

        // Upload file pendukung jika ada
        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $this->uploadFilePendukung($request->file('file_pendukung'), $karyawan);
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

        // Auto create presensi untuk setiap hari cuti (kecuali weekend)
        $this->createPresensiCuti($cuti, $karyawan);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim! Menunggu persetujuan dari admin.');
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
     * Upload file pendukung
     * Struktur: FilePendukungCuti/YYYY-MM/TGL_WAKTU_NAMA(NIP).ext
     */
    private function uploadFilePendukung($file, $karyawan)
    {
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama_lengkap);
        $namaKaryawan = preg_replace('/[^A-Za-z0-9_]/', '', $namaKaryawan);
        
        $yearMonth = date('Y-m');
        $folderPath = "FilePendukungCuti/{$yearMonth}";
        
        $tanggal = date('d-m-Y');
        $waktu = date('His');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$tanggal}_{$waktu}_{$namaKaryawan}({$karyawan->nip}).{$extension}";
        
        $fullPath = "{$folderPath}/{$filename}";
        
        Storage::disk('public')->makeDirectory($folderPath);
        Storage::disk('public')->put($fullPath, file_get_contents($file));
        
        return $fullPath;
    }

    /**
     * Create presensi records for cuti dates
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
                // Create or update presensi dengan status 'cuti'
                Presensi::updateOrCreate(
                    [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'tanggal_presensi' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'id_shift' => $shift?->id_shift,
                        'status_kehadiran' => 'cuti',
                        'status_verifikasi' => 'pending',
                        'keterlambatan_menit' => 0,
                        'catatan' => "Pengajuan cuti ({$cuti->jenis_cuti}): {$cuti->keterangan}",
                    ]
                );
            }
            
            $currentDate->addDay();
        }
    }
}