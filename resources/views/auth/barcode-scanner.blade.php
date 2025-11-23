<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via QR Code</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.min.css" rel="stylesheet">
    <!-- QR Scanner -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <style>
        :root {
            /* 🌿 Warna Utama (Sage Green) */
            --primary-sage: #9DC183;
            --secondary-sage: #6FA976;

            /* 🌼 Warna Aksen */
            --accent-cream: #FDF6EC;
            --accent-gold: #E4C988;

            /* 🪴 Warna Netral & Teks */
            --light-gray: #F5F7FA;
            --medium-gray: #A0AEC0;
            --dark-gray: #4A5568;

            /* 🌸 Warna Sekunder Pelengkap */
            --soft-pink: #F2C6B4;
            --soft-blue: #BFD8D2;

            /* 🌚 Shadow */
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--primary-sage) 0%, var(--secondary-sage) 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        #vanta-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .scanner-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .header-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .header-section h2 {
            color: var(--dark-gray);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .header-section .subtitle {
            color: var(--medium-gray);
            font-size: 0.9rem;
        }

        .header-section i {
            color: var(--secondary-sage);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        /* Custom Tab Styling */
        .nav-tabs {
            border-bottom: 2px solid var(--light-gray);
        }

        .nav-tabs .nav-link {
            color: var(--medium-gray);
            border: none;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--secondary-sage);
            background: var(--accent-cream);
            border-radius: 8px 8px 0 0;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background: var(--primary-sage);
            border-radius: 8px 8px 0 0;
            border: none;
        }

        /* QR Reader Container */
        #reader {
            width: 100%;
            margin: 1rem auto;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid var(--primary-sage);
        }

        /* Upload Area */
        .upload-area {
            border: 3px dashed var(--primary-sage);
            border-radius: 12px;
            padding: 40px 20px;
            background: var(--accent-cream);
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .upload-area:hover {
            background: var(--soft-blue);
            border-color: var(--secondary-sage);
            transform: translateY(-2px);
        }

        .upload-area.dragover {
            background: var(--soft-blue);
            border-color: var(--secondary-sage);
            transform: scale(1.02);
        }

        .upload-area i {
            font-size: 3rem;
            color: var(--secondary-sage);
            margin-bottom: 1rem;
        }

        .upload-area p {
            color: var(--dark-gray);
            margin: 0;
        }

        #fileInput {
            display: none;
        }

        #preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            margin: 1rem 0;
            display: none;
            border: 2px solid var(--light-gray);
        }

        #preview.show {
            display: block;
        }

        /* Status Badge */
        .status-badge {
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 1rem 0;
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-gray);
            color: var(--dark-gray);
        }

        .status-badge.success {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.error {
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.warning {
            background: #fff3cd;
            color: #856404;
        }

        /* Custom Buttons */
        .btn-sage {
            background: var(--primary-sage);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-sage:hover {
            background: var(--secondary-sage);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(157, 193, 131, 0.4);
        }

        .btn-sage:active {
            transform: translateY(0);
        }

        .btn-sage:disabled {
            background: var(--medium-gray);
            cursor: not-allowed;
            transform: none;
        }

        .btn-outline-sage {
            background: transparent;
            border: 2px solid var(--primary-sage);
            color: var(--secondary-sage);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-outline-sage:hover {
            background: var(--primary-sage);
            color: white;
        }

        /* Result Box */
        #result {
            margin-top: 1rem;
            padding: 15px;
            background: var(--accent-cream);
            border-radius: 8px;
            font-size: 0.85rem;
            word-break: break-all;
            display: none;
            border-left: 4px solid var(--secondary-sage);
            text-align: left;
        }

        #result.show {
            display: block;
        }

        #result strong {
            display: block;
            margin-bottom: 8px;
            color: var(--secondary-sage);
        }

        /* Loading Spinner */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--primary-sage);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* SweetAlert Custom Colors */
        .swal2-confirm {
            background-color: var(--primary-sage) !important;
        }

        .swal2-confirm:hover {
            background-color: var(--secondary-sage) !important;
        }

        @media (max-width: 480px) {
            .scanner-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div id="vanta-bg"></div>

    <div class="scanner-container">
        <div class="header-section">
            <i class="fas fa-qrcode"></i>
            <h2>Scan QR Code</h2>
            <p class="subtitle">Pilih metode scan QR Code</p>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link active w-100" id="camera-tab" data-bs-toggle="tab" data-bs-target="#camera"
                    type="button" role="tab">
                    <i class="fas fa-camera me-2"></i>Kamera
                </button>
            </li>
            <li class="nav-item flex-fill" role="presentation">
                <button class="nav-link w-100" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload"
                    type="button" role="tab">
                    <i class="fas fa-image me-2"></i>Upload
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Camera Tab -->
            <div class="tab-pane fade show active" id="camera" role="tabpanel">
                <div id="reader"></div>
                <div id="status" class="status-badge">Menyiapkan kamera...</div>
                <div class="d-flex gap-2 justify-content-center">
                    <button id="switchCamBtn" class="btn btn-sage" style="display:none;">
                        <i class="fas fa-sync-alt me-2"></i>Ganti Kamera
                    </button>
                    <button id="restartBtn" class="btn btn-outline-sage" style="display:none;">
                        <i class="fas fa-redo me-2"></i>Restart
                    </button>
                </div>
            </div>

            <!-- Upload Tab -->
            <div class="tab-pane fade" id="upload" role="tabpanel">
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><strong>Klik untuk pilih gambar</strong></p>
                    <p style="font-size: 0.85rem; margin-top: 8px;">atau drag & drop gambar QR Code di sini</p>
                </div>
                <input type="file" id="fileInput" accept="image/*" />
                <img id="preview" alt="Preview" class="mx-auto d-block" />
                <div id="statusUpload" class="status-badge" style="display:none;"></div>
                <div class="text-center">
                    <button id="clearBtn" class="btn btn-outline-sage" style="display:none;">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                </div>
            </div>
        </div>

        <div id="result"></div>
    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.10.1/sweetalert2.all.min.js"></script>
    <!-- THREE.js + VANTA.NET -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/tengbao/vanta@latest/dist/vanta.net.min.js"></script>
    <script>
        VANTA.NET({
            el: "#vanta-bg",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            color: 0x000000,
            backgroundColor: 0xffffff,
            points: 12.0,
            maxDistance: 20.0,
            spacing: 18.0
        });
    </script>
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

        // Tab switching handler
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const target = e.target.getAttribute('data-bs-target');
                currentTab = target === '#camera' ? 'camera' : 'upload';

                if (currentTab === 'camera') {
                    if (!html5QrCode || !html5QrCode.isScanning) {
                        init();
                    }
                } else {
                    if (html5QrCode && html5QrCode.isScanning) {
                        stopScanning();
                    }
                }

                resultEl.className = '';
                hasScanned = false;
            });
        });

        function setStatus(message, type = '') {
            if (currentTab === 'camera') {
                statusEl.textContent = message;
                statusEl.className = 'status-badge ' + type;
            } else {
                statusUploadEl.textContent = message;
                statusUploadEl.className = 'status-badge ' + type;
                statusUploadEl.style.display = 'block';
            }
        }

        function showResult(text) {
            resultEl.innerHTML = `<strong>QR Code Terdeteksi:</strong>${text}`;
            resultEl.className = 'show';
        }

        function processQRCode(decodedText) {
            setStatus('✓ QR Code berhasil terbaca!', 'success');
            showResult(decodedText);

            if (decodedText.includes('http') || decodedText.includes('.')) {
                let url = decodedText.trim();

                if (!url.startsWith('http://') && !url.startsWith('https://')) {
                    if (url.startsWith('www.')) {
                        url = 'https://' + url;
                    } else if (url.includes('.')) {
                        url = 'https://' + url;
                    }
                }

                setStatus('Login berhasil!', 'success');

                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: 'Anda akan login dalam beberapa saat...',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = url;
                });

            } else {
                setStatus('⚠️ Bukan URL yang valid', 'warning');

                Swal.fire({
                    icon: 'warning',
                    title: 'QR Code Terdeteksi',
                    text: 'Namun ini bukan URL yang valid untuk login',
                    confirmButtonText: 'OK'
                });

                hasScanned = false;
            }
        }

        function onScanSuccess(decodedText) {
            if (hasScanned) return;
            hasScanned = true;

            html5QrCode.stop().then(() => {
                processQRCode(decodedText);
            }).catch(err => {
                processQRCode(decodedText);
            });
        }

        function onScanError(err) {}

        async function startScanning(cameraId) {
            try {
                setStatus('Memulai kamera...');

                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                };

                await html5QrCode.start(cameraId, config, onScanSuccess, onScanError);

                setStatus('✓ Kamera siap - Scan QR Code', 'success');
                hasScanned = false;

            } catch (err) {
                setStatus('Error: ' + err.message, 'error');
                restartBtn.style.display = 'inline-block';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memulai Kamera',
                    text: err.message,
                    confirmButtonText: 'OK'
                });
            }
        }

        async function stopScanning() {
            try {
                if (html5QrCode && html5QrCode.isScanning) {
                    await html5QrCode.stop();
                }
            } catch (err) {}
        }

        async function init() {
            try {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                cameras = await Html5Qrcode.getCameras();

                if (!cameras || cameras.length === 0) {
                    setStatus('❌ Tidak ada kamera!', 'error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Tidak Ditemukan',
                        text: 'Pastikan browser memiliki izin akses kamera',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

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
                setStatus('Gagal initialize: ' + err.message, 'error');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menginisialisasi',
                    text: err.message,
                    confirmButtonText: 'OK'
                });
            }
        }

        // File input handler
        // File input handler
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

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

                // Gunakan scanFileV2 dengan konfigurasi yang lebih baik
                const result = await html5QrCode.scanFileV2(file, {
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    aspectRatio: 1.0,
                    disableFlip: false,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                }, true);

                processQRCode(result.decodedText);

            } catch (err) {
                // Coba metode alternatif jika gagal
                try {
                    const result = await html5QrCode.scanFile(file, false);
                    processQRCode(result);
                } catch (err2) {
                    setStatus('Tidak ada QR Code terdeteksi di gambar', 'error');

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Scan QR Code',
                        text: 'Tidak ada QR Code yang terdeteksi di gambar ini. Pastikan gambar jelas dan QR Code terlihat dengan baik.',
                        confirmButtonText: 'OK'
                    });
                }
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
                Swal.fire({
                    icon: 'error',
                    title: 'File Tidak Valid',
                    text: 'File harus berupa gambar (JPG, PNG, dll)',
                    confirmButtonText: 'OK'
                });
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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Library QR Scanner gagal dimuat. Refresh halaman.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            init();
        });
    </script>
</body>

</html>
