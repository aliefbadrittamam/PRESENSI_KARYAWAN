<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->foreignId('id_karyawan')->constrained('karyawan', 'id_karyawan');
            $table->foreignId('id_shift')->nullable()->constrained('shift_kerja', 'id_shift'); // ← tambahkan nullable
            $table->date('tanggal_presensi')->default(now()); // ← tambahkan default tanggal hari ini
            
            // Data Masuk
            $table->time('jam_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8)->nullable();
            $table->string('alamat_masuk')->nullable();
            $table->decimal('accuracy_masuk', 5, 2)->nullable();
            $table->text('face_id_data_masuk')->nullable();
            $table->decimal('confidence_score_masuk', 3, 2)->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_thumbnail_masuk')->nullable();

            // Data Keluar
            $table->time('jam_keluar')->nullable();
            $table->decimal('latitude_keluar', 10, 8)->nullable();
            $table->decimal('longitude_keluar', 11, 8)->nullable();
            $table->string('alamat_keluar')->nullable();
            $table->decimal('accuracy_keluar', 5, 2)->nullable();
            $table->text('face_id_data_keluar')->nullable();
            $table->decimal('confidence_score_keluar', 3, 2)->nullable();
            $table->string('foto_keluar')->nullable();
            $table->string('foto_thumbnail_keluar')->nullable();

            // Status & Validasi
            $table->enum('status_kehadiran', ['hadir', 'terlambat', 'izin', 'sakit', 'cuti', 'alpha'])->default('alpha');
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('alasan_reject')->nullable();
            $table->integer('keterlambatan_menit')->default(0);
            $table->decimal('total_jam_kerja', 5, 2)->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['id_karyawan', 'tanggal_presensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
