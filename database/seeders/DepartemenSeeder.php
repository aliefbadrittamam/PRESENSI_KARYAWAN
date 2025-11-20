<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departemen')->insert([
            [
                'id_departemen' => 1,
                'kode_departemen' => 'DEP-TI',
                'nama_departemen' => 'Departemen Teknologi Informasi',
                'id_fakultas' => 1,
                'deskripsi' => 'Fokus pada sistem informasi dan pengembangan perangkat lunak.',
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
