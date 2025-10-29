<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiPresensi extends Model
{
    use HasFactory;

    protected $table = 'lokasi_presensi';
    protected $primaryKey = 'id_lokasi';
    public $timestamps = true;

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_meter',
        'jenis_lokasi',
        'id_fakultas',
        'status_aktif',
        'waktu_operasional_mulai',
        'waktu_operasional_selesai',
        'keterangan'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
        'status_aktif' => 'boolean',
        'waktu_operasional_mulai' => 'datetime:H:i',
        'waktu_operasional_selesai' => 'datetime:H:i'
    ];

    // Relasi ke Fakultas
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    // Method untuk cek apakah koordinat dalam radius
    public function isDalamRadius($latitude, $longitude)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        $distance = $angle * $earthRadius;

        return $distance <= $this->radius_meter;
    }

    // Method untuk cek apakah waktu dalam jam operasional
    public function isDalamJamOperasional($waktu = null)
    {
        if (!$this->waktu_operasional_mulai || !$this->waktu_operasional_selesai) {
            return true; // Jika tidak ada batasan waktu
        }

        $waktuCheck = $waktu ? \Carbon\Carbon::parse($waktu) : now();
        $jamMulai = \Carbon\Carbon::parse($this->waktu_operasional_mulai);
        $jamSelesai = \Carbon\Carbon::parse($this->waktu_operasional_selesai);

        // Handle operasional yang melewati tengah malam
        if ($jamSelesai < $jamMulai) {
            return $waktuCheck >= $jamMulai || $waktuCheck <= $jamSelesai;
        }

        return $waktuCheck >= $jamMulai && $waktuCheck <= $jamSelesai;
    }

    // Scope untuk lokasi aktif
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    // Accessor untuk jenis lokasi dengan icon
    public function getJenisLokasiIconAttribute()
    {
        return match($this->jenis_lokasi) {
            'kantor' => 'fa-building',
            'gedung' => 'fa-university',
            'laboratorium' => 'fa-flask',
            'lainnya' => 'fa-map-marker-alt',
            default => 'fa-location-dot'
        };
    }
}