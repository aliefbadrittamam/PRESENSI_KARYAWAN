<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user admin sudah ada
        $adminExists = User::where('email', 'admin@presensi.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@presensi.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'), // Password default
                'remember_token' => Str::random(10),
                'role' => 'admin', // Tambahkan field role
                'status' => 'active',
            ]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@presensi.com');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('Admin user already exists!');
        }

        // Buat beberapa user contoh
        $users = [
            [
                'name' => 'Manager HRD',
                'email' => 'hrd@presensi.com',
                'password' => Hash::make('hrd123'),
                'role' => 'hrd',
                'status' => 'active',
            ],
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@presensi.com',
                'password' => Hash::make('spv123'),
                'role' => 'supervisor',
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}