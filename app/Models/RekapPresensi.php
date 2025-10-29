<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Method untuk generate rekap
    public static function generateRekap($tahun, $bulan)
    {
        // Hitung total hari kerja dalam bulan tersebut
        $totalHariKerja = self::hitungHariKerja($tahun, $bulan);
        
        $karyawan = Karyawan::where('status_aktif', true)->get();
        
        foreach ($karyawan as $k) {
            $presensi = Presensi::where('id_karyawan', $k->id_karyawan)
                ->whereYear('tanggal_presensi', $tahun)
                ->whereMonth('tanggal_presensi', $bulan)
                ->get();

            $rekap = self::firstOrNew([
                'id_karyawan' => $k->id_karyawan,
                'tahun' => $tahun,
                'bulan' => $bulan
            ]);

            $rekap->total_hari_kerja = $totalHariKerja;
            $rekap->jumlah_hadir = $presensi->where('status_kehadiran', 'hadir')->count();
            $rekap->jumlah_terlambat = $presensi->where('status_kehadiran', 'terlambat')->count();
            $rekap->jumlah_izin = $presensi->where('status_kehadiran', 'izin')->count();
            $rekap->jumlah_sakit = $presensi->where('status_kehadiran', 'sakit')->count();
            $rekap->jumlah_cuti = $presensi->where('status_kehadiran', 'cuti')->count();
            $rekap->jumlah_alpha = $presensi->where('status_kehadiran', 'alpha')->count();
            
            $rekap->total_menit_terlambat = $presensi->sum('keterlambatan_menit');
            $rekap->rata_rata_terlambat = $rekap->jumlah_terlambat > 0 ? $rekap->total_menit_terlambat / $rekap->jumlah_terlambat : 0;
            $rekap->total_jam_kerja = $presensi->sum('total_jam_kerja');
            
            $rekap->hitungPersentase();
            $rekap->save();
        }
        
        return true;
    }

    // Method untuk menghitung hari kerja
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