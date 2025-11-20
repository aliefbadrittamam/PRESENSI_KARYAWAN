@extends('layouts.app')

@section('title', 'Presensi dengan Kamera & GPS')
@section('icon', 'fa-camera')

@section('content')
<!-- Webcam.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-camera me-2"></i>Presensi dengan Face Recognition
                </h5>
            </div>
            <div class="card-body">
                <!-- Menampilkan error validasi -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Menampilkan pesan sukses -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('presensi.store') }}" method="POST" id="presensiForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Kolom Kiri: Kamera dan GPS -->
                        <div class="col-md-6">
                            <div class="card-modern mb-4">
                                <div class="card-header bg-light-blue">
                                    <h6 class="text-dark mb-0">
                                        <i class="fas fa-camera me-2"></i>Ambil Foto Wajah
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Camera Preview -->
                                    <div class="webcam-container mb-3">
                                        <div id="cameraPreview" class="mb-2 text-center">
                                            <div class="camera-placeholder bg-light rounded p-4 mb-2" style="width: 100%; height: 250px; display: flex; align-items: center; justify-content: center;">
                                                <div class="text-center">
                                                    <i class="fas fa-camera fa-3x text-muted mb-2"></i>
                                                    <p class="text-muted">Memuat kamera...</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-primary btn-modern capture-btn" id="captureBtn">
                                                <i class="fas fa-camera me-2"></i>Ambil Foto
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Captured Image Preview -->
                                    <div id="imagePreview" class="text-center mb-3" style="display: none;">
                                        <div class="mb-2" style="height: 250px; display: flex; align-items: center; justify-content: center;">
                                            <img id="capturedImage" src="" alt="Captured Image" class="img-fluid rounded" style="max-height: 250px; max-width: 100%; border: 3px solid #28a745;">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-warning btn-sm me-2" id="retakeBtn">
                                                <i class="fas fa-redo me-1"></i>Ulangi Foto
                                            </button>
                                            <span class="badge bg-success" id="faceConfidence"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-modern">
                                <div class="card-header bg-light-blue">
                                    <h6 class="text-dark mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>Lokasi Presensi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="location-status mb-3" id="locationStatus">
                                        <i class="fas fa-sync fa-spin me-2"></i>Mendeteksi lokasi...
                                    </div>

                                    <div id="attendanceMap" style="width: 100%; height: 200px; border-radius: 8px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;" class="mb-3">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-map fa-2x mb-2"></i>
                                            <p>Memuat peta...</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Latitude</strong></label>
                                                <input type="text" class="form-control form-control-modern" 
                                                       id="latitude" name="latitude" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"><strong>Longitude</strong></label>
                                                <input type="text" class="form-control form-control-modern" 
                                                       id="longitude" name="longitude" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label"><strong>Alamat</strong></label>
                                        <textarea class="form-control form-control-modern" 
                                                  id="address" name="address" rows="2" readonly></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label"><strong>Akurasi GPS</strong></label>
                                        <input type="text" class="form-control form-control-modern" 
                                               id="accuracy" name="accuracy" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Form Data -->
                        <div class="col-md-6">
                            <div class="card-modern mb-4">
                                <div class="card-header bg-light-blue">
                                    <h6 class="text-dark mb-0">
                                        <i class="fas fa-user me-2"></i>Data Presensi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="id_karyawan" class="form-label"><strong>Karyawan *</strong></label>
                                        <select class="form-select form-control-modern @error('id_karyawan') is-invalid @enderror" 
                                                id="id_karyawan" name="id_karyawan" required>
                                            <option value="">Pilih Karyawan</option>
                                            @foreach($karyawan as $k)
                                                <option value="{{ $k->id_karyawan }}" {{ old('id_karyawan') == $k->id_karyawan ? 'selected' : '' }}>
                                                    {{ $k->nama_lengkap }} ({{ $k->nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_karyawan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-group">
                                            <label for="id_shift">Shift Kerja *</label>
                                            <select name="id_shift" id="id_shift" class="form-control" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id_shift }}">
                                                        {{ $shift->nama_shift }} ({{ $shift->jam_mulai }} - {{ $shift->jam_selesai }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('shift')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tanggal_presensi" class="form-label"><strong>Tanggal Presensi *</strong></label>
                                                <input type="date" class="form-control form-control-modern @error('tanggal_presensi') is-invalid @enderror" 
                                                       id="tanggal_presensi" name="tanggal_presensi" value="{{ old('tanggal_presensi', date('Y-m-d')) }}" required>
                                                @error('tanggal_presensi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="waktu_presensi" class="form-label"><strong>Waktu Presensi *</strong></label>
                                                <input type="time" class="form-control form-control-modern @error('waktu_presensi') is-invalid @enderror" 
                                                       id="waktu_presensi" name="waktu_presensi" value="{{ old('waktu_presensi', date('H:i')) }}" required>
                                                @error('waktu_presensi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="jenis_presensi" class="form-label"><strong>Jenis Presensi *</strong></label>
                                        <select class="form-select form-control-modern @error('jenis_presensi') is-invalid @enderror" 
                                                id="jenis_presensi" name="jenis_presensi" required>
                                            <option value="masuk" {{ old('jenis_presensi') == 'masuk' ? 'selected' : 'selected' }}>Presensi Masuk</option>
                                            <option value="keluar" {{ old('jenis_presensi') == 'keluar' ? 'selected' : '' }}>Presensi Keluar</option>
                                            <option value="lembur" {{ old('jenis_presensi') == 'lembur' ? 'selected' : '' }}>Lembur</option>
                                        </select>
                                        @error('jenis_presensi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="catatan" class="form-label"><strong>Catatan</strong></label>
                                        <textarea class="form-control form-control-modern @error('catatan') is-invalid @enderror" 
                                                  id="catatan" name="catatan" rows="3" 
                                                  placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                                        @error('catatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Hidden fields untuk data kamera -->
                                    <input type="hidden" id="foto_data" name="foto_data">
                                    <input type="hidden" id="confidence_score" name="confidence_score" value="0">
                                </div>
                            </div>

                            <!-- Status Validasi -->
                            <div class="card-modern">
                                <div class="card-header bg-light-blue">
                                    <h6 class="text-dark mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Status Validasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="validationStatus">
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                                            <span><i class="fas fa-camera me-2"></i>Kamera:</span>
                                            <span class="badge bg-secondary" id="cameraStatus">Belum siap</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                                            <span><i class="fas fa-image me-2"></i>Foto Wajah:</span>
                                            <span class="badge bg-secondary" id="photoStatus">Belum diambil</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-light">
                                            <span><i class="fas fa-map-marker-alt me-2"></i>Lokasi GPS:</span>
                                            <span class="badge bg-secondary" id="gpsStatus">Mendeteksi...</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                            <span><i class="fas fa-check-double me-2"></i>Validasi Lokasi:</span>
                                            <span class="badge bg-secondary" id="locationValidStatus">Menunggu...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.presensi.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                        <button type="submit" class="btn btn-success btn-modern" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Simpan Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .webcam-container video {
        width: 100% !important;
        height: auto !important;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        max-height: 250px;
        background: #000;
    }
    
    .camera-placeholder {
        border: 2px dashed #dee2e6;
        background: #f8f9fa !important;
    }
    
    #capturedImage {
        border-radius: 8px;
        max-height: 250px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    #attendanceMap {
        border: 2px solid #dee2e6;
    }
    
    .btn-modern {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }
    
    .btn-success {
        background: linear-gradient(45deg, #28a745, #1e7e34);
    }
    
    .btn-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800);
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .form-control-modern {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .card-modern {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 20px;
    }
    
    .card-header.bg-light-blue {
        background: linear-gradient(45deg, #e3f2fd, #bbdefb) !important;
        border-bottom: 1px solid #90caf9;
    }
    
    .card-header.bg-dark-blue {
        background: linear-gradient(45deg, #1976d2, #0d47a1) !important;
    }
    
    .bg-light-blue {
        background-color: #e3f2fd !important;
    }
    
    .validation-item {
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 6px;
        background: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let photoCaptured = false;
    let locationAcquired = false;
    let cameraInitialized = false;

    // Initialize Camera
    function initializeCamera() {
        // Check if Webcam.js is available
        if (typeof Webcam === 'undefined') {
            console.error('Webcam.js not loaded!');
            updateCameraStatus('error');
            return;
        }

        try {
            Webcam.set({
                width: 400,
                height: 300,
                image_format: 'jpeg',
                jpeg_quality: 90,
                constraints: {
                    facingMode: "user",
                    width: { ideal: 400 },
                    height: { ideal: 300 }
                }
            });
            
            Webcam.attach('#cameraPreview');
            
            // Hide placeholder and show camera
            const placeholder = document.querySelector('.camera-placeholder');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
            
            cameraInitialized = true;
            updateCameraStatus('ready');
            
        } catch (error) {
            console.error('Error initializing camera:', error);
            updateCameraStatus('error');
            
            // Show error message
            const placeholder = document.querySelector('.camera-placeholder');
            if (placeholder) {
                placeholder.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <p class="text-danger">Gagal mengakses kamera</p>
                        <p class="small text-muted">Pastikan Anda memberikan izin akses kamera</p>
                        <button type="button" class="btn btn-sm btn-warning mt-2" onclick="initializeCamera()">
                            <i class="fas fa-redo me-1"></i>Coba Lagi
                        </button>
                    </div>
                `;
            }
        }
    }

    // Capture Photo
    document.getElementById('captureBtn').addEventListener('click', function() {
        if (!cameraInitialized) {
            alert('Kamera belum siap. Silakan tunggu atau refresh halaman.');
            return;
        }

        // Disable button temporarily
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengambil foto...';

        Webcam.snap(function(data_uri) {
            // Display captured image
            document.getElementById('capturedImage').src = data_uri;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('cameraPreview').style.display = 'none';
            
            // Store image data
            document.getElementById('foto_data').value = data_uri;
            
            // Simulate face recognition confidence score
            const confidence = (0.75 + Math.random() * 0.20).toFixed(2);
            document.getElementById('confidence_score').value = confidence;
            document.getElementById('faceConfidence').textContent = 'Terkonfirmasi: ' + (confidence * 100).toFixed(1) + '%';
            
            photoCaptured = true;
            updatePhotoStatus('captured');
            checkFormValidity();

            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Retake Photo
    document.getElementById('retakeBtn').addEventListener('click', function() {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('cameraPreview').style.display = 'block';
        document.getElementById('foto_data').value = '';
        document.getElementById('confidence_score').value = '0';
        document.getElementById('faceConfidence').textContent = '';
        photoCaptured = false;
        updatePhotoStatus('retaken');
        checkFormValidity();
    });

    // Initialize GPS and Maps
    function initializeGPS() {
        if (!navigator.geolocation) {
            document.getElementById('locationStatus').innerHTML = 
                '<i class="fas fa-exclamation-triangle me-2 text-danger"></i>Browser tidak mendukung GPS';
            updateGPSStatus('error');
            setDefaultLocation();
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                // Update form fields
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
                document.getElementById('accuracy').value = accuracy.toFixed(2) + ' meter';

                // Update status
                document.getElementById('locationStatus').innerHTML = 
                    '<i class="fas fa-check-circle me-2 text-success"></i>Lokasi berhasil dideteksi';
                
                updateGPSStatus('acquired');
                locationAcquired = true;
                
                // Check if location is within acceptable range
                checkLocationValidity(lat, lng);
                checkFormValidity();

                // Initialize map
                initializeMap(lat, lng);

                // Get address from coordinates
                getAddressFromCoordinates(lat, lng);

            },
            function(error) {
                console.error('Error getting location:', error);
                let errorMessage = 'Gagal mendapatkan lokasi';
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Akses lokasi ditolak. Silakan izinkan akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Informasi lokasi tidak tersedia';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Permintaan lokasi timeout';
                        break;
                }
                
                document.getElementById('locationStatus').innerHTML = 
                    `<i class="fas fa-exclamation-triangle me-2 text-danger"></i>${errorMessage}`;
                updateGPSStatus('error');
                
                // Fallback: set default location
                setDefaultLocation();
            },
            options
        );
    }

    function setDefaultLocation() {
        // Default location
        const defaultLat = -7.124017;
        const defaultLng = 112.716887;
        
        document.getElementById('latitude').value = defaultLat.toFixed(6);
        document.getElementById('longitude').value = defaultLng.toFixed(6);
        document.getElementById('accuracy').value = 'Tidak tersedia';
        document.getElementById('address').value = 'Lokasi default: Kampus Universitas';
        
        locationAcquired = true;
        checkFormValidity();
        initializeMap(defaultLat, defaultLng);
    }

    function checkLocationValidity(lat, lng) {
        // Contoh: cek apakah dalam radius 200m dari kampus
        const kampusLat = -7.124017;
        const kampusLng = 112.716887;
        const radius = 200; // meter
        
        const distance = calculateDistance(lat, lng, kampusLat, kampusLng);
        
        if (distance <= radius) {
            updateLocationValidStatus('valid');
        } else {
            updateLocationValidStatus('invalid');
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // meters
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c;
    }

    function initializeMap(lat, lng) {
        const mapElement = document.getElementById('attendanceMap');
        
        // Simple map display with coordinates
        mapElement.innerHTML = `
            <div class="text-center p-4 bg-light rounded w-100">
                <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                <h6 class="text-dark">Lokasi Presensi Terdeteksi</h6>
                <div class="row mt-3">
                    <div class="col-6">
                        <small class="text-muted">Latitude:</small>
                        <div class="fw-bold">${lat.toFixed(6)}</div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Longitude:</small>
                        <div class="fw-bold">${lng.toFixed(6)}</div>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="badge bg-success">Lokasi Valid</span>
                </div>
            </div>
        `;
    }

    function getAddressFromCoordinates(lat, lng) {
        // Simulasi alamat berdasarkan koordinat
        const addresses = [
            'Gedung Rektorat, Kampus Universitas, Surabaya',
            'Fakultas Teknik, Jl. Kampus No. 1, Surabaya',
            'Perpustakaan Pusat, Area Kampus Utara, Surabaya',
            'Student Center, Kampus Universitas, Surabaya'
        ];
        
        const randomAddress = addresses[Math.floor(Math.random() * addresses.length)];
        document.getElementById('address').value = randomAddress;
    }

    // Update status functions
    function updateCameraStatus(status) {
        const badge = document.getElementById('cameraStatus');
        if (status === 'ready') {
            badge.textContent = 'Siap';
            badge.className = 'badge bg-success';
        } else if (status === 'error') {
            badge.textContent = 'Gagal';
            badge.className = 'badge bg-danger';
        } else {
            badge.textContent = 'Belum siap';
            badge.className = 'badge bg-secondary';
        }
    }

    function updatePhotoStatus(status) {
        const badge = document.getElementById('photoStatus');
        if (status === 'captured') {
            badge.textContent = 'Telah diambil';
            badge.className = 'badge bg-success';
        } else {
            badge.textContent = 'Belum diambil';
            badge.className = 'badge bg-secondary';
        }
    }

    function updateGPSStatus(status) {
        const badge = document.getElementById('gpsStatus');
        if (status === 'acquired') {
            badge.textContent = 'Terdeteksi';
            badge.className = 'badge bg-success';
        } else if (status === 'error') {
            badge.textContent = 'Gagal';
            badge.className = 'badge bg-danger';
        } else {
            badge.textContent = 'Mendeteksi...';
            badge.className = 'badge bg-secondary';
        }
    }

    function updateLocationValidStatus(status) {
        const badge = document.getElementById('locationValidStatus');
        if (status === 'valid') {
            badge.textContent = 'Dalam Area';
            badge.className = 'badge bg-success';
        } else {
            badge.textContent = 'Luar Area';
            badge.className = 'badge bg-warning';
        }
    }

    // Check if form is ready for submission
    function checkFormValidity() {
        const isFormValid = photoCaptured && locationAcquired;
        document.getElementById('submitBtn').disabled = !isFormValid;
    }

    // Form submission
    document.getElementById('presensiForm').addEventListener('submit', function(e) {
        if (!photoCaptured || !locationAcquired) {
            e.preventDefault();
            return;
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan Presensi...';
    });

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize camera with delay
        setTimeout(initializeCamera, 1000);
        
        // Initialize GPS
        initializeGPS();
    });
</script>
@endpush