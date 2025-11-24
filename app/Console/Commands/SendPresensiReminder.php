<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SendPresensiReminder extends Command
{
    protected $signature = 'presensi:remind {--force : Force send reminder regardless of time}';
    protected $description = 'Kirim pengingat presensi dalam rentang 30 menit sebelum jam masuk';

    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        Log::info('=== Checking Presensi Reminder ===', [
            'time' => $now->format('H:i:s')
        ]);
        
        // Get active shift
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('Tidak ada shift aktif!');
            Log::error('No active shift found');
            return Command::FAILURE;
        }

        // Parse jam mulai
        $jamMulai = Carbon::parse($shift->jam_mulai);
        $jamMulaiHariIni = Carbon::today()->setTimeFrom($jamMulai);
        
        // Hitung rentang waktu reminder: 30 menit sebelum jam masuk sampai jam masuk
        $waktuReminderMulai = $jamMulaiHariIni->copy()->subMinutes(30);
        $waktuReminderSelesai = $jamMulaiHariIni->copy();
        
        // Log untuk debugging
        $this->info("Shift: {$shift->nama_shift}");
        $this->info("Jam Masuk: {$jamMulaiHariIni->format('H:i')}");
        $this->info("Rentang Reminder: {$waktuReminderMulai->format('H:i')} - {$waktuReminderSelesai->format('H:i')}");
        $this->info("Waktu Sekarang: {$now->format('H:i')}");
        
        Log::info('Shift info', [
            'shift' => $shift->nama_shift,
            'jam_mulai' => $jamMulaiHariIni->format('H:i'),
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
        $cacheKey = 'reminder_masuk_sent_' . $today->format('Y-m-d');
        
        if (Cache::has($cacheKey)) {
            $this->info("✓ Reminder masuk sudah dikirim hari ini (cache aktif)");
            Log::info('Reminder already sent today (cached)');
            return Command::SUCCESS;
        }

        // Get karyawan yang belum absen hari ini
        $karyawanBelumAbsen = Karyawan::where('status_aktif', 1)
            ->whereDoesntHave('presensi', function ($query) use ($today) {
                $query->where('tanggal_presensi', $today)
                      ->whereNotNull('jam_masuk');
            })
            ->get();

        if ($karyawanBelumAbsen->count() === 0) {
            $this->info('✓ Semua karyawan sudah absen masuk.');
            Log::info('All employees already checked in');
            
            // Set cache agar tidak cek lagi hari ini
            Cache::put($cacheKey, true, $today->copy()->addDay()->startOfDay());
            
            return Command::SUCCESS;
        }

        $this->info("📋 Ditemukan {$karyawanBelumAbsen->count()} karyawan yang belum absen.");
        Log::info("Found {$karyawanBelumAbsen->count()} employees without check-in");
        
        $successCount = 0;
        $failedCount = 0;

        foreach ($karyawanBelumAbsen as $karyawan) {
            // Skip jika nomor telepon tidak ada
            if (empty($karyawan->nomor_telepon)) {
                $this->warn("⚠️  {$karyawan->nama_lengkap} - Tidak ada nomor telepon");
                Log::warning('No phone number', ['karyawan' => $karyawan->nama_lengkap]);
                $failedCount++;
                continue;
            }

            try {
                $messageParams = [
                    'nama' => $karyawan->nama_lengkap,
                    'tanggal' => $today->format('d F Y'),
                    'waktu' => $now->format('H:i'),
                    'jam_mulai' => $jamMulaiHariIni->format('H:i'),
                    'shift' => $shift->nama_shift . ' (' . $jamMulaiHariIni->format('H:i') . ' - ' . Carbon::parse($shift->jam_selesai)->format('H:i') . ')',
                ];
                
                Log::info('Dispatching WhatsApp job', [
                    'karyawan' => $karyawan->nama_lengkap,
                    'phone' => $karyawan->nomor_telepon,
                ]);
                
                // Kirim WA reminder
                SendWhatsAppNotification::dispatch(
                    $karyawan->nomor_telepon,
                    'presensi_belum_absen',
                    $messageParams
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
        // Cache expire besok jam 00:00
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