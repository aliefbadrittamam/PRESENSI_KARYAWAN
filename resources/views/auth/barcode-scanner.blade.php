@extends('layouts.app')

@section('title', 'Scan QR Code Login')

@section('content')
<div class="container text-center py-5">
    <h2 class="mb-4 text-white"><i class="fas fa-qrcode me-2"></i>Login dengan Scan QR Code</h2>
    <p class="text-muted">Arahkan kamera ke QR Code pada profil Anda untuk login otomatis.</p>

    <div id="reader" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 10px; overflow: hidden;"></div>

    <p id="status" class="mt-4 text-secondary fw-bold">Menunggu hasil scan...</p>
</div>

{{-- HTML5 QR Code library (modern & maintained) --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reader = document.getElementById('reader');
    const statusText = document.getElementById('status');

    // Callback ketika QR terdeteksi
    function onScanSuccess(decodedText, decodedResult) {
        console.log("✅ QR Terdeteksi:", decodedText);
        statusText.innerText = "QR Code terdeteksi, sedang memproses login...";
        statusText.classList.remove('text-secondary');
        statusText.classList.add('text-success');

        // Pastikan format URL benar
        let targetUrl = decodedText;
targetUrl = "{{ url('/barcode-login') }}/" + decodedText;


        // Stop kamera dan arahkan
        html5QrcodeScanner.clear().then(() => {
            window.location.href = targetUrl;
        }).catch(err => console.error('Gagal menghentikan scanner:', err));
    }
function onScanFailure(error) {
  console.log("Scan gagal:", error);
}


    

    // Inisialisasi scanner
    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
            fps: 10,                // frame per detik
            qrbox: { width: 250, height: 250 }, // area pemindaian
            aspectRatio: 1.0,
            rememberLastUsedCamera: true,
            supportedScanTypes: [
                Html5QrcodeScanType.SCAN_TYPE_CAMERA
            ]
        },
        /* verbose= */ false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});
</script>
@endsection
