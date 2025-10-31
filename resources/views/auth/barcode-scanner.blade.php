<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via QR Code</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        body {
            background: #f5f7fa;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .scanner-container {
            background: #fff;
            padding: 1.8rem 1.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            width: 95%;
            max-width: 420px;
            text-align: center;
        }

        h2 {
            font-size: 1.4rem;
            color: #1d3557;
            margin-bottom: 0.5rem;
        }

        p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        #reader {
            width: 100%;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #007bff;
        }

        #status {
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #6c757d;
            min-height: 24px;
        }

        .success {
            color: #28a745 !important;
            font-weight: 600;
        }

        .error {
            color: #dc3545 !important;
            font-weight: 600;
        }

        .warning {
            color: #ff9800 !important;
            font-weight: 600;
        }

        button {
            background: #007bff;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 12px;
            cursor: pointer;
            font-size: 0.9rem;
            margin: 8px 4px;
        }

        button:hover {
            background: #0056b3;
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        #result {
            margin-top: 1rem;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.85rem;
            word-break: break-all;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        #result.show {
            display: block;
        }

        .redirect-info {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 0.9rem;
        }

        @media (max-width: 480px) {
            .scanner-container {
                padding: 1.5rem 1rem;
            }

            h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="scanner-container">
        <h2><i class="fas fa-qrcode me-2"></i>Login dengan Scan QR Code</h2>
        <p>Arahkan kamera ke QR Code pada profil Anda untuk login otomatis</p>

        <div id="reader"></div>
        <p id="status">Menyiapkan kamera...</p>
        
        <div>
            <button id="switchCamBtn" style="display:none;">Ganti Kamera</button>
            <button id="restartBtn" style="display:none;">Restart Scanner</button>
        </div>
        
        <div id="result"></div>
    </div>

    <script>
        let html5QrCode = null;
        let cameras = [];
        let currentCameraIndex = 0;
        let isScanning = true;
        let scanProcessing = false; // Prevent multiple simultaneous scans

        const statusEl = document.getElementById('status');
        const switchBtn = document.getElementById('switchCamBtn');
        const restartBtn = document.getElementById('restartBtn');
        const resultEl = document.getElementById('result');

        function showStatus(message, type = '') {
            statusEl.textContent = message;
            statusEl.className = type;
        }

        function showResult(text) {
            resultEl.innerHTML = '<strong>QR Terdeteksi:</strong><br>' + text;
            resultEl.className = 'show';
        }

        function isValidUrl(string) {
            try {
                // Check if it starts with http/https
                if (string.startsWith('http://') || string.startsWith('https://')) {
                    new URL(string);
                    return true;
                }
                
                // Check if it starts with www.
                if (string.startsWith('www.')) {
                    new URL('https://' + string);
                    return true;
                }
                
                // Check if it contains domain pattern
                const domainPattern = /^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}(\/.*)?$/;
                if (domainPattern.test(string)) {
                    new URL('https://' + string);
                    return true;
                }
                
                return false;
            } catch (e) {
                return false;
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Prevent multiple simultaneous processing
            if (scanProcessing) {
                console.log('Already processing a scan, ignoring...');
                return;
            }
            
            scanProcessing = true;
            
            console.log('=================================');
            console.log('QR CODE DETECTED!');
            console.log('Raw Data:', decodedText);
            console.log('Decoded Result:', decodedResult);
            console.log('Data Length:', decodedText.length);
            console.log('Data Type:', typeof decodedText);
            console.log('=================================');
            
            // Stop scanning immediately
            isScanning = false;
            
            showStatus('✓ QR Code Berhasil Terbaca!', 'success');
            showResult(decodedText);
            
            // Check if it's a valid URL
            if (isValidUrl(decodedText)) {
                let targetUrl = decodedText;
                
                // Add protocol if missing
                if (decodedText.startsWith('www.')) {
                    targetUrl = 'https://' + decodedText;
                } else if (!decodedText.startsWith('http://') && !decodedText.startsWith('https://')) {
                    targetUrl = 'https://' + decodedText;
                }
                
                console.log('Valid URL detected!');
                console.log('Original:', decodedText);
                console.log('Target URL:', targetUrl);
                
                showStatus('🚀 Mengarahkan ke: ' + targetUrl, 'success');
                
                resultEl.innerHTML += '<div class="redirect-info">⏳ Redirecting dalam 2 detik...</div>';
                
                // Stop camera before redirect
                html5QrCode.stop().then(() => {
                    console.log('Camera stopped, redirecting...');
                    
                    // Delay redirect so user can see the message
                    setTimeout(() => {
                        console.log('Executing redirect to:', targetUrl);
                        window.location.href = targetUrl;
                    }, 2000);
                }).catch(err => {
                    console.error('Error stopping camera:', err);
                    // Redirect anyway
                    setTimeout(() => {
                        window.location.href = targetUrl;
                    }, 2000);
                });
                
            } else {
                console.warn('Not a valid URL!');
                showStatus('⚠️ QR Code bukan URL yang valid', 'warning');
                resultEl.innerHTML += '<div class="redirect-info" style="background: #fff3cd;">Ini bukan URL login. Pastikan Anda scan QR Code yang benar.</div>';
                
                // Restart scanning after 4 seconds
                setTimeout(() => {
                    scanProcessing = false;
                    isScanning = true;
                    showStatus('Scanning aktif kembali...', '');
                    resultEl.className = '';
                }, 4000);
            }
        }

        function onScanError(errorMessage) {
            // Silent - don't log every frame error
        }

        async function startScanning(cameraId) {
            try {
                showStatus('Memulai kamera...', '');
                
                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                await html5QrCode.start(
                    cameraId,
                    config,
                    onScanSuccess,
                    onScanError
                );

                showStatus('✓ Kamera aktif - Arahkan ke QR Code', 'success');
                isScanning = true;
                scanProcessing = false;
                
            } catch (err) {
                console.error('Error starting camera:', err);
                showStatus('Error: ' + err.message, 'error');
                restartBtn.style.display = 'inline-block';
            }
        }

        async function stopScanning() {
            try {
                if (html5QrCode && html5QrCode.isScanning) {
                    await html5QrCode.stop();
                }
            } catch (err) {
                console.error('Error stopping:', err);
            }
        }

        async function initScanner() {
            try {
                html5QrCode = new Html5Qrcode("reader");
                
                cameras = await Html5Qrcode.getCameras();
                
                if (!cameras || cameras.length === 0) {
                    showStatus('Tidak ada kamera ditemukan!', 'error');
                    return;
                }

                console.log('Cameras found:', cameras.length);
                cameras.forEach((cam, idx) => {
                    console.log(`Camera ${idx + 1}:`, cam.label);
                });

                let backCameraIndex = cameras.findIndex(camera => 
                    camera.label.toLowerCase().includes('back') ||
                    camera.label.toLowerCase().includes('rear') ||
                    camera.label.toLowerCase().includes('environment')
                );

                currentCameraIndex = backCameraIndex >= 0 ? backCameraIndex : 0;
                
                if (cameras.length > 1) {
                    switchBtn.style.display = 'inline-block';
                }

                await startScanning(cameras[currentCameraIndex].id);
                
            } catch (err) {
                console.error('Init error:', err);
                showStatus('Gagal initialize: ' + err.message, 'error');
                resultEl.innerHTML = '<strong>Error Detail:</strong><br>' + err.message;
                resultEl.className = 'show';
            }
        }

        switchBtn.addEventListener('click', async () => {
            try {
                await stopScanning();
                currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                showStatus('Ganti ke kamera ' + (currentCameraIndex + 1), '');
                await startScanning(cameras[currentCameraIndex].id);
            } catch (err) {
                console.error('Switch camera error:', err);
                showStatus('Gagal ganti kamera', 'error');
            }
        });

        restartBtn.addEventListener('click', async () => {
            restartBtn.style.display = 'none';
            resultEl.className = '';
            scanProcessing = false;
            await stopScanning();
            await initScanner();
        });

        window.addEventListener('load', () => {
            if (typeof Html5Qrcode === 'undefined') {
                showStatus('Library gagal dimuat!', 'error');
                return;
            }
            
            console.log('Initializing QR Scanner...');
            initScanner();
        });

    </script>
</body>

</html>