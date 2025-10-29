<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = true;

    protected $fillable = [
        'id_karyawan',
        'id_shift',
        'tanggal_presensi',
        'jam_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'alamat_masuk',
        'accuracy_masuk',
        'face_id_data_masuk',
        'confidence_score_masuk',
        'foto_masuk',
        'foto_thumbnail_masuk',
        'jam_keluar',
        'latitude_keluar',
        'longitude_keluar',
        'alamat_keluar',
        'accuracy_keluar',
        'face_id_data_keluar',
        'confidence_score_keluar',
        'foto_keluar',
        'foto_thumbnail_keluar',
        'status_kehadiran',
        'status_verifikasi',
        'alasan_reject',
        'keterlambatan_menit',
        'total_jam_kerja',
        'catatan'
    ];

    protected $casts = [
        'tanggal_presensi' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
        'accuracy_masuk' => 'decimal:2',
        'accuracy_keluar' => 'decimal:2',
        'confidence_score_masuk' => 'decimal:2',
        'confidence_score_keluar' => 'decimal:2',
        'total_jam_kerja' => 'decimal:2',
        'keterlambatan_menit' => 'integer'
    ];

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    // Relasi ke Shift Kerja
    public function shift()
    {
        return $this->belongsTo(ShiftKerja::class, 'id_shift');
    }

    // Scope untuk presensi hari ini
    public function scopeHariIni($query, $karyawanId = null)
    {
        $query->where('tanggal_presensi', today());
        
        if ($karyawanId) {
            $query->where('id_karyawan', $karyawanId);
        }
        
        return $query;
    }

    // Scope untuk presensi bulan ini
    public function scopeBulanIni($query, $karyawanId = null)
    {
        $query->whereMonth('tanggal_presensi', now()->month)
              ->whereYear('tanggal_presensi', now()->year);
        
        if ($karyawanId) {
            $query->where('id_karyawan', $karyawanId);
        }
        
        return $query;
    }

    // Method untuk menghitung total jam kerja
    public function hitungTotalJamKerja()
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            
            // Handle jika keluar lebih kecil dari masuk (shift malam)
            if ($keluar < $masuk) {
                $keluar->addDay();
            }
            
            $this->total_jam_kerja = $masuk->diffInMinutes($keluar) / 60;
            $this->save();
        }
    }

    // Method untuk cek apakah presensi masuk valid
    public function isPresensiMasukValid()
    {
        return $this->confidence_score_masuk >= 0.75 && $this->accuracy_masuk <= 50;
    }

    // Method untuk cek apakah presensi keluar valid
    public function isPresensiKeluarValid()
    {
        return $this->confidence_score_keluar >= 0.75 && $this->accuracy_keluar <= 50;
    }

    // Accessor untuk status kehadiran dengan warna
    public function getStatusKehadiranColorAttribute()
    {
        return match($this->status_kehadiran) {
            'hadir' => 'success',
            'terlambat' => 'warning',
            'izin' => 'info',
            'sakit' => 'primary',
            'cuti' => 'secondary',
            'alpha' => 'danger',
            default => 'dark'
        };
    }
}