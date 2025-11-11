@extends('layouts.app')

@section('title', 'Monitoring Presensi')
@section('icon', 'fa-desktop')

@section('content')
<div class="row g-2 mb-2">
    <!-- Filter Card -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Presensi
                    </h6>
                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-down me-1"></i>Toggle Filter
                    </button>
                </div>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body p-3">
                    <form action="{{ route('admin.presensi.monitoring') }}" method="GET" id="filterForm">
                        <div class="row g-2">
                            <!-- Tanggal -->
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Tanggal Dari</label>
                                <input type="date" name="tanggal_dari" class="form-control form-control-sm bg-dark text-white border-secondary" 
                                       value="{{ request('tanggal_dari') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Tanggal Sampai</label>
                                <input type="date" name="tanggal_sampai" class="form-control form-control-sm bg-dark text-white border-secondary" 
                                       value="{{ request('tanggal_sampai') }}">
                            </div>
                            
                            <!-- Search Nama/NIP -->
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Cari Nama/NIP</label>
                                <input type="text" name="search" class="form-control form-control-sm bg-dark text-white border-secondary" 
                                       placeholder="Nama atau NIP" value="{{ request('search') }}">
                            </div>

                            <!-- Karyawan -->
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Karyawan</label>
                                <select name="karyawan_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua Karyawan</option>
                                    @foreach($karyawan as $k)
                                        <option value="{{ $k->id_karyawan }}" {{ request('karyawan_id') == $k->id_karyawan ? 'selected' : '' }}>
                                            {{ $k->nama_lengkap }} ({{ $k->nip }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Fakultas -->
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Fakultas</label>
                                <select name="fakultas_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua Fakultas</option>
                                    @foreach($fakultas as $f)
                                        <option value="{{ $f->id_fakultas }}" {{ request('fakultas_id') == $f->id_fakultas ? 'selected' : '' }}>
                                            {{ $f->nama_fakultas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Departemen -->
                            <div class="col-md-3">
                                <label class="form-label text-white small mb-1">Departemen</label>
                                <select name="departemen_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua Departemen</option>
                                    @foreach($departemen as $d)
                                        <option value="{{ $d->id_departemen }}" {{ request('departemen_id') == $d->id_departemen ? 'selected' : '' }}>
                                            {{ $d->nama_departemen }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Shift -->
                            <div class="col-md-2">
                                <label class="form-label text-white small mb-1">Shift</label>
                                <select name="shift_id" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua Shift</option>
                                    @foreach($shift as $s)
                                        <option value="{{ $s->id_shift }}" {{ request('shift_id') == $s->id_shift ? 'selected' : '' }}>
                                            {{ $s->nama_shift }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Kehadiran -->
                            <div class="col-md-2">
                                <label class="form-label text-white small mb-1">Status Kehadiran</label>
                                <select name="status_kehadiran" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua Status</option>
                                    <option value="hadir" {{ request('status_kehadiran') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="terlambat" {{ request('status_kehadiran') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                    <option value="izin" {{ request('status_kehadiran') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ request('status_kehadiran') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="cuti" {{ request('status_kehadiran') == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                    <option value="alpha" {{ request('status_kehadiran') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                            </div>

                            <!-- Status Verifikasi -->
                            <div class="col-md-2">
                                <label class="form-label text-white small mb-1">Status Verifikasi</label>
                                <select name="status_verifikasi" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">Semua</option>
                                    <option value="pending" {{ request('status_verifikasi') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="verified" {{ request('status_verifikasi') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ request('status_verifikasi') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('admin.presensi.monitoring') }}" class="btn btn-secondary btn-sm w-100">
                                    <i class="fas fa-redo me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Data Presensi ({{ $presensi->total() }} records)
                    </h6>
                    <div>
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="fas fa-file-excel me-1"></i>Export Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="exportPDF()">
                            <i class="fas fa-file-pdf me-1"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-white">#</th>
                                <th class="text-white">Tanggal</th>
                                <th class="text-white">Karyawan</th>
                                <th class="text-white">Departemen</th>
                                <th class="text-white">Shift</th>
                                <th class="text-center text-white">Jam Masuk</th>
                                <th class="text-center text-white">Jam Keluar</th>
                                <th class="text-center text-white">Status</th>
                                <th class="text-center text-white">Foto</th>
                                <th class="text-center text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presensi as $item)
                                <tr>
                                    <td class="text-white-50">{{ $loop->iteration }}</td>
                                    <td class="text-white">{{ \Carbon\Carbon::parse($item->tanggal_presensi)->format('d M Y') }}</td>
                                    <td class="text-white">
                                        <strong>{{ $item->karyawan->nama_lengkap }}</strong>
                                        <br><small class="text-muted">{{ $item->karyawan->nip }}</small>
                                    </td>
                                    <td class="text-white-50">{{ $item->karyawan->departemen->nama_departemen ?? '-' }}</td>
                                    <td class="text-white-50">{{ $item->shift->nama_shift ?? '-' }}</td>
                                    <td class="text-center text-white-50">
                                        {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                        @if($item->keterlambatan_menit > 0)
                                            <br><small class="badge bg-warning text-dark">+{{ $item->keterlambatan_menit }}m</small>
                                        @endif
                                    </td>
                                    <td class="text-center text-white-50">
                                        {{ $item->jam_keluar ? \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if($item->status_kehadiran == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($item->status_kehadiran == 'terlambat')
                                            <span class="badge bg-warning text-dark">Terlambat</span>
                                        @elseif($item->status_kehadiran == 'izin')
                                            <span class="badge bg-info">Izin</span>
                                        @elseif($item->status_kehadiran == 'sakit')
                                            <span class="badge bg-primary">Sakit</span>
                                        @elseif($item->status_kehadiran == 'cuti')
                                            <span class="badge bg-secondary">Cuti</span>
                                        @else
                                            <span class="badge bg-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->foto_masuk || $item->foto_keluar)
                                            <button class="btn btn-info btn-sm" onclick="showPhotos({{ json_encode($item) }})">
                                                <i class="fas fa-images"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.presensi.monitoring.show', $item->id_presensi) }}" 
                                           class="btn btn-primary btn-sm" data-bs-toggle="tooltip" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-white-50 py-4">
                                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                                        Tidak ada data presensi
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($presensi->hasPages())
                    <div class="d-flex justify-content-between align-items-center p-3 border-top border-secondary">
                        <div class="text-muted small">
                            Menampilkan {{ $presensi->firstItem() }} - {{ $presensi->lastItem() }} dari {{ $presensi->total() }} data
                        </div>
                        {{ $presensi->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Photo Viewer -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i>Foto Presensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6" id="fotoMasukContainer">
                        <h6 class="text-center mb-2">Foto Masuk</h6>
                        <img id="fotoMasuk" class="img-fluid rounded" alt="Foto Masuk">
                        <p class="text-center mt-2 small" id="waktuMasuk"></p>
                    </div>
                    <div class="col-md-6" id="fotoKeluarContainer">
                        <h6 class="text-center mb-2">Foto Keluar</h6>
                        <img id="fotoKeluar" class="img-fluid rounded" alt="Foto Keluar">
                        <p class="text-center mt-2 small" id="waktuKeluar"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.card').hide().fadeIn(400);
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function showPhotos(item) {
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    
    // Set foto masuk
    if (item.foto_masuk) {
        document.getElementById('fotoMasuk').src = '/storage/' + item.foto_masuk;
        document.getElementById('waktuMasuk').textContent = item.jam_masuk || '-';
        document.getElementById('fotoMasukContainer').style.display = 'block';
    } else {
        document.getElementById('fotoMasukContainer').style.display = 'none';
    }
    
    // Set foto keluar
    if (item.foto_keluar) {
        document.getElementById('fotoKeluar').src = '/storage/' + item.foto_keluar;
        document.getElementById('waktuKeluar').textContent = item.jam_keluar || '-';
        document.getElementById('fotoKeluarContainer').style.display = 'block';
    } else {
        document.getElementById('fotoKeluarContainer').style.display = 'none';
    }
    
    modal.show();
}

function exportExcel() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    window.location.href = '{{ route("admin.presensi.monitoring.export-excel") }}?' + params;
}

function exportPDF() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    window.open('{{ route("admin.presensi.monitoring.export-pdf") }}?' + params, '_blank');
}
</script>
@endpush

@push('css')
<style>
    .form-control, .form-select {
        background-color: #2d3236 !important;
        border-color: #4a5056 !important;
        color: #fff !important;
    }
    
    .form-control:focus, .form-select:focus {
        background-color: #343a40 !important;
        border-color: #007bff !important;
        color: #fff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    .form-select option {
        background-color: #2d3236 !important;
        color: #fff !important;
    }
    
    .border-secondary {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .modal-content {
        background-color: #2d3236 !important;
    }
    
    .modal-header {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endpush