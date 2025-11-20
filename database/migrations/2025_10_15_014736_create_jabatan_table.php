<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id('id_jabatan');
            $table->string('kode_jabatan', 10)->unique();
            $table->string('nama_jabatan', 100);
            $table->enum('jenis_jabatan', ['struktural', 'fungsional']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
