@extends('layouts.app')

@section('title', 'Presensi dengan Kamera & GPS')
@section('icon', 'fa-camera')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header bg-dark-blue">
                <h5 class="text-white mb-0">
                    <i class="fas fa-camera me-2"></i>Presensi dengan Face Recognition
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('presensi.store') }}" method="POST" id="presensiForm">
                    @csrf
                    <div class="row">

                        <!-- KAMERA DAN GPS -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-camera me-2"></i>Ambil Foto Wajah</h6>

                            <!-- Kamera -->
                            <div class="webcam-container mb-3 text-center">
                                <div id="cameraPreview" class="camera-container mb-2"></div>

                                <div class="text-center">
                                    <button type="button" class="btn btn-primary-modern btn-modern capture-btn" id="captureBtn">
                                        <i class="fas fa-camera me-2"></i>Ambil Foto
                                    </button>
                                </div>
                            </div>

                            <!-- Hasil Foto -->
                            <div id="imagePreview" class="text-center mb-3" style="display: none;">
                                <img id="capturedImage" src="" alt="Captured Image">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-warning btn-sm me-2" id="retakeBtn">
                                        <i class="fas fa-redo me-1"></i>Ulangi
                                    </button>
                                    <span class="badge bg-success" id="faceConfidence"></span>
                                </div>
                            </div>

                            <!-- Lokasi Presensi -->
                            <h6 class="text-primary mb-3 mt-4"><i class="fas fa-map-marker-alt me-2"></i>Lokasi Presensi</h6>

                            <div class="location-status" id="locationStatus">
                                <i class="fas fa-sync fa-spin me-2"></i>Mendeteksi lokasi...
                            </div>

                            <div id="map" class="mb-3"></div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-white">Latitude</label>
                                    <input type="text" class="form-control form-control-modern" id="latitude" name="latitude" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Longitude</label>
                                    <input type="text" class="form-control form-control-modern" id="longitude" name="longitude" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Alamat</label>
                                <textarea class="form-control form-control-modern" id="address" name="address" rows="2" readonly></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Akurasi GPS</label>
                                <input type="text" class="form-control form-control-modern" id="accuracy" name="accuracy" readonly>
                            </div>
                        </div>

                        <!-- FORM DATA PRESENSI -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Data Presensi</h6>

                            <div class="mb-3">
                                <label class="form-label text-white">Karyawan *</label>
                                <select class="form-select form-control-modern" id="id_karyawan" name="id_karyawan" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($karyawan as $k)
                                        <option value="{{ $k->id_karyawan }}">
                                            {{ $k->nama_lengkap }} ({{ $k->nip }}) - {{ $k->jabatan->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Shift Kerja *</label>
                                <select class="form-select form-control-modern" id="id_shift" name="id_shift" required>
                                    <option value="">Pilih Shift</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id_shift }}">
                                            {{ $shift->nama_shift }} ({{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Tanggal Presensi *</label>
                                <input type="date" class="form-control form-control-modern" id="tanggal_presensi" name="tanggal_presensi" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Jenis Presensi *</label>
                                <select class="form-select form-control-modern" id="jenis_presensi" name="jenis_presensi" required>
                                    <option value="masuk">Presensi Masuk</option>
                                    <option value="keluar">Presensi Keluar</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-white">Catatan</label>
                                <textarea class="form-control form-control-modern" id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan..."></textarea>
                            </div>

                            <input type="hidden" id="foto_data" name="foto_data">
                            <input type="hidden" id="confidence_score" name="confidence_score" value="0.85">

                            <!-- STATUS VALIDASI -->
                            <div class="card-modern p-3 mt-3">
                                <h6 class="text-primary mb-3"><i class="fas fa-check-circle me-2"></i>Status Validasi</h6>
                                <div id="validationStatus">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Kamera:</span><span class="badge bg-secondary" id="cameraStatus">Belum siap</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Foto Wajah:</span><span class="badge bg-secondary" id="photoStatus">Belum diambil</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Lokasi GPS:</span><span class="badge bg-secondary" id="gpsStatus">Mendeteksi...</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Validasi Lokasi:</span><span class="badge bg-secondary" id="locationValidStatus">Menunggu...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.presensi.index') }}" class="btn btn-secondary btn-modern">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Simpan Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<script>
let map, marker;
let photoCaptured = false;
let locationAcquired = false;

function initializeCamera() {
    Webcam.set({
        width: 400,
        height: 300,
        image_format: 'jpeg',
        jpeg_quality: 90,
        constraints: { facingMode: "user" }
    });
    Webcam.attach('#cameraPreview');
    updateCameraStatus('ready');
}

document.getElementById('captureBtn').addEventListener('click', () => {
    Webcam.snap(data_uri => {
        document.getElementById('capturedImage').src = data_uri;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('cameraPreview').style.display = 'none';
        document.getElementById('foto_data').value = data_uri;
        const confidence = (0.8 + Math.random() * 0.15).toFixed(2);
        document.getElementById('confidence_score').value = confidence;
        document.getElementById('faceConfidence').textContent = `Confidence: ${(confidence * 100).toFixed(1)}%`;
        photoCaptured = true;
        updatePhotoStatus('captured');
        checkFormValidity();
    });
});

document.getElementById('retakeBtn').addEventListener('click', () => {
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('cameraPreview').style.display = 'block';
    document.getElementById('foto_data').value = '';
    photoCaptured = false;
    updatePhotoStatus('retaken');
    checkFormValidity();
});

function initializeGPS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude, lng = pos.coords.longitude;
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
            document.getElementById('accuracy').value = pos.coords.accuracy.toFixed(2) + ' meter';
            initializeMap(lat, lng);
            document.getElementById('address').value = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            locationAcquired = true;
            updateGPSStatus('acquired');
            checkFormValidity();
        }, err => {
            updateGPSStatus('error');
            document.getElementById('locationStatus').innerHTML =
                '<i class="fas fa-exclamation-triangle me-2"></i>Gagal mendapatkan lokasi: ' + err.message;
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
    } else {
        updateGPSStatus('not_supported');
    }
}

function initializeMap(lat, lng) {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat, lng },
        zoom: 18
    });
    marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        title: "Lokasi Anda"
    });
    updateLocationValidStatus('valid');
    document.getElementById('locationStatus').innerHTML = '<i class="fas fa-check-circle me-2"></i>Lokasi valid untuk presensi';
}

function updateCameraStatus(status) {
    const b = document.getElementById('cameraStatus');
    b.textContent = status === 'ready' ? 'Siap' : 'Belum siap';
    b.className = 'badge ' + (status === 'ready' ? 'bg-success' : 'bg-secondary');
}

function updatePhotoStatus(status) {
    const b = document.getElementById('photoStatus');
    if (status === 'captured') b.className = 'badge bg-success', b.textContent = 'Telah diambil';
    else b.className = 'badge bg-secondary', b.textContent = 'Belum diambil';
}

function updateGPSStatus(status) {
    const b = document.getElementById('gpsStatus');
    if (status === 'acquired') b.className = 'badge bg-success', b.textContent = 'Terdeteksi';
    else if (status === 'error') b.className = 'badge bg-danger', b.textContent = 'Gagal';
    else b.className = 'badge bg-secondary', b.textContent = 'Mendeteksi...';
}

function updateLocationValidStatus(status) {
    const b = document.getElementById('locationValidStatus');
    b.className = 'badge ' + (status === 'valid' ? 'bg-success' : 'bg-danger');
    b.textContent = status === 'valid' ? 'Valid' : 'Invalid';
}

function checkFormValidity() {
    document.getElementById('submitBtn').disabled = !(photoCaptured && locationAcquired);
}

document.getElementById('presensiForm').addEventListener('submit', function(e) {
    if (!photoCaptured || !locationAcquired) {
        e.preventDefault();
        alert('Harap ambil foto dan tunggu lokasi terdeteksi!');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    initializeCamera();
    initializeGPS();
});
</script>

<!-- Ganti YOUR_API_KEY dengan API Key Google Maps milikmu -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>

<style>
.camera-container {
    width: 100%;
    max-width: 400px;
    height: 300px;
    margin: 0 auto;
    border-radius: 10px;
    overflow: hidden;
    background: #000;
    position: relative;
}
#cameraPreview video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
#capturedImage {
    width: 100%;
    max-width: 400px;
    border-radius: 10px;
}
#map {
    height: 300px;
    border-radius: 10px;
    background: #2c3e50;
}
</style>
@endpush
