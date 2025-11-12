@extends('layouts.app')

@section('title', 'Detail Fakultas')
@section('icon', 'fa-university')

@section('content')
<!-- Breadcrumb Enhancement -->
<div class="row mb-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2">
                <li class="breadcrumb-item"><a href="{{ route('admin.fakultas.index') }}"><i class="fas fa-university me-1"></i>Fakultas</a></li>
                <li class="breadcrumb-item active">{{ $fakultas->kode_fakultas }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1"><i class="fas fa-university text-primary me-2"></i>Detail Fakultas</h3>
                <p class="text-muted mb-0">Informasi lengkap fakultas {{ $fakultas->nama_fakultas }}</p>
            </div>
            <div>
                <a href="{{ route('admin.fakultas.edit', $fakultas->id_fakultas) }}" 
                   class="btn btn-warning btn-modern me-2">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                @if($fakultas->departemen->count() == 0)
                <form action="{{ route('admin.fakultas.destroy', $fakultas->id_fakultas) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-danger btn-modern" 
                            onclick="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sidebar Profile -->
    <div class="col-lg-4">
        <!-- Faculty Profile Card -->
        <div class="card card-modern shadow-sm text-center mb-3 bg-dark">
            <div class="card-body p-4">
                <div class="rounded-circle bg-primary bg-gradient d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 100px; height: 100px;">
                    <i class="fas fa-university fa-3x text-white"></i>
                </div>
                <h4 class="fw-bold mb-1 text-white">{{ $fakultas->nama_fakultas }}</h4>
                <p class="text-muted mb-3">
                    <span class="badge bg-primary badge-modern fs-6">{{ $fakultas->kode_fakultas }}</span>
                </p>
                @if($fakultas->status_aktif)
                    <span class="badge bg-success badge-modern px-4 py-2">
                        <i class="fas fa-check-circle me-1"></i>Aktif
                    </span>
                @else
                    <span class="badge bg-danger badge-modern px-4 py-2">
                        <i class="fas fa-times-circle me-1"></i>Non-Aktif
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Quick Statistics -->
        <div class="card card-modern shadow-sm mb-3 bg-dark">
            <div class="card-header bg-primary text-white border-0">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Ringkas</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center p-3 bg-secondary rounded mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-building text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Departemen</small>
                            <strong class="fs-5 text-white">{{ $fakultas->departemen->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-secondary rounded mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Karyawan</small>
                            <strong class="fs-5 text-white">{{ $fakultas->karyawan->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-secondary rounded mb-2">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-user-check text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Karyawan Aktif</small>
                            <strong class="fs-5 text-white">{{ $fakultas->karyawan->where('status_aktif', true)->count() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 bg-secondary rounded">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-3" 
                             style="width: 40px; height: 40px;">
                            <i class="fas fa-calendar text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Dibuat Pada</small>
                            <strong class="text-white">{{ $fakultas->created_at->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dekan Info -->
        @if($fakultas->dekan)
        <div class="card card-modern shadow-sm bg-dark">
            <div class="card-header bg-info text-white border-0">
                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Pimpinan Fakultas</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="fas fa-user fa-3x text-info"></i>
                </div>
                <h5 class="fw-bold mb-1 text-white">{{ $fakultas->dekan }}</h5>
                <small class="text-muted">Dekan {{ $fakultas->nama_fakultas }}</small>
            </div>
        </div>
        @else
        <div class="card card-modern shadow-sm border-warning bg-dark">
            <div class="card-body text-center p-4">
                <i class="fas fa-user-tie fa-3x text-warning mb-3"></i>
                <p class="text-muted mb-0">Dekan belum ditentukan</p>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Information Details -->
        <div class="card card-modern shadow-sm mb-3 bg-dark">
            <div class="card-header bg-secondary border-0">
                <h5 class="mb-0 text-white"><i class="fas fa-info-circle text-primary me-2"></i>Informasi Detail</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="border-start border-primary border-4 ps-3">
                            <label class="form-label text-muted small mb-1">Kode Fakultas</label>
                            <p class="mb-0">
                                <span class="badge bg-primary badge-modern fs-6">{{ $fakultas->kode_fakultas }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="border-start border-success border-4 ps-3">
                            <label class="form-label text-muted small mb-1">Status Operasional</label>
                            <p class="mb-0">
                                @if($fakultas->status_aktif)
                                    <span class="badge bg-success badge-modern">
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger badge-modern">
                                        <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="border-start border-info border-4 ps-3">
                            <label class="form-label text-muted small mb-1">Nama Dekan</label>
                            <p class="mb-0 text-white">
                                @if($fakultas->dekan)
                                    <i class="fas fa-user-tie text-info me-2"></i>{{ $fakultas->dekan }}
                                @else
                                    <span class="text-muted fst-italic">Belum ditentukan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="border-start border-warning border-4 ps-3">
                            <label class="form-label text-muted small mb-1">Terakhir Diupdate</label>
                            <p class="mb-0 text-white">
                                <i class="fas fa-clock text-warning me-2"></i>
                                {{ $fakultas->updated_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departemen List -->
        <div class="card card-modern shadow-sm bg-dark">
            <div class="card-header bg-secondary border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-building text-primary me-2"></i>Daftar Departemen
                    </h5>
                    <span class="badge bg-primary badge-modern">{{ $fakultas->departemen->count() }} Departemen</span>
                </div>
            </div>
            <div class="card-body p-3">
                @if($fakultas->departemen->count() > 0)
                    <div class="row g-3">
                        @foreach($fakultas->departemen as $departemen)
                        <div class="col-md-6">
                            <div class="card border {{ $departemen->status_aktif ? 'border-success' : 'border-secondary' }} h-100 bg-secondary">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-white">{{ $departemen->nama_departemen }}</h6>
                                                <small class="text-muted">{{ $departemen->kode_departemen }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-{{ $departemen->status_aktif ? 'success' : 'secondary' }}">
                                            {{ $departemen->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-dark">
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i>{{ $departemen->karyawan->count() }} Karyawan
                                        </small>
                                        <a href="{{ route('admin.departemen.show', $departemen->id_departemen) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                             style="width: 100px; height: 100px;">
                            <i class="fas fa-building fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-white mb-3">Belum Ada Departemen</h5>
                        <p class="text-muted mb-4">Fakultas ini belum memiliki departemen</p>
                        <a href="{{ route('admin.departemen.create') }}" class="btn btn-primary btn-modern">
                            <i class="fas fa-plus-circle me-2"></i>Tambah Departemen Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('admin.fakultas.index') }}" class="btn btn-secondary btn-modern">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
            </a>
            <div>
                <a href="{{ route('admin.fakultas.edit', $fakultas->id_fakultas) }}" 
                   class="btn btn-warning btn-modern me-2">
                    <i class="fas fa-edit me-2"></i>Edit Data
                </a>
                @if($fakultas->departemen->count() == 0)
                <form action="{{ route('admin.fakultas.destroy', $fakultas->id_fakultas) }}" 
                      method="POST" 
                      class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-danger btn-modern" 
                            onclick="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                        <i class="fas fa-trash me-2"></i>Hapus Fakultas
                    </button>
                </form>
                @else
                <button class="btn btn-danger btn-modern" 
                        disabled 
                        data-bs-toggle="tooltip" 
                        title="Tidak dapat menghapus fakultas yang masih memiliki departemen">
                    <i class="fas fa-trash me-2"></i>Hapus Fakultas
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush