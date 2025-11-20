<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departemen', function (Blueprint $table) {
            $table->id('id_departemen');
            $table->string('kode_departemen', 10)->unique();
            $table->string('nama_departemen', 100);
            $table->foreignId('id_fakultas')->constrained('fakultas', 'id_fakultas');
            $table->text('deskripsi')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departemen');
    }
};