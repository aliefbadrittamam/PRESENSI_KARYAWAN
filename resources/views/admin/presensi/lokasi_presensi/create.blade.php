@extends('layouts.app')

@section('title', 'Tambah Lokasi Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus"></i> Tambah Lokasi Presensi
                    </h4>
                </div>
                
                <form action="{{ route('admin.lokasi-presensi.store') }}" method="POST" id="lokasiForm">
                    @csrf
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Nama Lokasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_lokasi">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lokasi') is-invalid @enderror" 
                                           id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi') }}" 
                                           placeholder="Contoh: Kantor Pusat" required>
                                    @error('nama_lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Jenis Lokasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_lokasi">Jenis Lokasi <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_lokasi') is-invalid @enderror" 
                                            id="jenis_lokasi" name="jenis_lokasi" required>
                                        <option value="">Pilih Jenis</option>
                                        <option value="kantor" {{ old('jenis_lokasi') == 'kantor' ? 'selected' : '' }}>Kantor</option>
                                        <option value="gedung" {{ old('jenis_lokasi') == 'gedung' ? 'selected' : '' }}>Gedung</option>
                                        <option value="laboratorium" {{ old('jenis_lokasi') == 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                        <option value="lainnya" {{ old('jenis_lokasi') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('jenis_lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fakultas -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_fakultas">Fakultas</label>
                                    <select class="form-control @error('id_fakultas') is-invalid @enderror" 
                                            id="id_fakultas" name="id_fakultas">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $fak)
                                            <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas') == $fak->id_fakultas ? 'selected' : '' }}>
                                                {{ $fak->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika lokasi untuk semua fakultas</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_aktif">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_aktif') is-invalid @enderror" 
                                            id="status_aktif" name="status_aktif" required>
                                        <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    @error('status_aktif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Map Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-map-marked-alt"></i> Koordinat Lokasi</h5>
                                <p class="text-muted">Klik pada peta untuk menentukan lokasi atau drag marker</p>
                            </div>

                            <!-- Map -->
                            <div class="col-md-12">
                                <div id="map" style="height: 400px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #ddd;"></div>
                            </div>

                            <!-- Latitude -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', '-7.2575') }}" 
                                           placeholder="-7.2575" required>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Longitude -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', '112.7521') }}" 
                                           placeholder="112.7521" required>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Radius -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="radius_meter">Radius (meter) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('radius_meter') is-invalid @enderror" 
                                           id="radius_meter" name="radius_meter" value="{{ old('radius_meter', '100') }}" 
                                           min="10" max="10000" placeholder="100" required>
                                    @error('radius_meter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Jarak maksimal untuk presensi (10-10000 meter)</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Waktu Operasional -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-clock"></i> Waktu Operasional (Opsional)</h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_operasional_mulai">Jam Buka</label>
                                    <input type="time" class="form-control @error('waktu_operasional_mulai') is-invalid @enderror" 
                                           id="waktu_operasional_mulai" name="waktu_operasional_mulai" 
                                           value="{{ old('waktu_operasional_mulai', '07:00') }}">
                                    @error('waktu_operasional_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_operasional_selesai">Jam Tutup</label>
                                    <input type="time" class="form-control @error('waktu_operasional_selesai') is-invalid @enderror" 
                                           id="waktu_operasional_selesai" name="waktu_operasional_selesai" 
                                           value="{{ old('waktu_operasional_selesai', '17:00') }}">
                                    @error('waktu_operasional_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" name="keterangan" rows="3" 
                                              placeholder="Keterangan tambahan tentang lokasi ini">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('admin.lokasi-presensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker, circle;

function initMap() {
    const defaultLat = parseFloat($('#latitude').val()) || -7.2575;
    const defaultLng = parseFloat($('#longitude').val()) || 112.7521;
    const defaultRadius = parseInt($('#radius_meter').val()) || 100;
    
    // Initialize map
    map = L.map('map').setView([defaultLat, defaultLng], 15);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add marker
    marker = L.marker([defaultLat, defaultLng], {
        draggable: true,
        title: 'Drag untuk memindahkan lokasi'
    }).addTo(map);
    
    marker.bindPopup('<b>Lokasi Presensi</b><br>Drag marker untuk mengubah posisi').openPopup();
    
    // Add circle for radius
    circle = L.circle([defaultLat, defaultLng], {
        radius: defaultRadius,
        color: '#4285F4',
        fillColor: '#4285F4',
        fillOpacity: 0.2,
        weight: 2
    }).addTo(map);
    
    // Update coordinates when marker is dragged
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        $('#latitude').val(pos.lat.toFixed(8));
        $('#longitude').val(pos.lng.toFixed(8));
        circle.setLatLng(pos);
    });
    
    // Click on map to place marker
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        circle.setLatLng(e.latlng);
        $('#latitude').val(e.latlng.lat.toFixed(8));
        $('#longitude').val(e.latlng.lng.toFixed(8));
        marker.bindPopup('<b>Lokasi Presensi</b><br>Lat: ' + e.latlng.lat.toFixed(6) + '<br>Lng: ' + e.latlng.lng.toFixed(6)).openPopup();
    });
    
    // Update marker position when coordinates are manually changed
    $('#latitude, #longitude').on('change', function() {
        const lat = parseFloat($('#latitude').val());
        const lng = parseFloat($('#longitude').val());
        
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            const newPos = L.latLng(lat, lng);
            marker.setLatLng(newPos);
            circle.setLatLng(newPos);
            map.setView(newPos, map.getZoom());
        }
    });
    
    // Update circle radius
    $('#radius_meter').on('input', function() {
        const newRadius = parseInt($(this).val()) || 100;
        circle.setRadius(newRadius);
    });
}

$(document).ready(function() {
    initMap();
    
    // Form validation
    $('#lokasiForm').on('submit', function(e) {
        const lat = parseFloat($('#latitude').val());
        const lng = parseFloat($('#longitude').val());
        const radius = parseInt($('#radius_meter').val());
        
        if (isNaN(lat) || lat < -90 || lat > 90) {
            e.preventDefault();
            alert('Latitude harus antara -90 dan 90');
            $('#latitude').focus();
            return false;
        }
        
        if (isNaN(lng) || lng < -180 || lng > 180) {
            e.preventDefault();
            alert('Longitude harus antara -180 dan 180');
            $('#longitude').focus();
            return false;
        }
        
        if (isNaN(radius) || radius < 10 || radius > 10000) {
            e.preventDefault();
            alert('Radius harus antara 10 dan 10000 meter');
            $('#radius_meter').focus();
            return false;
        }
    });
});
</script>
@endpush
