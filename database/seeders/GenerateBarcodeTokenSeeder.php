<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class GenerateBarcodeTokenSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereNull('barcode_token')->orWhere('barcode_token', '')->get();

        foreach ($users as $user) {
            $user->barcode_token = Str::random(12);
            $user->save();
        }

        $this->command->info('✅ Barcode token berhasil dibuat untuk semua user yang belum punya.');
    }
}
