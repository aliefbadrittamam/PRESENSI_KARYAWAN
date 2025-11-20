<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;

class UserKaryawanSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Admin Sistem
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
            'address' => 'Jl. Raya Admin No. 1',
        ]);

        // 🔹 Karyawan Biasa
        $userKaryawan = User::create([
            'name' => 'Karyawan Satu',
            'email' => 'karyawan@example.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'status' => 'active',
            'phone' => '081298765432',
            'address' => 'Jl. Kantor Utama No. 2',
        ]);

        // 🔹 Data Karyawan yang terhubung ke user
        Karyawan::create([
            'user_id' => $userKaryawan->id,
            'nip' => 'KRY001',
            'nama_lengkap' => 'Karyawan Satu',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1998-05-01',
            'email' => 'karyawan@example.com',
            'nomor_telepon' => '081298765432',
            'id_jabatan' => 2, // sesuai data master 'Staff Karyawan'
            'id_departemen' => 1, // Departemen Teknologi Informasi
            'id_fakultas' => 1, // Fakultas Teknologi
            'status_aktif' => 1,
            'tanggal_mulai_kerja' => '2023-01-01',
        ]);
    }
}
