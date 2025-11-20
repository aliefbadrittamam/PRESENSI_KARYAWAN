<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LokasiPresensi extends Model
{
    use HasFactory;

    protected $table = 'lokasi_presensi';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = ['nama_lokasi', 'latitude', 'longitude', 'radius_meter', 'jenis_lokasi', 'id_fakultas', 'status_aktif', 'waktu_operasional_mulai', 'waktu_operasional_selesai', 'keterangan'];

    protected $casts = [
        'status_aktif' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relationship dengan Fakultas
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas', 'id_fakultas');
    }

    /**
     * Scope untuk lokasi aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 1);
    }

    /**
     * Scope untuk lokasi berdasarkan fakultas
     */
    public function scopeByFakultas($query, $fakultasId)
    {
        return $query->where('id_fakultas', $fakultasId);
    }

    /**
     * Check if coordinates are within radius
     */
    public function isWithinRadius($lat, $lng)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad((float) $this->latitude);
        $lon1 = deg2rad((float) $this->longitude);

        $lat2 = deg2rad((float) $lat);
        $lon2 = deg2rad((float) $lng);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance <= (float) $this->radius_meter;
    }

    public function getDistanceFrom($lat, $lng)
    {
        $earthRadius = 6371000; // meters

        $lat1 = deg2rad((float) $this->latitude);
        $lon1 = deg2rad((float) $this->longitude);

        $lat2 = deg2rad((float) $lat);
        $lon2 = deg2rad((float) $lng);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
