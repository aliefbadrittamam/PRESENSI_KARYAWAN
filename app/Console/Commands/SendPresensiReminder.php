<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\ShiftKerja;
use App\Jobs\SendWhatsAppNotification;
use Carbon\Carbon;

class SendPresensiReminder extends Command
{
    protected $signature = 'presensi:remind';
    protected $description = 'Kirim pengingat presensi ke karyawan yang belum absen';

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

        // Cek jam sekarang sudah lewat jam mulai + toleransi
        $jamMulai = Carbon::parse($shift->jam_mulai);
        $toleransi = $shift->toleransi_keterlambatan ?? 15;
        $batasWaktu = $jamMulai->copy()->addMinutes($toleransi);

        if ($now->lessThan($batasWaktu)) {
            $this->info('Belum waktunya mengirim reminder.');
            return;
        }

        // Get karyawan yang belum absen
        $karyawanBelumAbsen = Karyawan::where('status_aktif', 1)
            ->whereDoesntHave('presensi', function ($query) use ($today) {
                $query->where('tanggal_presensi', $today)
                      ->whereNotNull('jam_masuk');
            })
            ->get();

        $this->info("Ditemukan {$karyawanBelumAbsen->count()} karyawan yang belum absen.");

        foreach ($karyawanBelumAbsen as $karyawan) {
            // Skip jika nomor telepon tidak ada
            if (empty($karyawan->nomor_telepon)) {
                continue;
            }

            // Kirim WA reminder
            SendWhatsAppNotification::dispatch(
                $karyawan->nomor_telepon,
                'presensi_belum_absen',
                [
                    'nama' => $karyawan->nama_lengkap,
                    'tanggal' => $today->format('d F Y'),
                    'waktu' => $now->format('H:i'),
                    'shift' => $shift->nama_shift . ' (' . $shift->jam_mulai . ' - ' . $shift->jam_selesai . ')',
                ]
            );

            $this->info("✓ Reminder dikirim ke: {$karyawan->nama_lengkap}");
        }

        $this->info('Selesai!');
    }
}