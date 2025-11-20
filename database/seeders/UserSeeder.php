<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel users.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '081234567890',
                'address' => 'Jl. Raya Contoh No. 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            [
                'name' => 'Karyawan Satu',
                'email' => 'karyawan@example.com',
                'password' => Hash::make('karyawan123'),
                'role' => 'user',
                'status' => 'active',
                'phone' => '081298765432',
                'address' => 'Jl. Kantor Utama No. 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
