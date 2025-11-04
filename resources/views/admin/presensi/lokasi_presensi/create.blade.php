@extends('layouts.app')

@section('title', 'Tambah Lokasi Presensi')

@push('css')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
          crossorigin=""/>
    <style>
        /* CRITICAL FIX: Map container must have explicit dimensions BEFORE initialization */
        #map {
            height: 450px !important;
            width: 100% !important;
            position: relative !important;
            border-radius: 10px;
            border: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: #f0f0f0;
            overflow: hidden;
        }

        /* Override AdminLTE z-index issues */
        .leaflet-container {
            z-index: 1 !important;
            height: 100% !important;
            width: 100% !important;
            position: relative !important;
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

        /* Ensure tiles load properly */
        .leaflet-tile {
            max-width: none !important;
            max-height: none !important;
        }

        .leaflet-tile-container img {
            max-width: none !important;
        }

        /* Map wrapper untuk isolasi styling */
        .map-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            #map {
                height: 350px !important;
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
                                        <input type="text"
                                            class="form-control @error('nama_lokasi') is-invalid @enderror" 
                                            id="nama_lokasi"
                                            name="nama_lokasi" 
                                            value="{{ old('nama_lokasi') }}"
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
                                            <option value="kantor" {{ old('jenis_lokasi') == 'kantor' ? 'selected' : '' }}>
                                                Kantor</option>
                                            <option value="gedung" {{ old('jenis_lokasi') == 'gedung' ? 'selected' : '' }}>
                                                Gedung</option>
                                            <option value="laboratorium"
                                                {{ old('jenis_lokasi') == 'laboratorium' ? 'selected' : '' }}>Laboratorium
                                            </option>
                                            <option value="lainnya"
                                                {{ old('jenis_lokasi') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                            @foreach ($fakultas as $fak)
                                                <option value="{{ $fak->id_fakultas }}"
                                                    {{ old('id_fakultas') == $fak->id_fakultas ? 'selected' : '' }}>
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
                                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>
                                                Aktif</option>
                                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>
                                                Nonaktif</option>
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

                                <!-- Map Container - CRITICAL: Must be rendered before JS initialization -->
                                <div class="col-md-12">
                                    <div class="map-wrapper">
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
                                            value="{{ old('latitude', '-7.2575') }}"
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
                                        <input type="text" 
                                            class="form-control @error('longitude') is-invalid @enderror"
                                            id="longitude" 
                                            name="longitude" 
                                            value="{{ old('longitude', '112.7521') }}"
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
                                        <input type="number"
                                            class="form-control @error('radius_meter') is-invalid @enderror"
                                            id="radius_meter" 
                                            name="radius_meter" 
                                            value="{{ old('radius_meter', '100') }}"
                                            min="10" 
                                            max="10000" 
                                            placeholder="100" required>
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
                                            value="{{ old('waktu_operasional_mulai', '07:00') }}">
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
                                            id="keterangan" 
                                            name="keterangan"
                                            rows="3" 
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

@push('scripts')
    <!-- Leaflet JS - MUST be loaded after CSS and before initialization -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
            crossorigin=""></script>
    
    <script>
        // CRITICAL: Wait for ALL resources to load including CSS
        window.addEventListener('load', function() {
            // Additional delay to ensure DOM is fully ready
            setTimeout(initMap, 250);
        });

        function initMap() {
            console.log('Initializing map...');
            
            // Get input elements
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const radiusInput = document.getElementById('radius_meter');

            // Parse coordinates with fallback
            const defaultLat = parseFloat(latInput.value) || -7.2575;
            const defaultLng = parseFloat(lngInput.value) || 112.7521;
            const defaultRadius = parseInt(radiusInput.value) || 100;

            console.log('Default coordinates:', defaultLat, defaultLng);

            // Check if map container exists
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found!');
                return;
            }

            // Clear any existing map instance
            mapContainer.innerHTML = '';
            mapContainer._leaflet_id = null;

            try {
                // Initialize map with explicit configuration
                const map = L.map('map', {
                    center: [defaultLat, defaultLng],
                    zoom: 16,
                    zoomControl: true,
                    scrollWheelZoom: true,
                    doubleClickZoom: true,
                    touchZoom: true,
                    dragging: true,
                    attributionControl: true,
                    preferCanvas: false
                });

                console.log('Map initialized successfully');

                // Add OpenStreetMap tile layer
                const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                    minZoom: 5,
                    subdomains: ['a', 'b', 'c'],
                    crossOrigin: true
                });

                tileLayer.addTo(map);
                console.log('Tile layer added');

                // Add draggable marker
                const marker = L.marker([defaultLat, defaultLng], {
                    draggable: true,
                    autoPan: true
                }).addTo(map);

                marker.bindPopup('<strong>Lokasi Presensi</strong><br>Drag marker untuk memindahkan').openPopup();

                // Add radius circle
                const circle = L.circle([defaultLat, defaultLng], {
                    radius: defaultRadius,
                    color: '#007bff',
                    fillColor: '#007bff',
                    fillOpacity: 0.15,
                    weight: 2
                }).addTo(map);

                console.log('Marker and circle added');

                // Event: Marker drag end
                marker.on('dragend', function() {
                    const pos = marker.getLatLng();
                    latInput.value = pos.lat.toFixed(8);
                    lngInput.value = pos.lng.toFixed(8);
                    circle.setLatLng(pos);
                    
                    marker.setPopupContent(
                        `<strong>Lokasi Dipindahkan</strong><br>` +
                        `Lat: ${pos.lat.toFixed(6)}<br>` +
                        `Lng: ${pos.lng.toFixed(6)}`
                    ).openPopup();
                });

                // Event: Map click
                map.on('click', function(e) {
                    const pos = e.latlng;
                    marker.setLatLng(pos);
                    circle.setLatLng(pos);
                    latInput.value = pos.lat.toFixed(8);
                    lngInput.value = pos.lng.toFixed(8);
                    
                    marker.setPopupContent(
                        `<strong>Lokasi Baru</strong><br>` +
                        `Lat: ${pos.lat.toFixed(6)}<br>` +
                        `Lng: ${pos.lng.toFixed(6)}`
                    ).openPopup();
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
                    }
                }

                latInput.addEventListener('change', updateMarkerFromInput);
                lngInput.addEventListener('change', updateMarkerFromInput);

                // Update radius on input change
                radiusInput.addEventListener('input', function() {
                    const radius = parseInt(this.value) || 100;
                    circle.setRadius(radius);
                });

                // Force map to recalculate size (CRITICAL)
                setTimeout(function() {
                    map.invalidateSize();
                    console.log('Map size invalidated');
                }, 100);

                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        map.invalidateSize();
                    }, 250);
                });

                console.log('Map initialization complete!');

            } catch (error) {
                console.error('Error initializing map:', error);
                alert('Gagal menginisialisasi peta. Silakan refresh halaman.');
            }
        }
    </script>
@endpush