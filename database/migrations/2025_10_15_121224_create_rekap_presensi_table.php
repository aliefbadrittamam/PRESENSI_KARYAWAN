<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_presensi', function (Blueprint $table) {
            $table->id('id_rekap');
            $table->foreignId('id_karyawan')->constrained('karyawan', 'id_karyawan');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('total_hari_kerja')->default(0);
            
            // Data Kehadiran
            $table->integer('jumlah_hadir')->default(0);
            $table->integer('jumlah_terlambat')->default(0);
            $table->integer('jumlah_izin')->default(0);
            $table->integer('jumlah_sakit')->default(0);
            $table->integer('jumlah_cuti')->default(0);
            $table->integer('jumlah_alpha')->default(0);
            
            // Persentase
            $table->decimal('persentase_kehadiran', 5, 2)->default(0);
            $table->decimal('persentase_terlambat', 5, 2)->default(0);
            $table->decimal('persentase_tidak_hadir', 5, 2)->default(0);
            
            // Statistik
            $table->integer('total_menit_terlambat')->default(0);
            $table->decimal('rata_rata_terlambat', 5, 2)->default(0);
            $table->decimal('total_jam_kerja', 8, 2)->default(0);
            
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['id_karyawan', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_presensi');
    }
};