@extends('layouts.app')

@section('title', 'Detail Pengajuan Izin')
@section('icon', 'fa-user-clock')

@push('css')
<style>
    .info-card {
        border-left: 4px solid #17a2b8;
        margin-bottom: 20px;
    }
    
    .info-row {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .info-value {
        font-size: 1.1rem;
        color: #212529;
    }
    
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    
    .timeline-item {
        padding: 15px 0;
        position: relative;
        padding-left: 60px;
    }
    
    .timeline-badge {
        position: absolute;
        left: 0;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .timeline-content {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <div class="card card-modern info-card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-clock mr-2"></i>
                        Detail Pengajuan Izin
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="text-center mb-4 pb-3 border-bottom">
                        @php
                            $statusConfig = [
                                'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Menunggu Persetujuan'],
                                'approved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Disetujui'],
                                'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Ditolak']
                            ];
                            $status = $statusConfig[$izin->status_approval];
                        @endphp
                        <span class="badge badge-{{ $status['class'] }}" style="font-size: 1.2rem; padding: 10px 25px;">
                            <i class="fas fa-{{ $status['icon'] }} mr-2"></i>
                            {{ $status['text'] }}
                        </span>
                    </div>

                    <!-- Employee Information -->
                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-user mr-2"></i>Nama Karyawan
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">{{ $izin->karyawan->nama_lengkap }}</div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-id-card mr-2"></i>NIP
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">{{ $izin->karyawan->nip }}</div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-briefcase mr-2"></i>Jabatan
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">{{ $izin->karyawan->jabatan->nama_jabatan ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-building mr-2"></i>Departemen
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">{{ $izin->karyawan->departemen->nama_departemen ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Izin Information -->
                    <div class="row info-row mt-4 pt-3 border-top">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-tag mr-2"></i>Tipe Izin
                            </div>
                        </div>
                        <div class="col-md-8">
                            <span class="badge badge-info badge-lg">
                                {{ ucfirst($izin->tipe_izin) }}
                            </span>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt mr-2"></i>Tanggal Mulai
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d F Y') }}
                                ({{ \Carbon\Carbon::parse($izin->tanggal_mulai)->isoFormat('dddd') }})
                            </div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-calendar-check mr-2"></i>Tanggal Selesai
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d F Y') }}
                                ({{ \Carbon\Carbon::parse($izin->tanggal_selesai)->isoFormat('dddd') }})
                            </div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-clock mr-2"></i>Durasi
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">
                                <span class="badge badge-primary badge-lg">
                                    {{ $izin->jumlah_hari }} Hari
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-comment mr-2"></i>Keterangan
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">
                                <div class="alert alert-light mb-0">
                                    {{ $izin->keterangan }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($izin->file_pendukung)
                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-paperclip mr-2"></i>File Pendukung
                            </div>
                        </div>
                        <div class="col-md-8">
                            <a href="{{ asset('storage/' . $izin->file_pendukung) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="fas fa-download mr-1"></i> Download File
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Approval Information -->
                    @if($izin->status_approval !== 'pending')
                    <div class="row info-row mt-4 pt-3 border-top">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-user-check mr-2"></i>Disetujui/Ditolak Oleh
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">{{ $izin->approvedBy->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-calendar-day mr-2"></i>Tanggal Approval
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-value">
                                {{ $izin->tanggal_approval ? \Carbon\Carbon::parse($izin->tanggal_approval)->format('d F Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>

                    @if($izin->status_approval === 'rejected' && $izin->alasan_penolakan)
                    <div class="row info-row">
                        <div class="col-md-4">
                            <div class="info-label">
                                <i class="fas fa-exclamation-circle mr-2"></i>Alasan Penolakan
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="alert alert-danger mb-0">
                                {{ $izin->alasan_penolakan }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline & Actions -->
        <div class="col-lg-4">
            <!-- Timeline -->
            <div class="card card-modern">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Riwayat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Pengajuan -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-info text-white">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>Pengajuan Disubmit</strong>
                                <p class="text-muted mb-0">
                                    <small>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($izin->tanggal_pengajuan)->format('d M Y H:i') }}
                                    </small>
                                </p>
                            </div>
                        </div>

                        @if($izin->status_approval !== 'pending')
                        <!-- Approved/Rejected -->
                        <div class="timeline-item">
                            <div class="timeline-badge bg-{{ $izin->status_approval === 'approved' ? 'success' : 'danger' }} text-white">
                                <i class="fas fa-{{ $izin->status_approval === 'approved' ? 'check' : 'times' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>{{ $izin->status_approval === 'approved' ? 'Disetujui' : 'Ditolak' }}</strong>
                                <p class="mb-1">oleh {{ $izin->approvedBy->name ?? '-' }}</p>
                                <p class="text-muted mb-0">
                                    <small>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $izin->tanggal_approval ? \Carbon\Carbon::parse($izin->tanggal_approval)->format('d M Y H:i') : '-' }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($izin->status_approval === 'pending')
            <div class="card card-modern">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks mr-2"></i>
                        Tindakan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Silakan pilih tindakan untuk pengajuan ini:</p>
                    
                    <button type="button" 
                            class="btn btn-success btn-block mb-2" 
                            onclick="confirmApprove()">
                        <i class="fas fa-check-circle mr-2"></i>
                        Setujui Pengajuan
                    </button>
                    
                    <button type="button" 
                            class="btn btn-danger btn-block" 
                            data-toggle="modal" 
                            data-target="#rejectModal">
                        <i class="fas fa-times-circle mr-2"></i>
                        Tolak Pengajuan
                    </button>
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <a href="{{ route('admin.pengajuan.index') }}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.pengajuan.reject-izin', $izin->id_izin) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle mr-2"></i>
                        Tolak Pengajuan Izin
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menolak pengajuan izin dari: <strong>{{ $izin->karyawan->nama_lengkap }}</strong></p>
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
function confirmApprove() {
    if (confirm('Apakah Anda yakin ingin menyetujui pengajuan izin ini?\n\nPresensi akan otomatis dibuat untuk periode {{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format("d M Y") }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format("d M Y") }}.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.pengajuan.approve-izin", $izin->id_izin) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush