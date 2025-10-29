@extends('layouts.app')

@section('title', 'Data Fakultas')
@section('icon', 'fa-university')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0"><i class="fas fa-university me-2"></i>Data Fakultas</h4>
    <a href="{{ route('fakultas.create') }}" class="btn btn-primary-modern btn-modern">
        <i class="fas fa-plus-circle me-2"></i>Tambah Fakultas
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
                        <th>Nama Fakultas</th>
                        <th>Dekan</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakultas as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-primary badge-modern">{{ $item->kode_fakultas }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-university text-primary me-2"></i>
                                <strong>{{ $item->nama_fakultas }}</strong>
                            </div>
                        </td>
                        <td>{{ $item->dekan ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_aktif ? 'success' : 'danger' }} badge-modern">
                                {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('fakultas.edit', $item->id_fakultas) }}" 
                                   class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('fakultas.destroy', $item->id_fakultas) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-modern" 
                                            data-bs-toggle="tooltip" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus fakultas ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-university fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data fakultas.</p>
                            <a href="{{ route('fakultas.create') }}" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-plus me-2"></i>Tambah Fakultas Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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