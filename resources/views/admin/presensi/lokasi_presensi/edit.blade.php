@extends('layouts.app')

@section('title', 'Edit Lokasi Presensi')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>
<style>
    /* CRITICAL FIX: Map container dengan dimensi eksplisit */
    #map {
        height: 450px !important;
        width: 100% !important;
        position: relative !important;
        border-radius: 10px;
        border: 2px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        background-color: #f0f0f0;
        overflow: hidden;
        z-index: 1;
    }

    /* Override AdminLTE z-index issues */
    .leaflet-container {
        z-index: 1 !important;
        height: 100% !important;
        width: 100% !important;
        position: relative !important;
        font-family: inherit !important;
    }

    .leaflet-pane {
        z-index: 400 !important;
    }

    .leaflet-tile-pane {
        z-index: 200 !important;
    }

    .leaflet-overlay-pane {
        z-index: 400 !important;
    }

    .leaflet-shadow-pane {
        z-index: 500 !important;
    }

    .leaflet-marker-pane {
        z-index: 600 !important;
    }

    .leaflet-tooltip-pane {
        z-index: 650 !important;
    }

    .leaflet-popup-pane {
        z-index: 700 !important;
    }

    /* CRITICAL: Ensure tiles load properly */
    .leaflet-tile {
        max-width: none !important;
        max-height: none !important;
        image-rendering: -webkit-optimize-contrast !important;
        image-rendering: crisp-edges !important;
    }

    .leaflet-tile-container {
        position: absolute;
        z-index: 200;
    }

    .leaflet-tile-container img {
        max-width: none !important;
        max-height: none !important;
    }

    /* Fix untuk loading tiles */
    .leaflet-layer {
        position: absolute;
        left: 0;
        top: 0;
    }

    /* Map wrapper untuk isolasi styling */
    .map-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    /* Loading indicator */
    .map-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: rgba(255,255,255,0.9);
        padding: 20px;
        border-radius: 10px;
        display: none;
    }

    .map-loading.active {
        display: block;
    }

    /* Zoom control styling */
    .leaflet-control-zoom {
        border: 2px solid rgba(0,0,0,0.2) !important;
        border-radius: 4px !important;
    }

    .leaflet-control-zoom a {
        width: 30px !important;
        height: 30px !important;
        line-height: 30px !important;
        font-size: 18px !important;
    }

    /* Attribution control */
    .leaflet-control-attribution {
        font-size: 10px !important;
        background: rgba(255,255,255,0.8) !important;
    }

    /* Dark mode compatibility */
    .dark-mode #map {
        border-color: #4b545c;
        background-color: #2f3237;
    }

    .dark-mode .map-wrapper {
        background-color: #343a40;
    }

    .dark-mode .leaflet-control-zoom a,
    .dark-mode .leaflet-control-attribution {
        background-color: #454d55 !important;
        color: #fff !important;
        border-color: #4b545c !important;
    }

    .dark-mode .leaflet-popup-content-wrapper {
        background-color: #454d55 !important;
        color: #fff !important;
    }

    .dark-mode .leaflet-popup-tip {
        background-color: #454d55 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #map {
            height: 350px !important;
        }
    }

    /* Custom marker pulse animation */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-edit"></i> Edit Lokasi Presensi
                        </h4>
                        <a href="{{ route('admin.lokasi-presensi.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.lokasi-presensi.update', $lokasi->id_lokasi) }}" method="POST" id="lokasiForm">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Terdapat kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <div class="row">
                            <!-- Nama Lokasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_lokasi">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama_lokasi') is-invalid @enderror" 
                                           id="nama_lokasi" 
                                           name="nama_lokasi" 
                                           value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}"
                                           placeholder="Contoh: Gedung Rektorat" 
                                           required>
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
                                            id="jenis_lokasi" 
                                            name="jenis_lokasi" 
                                            required>
                                        <option value="">-- Pilih Jenis Lokasi --</option>
                                        <option value="kantor" {{ old('jenis_lokasi', $lokasi->jenis_lokasi) == 'kantor' ? 'selected' : '' }}>Kantor</option>
                                        <option value="gedung" {{ old('jenis_lokasi', $lokasi->jenis_lokasi) == 'gedung' ? 'selected' : '' }}>Gedung</option>
                                        <option value="laboratorium" {{ old('jenis_lokasi', $lokasi->jenis_lokasi) == 'laboratorium' ? 'selected' : '' }}>Laboratorium</option>
                                        <option value="lainnya" {{ old('jenis_lokasi', $lokasi->jenis_lokasi) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                            id="id_fakultas" 
                                            name="id_fakultas">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $fak)
                                        <option value="{{ $fak->id_fakultas }}" 
                                                {{ old('id_fakultas', $lokasi->id_fakultas) == $fak->id_fakultas ? 'selected' : '' }}>
                                            {{ $fak->nama_fakultas }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_fakultas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan jika lokasi untuk semua fakultas</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_aktif">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_aktif') is-invalid @enderror" 
                                            id="status_aktif" 
                                            name="status_aktif" 
                                            required>
                                        <option value="1" {{ old('status_aktif', $lokasi->status_aktif) == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status_aktif', $lokasi->status_aktif) == 0 ? 'selected' : '' }}>Nonaktif</option>
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
                                <h5 class="text-primary">
                                    <i class="fas fa-map-marked-alt"></i> Koordinat Lokasi
                                </h5>
                                <p class="text-muted">Klik pada peta untuk menentukan lokasi atau drag marker</p>
                            </div>

                            <!-- Map Container -->
                            <div class="col-md-12">
                                <div class="map-wrapper">
                                    <div class="map-loading" id="mapLoading">
                                        <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                                        <p class="mt-2 mb-0">Loading map...</p>
                                    </div>
                                    <div id="map"></div>
                                </div>
                            </div>

                            <!-- Latitude -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" 
                                           name="latitude" 
                                           value="{{ old('latitude', $lokasi->latitude) }}"
                                           placeholder="-7.2575" 
                                           required>
                                    @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Longitude -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" 
                                           name="longitude" 
                                           value="{{ old('longitude', $lokasi->longitude) }}"
                                           placeholder="112.7521" 
                                           required>
                                    @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Radius -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="radius_meter">Radius (meter) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control @error('radius_meter') is-invalid @enderror" 
                                           id="radius_meter" 
                                           name="radius_meter" 
                                           value="{{ old('radius_meter', $lokasi->radius_meter) }}"
                                           min="10" 
                                           max="10000" 
                                           placeholder="100"
                                           required>
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
                                <h5 class="text-primary">
                                    <i class="fas fa-clock"></i> Waktu Operasional (Opsional)
                                </h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_operasional_mulai">Jam Buka</label>
                                    <input type="time" 
                                           class="form-control @error('waktu_operasional_mulai') is-invalid @enderror" 
                                           id="waktu_operasional_mulai" 
                                           name="waktu_operasional_mulai"
                                           value="{{ old('waktu_operasional_mulai', $lokasi->waktu_operasional_mulai ? substr($lokasi->waktu_operasional_mulai, 0, 5) : '') }}">
                                    @error('waktu_operasional_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_operasional_selesai">Jam Tutup</label>
                                    <input type="time" 
                                           class="form-control @error('waktu_operasional_selesai') is-invalid @enderror" 
                                           id="waktu_operasional_selesai" 
                                           name="waktu_operasional_selesai"
                                           value="{{ old('waktu_operasional_selesai', $lokasi->waktu_operasional_selesai ? substr($lokasi->waktu_operasional_selesai, 0, 5) : '') }}">
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
                                              id="keterangan" 
                                              name="keterangan" 
                                              rows="3" 
                                              placeholder="Keterangan tambahan tentang lokasi ini">{{ old('keterangan', $lokasi->keterangan) }}</textarea>
                                    @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Lokasi
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

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
// Global variables
let map, marker, circle;

// Wait for complete page load
window.addEventListener('load', function() {
    console.log('Page loaded, initializing map...');
    // Delay untuk memastikan semua resource dimuat
    setTimeout(initMap, 250);
});

function initMap() {
    console.log('=== Starting Map Initialization ===');
    
    // Show loading indicator
    const loadingEl = document.getElementById('mapLoading');
    if (loadingEl) {
        loadingEl.classList.add('active');
    }
    
    // Get input elements
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meter');

    if (!latInput || !lngInput || !radiusInput) {
        console.error('Input elements not found!');
        return;
    }

    // Parse coordinates with validation
    const defaultLat = parseFloat(latInput.value) || {{ $lokasi->latitude }};
    const defaultLng = parseFloat(lngInput.value) || {{ $lokasi->longitude }};
    const defaultRadius = parseInt(radiusInput.value) || {{ $lokasi->radius_meter }};

    console.log('Coordinates:', { lat: defaultLat, lng: defaultLng, radius: defaultRadius });

    // Check if map container exists
    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.error('Map container (#map) not found!');
        return;
    }

    // Clear existing map instance
    mapContainer.innerHTML = '';
    if (mapContainer._leaflet_id) {
        mapContainer._leaflet_id = null;
    }

    try {
        // Initialize map with enhanced configuration
        map = L.map('map', {
            center: [defaultLat, defaultLng],
            zoom: 16,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            touchZoom: true,
            dragging: true,
            boxZoom: true,
            keyboard: true,
            attributionControl: true,
            preferCanvas: false,
            fadeAnimation: true,
            zoomAnimation: true,
            markerZoomAnimation: true
        });

        console.log('✓ Map object created');

        // Add tile layer
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 5,
            tileSize: 256,
            subdomains: ['a', 'b', 'c'],
            crossOrigin: true,
            errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            keepBuffer: 2
        });

        // Event handlers untuk tile loading
        tileLayer.on('loading', function() {
            console.log('Tiles loading...');
        });

        tileLayer.on('load', function() {
            console.log('✓ Tiles loaded successfully');
            if (loadingEl) {
                loadingEl.classList.remove('active');
            }
        });

        tileLayer.on('tileerror', function(error) {
            console.warn('Tile loading error:', error);
        });

        tileLayer.addTo(map);
        console.log('✓ Tile layer added');

        // Custom marker icon
        const customIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add draggable marker
        marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            autoPan: true,
            icon: customIcon
        }).addTo(map);

        const namaLokasi = document.getElementById('nama_lokasi').value || 'Lokasi Presensi';
        marker.bindPopup(`
            <div style="text-align: center;">
                <strong style="font-size: 14px;">${namaLokasi}</strong><br>
                <small style="color: #666;">Drag untuk memindahkan</small>
            </div>
        `).openPopup();

        console.log('✓ Marker added');

        // Add radius circle
        circle = L.circle([defaultLat, defaultLng], {
            radius: defaultRadius,
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.15,
            weight: 2,
            dashArray: '5, 5'
        }).addTo(map);

        console.log('✓ Circle added');

        // Event: Marker drag
        marker.on('drag', function() {
            const pos = marker.getLatLng();
            circle.setLatLng(pos);
        });

        // Event: Marker drag end
        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            latInput.value = pos.lat.toFixed(8);
            lngInput.value = pos.lng.toFixed(8);
            circle.setLatLng(pos);
            
            marker.setPopupContent(`
                <div style="text-align: center;">
                    <strong style="color: #007bff;">📍 Lokasi Dipindahkan</strong><br>
                    <small>Lat: ${pos.lat.toFixed(6)}</small><br>
                    <small>Lng: ${pos.lng.toFixed(6)}</small>
                </div>
            `).openPopup();
        });

        // Event: Map click
        map.on('click', function(e) {
            const pos = e.latlng;
            marker.setLatLng(pos);
            circle.setLatLng(pos);
            latInput.value = pos.lat.toFixed(8);
            lngInput.value = pos.lng.toFixed(8);
            
            marker.setPopupContent(`
                <div style="text-align: center;">
                    <strong>📍 Lokasi Baru</strong><br>
                    <small>Lat: ${pos.lat.toFixed(6)}</small><br>
                    <small>Lng: ${pos.lng.toFixed(6)}</small>
                </div>
            `).openPopup();
        });

        // Update marker from manual input
        function updateMarkerFromInput() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng) && 
                lat >= -90 && lat <= 90 && 
                lng >= -180 && lng <= 180) {
                const pos = L.latLng(lat, lng);
                marker.setLatLng(pos);
                circle.setLatLng(pos);
                map.setView(pos, map.getZoom());
                
                marker.setPopupContent(`
                    <div style="text-align: center;">
                        <strong>✏️ Koordinat Manual</strong><br>
                        <small>Lat: ${lat.toFixed(6)}</small><br>
                        <small>Lng: ${lng.toFixed(6)}</small>
                    </div>
                `).openPopup();
            } else {
                alert('Koordinat tidak valid!\nLatitude: -90 hingga 90\nLongitude: -180 hingga 180');
            }
        }

        latInput.addEventListener('change', updateMarkerFromInput);
        latInput.addEventListener('blur', updateMarkerFromInput);
        lngInput.addEventListener('change', updateMarkerFromInput);
        lngInput.addEventListener('blur', updateMarkerFromInput);

        // Update radius on input change
        radiusInput.addEventListener('input', function() {
            const radius = parseInt(this.value) || 100;
            if (radius >= 10 && radius <= 10000) {
                circle.setRadius(radius);
            }
        });

        radiusInput.addEventListener('change', function() {
            const radius = parseInt(this.value) || 100;
            if (radius >= 10 && radius <= 10000) {
                circle.setRadius(radius);
            } else {
                alert('Radius harus antara 10 dan 10000 meter!');
                this.value = defaultRadius;
                circle.setRadius(defaultRadius);
            }
        });

        // Update marker popup when name changes
        document.getElementById('nama_lokasi').addEventListener('input', function() {
            const name = this.value || 'Lokasi Presensi';
            marker.setPopupContent(`
                <div style="text-align: center;">
                    <strong style="font-size: 14px;">${name}</strong><br>
                    <small style="color: #666;">Drag untuk memindahkan</small>
                </div>
            `);
        });

        // Force map to recalculate size
        setTimeout(function() {
            map.invalidateSize();
            console.log('✓ Map size invalidated');
            
            // Hide loading
            if (loadingEl) {
                loadingEl.classList.remove('active');
            }
        }, 100);

        // Additional invalidation after tiles load
        setTimeout(function() {
            map.invalidateSize();
            map.setView([defaultLat, defaultLng], 16);
            console.log('✓ Map fully initialized');
        }, 500);

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (map) {
                    map.invalidateSize();
                    console.log('Map resized');
                }
            }, 250);
        });

        // Handle tab visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && map) {
                setTimeout(function() {
                    map.invalidateSize();
                }, 100);
            }
        });

        console.log('=== Map initialization complete! ===');

    } catch (error) {
        console.error('❌ Error initializing map:', error);
        if (loadingEl) {
            loadingEl.innerHTML = `
                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                <p class="mt-2 mb-0 text-danger">Gagal memuat peta</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Muat Ulang
                </button>
            `;
        }
        alert('Gagal menginisialisasi peta. Silakan refresh halaman.');
    }
}

// Form validation
document.getElementById('lokasiForm').addEventListener('submit', function(e) {
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(lngInput.value);
    const radius = parseInt(radiusInput.value);
    const namaLokasi = document.getElementById('nama_lokasi').value.trim();

    // Validate nama lokasi
    if (!namaLokasi) {
        e.preventDefault();
        alert('Nama lokasi harus diisi!');
        document.getElementById('nama_lokasi').focus();
        return false;
    }

    // Validate latitude
    if (isNaN(lat) || lat < -90 || lat > 90) {
        e.preventDefault();
        alert('Latitude tidak valid!\nHarus antara -90 dan 90');
        latInput.focus();
        return false;
    }

    // Validate longitude
    if (isNaN(lng) || lng < -180 || lng > 180) {
        e.preventDefault();
        alert('Longitude tidak valid!\nHarus antara -180 dan 180');
        lngInput.focus();
        return false;
    }

    // Validate radius
    if (isNaN(radius) || radius < 10 || radius > 10000) {
        e.preventDefault();
        alert('Radius tidak valid!\nHarus antara 10 dan 10000 meter');
        radiusInput.focus();
        return false;
    }

    // Show loading on submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    submitBtn.disabled = true;

    // Re-enable button after timeout (in case of error)
    setTimeout(function() {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 10000);

    return true;
});

// Input validation on keyup
latInput.addEventListener('keyup', function() {
    const val = parseFloat(this.value);
    if (isNaN(val)) return;
    
    if (val < -90 || val > 90) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

lngInput.addEventListener('keyup', function() {
    const val = parseFloat(this.value);
    if (isNaN(val)) return;
    
    if (val < -180 || val > 180) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});

radiusInput.addEventListener('keyup', function() {
    const val = parseInt(this.value);
    if (isNaN(val)) return;
    
    if (val < 10 || val > 10000) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
    }
});
</script>
@endpush