<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';
    protected $primaryKey = 'id_fakultas';
    public $timestamps = true;

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'dekan',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    // Relasi ke Departemen
    public function departemen()
    {
        return $this->hasMany(Departemen::class, 'id_fakultas');
    }

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_fakultas');
    }
}