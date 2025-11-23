@extends('layouts.app')

@section('title', 'Detail Lokasi Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle"></i> Detail Lokasi Presensi
                    </h4>
                </div>
                
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama Lokasi</th>
                            <td>{{ $lokasi->nama_lokasi }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Lokasi</th>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($lokasi->jenis_lokasi) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Fakultas</th>
                            <td>{{ $lokasi->fakultas->nama_fakultas ?? 'Semua Fakultas' }}</td>
                        </tr>
                        <tr>
                            <th>Latitude</th>
                            <td>{{ $lokasi->latitude }}</td>
                        </tr>
                        <tr>
                            <th>Longitude</th>
                            <td>{{ $lokasi->longitude }}</td>
                        </tr>
                        <tr>
                            <th>Radius</th>
                            <td>
                                <span class="badge badge-secondary">{{ $lokasi->radius_meter }} meter</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Waktu Operasional</th>
                            <td>
                                @if($lokasi->waktu_operasional_mulai && $lokasi->waktu_operasional_selesai)
                                    {{ substr($lokasi->waktu_operasional_mulai, 0, 5) }} - {{ substr($lokasi->waktu_operasional_selesai, 0, 5) }}
                                @else
                                    <span class="text-muted">Tidak ditentukan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($lokasi->status_aktif)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $lokasi->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $lokasi->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Update</th>
                            <td>{{ $lokasi->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('admin.lokasi-presensi.edit', $lokasi->id_lokasi) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.lokasi-presensi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="https://www.google.com/maps?q={{ $lokasi->latitude }},{{ $lokasi->longitude }}" 
                           target="_blank" class="btn btn-success">
                            <i class="fas fa-map-marker-alt"></i> Buka di Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Map Preview -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-map"></i> Peta Lokasi</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Radius</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Radius saat ini:</strong> 
                        <span class="badge badge-secondary">{{ $lokasi->radius_meter }} meter</span>
                    </p>
                    <p class="text-muted small mb-0">
                        Karyawan dapat melakukan presensi dalam radius {{ $lokasi->radius_meter }} meter 
                        dari titik koordinat yang ditentukan.
                    </p>
                    <hr>
                    <p class="mb-1"><small><i class="fas fa-circle text-success"></i> Area Valid (dalam radius)</small></p>
                    <p class="mb-0"><small><i class="fas fa-circle text-danger"></i> Area Tidak Valid (di luar radius)</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    const latitude = {{ $lokasi->latitude }};
    const longitude = {{ $lokasi->longitude }};
    const radius = {{ $lokasi->radius_meter }};
    const namaLokasi = '{{ $lokasi->nama_lokasi }}';
    
    // Initialize map
    const map = L.map('map').setView([latitude, longitude], 15);
    
    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add marker
    const marker = L.marker([latitude, longitude]).addTo(map);
    marker.bindPopup(`<b>${namaLokasi}</b><br>Lat: ${latitude}<br>Lng: ${longitude}`).openPopup();
    
    // Add circle to show radius
    L.circle([latitude, longitude], {
        color: '#4CAF50',
        fillColor: '#4CAF50',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);
    
    // Fit map to show the entire circle
    const bounds = L.circle([latitude, longitude], radius).getBounds();
    map.fitBounds(bounds);
});
</script>
@endpush