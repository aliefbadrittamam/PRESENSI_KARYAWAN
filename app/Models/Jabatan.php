<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = true;

   protected $fillable = [
    'kode_jabatan',
    'nama_jabatan',
    'jenis_jabatan',
    'keterangan'
];


    // Relasi ke Karyawan (opsional, jika masih digunakan)
    public function karyawan()
    {
        return $this->hasMany(Karyawan::class, 'id_jabatan');
    }
}
