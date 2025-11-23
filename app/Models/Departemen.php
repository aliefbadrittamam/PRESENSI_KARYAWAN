<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';
    public $timestamps = true;

    protected $fillable = [
        'kode_departemen',
        'nama_departemen',
        'id_fakultas',
        'deskripsi',
        'status_aktif'
    ];

    protected $casts = [
        'status_aktif' => 'boolean'
    ];

    // Relasi ke Fakultas
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    // Relasi ke Karyawan
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_departemen');
    }
}