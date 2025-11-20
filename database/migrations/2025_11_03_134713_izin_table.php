<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id('id_izin');
            $table->unsignedBigInteger('id_karyawan');
            $table->enum('tipe_izin', ['izin', 'sakit'])->default('izin')->comment('Jenis izin: izin atau sakit');
            $table->date('tanggal_mulai')->comment('Tanggal mulai izin');
            $table->date('tanggal_selesai')->comment('Tanggal selesai izin');
            $table->integer('jumlah_hari')->default(1)->comment('Total hari izin');
            $table->text('keterangan')->comment('Alasan/keterangan izin');
            $table->string('file_pendukung', 255)->nullable()->comment('Path file surat dokter/pendukung');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending')->comment('Status persetujuan');
            $table->dateTime('tanggal_pengajuan')->comment('Waktu pengajuan izin');
            $table->dateTime('tanggal_approval')->nullable()->comment('Waktu disetujui/ditolak');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('ID user yang approve/reject');
            $table->text('alasan_penolakan')->nullable()->comment('Alasan jika ditolak');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_karyawan')
                  ->references('id_karyawan')
                  ->on('karyawan')
                  ->onDelete('cascade');
            
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Indexes untuk performa
            $table->index(['id_karyawan', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index('status_approval');
            $table->index('tipe_izin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};