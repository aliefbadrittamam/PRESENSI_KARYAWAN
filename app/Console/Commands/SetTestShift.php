<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShiftKerja;
use App\Models\Presensi;
use Carbon\Carbon;

class SetTestShift extends Command
{
    protected $signature = 'test:shift {--keep-presensi : Jangan hapus data presensi}';
    protected $description = 'Set shift ke 2 menit dari sekarang untuk testing';

    public function handle()
    {
        $now = Carbon::now();
        
        // Set jam mulai 2 menit dari sekarang
        $jamMulai = $now->copy()->addMinutes(2);
        $jamSelesai = $jamMulai->copy()->addHours(8);
        
        // Update shift aktif
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('❌ Tidak ada shift aktif!');
            return Command::FAILURE;
        }
        
        $shift->update([
            'jam_mulai' => $jamMulai->format('H:i:s'),
            'jam_selesai' => $jamSelesai->format('H:i:s'),
        ]);
        
        // Hapus data presensi hari ini (kecuali jika pakai flag --keep-presensi)
        if (!$this->option('keep-presensi')) {
            $today = Carbon::today();
            $deletedCount = Presensi::whereDate('tanggal_presensi', $today)->delete();
            
            $this->newLine();
            $this->info("🗑️  Data presensi hari ini dihapus: {$deletedCount} record");
        }
        
        $reminderMasuk = $jamMulai->copy()->subMinutes(30);
        
        $this->newLine();
        $this->info("✅ Shift berhasil diset!");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("⏰ Waktu Sekarang    : " . $now->format('H:i:s'));
        $this->info("📱 Reminder akan kirim: " . $reminderMasuk->format('H:i:s'));
        $this->info("🏢 Jam Mulai Shift   : " . $jamMulai->format('H:i:s'));
        $this->info("🏁 Jam Selesai Shift : " . $jamSelesai->format('H:i:s'));
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->warn("\n⚠️  Reminder akan LANGSUNG dikirim karena sudah lewat 30 menit!");
        $this->info("\n💡 Sekarang jalankan: php artisan presensi:remind --force");
        
        return Command::SUCCESS;
    }
}