<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            
            
            FakultasSeeder::class,
            DepartemenSeeder::class,
            JabatanSeeder::class,
            ShiftKerjaSeeder::class,
            LokasiPresensiSeeder::class,
            UserKaryawanSeeder::class,
            BarcodeTokenSeeder::class,
            // Seeders lainnya
        ]);
    }
}
