<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
     * Store a newly created izin
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

        // Check apakah sudah ada izin di tanggal yang sama
        $existingIzin = Izin::where('id_karyawan', $karyawan->id_karyawan)
            ->where(function($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('tanggal_mulai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                      ->orWhereBetween('tanggal_selesai', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                      ->orWhere(function($q) use ($tanggalMulai, $tanggalSelesai) {
                          $q->where('tanggal_mulai', '<=', $tanggalMulai->format('Y-m-d'))
                            ->where('tanggal_selesai', '>=', $tanggalSelesai->format('Y-m-d'));
                      });
            })
            ->whereIn('status_approval', ['pending', 'approved']) // Cek yang pending atau approved
            ->exists();

        if ($existingIzin) {
            return back()->withErrors([
                'error' => 'Anda sudah mengajukan izin pada rentang tanggal tersebut. Silakan pilih tanggal lain.'
            ])->withInput();
        }

        // Upload file pendukung jika ada
        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $this->uploadFilePendukung($request->file('file_pendukung'), $karyawan);
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

        // Auto create presensi untuk setiap hari izin (kecuali weekend)
        $this->createPresensiIzin($izin, $karyawan);

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin berhasil dikirim! Menunggu persetujuan dari admin.');
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
     * Upload file pendukung (surat dokter, dll)
     * Struktur: FilePendukung/YYYY-MM/TGL_WAKTU_NAMA(NIP).ext
     */
    private function uploadFilePendukung($file, $karyawan)
    {
        $namaKaryawan = str_replace(' ', '_', $karyawan->nama_lengkap);
        $namaKaryawan = preg_replace('/[^A-Za-z0-9_]/', '', $namaKaryawan);
        
        $yearMonth = date('Y-m');
        $folderPath = "FilePendukung/{$yearMonth}";
        
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
     * Create presensi records for izin dates
     * Sesuaikan dengan struktur tabel presensi yang ada
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
                // Create or update presensi sesuai struktur tabel
                Presensi::updateOrCreate(
                    [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'tanggal_presensi' => $currentDate->format('Y-m-d'),
                    ],
                    [
                        'id_shift' => $shift?->id_shift,
                        'status_kehadiran' => $izin->tipe_izin, // 'izin' atau 'sakit'
                        'status_verifikasi' => 'pending', // Akan diverifikasi setelah approval
                        'keterlambatan_menit' => 0,
                        'catatan' => "Pengajuan {$izin->tipe_izin}: {$izin->keterangan}",
                    ]
                );
            }
            
            $currentDate->addDay();
        }
    }
}