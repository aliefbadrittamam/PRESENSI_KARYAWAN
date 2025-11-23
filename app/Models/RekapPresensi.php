<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RekapPresensi extends Model
{
    use HasFactory;

    protected $table = 'rekap_presensi';
    protected $primaryKey = 'id_rekap';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'tahun',
        'bulan',
        'total_hari_kerja',
        'jumlah_hadir',
        'jumlah_terlambat',
        'jumlah_izin',
        'jumlah_sakit',
        'jumlah_cuti',
        'jumlah_alpha',
        'persentase_kehadiran',
        'persentase_terlambat',
        'persentase_tidak_hadir',
        'total_menit_terlambat',
        'rata_rata_terlambat',
        'total_jam_kerja'
    ];

    protected $casts = [
        'persentase_kehadiran' => 'decimal:2',
        'persentase_terlambat' => 'decimal:2',
        'persentase_tidak_hadir' => 'decimal:2',
        'rata_rata_terlambat' => 'decimal:2',
        'total_jam_kerja' => 'decimal:2'
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    // Accessor untuk nama bulan
    public function getNamaBulanAttribute()
    {
        return \Carbon\Carbon::create()->month($this->bulan)->monthName;
    }

    // Accessor untuk periode
    public function getPeriodeAttribute()
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    // Scope untuk bulan dan tahun tertentu
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    // Method untuk menghitung persentase
    public function hitungPersentase()
    {
        if ($this->total_hari_kerja > 0) {
            $this->persentase_kehadiran = (($this->jumlah_hadir + $this->jumlah_terlambat) / $this->total_hari_kerja) * 100;
            $this->persentase_terlambat = ($this->jumlah_terlambat / $this->total_hari_kerja) * 100;
            $this->persentase_tidak_hadir = (($this->jumlah_izin + $this->jumlah_sakit + $this->jumlah_cuti + $this->jumlah_alpha) / $this->total_hari_kerja) * 100;
        }
    }

    /**
     * ✅ ENHANCEMENT: Generate rekap dengan DB Transaction dan Locking
     * 
     * @param int $tahun Tahun rekap
     * @param int $bulan Bulan rekap (1-12)
     * @return bool Success status
     * @throws \Exception on failure
     */
    public static function generateRekap($tahun, $bulan)
    {
        try {
            DB::beginTransaction();

            // Hitung total hari kerja dalam bulan tersebut
            $totalHariKerja = self::hitungHariKerja($tahun, $bulan);
            
            // Get semua karyawan aktif
            $karyawan = Karyawan::where('status_aktif', true)->get();
            
            Log::info("Starting rekap generation for {$tahun}-{$bulan}, {$karyawan->count()} karyawan");
            
            foreach ($karyawan as $k) {
                try {
                    // ✅ ENHANCEMENT: Lock untuk prevent concurrent generation
                    // Cek apakah rekap sudah ada
                    $rekap = self::where('id_karyawan', $k->id_karyawan)
                        ->where('tahun', $tahun)
                        ->where('bulan', $bulan)
                        ->lockForUpdate() // 🔒 Lock row untuk prevent race condition
                        ->first();

                    if (!$rekap) {
                        // Create new rekap
                        $rekap = new self([
                            'id_karyawan' => $k->id_karyawan,
                            'tahun' => $tahun,
                            'bulan' => $bulan
                        ]);
                    }

                    // Get presensi data untuk karyawan ini
                    $presensi = Presensi::where('id_karyawan', $k->id_karyawan)
                        ->whereYear('tanggal_presensi', $tahun)
                        ->whereMonth('tanggal_presensi', $bulan)
                        ->get();

                    // Calculate statistics
                    $rekap->total_hari_kerja = $totalHariKerja;
                    $rekap->jumlah_hadir = $presensi->where('status_kehadiran', 'hadir')->count();
                    $rekap->jumlah_terlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
                    $rekap->jumlah_izin = $presensi->where('status_kehadiran', 'izin')->count();
                    $rekap->jumlah_sakit = $presensi->where('status_kehadiran', 'sakit')->count();
                    $rekap->jumlah_cuti = $presensi->where('status_kehadiran', 'cuti')->count();
                    $rekap->jumlah_alpha = $presensi->where('status_kehadiran', 'alpha')->count();
                    
                    $rekap->total_menit_terlambat = $presensi->sum('keterlambatan_menit');
                    $rekap->rata_rata_terlambat = $rekap->jumlah_terlambat > 0 
                        ? $rekap->total_menit_terlambat / $rekap->jumlah_terlambat 
                        : 0;
                    $rekap->total_jam_kerja = $presensi->sum('total_jam_kerja');
                    
                    // Hitung persentase
                    $rekap->hitungPersentase();
                    
                    // Save dengan handling duplicate key
                    try {
                        $rekap->save();
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Handle unique constraint violation
                        if ($e->getCode() === '23000') {
                            Log::warning("Duplicate rekap detected for karyawan {$k->id_karyawan}, retrying...");
                            
                            // Retry dengan lock
                            $rekap = self::where('id_karyawan', $k->id_karyawan)
                                ->where('tahun', $tahun)
                                ->where('bulan', $bulan)
                                ->lockForUpdate()
                                ->firstOrFail();

                            // Update existing
                            $rekap->total_hari_kerja = $totalHariKerja;
                            $rekap->jumlah_hadir = $presensi->where('status_kehadiran', 'hadir')->count();
                            $rekap->jumlah_terlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
                            $rekap->jumlah_izin = $presensi->where('status_kehadiran', 'izin')->count();
                            $rekap->jumlah_sakit = $presensi->where('status_kehadiran', 'sakit')->count();
                            $rekap->jumlah_cuti = $presensi->where('status_kehadiran', 'cuti')->count();
                            $rekap->jumlah_alpha = $presensi->where('status_kehadiran', 'alpha')->count();
                            $rekap->total_menit_terlambat = $presensi->sum('keterlambatan_menit');
                            $rekap->rata_rata_terlambat = $rekap->jumlah_terlambat > 0 
                                ? $rekap->total_menit_terlambat / $rekap->jumlah_terlambat 
                                : 0;
                            $rekap->total_jam_kerja = $presensi->sum('total_jam_kerja');
                            $rekap->hitungPersentase();
                            $rekap->save();
                        } else {
                            throw $e;
                        }
                    }

                    Log::info("Rekap generated for karyawan {$k->id_karyawan}");
                } catch (\Exception $e) {
                    Log::error("Error generating rekap for karyawan {$k->id_karyawan}: " . $e->getMessage());
                    // Continue dengan karyawan berikutnya
                }
            }
            
            DB::commit();
            
            Log::info("Rekap generation completed for {$tahun}-{$bulan}");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Rekap generation failed for {$tahun}-{$bulan}: " . $e->getMessage());
            
            throw $e;
        }
    }

    /**
     * ✅ ENHANCEMENT: Generate rekap untuk single karyawan
     * 
     * @param int $idKaryawan ID Karyawan
     * @param int $tahun Tahun rekap
     * @param int $bulan Bulan rekap
     * @return bool Success status
     */
    public static function generateRekapKaryawan($idKaryawan, $tahun, $bulan)
    {
        try {
            DB::beginTransaction();

            $totalHariKerja = self::hitungHariKerja($tahun, $bulan);
            
            $karyawan = Karyawan::findOrFail($idKaryawan);
            
            // Lock untuk prevent concurrent generation
            $rekap = self::where('id_karyawan', $idKaryawan)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->lockForUpdate()
                ->first();

            if (!$rekap) {
                $rekap = new self([
                    'id_karyawan' => $idKaryawan,
                    'tahun' => $tahun,
                    'bulan' => $bulan
                ]);
            }

            $presensi = Presensi::where('id_karyawan', $idKaryawan)
                ->whereYear('tanggal_presensi', $tahun)
                ->whereMonth('tanggal_presensi', $bulan)
                ->get();

            $rekap->total_hari_kerja = $totalHariKerja;
            $rekap->jumlah_hadir = $presensi->where('status_kehadiran', 'hadir')->count();
            $rekap->jumlah_terlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
            $rekap->jumlah_izin = $presensi->where('status_kehadiran', 'izin')->count();
            $rekap->jumlah_sakit = $presensi->where('status_kehadiran', 'sakit')->count();
            $rekap->jumlah_cuti = $presensi->where('status_kehadiran', 'cuti')->count();
            $rekap->jumlah_alpha = $presensi->where('status_kehadiran', 'alpha')->count();
            $rekap->total_menit_terlambat = $presensi->sum('keterlambatan_menit');
            $rekap->rata_rata_terlambat = $rekap->jumlah_terlambat > 0 
                ? $rekap->total_menit_terlambat / $rekap->jumlah_terlambat 
                : 0;
            $rekap->total_jam_kerja = $presensi->sum('total_jam_kerja');
            $rekap->hitungPersentase();
            $rekap->save();

            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Error generating rekap for karyawan {$idKaryawan}: " . $e->getMessage());
            
            throw $e;
        }
    }

    /**
     * Method untuk menghitung hari kerja (exclude weekend)
     * 
     * @param int $tahun Tahun
     * @param int $bulan Bulan (1-12)
     * @return int Total hari kerja
     */
    private static function hitungHariKerja($tahun, $bulan)
    {
        $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
        $weekendDays = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = \Carbon\Carbon::create($tahun, $bulan, $day);
            if ($date->isWeekend()) {
                $weekendDays++;
            }
        }
        
        return $daysInMonth - $weekendDays;
    }
}