@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('icon', 'fa-users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0"><i class="fas fa-users me-2"></i>Data Karyawan</h4>
    <a href="{{ route('karyawan.create') }}" class="btn btn-primary-modern btn-modern">
        <i class="fas fa-plus-circle me-2"></i>Tambah Karyawan
    </a>
</div>

<div class="card-modern">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>NIP</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Fakultas</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}" 
                                         class="rounded-circle me-2" width="32" height="32" 
                                         alt="{{ $item->nama_lengkap }}">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <strong>{{ $item->nip }}</strong>
                            </div>
                        </td>
                        <td>{{ $item->nama_lengkap }}</td>
                        <td>
                            <span class="badge bg-info badge-modern">{{ $item->jabatan->nama_jabatan }}</span>
                        </td>
                        <td>{{ $item->departemen->nama_departemen }}</td>
                        <td>{{ $item->fakultas->nama_fakultas }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_aktif ? 'success' : 'danger' }} badge-modern">
                                <i class="fas fa-{{ $item->status_aktif ? 'check' : 'times' }}-circle me-1"></i>
                                {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('karyawan.show', $item->id_karyawan) }}" 
                                   class="btn btn-info btn-modern" data-bs-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('karyawan.edit', $item->id_karyawan) }}" 
                                   class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('karyawan.destroy', $item->id_karyawan) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-modern" 
                                            data-bs-toggle="tooltip" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data karyawan.</p>
                            <a href="{{ route('karyawan.create') }}" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-plus me-2"></i>Tambah Karyawan Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($karyawan->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Menampilkan {{ $karyawan->firstItem() }} - {{ $karyawan->lastItem() }} dari {{ $karyawan->total() }} data
            </div>
            {{ $karyawan->links() }}
        </div>
        @endif
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