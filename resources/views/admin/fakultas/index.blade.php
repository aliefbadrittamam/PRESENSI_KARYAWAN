@extends('layouts.app')

@section('title', 'Data Fakultas')
@section('icon', 'fa-university')

@section('content')
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1"><i class="fas fa-university text-primary me-2"></i>Data Fakultas</h3>
                <p class="text-muted mb-0">Kelola data fakultas di universitas</p>
            </div>
            <a href="{{ route('admin.fakultas.create') }}" class="btn btn-primary btn-modern">
                <i class="fas fa-plus-circle me-2"></i>Tambah Fakultas
            </a>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="row">
    <div class="col-12">
        <div class="card card-modern shadow-sm bg-dark">
            <div class="card-header border-0 bg-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="fas fa-list text-primary me-2"></i>Daftar Fakultas</h5>
                    <span class="badge bg-primary badge-modern">Total: {{ $fakultas->count() }} Fakultas</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-modern mb-0 table-dark">
                        <thead class="bg-secondary">
                            <tr>
                                <th width="60" class="text-center">#</th>
                                <th width="120">Kode</th>
                                <th>Nama Fakultas</th>
                                <th width="200">Dekan</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="140" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fakultas as $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-bold text-white">{{ $loop->iteration }}</span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-primary badge-modern fs-6">{{ $item->kode_fakultas }}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-university text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-white">{{ $item->nama_fakultas }}</h6>
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>{{ $item->departemen->count() }} Departemen
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    @if($item->dekan)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie text-info me-2"></i>
                                            <span class="text-white">{{ $item->dekan }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($item->status_aktif)
                                        <span class="badge bg-success badge-modern">
                                            <i class="fas fa-check-circle me-1"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger badge-modern">
                                            <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.fakultas.show', $item->id_fakultas) }}" 
                                           class="btn btn-info btn-modern" 
                                           data-bs-toggle="tooltip" 
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.fakultas.edit', $item->id_fakultas) }}" 
                                           class="btn btn-warning btn-modern" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.fakultas.destroy', $item->id_fakultas) }}" 
                                              method="POST" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-modern" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Hapus Data"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 bg-dark">
                                    <div class="empty-state">
                                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center mb-3" 
                                             style="width: 100px; height: 100px;">
                                            <i class="fas fa-university fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="text-white mb-3">Belum Ada Data Fakultas</h5>
                                        <p class="text-muted mb-4">Mulai dengan menambahkan fakultas pertama Anda</p>
                                        <a href="{{ route('admin.fakultas.create') }}" class="btn btn-primary btn-modern">
                                            <i class="fas fa-plus-circle me-2"></i>Tambah Fakultas Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($fakultas->count() > 0)
            <div class="card-footer bg-secondary border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan {{ $fakultas->count() }} fakultas
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .empty-state {
        padding: 2rem 0;
    }
    
    .table-dark tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        transition: all 0.3s ease;
    }
    
    .table-dark th,
    .table-dark td {
        border-color: #4b545c !important;
    }
    
    .btn-group .btn {
        border-radius: 0;
    }
    
    .btn-group .btn:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }

    .card.bg-dark {
        background-color: #343a40 !important;
        border-color: #4b545c;
    }

    .card-header.bg-secondary {
        background-color: #454d55 !important;
        border-bottom: 1px solid #4b545c;
    }

    .card-footer.bg-secondary {
        background-color: #454d55 !important;
        border-top: 1px solid #4b545c;
    }
</style>
@endpush

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