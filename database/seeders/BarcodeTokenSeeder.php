<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class BarcodeTokenSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk menambahkan barcode_token ke semua user.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Jika user belum punya barcode_token, buatkan
            if (!$user->barcode_token) {
                $user->barcode_token = Str::random(12); // token unik
                $user->save();
            }
        }

        $this->command->info('✅ Semua user telah memiliki barcode_token unik.');
    }
}
