<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckPresensiTable extends Command
{
    protected $signature = 'check:presensi-table';
    protected $description = 'Check struktur tabel presensi';

    public function handle()
    {
        $this->info("🔍 Checking tabel presensi...");
        $this->newLine();
        
        // Get all columns
        $columns = DB::getSchemaBuilder()->getColumnListing('presensi');
        
        $this->info("📋 Kolom-kolom di tabel presensi:");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        foreach ($columns as $index => $column) {
            $this->line("   " . ($index + 1) . ". " . $column);
        }
        
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Cek foreign key karyawan
        $this->newLine();
        $this->info("🔑 Foreign Key Karyawan:");
        
        if (in_array('id_karyawan', $columns)) {
            $this->info("   ✓ Menggunakan: id_karyawan");
        } elseif (in_array('karyawan_id', $columns)) {
            $this->info("   ✓ Menggunakan: karyawan_id");
        } else {
            $this->warn("   ⚠ Tidak ditemukan kolom foreign key karyawan!");
        }
        
        // Cek Model Presensi fillable
        $this->newLine();
        $this->info("📝 Model Presensi fillable:");
        
        $presensi = new \App\Models\Presensi();
        $fillable = $presensi->getFillable();
        
        if (!empty($fillable)) {
            foreach ($fillable as $field) {
                $this->line("   • " . $field);
            }
        } else {
            $this->warn("   ⚠ Fillable kosong atau menggunakan guarded");
        }
        
        return Command::SUCCESS;
    }
}