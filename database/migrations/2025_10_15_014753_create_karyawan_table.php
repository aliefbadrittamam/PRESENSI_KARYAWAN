<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->string('nip', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('email')->unique();
            $table->string('nomor_telepon', 15);
            $table->foreignId('id_jabatan')->constrained('jabatan', 'id_jabatan');
            $table->foreignId('id_departemen')->constrained('departemen', 'id_departemen');
            $table->foreignId('id_fakultas')->constrained('fakultas', 'id_fakultas');
            $table->boolean('status_aktif')->default(true);
            $table->date('tanggal_mulai_kerja');
            $table->date('tanggal_berhenti_kerja')->nullable();
            $table->string('foto')->nullable();
            // $table->text('template_face_id')->nullable();
            $table->enum('status_verifikasi_face_id', ['pending', 'verified', 'failed'])->default('pending');
            $table->timestamp('tanggal_verifikasi_face_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};