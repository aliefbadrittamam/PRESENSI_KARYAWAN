<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via QR Code</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .scanner-container {
            background: #fff;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .tab {
            flex: 1;
            padding: 12px;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s;
        }

        .tab.active {
            background: #667eea;
            color: white;
        }

        .tab:hover {
            background: #5568d3;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        #reader {
            width: 100%;
            margin: 0 auto 1rem;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid #667eea;
        }

        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 12px;
            padding: 40px 20px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .upload-area:hover {
            background: #e9ecef;
            border-color: #5568d3;
        }

        .upload-area.dragover {
            background: #d4e3fc;
            border-color: #667eea;
        }

        .upload-area i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .upload-area p {
            color: #666;
            margin: 0;
        }

        #fileInput {
            display: none;
        }

        #preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            margin: 1rem 0;
            display: none;
        }

        #preview.show {
            display: block;
        }

        #status {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 1rem;
            background: #f0f0f0;
            color: #333;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        #status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        #status.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        button {
            background: #667eea;
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 5px;
            transition: all 0.3s;
        }

        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        #result {
            margin-top: 1rem;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 0.85rem;
            word-break: break-all;
            display: none;
            border-left: 4px solid #667eea;
            text-align: left;
        }

        #result.show {
            display: block;
        }

        #result strong {
            display: block;
            margin-bottom: 8px;
            color: #667eea;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 1rem;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 480px) {
            .scanner-container {
                padding: 1.5rem;
            }

            h2 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>

<body>
    <div class="scanner-container">
        <h2><i class="fas fa-qrcode"></i> Scan QR Code</h2>
        <p class="subtitle">Pilih metode scan QR Code</p>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('camera')">
                <i class="fas fa-camera"></i> Kamera
            </button>
            <button class="tab" onclick="switchTab('upload')">
                <i class="fas fa-image"></i> Upload Gambar
            </button>
        </div>

        <!-- Camera Tab -->
        <div id="cameraTab" class="tab-content active">
            <div id="reader"></div>
            <div id="status">Menyiapkan kamera...</div>
            <div class="btn-group">
                <button id="switchCamBtn" style="display:none;">
                    <i class="fas fa-sync-alt"></i> Ganti Kamera
                </button>
                <button id="restartBtn" style="display:none;">
                    <i class="fas fa-redo"></i> Restart
                </button>
            </div>
        </div>

        <!-- Upload Tab -->
        <div id="uploadTab" class="tab-content">
            <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p><strong>Klik untuk pilih gambar</strong></p>
                <p style="font-size: 0.85rem; margin-top: 8px;">atau drag & drop gambar QR Code di sini</p>
            </div>
            <input type="file" id="fileInput" accept="image/*" />
            <img id="preview" alt="Preview" />
            <div id="statusUpload" style="display:none;"></div>
            <button id="clearBtn" style="display:none;">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>

        <div id="result"></div>
    </div>

    <script>
        let html5QrCode = null;
        let cameras = [];
        let currentCameraIndex = 0;
        let hasScanned = false;
        let currentTab = 'camera';

        const statusEl = document.getElementById('status');
        const statusUploadEl = document.getElementById('statusUpload');
        const switchBtn = document.getElementById('switchCamBtn');
        const restartBtn = document.getElementById('restartBtn');
        const resultEl = document.getElementById('result');
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        const preview = document.getElementById('preview');
        const clearBtn = document.getElementById('clearBtn');

        function switchTab(tab) {
            currentTab = tab;
            
            // Update tab buttons
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.closest('.tab').classList.add('active');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            if (tab === 'camera') {
                document.getElementById('cameraTab').classList.add('active');
                if (!html5QrCode || !html5QrCode.isScanning) {
                    init();
                }
            } else {
                document.getElementById('uploadTab').classList.add('active');
                if (html5QrCode && html5QrCode.isScanning) {
                    stopScanning();
                }
            }

            // Clear result
            resultEl.className = '';
            hasScanned = false;
        }

        function setStatus(message, type = '') {
            if (currentTab === 'camera') {
                statusEl.textContent = message;
                statusEl.className = type;
            } else {
                statusUploadEl.textContent = message;
                statusUploadEl.className = 'status ' + type;
                statusUploadEl.style.display = 'block';
            }
        }

        function showResult(text) {
            resultEl.innerHTML = `<strong>QR Code Terdeteksi:</strong>${text}`;
            resultEl.className = 'show';
        }

        function processQRCode(decodedText) {
            console.log('╔════════════════════════════════════╗');
            console.log('║      QR CODE DETECTED!             ║');
            console.log('╚════════════════════════════════════╝');
            console.log('📱 Raw Data:', decodedText);
            console.log('📏 Length:', decodedText.length);
            console.log('🔤 Type:', typeof decodedText);

            setStatus('✓ QR Code berhasil terbaca!', 'success');
            showResult(decodedText);

            // Check if URL
            if (decodedText.includes('http') || decodedText.includes('.')) {
                let url = decodedText.trim();

                // Ensure protocol
                if (!url.startsWith('http://') && !url.startsWith('https://')) {
                    if (url.startsWith('www.')) {
                        url = 'https://' + url;
                    } else if (url.includes('.')) {
                        url = 'https://' + url;
                    }
                }

                console.log('🚀 Redirecting to:', url);
                setStatus('Redirecting ke: ' + url, 'success');

                setTimeout(() => {
                    console.log('🔄 Executing redirect NOW...');
                    window.location.href = url;
                }, 1500);

            } else {
                console.warn('⚠️ Not a URL:', decodedText);
                setStatus('⚠️ Bukan URL yang valid', 'warning');
                hasScanned = false;
            }
        }

        function onScanSuccess(decodedText) {
            if (hasScanned) return;
            hasScanned = true;

            html5QrCode.stop().then(() => {
                processQRCode(decodedText);
            }).catch(err => {
                console.error('Error stopping camera:', err);
                processQRCode(decodedText);
            });
        }

        function onScanError(err) {
            // Silent
        }

        async function startScanning(cameraId) {
            try {
                setStatus('Memulai kamera...');

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                };

                await html5QrCode.start(cameraId, config, onScanSuccess, onScanError);

                setStatus('✓ Kamera siap - Scan QR Code', 'success');
                hasScanned = false;

            } catch (err) {
                console.error('❌ Camera error:', err);
                setStatus('Error: ' + err.message, 'error');
                restartBtn.style.display = 'inline-block';
            }
        }

        async function stopScanning() {
            try {
                if (html5QrCode && html5QrCode.isScanning) {
                    await html5QrCode.stop();
                }
            } catch (err) {
                console.error('Stop error:', err);
            }
        }

        async function init() {
            try {
                console.log('🔧 Initializing scanner...');

                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                cameras = await Html5Qrcode.getCameras();

                if (!cameras || cameras.length === 0) {
                    setStatus('❌ Tidak ada kamera!', 'error');
                    return;
                }

                console.log('📷 Cameras found:', cameras.length);

                let backCamIdx = cameras.findIndex(c =>
                    c.label.toLowerCase().includes('back') ||
                    c.label.toLowerCase().includes('rear') ||
                    c.label.toLowerCase().includes('environment')
                );

                currentCameraIndex = backCamIdx >= 0 ? backCamIdx : 0;

                if (cameras.length > 1) {
                    switchBtn.style.display = 'inline-block';
                }

                await startScanning(cameras[currentCameraIndex].id);

            } catch (err) {
                console.error('❌ Init error:', err);
                setStatus('Gagal initialize: ' + err.message, 'error');
            }
        }

        // File input handler
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            console.log('📁 File selected:', file.name);
            setStatus('Memproses gambar...', '');

            // Show preview
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.classList.add('show');
                clearBtn.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);

            // Scan QR from image
            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                const result = await html5QrCode.scanFile(file, true);
                console.log('✓ QR Code found in image');
                processQRCode(result);

            } catch (err) {
                console.error('❌ Scan error:', err);
                setStatus('Tidak ada QR Code terdeteksi di gambar', 'error');
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            } else {
                setStatus('File harus berupa gambar!', 'error');
            }
        });

        // Clear button
        clearBtn.addEventListener('click', () => {
            fileInput.value = '';
            preview.src = '';
            preview.classList.remove('show');
            clearBtn.style.display = 'none';
            statusUploadEl.style.display = 'none';
            resultEl.className = '';
            hasScanned = false;
        });

        // Switch camera
        switchBtn.addEventListener('click', async () => {
            await stopScanning();
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            setStatus('Ganti ke kamera ' + (currentCameraIndex + 1));
            await startScanning(cameras[currentCameraIndex].id);
        });

        // Restart
        restartBtn.addEventListener('click', async () => {
            restartBtn.style.display = 'none';
            resultEl.className = '';
            hasScanned = false;
            await stopScanning();
            await init();
        });

        // Start
        window.addEventListener('load', () => {
            if (typeof Html5Qrcode === 'undefined') {
                setStatus('❌ Library gagal dimuat!', 'error');
                return;
            }
            init();
        });
    </script>
</body>

</html>