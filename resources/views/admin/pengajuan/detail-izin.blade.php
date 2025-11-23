@extends('layouts.app')

@section('title', 'Detail Pengajuan Izin')
@section('icon', 'fa-file-medical')

@push('css')
    <style>
        .info-card {
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
            background: #fff;
        }

        .info-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .info-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
            display: flex;
            align-items: center;
        }

        .info-section-title i {
            margin-right: 10px;
            color: #007bff;
        }

        .info-item {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        .info-value {
            font-size: 1.15rem;
            color: #212529;
            font-weight: 500;
            line-height: 1.5;
        }

        .info-row {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .keterangan-box {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e9ecef;
            min-height: 100px;
            line-height: 1.8;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .timeline-content {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .status-badge-large {
            font-size: 1.3rem;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .badge-lg {
            padding: 8px 16px;
            font-size: 0.95rem;
        }

        body,
        .container-fluid,
        .card,
        .card-body,
        .card-header,
        .info-label,
        .info-value,
        .alert,
        .badge,
        .modal-content,
        h4,
        h5,
        p,
        span,
        strong,
        small,
        label,
        textarea,
        button,
        a {
            color: #ffffff !important;
        }

        textarea.form-control,
        .form-control,
        .card,
        .card-body,
        .alert,
        .badge,
        .timeline-item,
        .timeline-content {
            background-color: #2a2d31 !important;
            color: #ffffff !important;
            border-color: #3a3d41 !important;
        }

        .badge.bg-primary {
            background-color: #007bffcc !important;
            color: #ffffff !important;
        }

        .timeline-content {
            background-color: #2f3237 !important;
            color: #ffffff !important;
            border-radius: 8px;
        }

        textarea.form-control {
            background-color: #2c2f33 !important;
            color: #ffffff !important;
        }

        .form-control:focus {
            border-color: #555 !important;
            box-shadow: 0 0 5px #007bff55 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Main Information -->
            <div class="col-lg-8">
                <div class="card card-modern info-card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-file-medical mr-2"></i>
                            Detail Pengajuan Izin
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Status Badge -->
                        <div class="text-center mb-4 pb-3 border-bottom">
                            @php
                                $statusConfig = [
                                    'pending' => [
                                        'class' => 'warning',
                                        'icon' => 'clock',
                                        'text' => 'Menunggu Persetujuan',
                                    ],
                                    'approved' => [
                                        'class' => 'success',
                                        'icon' => 'check-circle',
                                        'text' => 'Disetujui',
                                    ],
                                    'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Ditolak'],
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
                                    <i class="fas fa-building mr-2"></i>Divisi
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="info-value">{{ $izin->karyawan->divisi->nama_divisi ?? '-' }}</div>
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
                                <div class="info-value">
                                    {!! $izin->tipe_izin_badge !!}
                                </div>
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

                        @if ($izin->file_pendukung)
                            <div class="row info-row">
                                <div class="col-md-4">
                                    <div class="info-label">
                                        <i class="fas fa-paperclip mr-2"></i>File Pendukung
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <a href="{{ Storage::url($izin->file_pendukung) }}"
                                        class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-download mr-1"></i> Download File
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Approval Information -->
                        @if ($izin->status_approval !== 'pending')
                            <div class="row info-row mt-4 pt-3 border-top">
                                <div class="col-md-4">
                                    <div class="info-label">
                                        <i class="fas fa-user-check mr-2"></i>Disetujui/Ditolak Oleh
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="info-value">{{ $izin->approver->name ?? '-' }}</div>
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
                                        {{ $izin->approved_at ? $izin->approved_at->format('d F Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>

                            @if ($izin->status_approval === 'rejected' && $izin->alasan_penolakan)
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

                <!-- Presensi Records -->
                @if ($presensiList->isNotEmpty())
                    <div class="card card-modern">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-check mr-2"></i>
                                Data Presensi Terkait
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Status Kehadiran</th>
                                            <th>Status Verifikasi</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($presensiList as $index => $presensi)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d M Y') }}
                                                </td>
                                                <td>{{ $presensi->shift->nama_shift ?? '-' }}</td>
                                                <td>{!! $presensi->status_kehadiran_badge !!}</td>
                                                <td>{!! $presensi->status_verifikasi_badge !!}</td>
                                                <td>{{ $presensi->catatan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
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
                                <div class="timeline-badge bg-primary text-white">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>Pengajuan Disubmit</strong>
                                    <p class="text-muted mb-0">
                                        <small>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $izin->tanggal_pengajuan->format('d M Y H:i') }}
                                        </small>
                                    </p>
                                </div>
                            </div>

                            @if ($izin->status_approval !== 'pending')
                                <!-- Approved/Rejected -->
                                <div class="timeline-item">
                                    <div
                                        class="timeline-badge bg-{{ $izin->status_approval === 'approved' ? 'success' : 'danger' }} text-white">
                                        <i
                                            class="fas fa-{{ $izin->status_approval === 'approved' ? 'check' : 'times' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>{{ $izin->status_approval === 'approved' ? 'Disetujui' : 'Ditolak' }}</strong>
                                        <p class="mb-1">oleh {{ $izin->approver->name ?? '-' }}</p>
                                        <p class="text-muted mb-0">
                                            <small>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $izin->approved_at ? $izin->approved_at->format('d M Y H:i') : '-' }}
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if ($izin->status_approval === 'pending')
                    <div class="card card-modern">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tasks mr-2"></i>
                                Tindakan
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Silakan pilih tindakan untuk pengajuan ini:</p>

                            <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal"
                                data-target="#approveModal">
                                <i class="fas fa-check-circle mr-2"></i>
                                Setujui Pengajuan
                            </button>

                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
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

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.pengajuan.approve-izin', $izin->id_izin) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle mr-2"></i>
                            Konfirmasi Persetujuan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Anda akan menyetujui pengajuan izin ini. Data presensi akan otomatis terverifikasi.
                        </div>
                        <p><strong>Karyawan:</strong> {{ $izin->karyawan->nama_lengkap }}</p>
                        <p><strong>Tipe Izin:</strong> {{ ucfirst($izin->tipe_izin) }}</p>
                        <p><strong>Durasi:</strong> {{ $izin->tanggal_mulai_formatted }} -
                            {{ $izin->tanggal_selesai_formatted }} ({{ $izin->jumlah_hari }} hari)</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check mr-2"></i>
                            Ya, Setujui
                        </button>
                    </div>
                </form>
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
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Perhatian!</strong> Data presensi yang sudah dibuat akan dihapus otomatis.
                        </div>
                        <p><strong>Karyawan:</strong> {{ $izin->karyawan->nama_lengkap }}</p>
                        <p><strong>Tipe Izin:</strong> {{ ucfirst($izin->tipe_izin) }}</p>
                        <p><strong>Durasi:</strong> {{ $izin->tanggal_mulai_formatted }} -
                            {{ $izin->tanggal_selesai_formatted }} ({{ $izin->jumlah_hari }} hari)</p>

                        <hr>

                        <div class="form-group">
                            <label for="alasan_penolakan">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alasan_penolakan') is-invalid @enderror" id="alasan_penolakan"
                                name="alasan_penolakan" rows="4" required placeholder="Masukkan alasan penolakan (minimal 10 karakter)"></textarea>
                            @error('alasan_penolakan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Alasan ini akan dikirimkan ke karyawan</small>
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
        // Auto show reject modal if validation error
        @if ($errors->has('alasan_penolakan'))
            $('#rejectModal').modal('show');
        @endif
    </script>
@endpush