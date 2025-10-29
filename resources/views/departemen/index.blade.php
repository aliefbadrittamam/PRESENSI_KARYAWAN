@extends('layouts.app')

@section('title', 'Data Departemen')
@section('icon', 'fa-building')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0"><i class="fas fa-building me-2"></i>Data Departemen</h4>
    <a href="{{ route('departemen.create') }}" class="btn btn-primary-modern btn-modern">
        <i class="fas fa-plus-circle me-2"></i>Tambah Departemen
    </a>
</div>

<div class="card-modern">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Kode</th>
                        <th>Nama Departemen</th>
                        <th>Fakultas</th>
                        <th>Total Karyawan</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
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
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-info me-2"></i>
                                <div>
                                    <strong>{{ $item->nama_departemen }}</strong>
                                    @if($item->deskripsi)
                                    <small class="d-block text-muted">{{ Str::limit($item->deskripsi, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-dark badge-modern">
                                <i class="fas fa-university me-1"></i>{{ $item->fakultas->nama_fakultas }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->karyawan->count() > 0 ? 'success' : 'secondary' }} badge-modern">
                                {{ $item->karyawan->count() }} orang
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->status_aktif ? 'success' : 'danger' }} badge-modern">
                                {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('departemen.show', $item->id_departemen) }}" 
                                   class="btn btn-info btn-modern" data-bs-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('departemen.edit', $item->id_departemen) }}" 
                                   class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('departemen.destroy', $item->id_departemen) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-modern" 
                                            data-bs-toggle="tooltip" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus departemen ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data departemen.</p>
                            <a href="{{ route('departemen.create') }}" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-plus me-2"></i>Tambah Departemen Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->where('status_aktif', true)->count() }}</div>
            <div class="stats-label">Departemen Aktif</div>
            <i class="fas fa-check-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->where('status_aktif', false)->count() }}</div>
            <div class="stats-label">Departemen Non-Aktif</div>
            <i class="fas fa-times-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $departemen->count() }}</div>
            <div class="stats-label">Total Departemen</div>
            <i class="fas fa-layer-group fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
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
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush