<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use App\Models\Karyawan;
use App\Models\ShiftKerja;
use Carbon\Carbon;

class SimulateAbsenMasuk extends Command
{
    protected $signature = 'simulate:absen-masuk';
    protected $description = 'Simulasi karyawan absen masuk (untuk testing)';

    public function handle()
    {
        $today = Carbon::today();
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('❌ Tidak ada shift aktif!');
            return Command::FAILURE;
        }
        
        $jamMasuk = Carbon::parse($shift->jam_mulai);
        $karyawanList = Karyawan::where('status_aktif', 1)->get();
        
        if ($karyawanList->count() === 0) {
            $this->error('❌ Tidak ada karyawan aktif!');
            return Command::FAILURE;
        }
        
        $this->info("📝 Membuat data presensi masuk untuk {$karyawanList->count()} karyawan...");
        $this->newLine();
        
        $createdCount = 0;
        
        foreach ($karyawanList as $karyawan) {
            try {
                Presensi::create([
                    'id_karyawan' => $karyawan->getKey(),
                    'id_shift' => $shift->getKey(),
                    'tanggal_presensi' => $today,
                    'jam_masuk' => $jamMasuk->format('H:i:s'),
                    'jam_keluar' => null,
                    'latitude_masuk' => -6.2088,
                    'longitude_masuk' => 106.8456,
                    'alamat_masuk' => 'Jakarta',
                    'status_kehadiran' => 'hadir',
                    'status_verifikasi' => 'verified',
                ]);
                
                $this->info("✓ {$karyawan->nama_lengkap} - Absen masuk jam {$jamMasuk->format('H:i')}");
                $createdCount++;
                
            } catch (\Exception $e) {
                $this->error("✗ {$karyawan->nama_lengkap} - Error: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("✅ Simulasi absen masuk selesai!");
        $this->info("📊 Total: {$createdCount} karyawan sudah absen masuk");
        $this->comment("💡 Sekarang bisa test reminder keluar: php artisan test:shift-keluar");
        
        return Command::SUCCESS;
    }
}