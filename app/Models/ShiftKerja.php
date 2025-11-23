<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftKerja extends Model
{
    use HasFactory;

    protected $table = 'shift_kerja';
    protected $primaryKey = 'id_shift';
    public $timestamps = true;

    protected $fillable = [
        'kode_shift',
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
        'toleransi_keterlambatan',
        'keterangan',
        'status_aktif'
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'status_aktif' => 'boolean'
    ];

    // Relasi ke Presensi
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_shift');
    }

    // Accessor untuk durasi shift
    public function getDurasiShiftAttribute()
    {
        $start = \Carbon\Carbon::parse($this->jam_mulai);
        $end = \Carbon\Carbon::parse($this->jam_selesai);
        
        // Handle shift yang melewati tengah malam
        if ($end < $start) {
            $end->addDay();
        }
        
        return $start->diffInHours($end);
    }

    // Method untuk cek apakah waktu termasuk dalam shift
    public function isWaktuDalamShift($waktu)
    {
        $waktuCheck = \Carbon\Carbon::parse($waktu);
        $jamMulai = \Carbon\Carbon::parse($this->jam_mulai);
        $jamSelesai = \Carbon\Carbon::parse($this->jam_selesai);

        // Handle shift malam (melewati tengah malam)
        if ($jamSelesai < $jamMulai) {
            return $waktuCheck >= $jamMulai || $waktuCheck <= $jamSelesai;
        }

        return $waktuCheck >= $jamMulai && $waktuCheck <= $jamSelesai;
    }
}