@extends('layouts.app')

@section('title', 'Pengajuan Izin & Cuti')
@section('icon', 'fa-file-alt')

@push('css')
<style>
    .badge-status {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
    }
    
    .card-pengajuan {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .card-pengajuan:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .card-pengajuan.izin {
        border-left-color: #17a2b8;
    }
    
    .card-pengajuan.cuti {
        border-left-color: #ffc107;
    }
    
    .stats-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0;
    }
    
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    
    .filter-pills .btn {
        border-radius: 20px;
        padding: 8px 20px;
        margin: 0 5px;
    }
    
    .timeline-badge {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .action-buttons .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-warning text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stats-number">{{ $stats['total_pending'] }}</p>
                        <p class="stats-label mb-0">Menunggu Persetujuan</p>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stats-number">{{ $stats['total_approved'] }}</p>
                        <p class="stats-label mb-0">Disetujui</p>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stats-number">{{ $stats['total_rejected'] }}</p>
                        <p class="stats-label mb-0">Ditolak</p>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stats-number">{{ $stats['izin_pending'] + $stats['cuti_pending'] }}</p>
                        <p class="stats-label mb-0">Total Pengajuan</p>
                    </div>
                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card card-modern">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i> Daftar Pengajuan Izin & Cuti
            </h3>
        </div>
        <div class="card-body">
            <!-- Filter Pills -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="filter-pills mb-2">
                        <span class="font-weight-bold mr-2">Filter Tipe:</span>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => 'semua', 'status' => request('status', 'pending')]) }}" 
                           class="btn btn-sm {{ $filter == 'semua' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-list"></i> Semua
                        </a>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => 'izin', 'status' => request('status', 'pending')]) }}" 
                           class="btn btn-sm {{ $filter == 'izin' ? 'btn-info' : 'btn-outline-info' }}">
                            <i class="fas fa-user-clock"></i> Izin ({{ $stats['izin_pending'] }})
                        </a>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => 'cuti', 'status' => request('status', 'pending')]) }}" 
                           class="btn btn-sm {{ $filter == 'cuti' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="fas fa-calendar-day"></i> Cuti ({{ $stats['cuti_pending'] }})
                        </a>
                    </div>
                    
                    <div class="filter-pills mb-2">
                        <span class="font-weight-bold mr-2">Status:</span>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => request('filter', 'semua'), 'status' => 'pending']) }}" 
                           class="btn btn-sm {{ $status == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                            <i class="fas fa-clock"></i> Pending
                        </a>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => request('filter', 'semua'), 'status' => 'approved']) }}" 
                           class="btn btn-sm {{ $status == 'approved' ? 'btn-success' : 'btn-outline-success' }}">
                            <i class="fas fa-check"></i> Disetujui
                        </a>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => request('filter', 'semua'), 'status' => 'rejected']) }}" 
                           class="btn btn-sm {{ $status == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
                            <i class="fas fa-times"></i> Ditolak
                        </a>
                        <a href="{{ route('admin.pengajuan.index', ['filter' => request('filter', 'semua'), 'status' => 'all']) }}" 
                           class="btn btn-sm {{ $status == 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            <i class="fas fa-list-ul"></i> Semua
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pengajuan List -->
            @if($pengajuan->count() > 0)
                <div class="row">
                    @foreach($pengajuan as $item)
                        @php
                            $isIzin = $item instanceof App\Models\Izin;
                            $statusBadge = [
                                'pending' => 'badge-warning',
                                'approved' => 'badge-success',
                                'rejected' => 'badge-danger'
                            ];
                        @endphp
                        
                        <div class="col-md-6 mb-3">
                            <div class="card card-pengajuan {{ $isIzin ? 'izin' : 'cuti' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="timeline-badge {{ $isIzin ? 'bg-info' : 'bg-warning' }} text-white mr-3">
                                                <i class="fas {{ $isIzin ? 'fa-user-clock' : 'fa-calendar-day' }}"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">
                                                    {{ $item->karyawan->nama_lengkap }}
                                                    <small class="text-muted">({{ $item->karyawan->nip }})</small>
                                                </h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-briefcase mr-1"></i>
                                                    {{ $item->karyawan->jabatan->nama_jabatan ?? '-' }} - 
                                                    {{ $item->karyawan->departemen->nama_departemen ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                        <span class="badge badge-status {{ $statusBadge[$item->status_approval] }}">
                                            {{ strtoupper($item->status_approval) }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Jenis:</small>
                                                <strong>
                                                    @if($isIzin)
                                                        <span class="badge badge-info">
                                                            <i class="fas fa-user-clock mr-1"></i>
                                                            {{ ucfirst($item->tipe_izin) }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-calendar-day mr-1"></i>
                                                            Cuti {{ ucfirst($item->jenis_cuti) }}
                                                        </span>
                                                    @endif
                                                </strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Durasi:</small>
                                                <strong>{{ $item->jumlah_hari }} Hari</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Periode:</small>
                                        <strong>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                        </strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Keterangan:</small>
                                        <p class="mb-0">{{ Str::limit($item->keterangan, 100) }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-clock mr-1"></i>
                                            Diajukan: {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y H:i') }}
                                        </small>
                                    </div>

                                    @if($item->status_approval !== 'pending')
                                        <div class="alert alert-{{ $item->status_approval === 'approved' ? 'success' : 'danger' }} mb-3">
                                            <small>
                                                <i class="fas fa-{{ $item->status_approval === 'approved' ? 'check' : 'times' }}-circle mr-1"></i>
                                                <strong>{{ $item->status_approval === 'approved' ? 'Disetujui' : 'Ditolak' }}</strong> 
                                                oleh {{ $item->approvedBy->name ?? '-' }} 
                                                pada {{ $item->tanggal_approval ? \Carbon\Carbon::parse($item->tanggal_approval)->format('d M Y H:i') : '-' }}
                                            </small>
                                            @if($item->status_approval === 'rejected' && $item->alasan_penolakan)
                                                <hr class="my-2">
                                                <small><strong>Alasan:</strong> {{ $item->alasan_penolakan }}</small>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="action-buttons d-flex justify-content-end">
                                        <a href="{{ $isIzin ? route('admin.pengajuan.show-izin', $item->id_izin) : route('admin.pengajuan.show-cuti', $item->id_cuti) }}" 
                                           class="btn btn-sm btn-info mr-2">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                        
                                        @if($item->status_approval === 'pending')
                                            <button type="button" 
                                                    class="btn btn-sm btn-success mr-2" 
                                                    onclick="confirmApprove('{{ $isIzin ? 'izin' : 'cuti' }}', '{{ $isIzin ? $item->id_izin : $item->id_cuti }}', '{{ $item->karyawan->nama_lengkap }}')">
                                                <i class="fas fa-check mr-1"></i> Setujui
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#rejectModal"
                                                    data-type="{{ $isIzin ? 'izin' : 'cuti' }}"
                                                    data-id="{{ $isIzin ? $item->id_izin : $item->id_cuti }}"
                                                    data-name="{{ $item->karyawan->nama_lengkap }}">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada pengajuan dengan filter yang dipilih</h5>
                    <p class="text-muted">Silakan ubah filter untuk melihat data lainnya</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle mr-2"></i>
                        Tolak Pengajuan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menolak pengajuan dari: <strong id="rejectName"></strong></p>
                    <div class="form-group">
                        <label for="alasan_penolakan">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="alasan_penolakan" 
                                  name="alasan_penolakan" 
                                  rows="4" 
                                  required
                                  placeholder="Masukkan alasan penolakan..."></textarea>
                        <small class="form-text text-muted">Alasan ini akan dilihat oleh karyawan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle mr-1"></i> Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Approve confirmation
function confirmApprove(type, id, name) {
    if (confirm(`Apakah Anda yakin ingin menyetujui pengajuan ${type} dari ${name}?\n\nPresensi akan otomatis dibuat untuk periode yang diajukan.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = type === 'izin' 
            ? '{{ route("admin.pengajuan.approve-izin", ":id") }}'.replace(':id', id)
            : '{{ route("admin.pengajuan.approve-cuti", ":id") }}'.replace(':id', id);
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Reject modal
$('#rejectModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const type = button.data('type');
    const id = button.data('id');
    const name = button.data('name');
    
    const actionUrl = type === 'izin'
        ? '{{ route("admin.pengajuan.reject-izin", ":id") }}'.replace(':id', id)
        : '{{ route("admin.pengajuan.reject-cuti", ":id") }}'.replace(':id', id);
    
    $('#rejectForm').attr('action', actionUrl);
    $('#rejectName').text(name);
    $('#alasan_penolakan').val('');
});
</script>
@endpush