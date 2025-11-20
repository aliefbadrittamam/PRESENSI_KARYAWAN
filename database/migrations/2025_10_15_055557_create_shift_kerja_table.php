<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_kerja', function (Blueprint $table) {
            $table->id('id_shift');
            $table->string('kode_shift', 20)->unique();
            $table->string('nama_shift', 100);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('toleransi_keterlambatan')->default(15); // dalam menit
            $table->text('keterangan')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_kerja');
    }
};