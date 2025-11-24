<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendAbsenKeluarReminder extends Command
{
    protected $signature = 'presensi:remind-keluar {--force : Force send reminder regardless of time}';
    protected $description = 'Kirim pengingat absen keluar dalam rentang 30 menit sebelum jam selesai';

    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        Log::info('=== Checking Absen Keluar Reminder ===', [
            'time' => $now->format('H:i:s')
        ]);
        
        // Get active shift
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('Tidak ada shift aktif!');
            Log::error('No active shift found');
            return Command::FAILURE;
        }

        // Parse jam selesai
        $jamSelesai = Carbon::parse($shift->jam_selesai);
        $jamSelesaiHariIni = Carbon::today()->setTimeFrom($jamSelesai);
        
        // Hitung rentang waktu reminder: 30 menit sebelum jam selesai sampai jam selesai
        $waktuReminderMulai = $jamSelesaiHariIni->copy()->subMinutes(30);
        $waktuReminderSelesai = $jamSelesaiHariIni->copy();
        
        // Log untuk debugging
        $this->info("Shift: {$shift->nama_shift}");
        $this->info("Jam Selesai: {$jamSelesaiHariIni->format('H:i')}");
        $this->info("Rentang Reminder: {$waktuReminderMulai->format('H:i')} - {$waktuReminderSelesai->format('H:i')}");
        $this->info("Waktu Sekarang: {$now->format('H:i')}");
        
        Log::info('Shift info', [
            'shift' => $shift->nama_shift,
            'jam_selesai' => $jamSelesaiHariIni->format('H:i'),
            'rentang_mulai' => $waktuReminderMulai->format('H:i'),
            'rentang_selesai' => $waktuReminderSelesai->format('H:i'),
        ]);
        
        // CEK: Apakah sekarang dalam rentang waktu reminder?
        if (!$this->option('force')) {
            if (!$now->between($waktuReminderMulai, $waktuReminderSelesai)) {
                $this->info("❌ Diluar rentang waktu reminder.");
                
                if ($now->lessThan($waktuReminderMulai)) {
                    $menitTunggu = $now->diffInMinutes($waktuReminderMulai);
                    $this->info("⏳ Reminder akan mulai dalam {$menitTunggu} menit ({$waktuReminderMulai->format('H:i')})");
                } else {
                    $this->info("✓ Rentang waktu reminder sudah lewat");
                }
                
                Log::info('Not in reminder window');
                return Command::SUCCESS;
            }
        }
        
        $this->warn("✅ DALAM RENTANG WAKTU REMINDER!");
        
        // CEK CACHE: Apakah hari ini sudah pernah kirim?
        $cacheKey = 'reminder_keluar_sent_' . $today->format('Y-m-d');
        
        if (Cache::has($cacheKey)) {
            $this->info("✓ Reminder keluar sudah dikirim hari ini (cache aktif)");
            Log::info('Reminder already sent today (cached)');
            return Command::SUCCESS;
        }

        // Get presensi yang sudah absen masuk tapi belum keluar
        $presensiList = Presensi::with('karyawan')
            ->where('tanggal_presensi', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->get();

        if ($presensiList->count() === 0) {
            $this->info('✓ Semua karyawan sudah absen keluar.');
            Log::info('All employees already checked out');
            
            // Set cache agar tidak cek lagi hari ini
            Cache::put($cacheKey, true, $today->copy()->addDay()->startOfDay());
            
            return Command::SUCCESS;
        }

        $this->info("📋 Ditemukan {$presensiList->count()} karyawan yang belum absen keluar.");
        Log::info("Found {$presensiList->count()} employees without check-out");
        
        $successCount = 0;
        $failedCount = 0;

        foreach ($presensiList as $presensi) {
            $karyawan = $presensi->karyawan;
            
            // Skip jika nomor telepon tidak ada
            if (empty($karyawan->nomor_telepon)) {
                $this->warn("⚠️  {$karyawan->nama_lengkap} - Tidak ada nomor telepon");
                Log::warning('No phone number', ['karyawan' => $karyawan->nama_lengkap]);
                $failedCount++;
                continue;
            }

            try {
                Log::info('Dispatching WhatsApp job', [
                    'karyawan' => $karyawan->nama_lengkap,
                    'phone' => $karyawan->nomor_telepon,
                ]);
                
                // Kirim WA reminder
                SendWhatsAppNotification::dispatch(
                    $karyawan->nomor_telepon,
                    'presensi_belum_absen_keluar',
                    [
                        'nama' => $karyawan->nama_lengkap,
                        'tanggal' => $today->format('d F Y'),
                        'jam_masuk' => $presensi->jam_masuk,
                        'jam_selesai' => $jamSelesaiHariIni->format('H:i'),
                    ]
                );

                $this->info("✓ {$karyawan->nama_lengkap} ({$karyawan->nomor_telepon})");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("✗ {$karyawan->nama_lengkap} - Error: " . $e->getMessage());
                Log::error('Failed to dispatch job', [
                    'karyawan' => $karyawan->nama_lengkap,
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Ringkasan:");
        $this->info("✓ Berhasil: {$successCount}");
        if ($failedCount > 0) {
            $this->warn("✗ Gagal: {$failedCount}");
        }
        
        // SET CACHE: Tandai bahwa hari ini sudah kirim reminder
        Cache::put($cacheKey, true, $today->copy()->addDay()->startOfDay());
        $this->info("✓ Cache diset: reminder tidak akan dikirim lagi hari ini");
        
        Log::info('=== Reminder Complete ===', [
            'success' => $successCount,
            'failed' => $failedCount,
            'cache_set' => true
        ]);
        
        return Command::SUCCESS;
    }
}