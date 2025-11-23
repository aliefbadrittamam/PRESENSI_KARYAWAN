@extends('layouts.app')

@section('title', 'Detail Presensi')
@section('icon', 'fa-info-circle')

@section('content')
<div class="row">
    <!-- Informasi Karyawan -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user mr-2"></i>Informasi Karyawan
                </h3>
            </div>
            <div class="card-body box-profile">
                <div class="text-center">
                    @if($presensi->karyawan->foto)
                        <img class="profile-user-img img-fluid img-circle" 
                             src="{{ asset('public/' . $presensi->karyawan->foto) }}" 
                             alt="Foto Karyawan"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img class="profile-user-img img-fluid img-circle" 
                             src="https://ui-avatars.com/api/?name={{ urlencode($presensi->karyawan->nama_lengkap) }}&size=150&background=007bff&color=fff" 
                             alt="Avatar">
                    @endif
                </div>

                <h3 class="profile-username text-center mt-3">{{ $presensi->karyawan->nama_lengkap }}</h3>
                <p class="text-muted text-center">{{ $presensi->karyawan->nip }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b><i class="fas fa-building mr-2"></i>Fakultas</b>
                        <span class="float-right">{{ $presensi->karyawan->fakultas->nama_fakultas ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-briefcase mr-2"></i>Departemen</b>
                        <span class="float-right">{{ $presensi->karyawan->departemen->nama_departemen ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-user-tie mr-2"></i>Jabatan</b>
                        <span class="float-right">{{ $presensi->karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-clock mr-2"></i>Shift</b>
                        <span class="float-right">{{ $presensi->shift->nama_shift ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-envelope mr-2"></i>Email</b>
                        <span class="float-right">{{ $presensi->karyawan->email ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-phone mr-2"></i>Telepon</b>
                        <span class="float-right">{{ $presensi->karyawan->no_telepon ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Detail Presensi -->
    <div class="col-md-8">
        <!-- Informasi Presensi -->
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-check mr-2"></i>Detail Presensi
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.presensi.monitoring') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="150"><i class="fas fa-calendar mr-2"></i>Tanggal</th>
                                <td>: {{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-sign-in-alt mr-2"></i>Jam Masuk</th>
                                <td>: {{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-sign-out-alt mr-2"></i>Jam Keluar</th>
                                <td>: {{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') : '-' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-clock mr-2"></i>Total Jam Kerja</th>
                                <td>: {{ $presensi->total_jam_kerja ? number_format($presensi->total_jam_kerja, 2) . ' jam' : '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="150"><i class="fas fa-hourglass-half mr-2"></i>Keterlambatan</th>
                                <td>
                                    @if($presensi->keterlambatan_menit > 0)
                                        : <span class="badge badge-warning">{{ $presensi->keterlambatan_menit }} menit</span>
                                    @else
                                        : <span class="badge badge-success">Tidak terlambat</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-info-circle mr-2"></i>Status Kehadiran</th>
                                <td>
                                    @if($presensi->status_kehadiran == 'hadir')
                                        : <span class="badge badge-success">Hadir</span>
                                    @elseif($presensi->status_kehadiran == 'terlambat')
                                        : <span class="badge badge-warning">Terlambat</span>
                                    @elseif($presensi->status_kehadiran == 'izin')
                                        : <span class="badge badge-info">Izin</span>
                                    @elseif($presensi->status_kehadiran == 'sakit')
                                        : <span class="badge badge-primary">Sakit</span>
                                    @elseif($presensi->status_kehadiran == 'cuti')
                                        : <span class="badge badge-secondary">Cuti</span>
                                    @else
                                        : <span class="badge badge-danger">Alpha</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-check-circle mr-2"></i>Status Verifikasi</th>
                                <td>
                                    @if($presensi->status_verifikasi == 'verified')
                                        : <span class="badge badge-success">Verified</span>
                                    @elseif($presensi->status_verifikasi == 'rejected')
                                        : <span class="badge badge-danger">Rejected</span>
                                    @else
                                        : <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @if($presensi->catatan)
                            <tr>
                                <th><i class="fas fa-sticky-note mr-2"></i>Catatan</th>
                                <td>: {{ $presensi->catatan }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Foto Presensi -->
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-images mr-2"></i>Foto Presensi
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Foto Masuk -->
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-sign-in-alt text-success mr-2"></i>Foto Masuk
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if($presensi->foto_masuk)
                                    <img src="{{ asset('public/' . $presensi->foto_masuk) }}" 
                                         class="img-fluid rounded border shadow-sm" 
                                         alt="Foto Masuk"
                                         style="max-height: 300px; cursor: pointer;"
                                         onclick="showImageModal('{{ asset('public/' . $presensi->foto_masuk) }}', 'Foto Masuk')">
                                    <p class="mt-2 mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $presensi->jam_masuk ? \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') : '-' }}
                                        </small>
                                    </p>
                                    @if($presensi->confidence_score_masuk)
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Confidence: {{ number_format($presensi->confidence_score_masuk * 100, 2) }}%
                                        </small>
                                    </p>
                                    @endif
                                @else
                                    <div class="text-muted py-5">
                                        <i class="fas fa-image fa-3x mb-3 d-block"></i>
                                        <p>Tidak ada foto masuk</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Foto Keluar -->
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-sign-out-alt text-danger mr-2"></i>Foto Keluar
                                </h5>
                            </div>
                            <div class="card-body text-center">
                                @if($presensi->foto_keluar)
                                    <img src="{{ asset('public/' . $presensi->foto_keluar) }}" 
                                         class="img-fluid rounded border shadow-sm" 
                                         alt="Foto Keluar"
                                         style="max-height: 300px; cursor: pointer;"
                                         onclick="showImageModal('{{ asset('public/' . $presensi->foto_keluar) }}', 'Foto Keluar')">
                                    <p class="mt-2 mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $presensi->jam_keluar ? \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') : '-' }}
                                        </small>
                                    </p>
                                    @if($presensi->confidence_score_keluar)
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Confidence: {{ number_format($presensi->confidence_score_keluar * 100, 2) }}%
                                        </small>
                                    </p>
                                    @endif
                                @else
                                    <div class="text-muted py-5">
                                        <i class="fas fa-image fa-3x mb-3 d-block"></i>
                                        <p>Belum melakukan presensi keluar</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lokasi Presensi -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marker-alt mr-2"></i>Lokasi Presensi
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Lokasi Masuk -->
                    <div class="col-md-6">
                        <h6 class="text-success"><i class="fas fa-map-pin mr-2"></i>Lokasi Masuk</h6>
                        @if($presensi->latitude_masuk && $presensi->longitude_masuk)
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="100">Latitude</th>
                                    <td>: {{ $presensi->latitude_masuk }}</td>
                                </tr>
                                <tr>
                                    <th>Longitude</th>
                                    <td>: {{ $presensi->longitude_masuk }}</td>
                                </tr>
                                <tr>
                                    <th>Akurasi</th>
                                    <td>: {{ $presensi->accuracy_masuk ? number_format($presensi->accuracy_masuk, 2) . ' meter' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $presensi->alamat_masuk ?? '-' }}</td>
                                </tr>
                            </table>
                            <a href="https://www.google.com/maps?q={{ $presensi->latitude_masuk }},{{ $presensi->longitude_masuk }}" 
                               target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-map-marked-alt mr-1"></i>Lihat di Google Maps
                            </a>
                        @else
                            <p class="text-muted">Data lokasi tidak tersedia</p>
                        @endif
                    </div>

                    <!-- Lokasi Keluar -->
                    <div class="col-md-6">
                        <h6 class="text-danger"><i class="fas fa-map-pin mr-2"></i>Lokasi Keluar</h6>
                        @if($presensi->latitude_keluar && $presensi->longitude_keluar)
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="100">Latitude</th>
                                    <td>: {{ $presensi->latitude_keluar }}</td>
                                </tr>
                                <tr>
                                    <th>Longitude</th>
                                    <td>: {{ $presensi->longitude_keluar }}</td>
                                </tr>
                                <tr>
                                    <th>Akurasi</th>
                                    <td>: {{ $presensi->accuracy_keluar ? number_format($presensi->accuracy_keluar, 2) . ' meter' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: {{ $presensi->alamat_keluar ?? '-' }}</td>
                                </tr>
                            </table>
                            <a href="https://www.google.com/maps?q={{ $presensi->latitude_keluar }},{{ $presensi->longitude_keluar }}" 
                               target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-map-marked-alt mr-1"></i>Lihat di Google Maps
                            </a>
                        @else
                            <p class="text-muted">Belum melakukan presensi keluar</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Zoom Foto -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Foto">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showImageModal(src, title) {
    $('#imageModalLabel').text(title);
    $('#modalImage').attr('src', src);
    $('#imageModal').modal('show');
}

$(document).ready(function() {
    $('.card').hide().fadeIn(400);
});
</script>
@endpush

@push('css')
<style>
.profile-user-img {
    border: 3px solid #adb5bd;
    margin: 0 auto;
    padding: 3px;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.card-body img[style*="cursor: pointer"]:hover {
    opacity: 0.8;
    transform: scale(1.02);
    transition: all 0.3s ease;
}
</style>
@endpush