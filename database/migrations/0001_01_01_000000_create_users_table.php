<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Informasi dasar user
            $table->string('name'); // Nama lengkap pengguna
            $table->string('email')->unique(); // Email untuk login
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable()->default(null); // Disimpan dalam bentuk hash bcrypt

            // Peran dan status sistem
            $table->enum('role', ['admin', 'hrd', 'supervisor', 'user'])->default('user'); // Role 'user' untuk karyawan biasa
            $table->enum('status', ['active', 'inactive'])->default('active');
            // Aktif/nonaktif akun
            // Info tambahan (opsional)
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();

            // Token & waktu
            $table->rememberToken();
            $table->timestamps();
            $table->string('barcode_token', 255)->unique();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
