<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('presensi', function (Blueprint $table) {
            // Update accuracy columns - bisa menampung nilai sampai 999999.99
            $table->decimal('accuracy_masuk', 8, 2)->nullable()->change();
            $table->decimal('accuracy_keluar', 8, 2)->nullable()->change();
            
            // Update keterlambatan - ubah ke unsigned untuk prevent negative
            $table->unsignedInteger('keterlambatan_menit')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->decimal('accuracy_masuk', 5, 2)->nullable()->change();
            $table->decimal('accuracy_keluar', 5, 2)->nullable()->change();
            $table->integer('keterlambatan_menit')->default(0)->change();
        });
    }
};