<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class TestWhatsApp extends Command
{
    protected $signature = 'test:whatsapp {phone} {--message=}';
    protected $description = 'Test kirim WhatsApp ke nomor tertentu';

    public function handle(WhatsAppService $whatsapp)
    {
        $phone = $this->argument('phone');
        $message = $this->option('message') ?? "🔔 *Test Notifikasi*\n\nHalo, test whatsaappp 123 .\n\nWaktu: " . now()->format('d F Y H:i');

        $this->info("Mengirim pesan ke: {$phone}");
        $this->info("Pesan: {$message}");
        
        $result = $whatsapp->sendMessage($phone, $message);

        if ($result['success']) {
            $this->info("✓ Pesan berhasil dikirim!");
            $this->line(json_encode($result['data'], JSON_PRETTY_PRINT));
        } else {
            $this->error("✗ Gagal mengirim pesan!");
            $this->error($result['message']);
        }
    }
}