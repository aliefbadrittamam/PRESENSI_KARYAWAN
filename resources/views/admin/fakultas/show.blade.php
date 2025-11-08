@extends('layouts.app')

@section('title', 'Detail Fakultas')
@section('icon', 'fa-university')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Fakultas Info Card -->
        <div class="card-modern text-center p-4">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 100px; height: 100px;">
                <i class="fas fa-university fa-3x text-white"></i>
            </div>
            <h4 class="text-white">{{ $fakultas->nama_fakultas }}</h4>
            <p class="text-muted">{{ $fakultas->kode_fakultas }}</p>
            <span class="badge bg-{{ $fakultas->status_aktif ? 'success' : 'danger' }} badge-modern">
                {{ $fakultas->status_aktif ? 'Aktif' : 'Non-Aktif' }}
            </span>
        </div>
        
        <!-- Quick Stats -->
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i>Statistik</h6>
            <div class="text-white-50">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-building me-2"></i>Total Departemen</span>
                    <span class="badge bg-primary">{{ $fakultas->departemen->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-users me-2"></i>Total Karyawan</span>
                    <span class="badge bg-success">{{ $fakultas->karyawan->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-user-check me-2"></i>Karyawan Aktif</span>
                    <span class="badge bg-info">{{ $fakultas->karyawan->where('status_aktif', true)->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar me-2"></i>Dibuat Pada</span>
                    <small>{{ $fakultas->created_at->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>

        <!-- Dekan Info -->
        @if($fakultas->dekan)
        <div class="card-modern mt-4 p-4">
            <h6 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Dekan</h6>
            <div class="text-center">
                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-2" 
                     style="width: 60px; height: 60px;">
                    <i class="fas fa-user fa-2x text-white"></i>
                </div>
                <h6 class="text-white mb-1">{{ $fakultas->dekan }}</h6>
                <small class="text-muted">Dekan {{ $fakultas->nama_fakultas }}</small>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-8">
        <!-- Main Details -->
        <div class="card-modern p-4">
            <h5 class="text-white mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Fakultas</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label text-muted">Kode Fakultas</label>
                        <p class="text-white">
                            <span class="badge bg-primary badge-modern">{{ $fakultas->kode_fakultas }}</span>
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Status</label>
                        <p class="text-white">
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
                    <div class="mb-4">
                        <label class="form-label text-muted">Dekan</label>
                        <p class="text-white">
                            @if($fakultas->dekan)
                                <i class="fas fa-user-tie me-2 text-primary"></i>{{ $fakultas->dekan }}
                            @else
                                <span class="text-muted">- Belum ditentukan -</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Terakhir Diupdate</label>
                        <p class="text-white">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            {{ $fakultas->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Departemen List -->
            <div class="mt-4">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-building me-2"></i>Daftar Departemen
                    <span class="badge bg-primary ms-2">{{ $fakultas->departemen->count() }}</span>
                </h6>
                
                @if($fakultas->departemen->count() > 0)
                    <div class="row">
                        @foreach($fakultas->departemen as $departemen)
                        <div class="col-md-6 mb-3">
                            <div class="card bg-dark border-secondary">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-white mb-1">{{ $departemen->nama_departemen }}</h6>
                                            <small class="text-muted">{{ $departemen->kode_departemen }}</small>
                                        </div>
                                        <span class="badge bg-{{ $departemen->status_aktif ? 'success' : 'secondary' }}">
                                            {{ $departemen->karyawan->count() }} karyawan
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada departemen di fakultas ini.</p>
                        <a href="{{ route('admin.departemen.create') }}" class="btn btn-primary-modern btn-sm">
                            <i class="fas fa-plus me-2"></i>Tambah Departemen
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('admin.fakultas.index') }}" class="btn btn-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                </a>
                <div>
                    <a href="{{ route('admin.fakultas.edit', $fakultas->id_fakultas) }}" class="btn btn-warning btn-modern me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    @if($fakultas->departemen->count() == 0)
                    <form action="{{ route('admin.fakultas.destroy', $fakultas->id_fakultas) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-modern" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </button>
                    </form>
                    @else
                    <button class="btn btn-danger btn-modern" disabled data-bs-toggle="tooltip" 
                            title="Tidak dapat menghapus fakultas yang masih memiliki departemen">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush