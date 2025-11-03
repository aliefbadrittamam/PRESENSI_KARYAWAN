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
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
<script>
function initMap() {
    const latitude = {{ $lokasi->latitude }};
    const longitude = {{ $lokasi->longitude }};
    const radius = {{ $lokasi->radius_meter }};
    
    const position = { lat: latitude, lng: longitude };
    
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: position,
        mapTypeId: 'roadmap'
    });
    
    // Marker
    new google.maps.Marker({
        position: position,
        map: map,
        title: '{{ $lokasi->nama_lokasi }}'
    });
    
    // Circle to show radius
    new google.maps.Circle({
        map: map,
        center: position,
        radius: radius,
        fillColor: '#4CAF50',
        fillOpacity: 0.2,
        strokeColor: '#4CAF50',
        strokeOpacity: 0.8,
        strokeWeight: 2
    });
}

$(document).ready(function() {
    initMap();
});
</script>
@endpush