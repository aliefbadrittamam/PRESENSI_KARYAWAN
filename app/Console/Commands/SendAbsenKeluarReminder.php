<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;

class SendAbsenKeluarReminder extends Command
{
    protected $signature = 'presensi:remind-keluar';
    protected $description = 'Kirim pengingat absen keluar ke karyawan yang belum absen keluar';

    public function handle()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        
        // Get active shift
        $shift = ShiftKerja::where('status_aktif', 1)->first();
        
        if (!$shift) {
            $this->error('Tidak ada shift aktif!');
            return;
        }

        // Cek jam sekarang sudah lewat jam selesai
        $jamSelesai = Carbon::parse($shift->jam_selesai);
        
        if ($now->lessThan($jamSelesai)) {
            $this->info('Belum waktunya mengirim reminder absen keluar.');
            return;
        }

        // Get presensi yang sudah absen masuk tapi belum keluar
        $presensiList = Presensi::with('karyawan')
            ->where('tanggal_presensi', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->get();

        $this->info("Ditemukan {$presensiList->count()} karyawan yang belum absen keluar.");

        foreach ($presensiList as $presensi) {
            $karyawan = $presensi->karyawan;
            
            // Skip jika nomor telepon tidak ada
            if (empty($karyawan->nomor_telepon)) {
                continue;
            }

            // Kirim WA reminder
            SendWhatsAppNotification::dispatch(
                $karyawan->nomor_telepon,
                'presensi_belum_absen_keluar',
                [
                    'nama' => $karyawan->nama_lengkap,
                    'tanggal' => $today->format('d F Y'),
                    'jam_masuk' => $presensi->jam_masuk,
                ]
            );

            $this->info("✓ Reminder absen keluar dikirim ke: {$karyawan->nama_lengkap}");
        }

        $this->info('Selesai!');
    }
}