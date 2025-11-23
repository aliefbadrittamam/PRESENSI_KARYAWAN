@extends('layouts.app')

@section('title', 'Data Departemen')
@section('icon', 'fa-building')

@section('content')
{{-- Header Section --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0">
        <i class="fas fa-building me-2"></i>Data Departemen
    </h4>
    <a href="{{ route('admin.departemen.create') }}" class="btn btn-primary">
    <i class="fas fa-plus-circle me-2"></i>Tambah Departemen
</a>

</div>

{{-- Main Table Card --}}
<div class="card-modern">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th style="width: 100px;">Kode</th>
                        <th>Nama Departemen</th>
                        <th style="width: 180px;">Fakultas</th>
                        <th style="width: 130px;" class="text-center">Total Karyawan</th>
                        <th style="width: 100px;" class="text-center">Status</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departemen as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-primary badge-modern">{{ $item->kode_departemen }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-start">
                                <i class="fas fa-building text-info me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong class="d-block">{{ $item->nama_departemen }}</strong>
                                    @if($item->deskripsi)
                                    <small class="text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-dark badge-modern">
                                <i class="fas fa-university me-1"></i>{{ $item->fakultas->nama_fakultas }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $item->karyawan->count() > 0 ? 'success' : 'secondary' }} badge-modern">
                                {{ $item->karyawan->count() }} orang
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $item->status_aktif ? 'success' : 'danger' }} badge-modern">
                                {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm d-flex justify-content-center" role="group">
                                <a href="{{ route('admin.departemen.show', $item->id_departemen) }}" 
                                   class="btn btn-info btn-modern" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top"
                                   title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.departemen.edit', $item->id_departemen) }}" 
                                   class="btn btn-warning btn-modern" 
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="top"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.departemen.destroy', $item->id_departemen) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger btn-modern" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-building fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-3">Tidak ada data departemen.</p>
                                <a href="{{ route('admin.departemen.create') }}" class="btn btn-primary-modern btn-modern">
                                    <i class="fas fa-plus me-2"></i>Tambah Departemen Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-3 mt-4">
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->where('status_aktif', true)->count() }}</div>
            <div class="stats-label">Departemen Aktif</div>
            <i class="fas fa-check-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->where('status_aktif', false)->count() }}</div>
            <div class="stats-label">Departemen Non-Aktif</div>
            <i class="fas fa-times-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->count() }}</div>
            <div class="stats-label">Total Departemen</div>
            <i class="fas fa-layer-group fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->groupBy('id_fakultas')->count() }}</div>
            <div class="stats-label">Fakultas Terlibat</div>
            <i class="fas fa-university fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Bootstrap 5 tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>
@endpush