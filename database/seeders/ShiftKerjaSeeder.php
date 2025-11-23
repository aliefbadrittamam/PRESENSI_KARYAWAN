<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftKerjaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shift_kerja')->insert([
            [
                'id_shift' => 1,
                'kode_shift' => 'SHIFT-PAGI',
                'nama_shift' => 'Shift Pagi',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'toleransi_keterlambatan' => 15,
                'keterangan' => 'Jam kerja normal pagi',
                'status_aktif' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
