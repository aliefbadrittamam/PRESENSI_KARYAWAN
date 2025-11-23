<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            [
                'id_jabatan' => 1,
                'kode_jabatan' => 'JAB-ADM',
                'nama_jabatan' => 'Administrator',
                'jenis_jabatan' => 'struktural',
                'keterangan' => 'Bertanggung jawab atas pengelolaan sistem dan pengguna.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_jabatan' => 2,
                'kode_jabatan' => 'JAB-KRY',
                'nama_jabatan' => 'Staff Karyawan',
                'jenis_jabatan' => 'fungsional',
                'keterangan' => 'Pegawai bagian operasional.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
