<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $url;
    protected $client;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->url = env('FONNTE_URL');
        $this->client = new Client();
    }

    /**
     * Send WhatsApp message
     * 
     * @param string $phone - Format: 628xxx (tanpa +)
     * @param string $message
     * @return array
     */
    public function sendMessage($phone, $message)
    {
        try {
            // Format nomor telepon (hapus +, 0, spasi, dan karakter lain)
            $phone = $this->formatPhone($phone);

            $response = $this->client->post($this->url, [
                'headers' => [
                    'Authorization' => $this->token,
                ],
                'form_params' => [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62', // Indonesia
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('WhatsApp sent successfully', [
                'phone' => $phone,
                'response' => $result
            ]);

            return [
                'success' => true,
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number untuk Fonnte
     * Input: 08123456789, +628123456789, 8123456789
     * Output: 628123456789
     */
    private function formatPhone($phone)
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika tidak diawali 62, tambahkan 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send message dengan template
     */
    public function sendTemplate($phone, $templateName, $params = [])
    {
        $message = $this->getTemplate($templateName, $params);
        return $this->sendMessage($phone, $message);
    }

    /**
     * Get message templates
     */
    private function getTemplate($name, $params)
    {
        $templates = [
            'presensi_belum_absen' => "🔔 *Pengingat Presensi*\n\nHalo {nama},\n\nAnda belum melakukan presensi masuk hari ini.\n\n📅 Tanggal: {tanggal}\n⏰ Waktu: {waktu}\n🏢 Shift: {shift}\n\nSilakan lakukan presensi segera.\n\n_Pesan otomatis dari Sistem Presensi_",

            'presensi_belum_absen_keluar' => "🔔 *Pengingat Presensi Keluar*\n\nHalo {nama},\n\nAnda belum melakukan presensi keluar hari ini.\n\n📅 Tanggal: {tanggal}\n⏰ Jam Masuk: {jam_masuk}\n\nSilakan lakukan presensi keluar.\n\n_Pesan otomatis dari Sistem Presensi_",

            'izin_approved' => "✅ *Izin Disetujui*\n\nHalo {nama},\n\nPengajuan izin Anda telah *DISETUJUI*.\n\n📋 Jenis: {jenis}\n📅 Tanggal: {tanggal_mulai} s/d {tanggal_selesai}\n💬 Keterangan: {keterangan}\n\nTerima kasih.\n\n_Pesan otomatis dari Sistem Presensi_",

            'izin_rejected' => "❌ *Izin Ditolak*\n\nHalo {nama},\n\nPengajuan izin Anda *DITOLAK*.\n\n📋 Jenis: {jenis}\n📅 Tanggal: {tanggal_mulai} s/d {tanggal_selesai}\n💬 Alasan: {alasan}\n\nSilakan hubungi HRD untuk informasi lebih lanjut.\n\n_Pesan otomatis dari Sistem Presensi_",

            'cuti_approved' => "✅ *Cuti Disetujui*\n\nHalo {nama},\n\nPengajuan cuti Anda telah *DISETUJUI*.\n\n📋 Jenis: {jenis}\n📅 Tanggal: {tanggal_mulai} s/d {tanggal_selesai}\n📊 Jumlah Hari: {jumlah_hari} hari\n💬 Keterangan: {keterangan}\n\nSelamat menikmati cuti Anda.\n\n_Pesan otomatis dari Sistem Presensi_",

            'cuti_rejected' => "❌ *Cuti Ditolak*\n\nHalo {nama},\n\nPengajuan cuti Anda *DITOLAK*.\n\n📋 Jenis: {jenis}\n📅 Tanggal: {tanggal_mulai} s/d {tanggal_selesai}\n💬 Alasan: {alasan}\n\nSilakan hubungi HRD untuk informasi lebih lanjut.\n\n_Pesan otomatis dari Sistem Presensi_",

            'keterlambatan_warning' => "⚠️ *Peringatan Keterlambatan*\n\nHalo {nama},\n\nAnda telat masuk kerja hari ini.\n\n📅 Tanggal: {tanggal}\n⏰ Jam Masuk: {jam_masuk}\n⏱️ Keterlambatan: {keterlambatan} menit\n\nHarap lebih disiplin kedepannya.\n\n_Pesan otomatis dari Sistem Presensi_",
        ];

        $message = $templates[$name] ?? '';

        // Replace parameters
        foreach ($params as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $message;
    }
}