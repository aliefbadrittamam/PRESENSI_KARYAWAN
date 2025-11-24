<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShiftKerja;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SetTestShiftKeluar extends Command
{
    protected $signature = 'test:shift-keluar';
    protected $description = 'Set shift keluar ke 2 menit dari sekarang (gunakan data presensi yang sudah ada)';

    public function handle()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        
        // Cek apakah ada data presensi hari ini
        $presensiCount = Presensi::whereDate('tanggal_presensi', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->count();
        
        if ($presensiCount === 0) {
            $this->error('❌ Tidak ada data presensi hari ini!');
            $this->warn('⚠️  Jalankan dulu: php artisan test:shift');
            $this->warn('    Kemudian jalankan: php artisan presensi:remind --force');
            $this->warn('    Baru kemudian jalankan: php artisan test:shift-keluar');
            return Command::FAILURE;
        }
        
        $this->info("✓ Ditemukan {$presensiCount} data presensi (sudah masuk, belum keluar)");
        
        // Set jam selesai 2 menit dari sekarang
        $jamSelesai = $now->copy()->addMinutes(2);
        
        // Get shift aktif
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('❌ Tidak ada shift aktif!');
            return Command::FAILURE;
        }
        
        // Simpan jam lama untuk info
        $jamMulaiLama = $shift->jam_mulai;
        $jamSelesaiLama = $shift->jam_selesai;
        
        // Update hanya jam selesai
        $shift->update([
            'jam_selesai' => $jamSelesai->format('H:i:s'),
        ]);
        
        // Clear cache reminder keluar
        $cacheKey = 'reminder_keluar_sent_' . $today->format('Y-m-d');
        Cache::forget($cacheKey);
        $this->info("🗑️  Cache reminder keluar dihapus");
        
        $reminderKeluar = $jamSelesai->copy()->subMinutes(30);
        
        $this->newLine();
        $this->info("✅ Shift Keluar berhasil diset untuk testing!");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $this->comment("📋 Perubahan Shift:");
        $this->line("   Jam Mulai  : {$jamMulaiLama} (tidak berubah)");
        $this->line("   Jam Selesai: {$jamSelesaiLama} → " . $jamSelesai->format('H:i:s'));
        
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("⏰ Waktu Sekarang        : " . $now->format('H:i:s'));
        $this->info("📱 Reminder Keluar mulai : " . $reminderKeluar->format('H:i:s'));
        $this->info("🏁 Jam Selesai Shift     : " . $jamSelesai->format('H:i:s'));
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Tampilkan data presensi yang ada
        $this->newLine();
        $this->comment("📋 Data Presensi yang Akan Dapat Reminder:");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $presensiList = Presensi::with('karyawan')
            ->whereDate('tanggal_presensi', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->get();
        
        foreach ($presensiList as $index => $presensi) {
            $this->line(sprintf(
                "   %d. %-25s | Masuk: %s | Keluar: -",
                $index + 1,
                $presensi->karyawan->nama_lengkap,
                $presensi->jam_masuk
            ));
        }
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Hitung rentang reminder
        $rentangMulai = $reminderKeluar->format('H:i');
        $rentangSelesai = $jamSelesai->format('H:i');
        
        $this->newLine();
        $this->info("📅 Rentang Reminder Keluar: {$rentangMulai} - {$rentangSelesai}");
        
        // Cek apakah sekarang sudah dalam rentang
        if ($now->between($reminderKeluar, $jamSelesai)) {
            $this->warn("✅ SEKARANG SUDAH DALAM RENTANG REMINDER!");
            $this->warn("   Reminder akan LANGSUNG dikirim!");
        } else {
            $menitTunggu = $now->diffInMinutes($reminderKeluar);
            $this->info("⏳ Reminder akan mulai dalam ~{$menitTunggu} menit");
        }
        
        $this->newLine();
        $this->comment("💡 Next Steps:");
        $this->line("   1. Manual : php artisan presensi:remind-keluar --force");
        $this->line("   2. Auto   : php artisan schedule:work");
        $this->line("   3. Monitor: tail -f storage/logs/reminder-keluar.log");
        
        $this->newLine();
        $this->comment("📝 Timeline:");
        $this->line("   • Sekarang         : " . $now->format('H:i:s'));
        $this->line("   • Rentang Reminder : {$rentangMulai} - {$rentangSelesai}");
        $this->line("   • Jam Selesai      : " . $jamSelesai->format('H:i:s'));
        
        return Command::SUCCESS;
    }
}