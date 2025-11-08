@extends('layouts.app')

@section('title', 'Data Shift Kerja')
@section('icon', 'fa-clock')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-white mb-0"><i class="fas fa-clock me-2"></i>Data Shift Kerja</h4>
    <a href="{{ route('admin.shift.create') }}" class="btn btn-primary-modern btn-modern">
        <i class="fas fa-plus-circle me-2"></i>Tambah Shift
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
                        <th>Nama Shift</th>
                        <th>Jam Kerja</th>
                        <th>Durasi</th>
                        <th>Toleransi</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <span class="badge bg-primary badge-modern">{{ $shift->kode_shift }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <strong>{{ $shift->nama_shift }}</strong>
                            </div>
                            @if($shift->keterangan)
                            <small class="text-muted d-block">{{ Str::limit($shift->keterangan, 50) }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="text-white">
                                <small class="text-muted d-block">Mulai</small>
                                <strong>{{ \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') }}</strong>
                            </div>
                            <div class="text-white mt-1">
                                <small class="text-muted d-block">Selesai</small>
                                <strong>{{ \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') }}</strong>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info badge-modern">
                                {{ $shift->durasi_shift }} jam
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary badge-modern">
                                {{ $shift->toleransi_keterlambatan }} menit
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $shift->status_aktif ? 'success' : 'danger' }} badge-modern">
                                {{ $shift->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.shift.show', $shift->id_shift) }}" 
                                   class="btn btn-info btn-modern" data-bs-toggle="tooltip" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.shift.edit', $shift->id_shift) }}" 
                                   class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.shift.destroy', $shift->id_shift) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-modern" 
                                            data-bs-toggle="tooltip" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus shift ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada data shift kerja.</p>
                            <a href="{{ route('admin.shift.create') }}" class="btn btn-primary-modern btn-modern">
                                <i class="fas fa-plus me-2"></i>Tambah Shift Pertama
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
            <div class="stats-number">{{ $shifts->where('status_aktif', true)->count() }}</div>
            <div class="stats-label">Shift Aktif</div>
            <i class="fas fa-play-circle fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $shifts->count() }}</div>
            <div class="stats-label">Total Shift</div>
            <i class="fas fa-layer-group fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $shifts->sum('durasi_shift') }}</div>
            <div class="stats-label">Total Jam</div>
            <i class="fas fa-clock fa-2x mt-2 opacity-50"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-number">{{ $shifts->avg('toleransi_keterlambatan') }}</div>
            <div class="stats-label">Rata-rata Toleransi</div>
            <i class="fas fa-hourglass-half fa-2x mt-2 opacity-50"></i>
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