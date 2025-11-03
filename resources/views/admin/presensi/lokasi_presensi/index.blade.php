@extends('layouts.app')

@section('title', 'Edit Lokasi Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Lokasi Presensi
                    </h4>
                </div>
                
                <form action="{{ route('admin.lokasi-presensi.update', $lokasi->id_lokasi) }}" method="POST" id="lokasiForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Nama Lokasi -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_lokasi">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lokasi') is-invalid @enderror" 
                                           id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}" required>
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
                                            id="id_fakultas" name="id_fakultas">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $fak)
                                            <option value="{{ $fak->id_fakultas }}" {{ old('id_fakultas', $lokasi->id_fakultas) == $fak->id_fakultas ? 'selected' : '' }}>
                                                {{ $fak->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_fakultas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_aktif">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status_aktif') is-invalid @enderror" 
                                            id="status_aktif" name="status_aktif" required>
                                        <option value="1" {{ old('status_aktif', $lokasi->status_aktif) == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status_aktif', $lokasi->status_aktif) == '0' ? 'selected' : '' }}>Nonaktif</option>
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
                                <h5 class="text-warning"><i class="fas fa-map-marked-alt"></i> Koordinat Lokasi</h5>
                            </div>

                            <div class="col-md-12">
                                <div id="map" style="height: 400px; border-radius: 8px; margin-bottom: 20px;"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $lokasi->latitude) }}" required>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $lokasi->longitude) }}" required>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="radius_meter">Radius (meter) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('radius_meter') is-invalid @enderror" 
                                           id="radius_meter" name="radius_meter" value="{{ old('radius_meter', $lokasi->radius_meter) }}" 
                                           min="10" max="10000" required>
                                    @error('radius_meter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-warning"><i class="fas fa-clock"></i> Waktu Operasional</h5>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_operasional_mulai">Jam Buka</label>
                                    <input type="time" class="form-control @error('waktu_operasional_mulai') is-invalid @enderror" 
                                           id="waktu_operasional_mulai" name="waktu_operasional_mulai" 
                                           value="{{ old('waktu_operasional_mulai', substr($lokasi->waktu_operasional_mulai ?? '07:00', 0, 5)) }}">
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
                                           value="{{ old('waktu_operasional_selesai', substr($lokasi->waktu_operasional_selesai ?? '17:00', 0, 5)) }}">
                                    @error('waktu_operasional_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                              id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $lokasi->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update
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
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
<script>
let map;
let marker;
let circle;

function initMap() {
    const defaultLat = parseFloat($('#latitude').val());
    const defaultLng = parseFloat($('#longitude').val());
    const defaultRadius = parseInt($('#radius_meter').val());
    
    const centerPosition = { lat: defaultLat, lng: defaultLng };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: centerPosition,
        mapTypeId: 'roadmap'
    });
    
    marker = new google.maps.Marker({
        position: centerPosition,
        map: map,
        draggable: true,
        title: 'Lokasi Presensi'
    });
    
    circle = new google.maps.Circle({
        map: map,
        radius: defaultRadius,
        fillColor: '#FFA500',
        fillOpacity: 0.2,
        strokeColor: '#FFA500',
        strokeOpacity: 0.8,
        strokeWeight: 2
    });
    circle.bindTo('center', marker, 'position');
    
    map.addListener('click', function(event) {
        marker.setPosition(event.latLng);
        $('#latitude').val(event.latLng.lat().toFixed(8));
        $('#longitude').val(event.latLng.lng().toFixed(8));
    });
    
    marker.addListener('dragend', function(event) {
        $('#latitude').val(event.latLng.lat().toFixed(8));
        $('#longitude').val(event.latLng.lng().toFixed(8));
    });
    
    $('#radius_meter').on('input', function() {
        circle.setRadius(parseInt($(this).val()) || 100);
    });
    
    $('#latitude, #longitude').on('change', function() {
        const lat = parseFloat($('#latitude').val());
        const lng = parseFloat($('#longitude').val());
        if (!isNaN(lat) && !isNaN(lng)) {
            const newPosition = { lat: lat, lng: lng };
            marker.setPosition(newPosition);
            map.setCenter(newPosition);
        }
    });
}

$(document).ready(function() {
    initMap();
});
</script>
@endpush