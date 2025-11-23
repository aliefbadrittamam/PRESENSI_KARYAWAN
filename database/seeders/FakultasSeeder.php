<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fakultas')->insert([
            [
                'id_fakultas' => 1,
                'kode_fakultas' => 'FKT',
                'nama_fakultas' => 'Fakultas Teknologi',
                'dekan' => 'Dr. Ahmad Fauzi',
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_fakultas' => 2,
                'kode_fakultas' => 'FIK',
                'nama_fakultas' => 'Fakultas Ilmu Komputer',
                'dekan' => 'RONY S.KOM',
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
