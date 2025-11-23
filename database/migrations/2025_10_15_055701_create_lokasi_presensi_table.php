<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_presensi', function (Blueprint $table) {
            $table->id('id_lokasi');
            $table->string('nama_lokasi', 100);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meter')->default(100); // radius dalam meter
            $table->enum('jenis_lokasi', ['kantor', 'gedung', 'laboratorium', 'lainnya']);
            $table->foreignId('id_fakultas')->nullable()->constrained('fakultas', 'id_fakultas');
            $table->boolean('status_aktif')->default(true);
            $table->time('waktu_operasional_mulai')->nullable();
            $table->time('waktu_operasional_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_presensi');
    }
};