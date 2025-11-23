<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiPresensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lokasi_presensi')->insert([
            [
                'id_lokasi' => 1,
                'nama_lokasi' => 'Kantor Pusat',
                'latitude' => -7.15687000,
                'longitude' => 113.47521000,
                'radius_meter' => 100,
                'jenis_lokasi' => 'kantor',
                'id_fakultas' => 1,
                'status_aktif' => 1,
                'waktu_operasional_mulai' => '07:00:00',
                'waktu_operasional_selesai' => '17:00:00',
                'keterangan' => 'Lokasi utama untuk presensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
