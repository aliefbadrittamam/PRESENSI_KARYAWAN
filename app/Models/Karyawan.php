<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $timestamps = true;

     protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'id_jabatan', 'id_departemen', 'id_fakultas', 'nip', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir', 'email', 'nomor_telepon', 'id_jabatan', 'id_departemen', 'id_fakultas', 'status_aktif', 'tanggal_mulai_kerja', 'tanggal_berhenti_kerja', 'foto', 'template_face_id', 'status_verifikasi_face_id', 'tanggal_verifikasi_face_id', 'password', 'role'];

    protected $hidden = ['password'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_mulai_kerja' => 'date',
        'tanggal_berhenti_kerja' => 'date',
        'status_aktif' => 'boolean',
        'tanggal_verifikasi_face_id' => 'datetime',
    ];

    // Relasi ke Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    // Relasi ke Departemen
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }

    // Relasi ke Fakultas
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'id_fakultas');
    }

    public function presensi()
{
    return $this->hasMany(Presensi::class, 'id_karyawan', 'id_karyawan');
}
}
