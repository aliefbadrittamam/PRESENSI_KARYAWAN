{{-- File: resources/views/user/presensi/index.blade.php --}}
@extends('layouts.user')

@section('title', 'Absensi')

@section('content')
    <div class="container-desktop">
        <!-- Header -->
        @include('user.components.header', ['karyawan' => $karyawan])

        <!-- Presensi Card -->
        <div class="presensi-container">
            <div class="presensi-card">
                <!-- Status Info -->
                <div class="status-info-section">
                    <div class="text-center mb-4">
                        <div class="date-display">
                            <h2 class="current-date mb-1" id="currentDate"></h2>
                            <div class="current-time" id="currentTime"></div>
                        </div>
                    </div>

                    <!-- Status Absen Hari Ini -->
                    @if ($presensiHariIni)
                        <div class="status-today">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div
                                        class="status-box {{ $presensiHariIni->jam_masuk ? 'status-done' : 'status-pending' }}">
                                        <i class="fas fa-sign-in-alt mb-2"></i>
                                        <div class="status-label">Absen Masuk</div>
                                        <div class="status-time">
                                            {{ $presensiHariIni->jam_masuk ? date('H:i', strtotime($presensiHariIni->jam_masuk)) : 'Belum Absen' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div
                                        class="status-box {{ $presensiHariIni->jam_keluar ? 'status-done' : 'status-pending' }}">
                                        <i class="fas fa-sign-out-alt mb-2"></i>
                                        <div class="status-label">Absen Keluar</div>
                                        <div class="status-time">
                                            {{ $presensiHariIni->jam_keluar ? date('H:i', strtotime($presensiHariIni->jam_keluar)) : 'Belum Absen' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Camera Section -->
                <div class="camera-section mt-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-camera me-2"></i>
                        Ambil Foto Selfie
                    </h5>

                    <div class="camera-container">
                        <video id="camera" class="camera-preview" autoplay playsinline></video>
                        <canvas id="canvas" style="display: none;"></canvas>
                        <img id="preview" class="photo-preview" style="display: none;">

                        <div class="camera-overlay">
                            <div class="camera-guide"></div>
                        </div>
                    </div>

                    <div class="camera-controls mt-3">
                        <button type="button" class="btn btn-primary btn-lg w-100" id="captureBtn">
                            <i class="fas fa-camera me-2"></i>
                            Ambil Foto
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg w-100 mt-2" id="retakeBtn"
                            style="display: none;">
                            <i class="fas fa-redo me-2"></i>
                            Foto Ulang
                        </button>
                    </div>
                </div>

                <!-- Location Info -->
                <div class="location-section mt-4">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Lokasi Anda
                    </h5>

                    <div class="location-info" id="locationInfo">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0 text-muted">Mendapatkan lokasi...</p>
                        </div>
                    </div>

                    <div id="locationDetails" style="display: none;">
                        <div class="location-item">
                            <i class="fas fa-map-pin text-primary"></i>
                            <div class="location-text">
                                <small class="text-muted d-block">Koordinat</small>
                                <span id="coordinates">-</span>
                            </div>
                        </div>
                        <div class="location-item">
                            <i class="fas fa-crosshairs text-success"></i>
                            <div class="location-text">
                                <small class="text-muted d-block">Akurasi</small>
                                <span id="accuracy">-</span>
                            </div>
                        </div>
                        <div class="location-item">
                            <i class="fas fa-location-arrow text-info"></i>
                            <div class="location-text">
                                <small class="text-muted d-block">Alamat</small>
                                <span id="address">Memuat alamat...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Presensi -->
                <form id="presensiForm" class="mt-4">
                    @csrf
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="accuracy" id="accuracyInput">
                    <input type="hidden" name="alamat" id="alamatInput">
                    <input type="hidden" name="foto" id="fotoInput">
                    <input type="hidden" name="tipe_absen" id="tipeAbsen" value="">

                    <!-- Catatan (Optional) -->
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="submit-section">
                        @if ($presensiHariIni && !$presensiHariIni->jam_masuk)
                            <button type="submit" class="btn btn-success btn-lg w-100" id="submitMasuk">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Absen Masuk
                            </button>
                        @elseif($presensiHariIni && $presensiHariIni->jam_masuk && !$presensiHariIni->jam_keluar)
                            <button type="submit" class="btn btn-danger btn-lg w-100" id="submitKeluar">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Absen Keluar
                            </button>
                        @else
                            <button type="submit" class="btn btn-success btn-lg w-100" id="submitMasuk">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Absen Masuk
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Info Card -->
            <div class="info-card mt-3">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Penting
                </h6>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-check-circle text-success"></i>
                        Pastikan foto wajah Anda terlihat jelas
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success"></i>
                        Aktifkan GPS dan izinkan akses lokasi
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success"></i>
                        Pastikan Anda berada di area kantor
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success"></i>
                        Absen masuk: {{ $shift ? date('H:i', strtotime($shift->jam_mulai)) : '-' }}
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success"></i>
                        Absen keluar: {{ $shift ? date('H:i', strtotime($shift->jam_selesai)) : '-' }}
                    </li>
                </ul>
            </div>

            <!-- Mode Demo Switch -->
            <div class="demo-mode-card mt-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="modeDemoSwitch">
                    <label class="form-check-label" for="modeDemoSwitch">
                        <i class="fas fa-flask me-2"></i>
                        <strong>Mode Uji Coba / Demo</strong>
                    </label>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Aktifkan untuk bypass validasi lokasi (hanya untuk testing)
                </small>
                <div id="demoModeAlert" class="alert alert-warning mt-2" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Mode Demo Aktif!</strong> Validasi radius lokasi dinonaktifkan.
                </div>
            </div>

            <!-- Info Lokasi Kantor -->
            @if ($lokasiPresensi)
                <div class="lokasi-info-card mt-3">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Lokasi Kantor Anda
                    </h6>
                    <div class="lokasi-detail">
                        <div class="lokasi-item-info">
                            <i class="fas fa-building text-primary"></i>
                            <div>
                                <small class="text-muted">Nama Lokasi</small>
                                <div class="fw-bold">{{ $lokasiPresensi->nama_lokasi }}</div>
                            </div>
                        </div>
                        <div class="lokasi-item-info">
                            <i class="fas fa-crosshairs text-success"></i>
                            <div>
                                <small class="text-muted">Radius yang Diizinkan</small>
                                <div class="fw-bold">{{ $lokasiPresensi->radius_meter }} meter</div>
                            </div>
                        </div>
                        <div class="lokasi-item-info">
                            <i class="fas fa-map-pin text-danger"></i>
                            <div>
                                <small class="text-muted">Koordinat Kantor</small>
                                <div class="fw-bold">{{ $lokasiPresensi->latitude }}, {{ $lokasiPresensi->longitude }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Bottom Navigation -->
            @include('user.components.bottom-nav')

            <!-- Sidebar Menu -->
            @include('user.components.sidebar-menu')
        @endsection

        @push('styles')
            <style>
                .presensi-container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 1rem;
                }

                .presensi-card {
                    background: white;
                    border-radius: 24px;
                    padding: 1.5rem;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                }

                .date-display {
                    padding: 1rem;
                    background: linear-gradient(135deg, #FF4F7E 0%, #FF6B8F 100%);
                    border-radius: 16px;
                    color: white;
                }

                .current-date {
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin: 0;
                }

                .current-time {
                    font-size: 2.5rem;
                    font-weight: 700;
                }

                .status-today {
                    margin-top: 1rem;
                }

                .status-box {
                    background: #f8f9fa;
                    border-radius: 16px;
                    padding: 1.5rem 1rem;
                    text-align: center;
                    transition: all 0.3s;
                }

                .status-box i {
                    font-size: 2rem;
                    color: #6c757d;
                }

                .status-box.status-done {
                    background: linear-gradient(135deg, #26A69A 0%, #4DB6AC 100%);
                    color: white;
                }

                .status-box.status-done i {
                    color: white;
                }

                .status-box.status-pending {
                    background: #f8f9fa;
                    color: #6c757d;
                }

                .status-label {
                    font-size: 0.85rem;
                    margin-bottom: 0.3rem;
                }

                .status-time {
                    font-size: 1.2rem;
                    font-weight: 700;
                }

                .section-title {
                    color: #1F2937;
                    font-weight: 600;
                }

                .camera-container {
                    position: relative;
                    width: 100%;
                    border-radius: 16px;
                    overflow: hidden;
                    background: #000;
                    aspect-ratio: 4/3;
                }

                .camera-preview,
                .photo-preview {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }

                .camera-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    pointer-events: none;
                }

                .camera-guide {
                    width: 200px;
                    height: 250px;
                    border: 3px dashed rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                }

                .location-info {
                    background: #f8f9fa;
                    border-radius: 12px;
                    padding: 1rem;
                }

                .location-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 1rem;
                    padding: 0.75rem;
                    background: white;
                    border-radius: 8px;
                    margin-bottom: 0.5rem;
                }

                .location-item i {
                    font-size: 1.2rem;
                    margin-top: 0.2rem;
                }

                .location-text {
                    flex: 1;
                }

                .info-card {
                    background: white;
                    border-radius: 16px;
                    padding: 1.5rem;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                }

                .info-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }

                .info-list li {
                    padding: 0.5rem 0;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }

                .info-list i {
                    font-size: 1rem;
                }

                .submit-section {
                    margin-top: 1.5rem;
                }

                .demo-mode-card {
                    background: white;
                    border-radius: 16px;
                    padding: 1.5rem;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                }

                .form-check-input:checked {
                    background-color: #FF6B8F;
                    border-color: #FF6B8F;
                }

                .form-check-input {
                    width: 3rem;
                    height: 1.5rem;
                    cursor: pointer;
                }

                .form-check-label {
                    cursor: pointer;
                    margin-left: 0.5rem;
                }

                .lokasi-info-card {
                    background: white;
                    border-radius: 16px;
                    padding: 1.5rem;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                }

                .lokasi-detail {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }

                .lokasi-item-info {
                    display: flex;
                    align-items: flex-start;
                    gap: 1rem;
                    padding: 1rem;
                    background: #f8f9fa;
                    border-radius: 12px;
                }

                .lokasi-item-info i {
                    font-size: 1.5rem;
                    margin-top: 0.25rem;
                }

                @media (min-width: 768px) {
                    .presensi-container {
                        padding: 2rem;
                    }
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                let stream = null;
                let photoData = null;
                let latitude = null;
                let longitude = null;
                let accuracy = null;

                // Update time
                function updateTime() {
                    const now = new Date();

                    // Format date
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', options);

                    // Format time
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
                }

                // Initialize camera
                async function initCamera() {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 960
                                }
                            }
                        });
                        document.getElementById('camera').srcObject = stream;
                    } catch (error) {
                        alert('Gagal mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.');
                        console.error('Camera error:', error);
                    }
                }

                // Capture photo
                document.getElementById('captureBtn').addEventListener('click', function() {
                    const video = document.getElementById('camera');
                    const canvas = document.getElementById('canvas');
                    const preview = document.getElementById('preview');

                    // Set canvas size
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Draw video frame to canvas
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0);

                    // Get image data
                    photoData = canvas.toDataURL('image/jpeg', 0.8);

                    // Show preview
                    preview.src = photoData;
                    preview.style.display = 'block';
                    video.style.display = 'none';

                    // Update buttons
                    document.getElementById('captureBtn').style.display = 'none';
                    document.getElementById('retakeBtn').style.display = 'block';

                    // Set to hidden input
                    document.getElementById('fotoInput').value = photoData;
                });

                // Retake photo
                document.getElementById('retakeBtn').addEventListener('click', function() {
                    const video = document.getElementById('camera');
                    const preview = document.getElementById('preview');

                    preview.style.display = 'none';
                    video.style.display = 'block';

                    document.getElementById('captureBtn').style.display = 'block';
                    document.getElementById('retakeBtn').style.display = 'none';

                    photoData = null;
                    document.getElementById('fotoInput').value = '';
                });

                // Get location
                function getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            async (position) => {
                                    latitude = position.coords.latitude;
                                    longitude = position.coords.longitude;
                                    accuracy = position.coords.accuracy;

                                    // Update UI
                                    document.getElementById('latitude').value = latitude;
                                    document.getElementById('longitude').value = longitude;
                                    document.getElementById('accuracyInput').value = accuracy;
                                    document.getElementById('coordinates').textContent =
                                        `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                                    document.getElementById('accuracy').textContent = `${accuracy.toFixed(0)} meter`;

                                    // Show location details
                                    document.getElementById('locationInfo').style.display = 'none';
                                    document.getElementById('locationDetails').style.display = 'block';

                                    // Get address from coordinates
                                    await getAddress(latitude, longitude);
                                },
                                (error) => {
                                    document.getElementById('locationInfo').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin lokasi diberikan.
                        </div>
                    `;
                                    console.error('Location error:', error);
                                }, {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                }
                        );
                    } else {
                        alert('Browser Anda tidak mendukung geolocation');
                    }
                }

                // Get address from coordinates (using Nominatim API)
                async function getAddress(lat, lon) {
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`
                        );
                        const data = await response.json();

                        const address = data.display_name || 'Alamat tidak ditemukan';
                        document.getElementById('address').textContent = address;
                        document.getElementById('alamatInput').value = address;
                    } catch (error) {
                        document.getElementById('address').textContent = 'Gagal memuat alamat';
                        console.error('Address error:', error);
                    }
                }

                // Submit form
                document.getElementById('presensiForm').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    // Validation
                    if (!photoData) {
                        alert('Silakan ambil foto terlebih dahulu');
                        return;
                    }

                    if (!latitude || !longitude) {
                        alert('Lokasi belum terdeteksi. Silakan tunggu atau refresh halaman');
                        return;
                    }

                    // Determine tipe absen
                    const submitBtn = e.submitter;
                    const tipeAbsen = submitBtn.id === 'submitMasuk' ? 'masuk' : 'keluar';
                    document.getElementById('tipeAbsen').value = tipeAbsen;

                    // Show loading
                    const btnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

                    try {
                        const formData = new FormData(this);

                        // Tambahkan mode demo flag
                        const modeDemo = document.getElementById('modeDemoSwitch').checked;
                        formData.append('mode_demo', modeDemo ? '1' : '0');

                        const response = await fetch('{{ route('presensi.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (response.ok) {
                            alert(result.message || 'Presensi berhasil disimpan!');
                            window.location.href = '{{ route('karyawan.dashboard') }}';
                        } else {
                            // Tampilkan error dengan detail lokasi jika ada
                            let errorMessage = result.message || 'Gagal menyimpan presensi';
                            if (result.lokasi_kantor) {
                                errorMessage += `\n\nLokasi Kantor: ${result.lokasi_kantor.nama}`;
                                errorMessage += `\nRadius: ${result.lokasi_kantor.radius} meter`;
                            }
                            alert(errorMessage);
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = btnText;
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        console.error('Submit error:', error);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = btnText;
                    }
                });
                // Initialize on page load
                document.addEventListener('DOMContentLoaded', function() {
                    updateTime();
                    setInterval(updateTime, 1000);
                    initCamera();
                    getLocation();
                });

                document.getElementById('modeDemoSwitch').addEventListener('change', function() {
                    const demoAlert = document.getElementById('demoModeAlert');
                    if (this.checked) {
                        demoAlert.style.display = 'block';
                        console.log('Mode Demo: AKTIF - Validasi lokasi dinonaktifkan');
                    } else {
                        demoAlert.style.display = 'none';
                        console.log('Mode Demo: NONAKTIF - Validasi lokasi aktif');
                    }
                });

                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                    }
                });
            </script>
        @endpush
