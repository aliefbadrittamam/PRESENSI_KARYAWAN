@extends('layouts.app')

@section('title', 'Pengajuan Izin & Cuti')
@section('icon', 'fa-file-alt')

@push('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

    <style>
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .filter-pills .btn {
            border-radius: 20px;
            padding: 8px 20px;
            margin: 0 5px 5px 0;
        }

        .badge-status {
            padding: 8px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        .badge-warning {
            background-color: #f39c12;
            color: white;
            border: 1px solid #e67e22;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
            border: 1px solid #218838;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            border: 1px solid #c82333;
        }

        .badge-primary {
            background-color: #007bff;
            color: white;
            border: 1px solid #0056b3;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
            border: 1px solid #138496;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
            border: 1px solid #545b62;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 2px;
        }

        table.dataTable tbody td,
        table.dataTable thead th {
            color: #ffffff !important;
        }

        table.dataTable tbody td small {
            color: #f8f9fa !important;
        }

        table.dataTable tbody td small {
            color: #f8f9fa !important;
        }



        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 5px 15px;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 5px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .type-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .type-badge.izin {
            background-color: #17a2b8;
            color: white;
            border: 1px solid #138496;
        }

        .type-badge.cuti {
            background-color: #f39c12;
            color: white;
            border: 1px solid #e67e22;
        }

        table.dataTable thead th {
            background-color: #343a40;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            color: white;
        }

        table.dataTable tbody td {
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .02);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #667eea !important;
            border-color: #667eea !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #764ba2 !important;
            border-color: #764ba2 !important;
            color: white !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
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
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stats-number">{{ $pengajuan->count() }}</p>
                            <p class="stats-label mb-0">Total Data</p>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="card card-modern shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-list mr-2"></i> Daftar Pengajuan Izin & Cuti
                </h3>
            </div>
            <div class="card-body">
                <!-- Filter Pills -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="filter-pills mb-2">
                            <strong class="mr-2">Filter Tipe:</strong>
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
                            <strong class="mr-2">Status:</strong>
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

                <!-- DataTable -->
                <div class="table-responsive">
                    <table id="pengajuanTable" class="table table-bordered table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tipe</th>
                                <th width="15%">Nama Karyawan</th>
                                <th width="10%">NIP</th>
                                <th width="12%">Jenis</th>
                                <th width="15%">Periode</th>
                                <th width="8%">Durasi</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengajuan as $index => $item)
                                @php
                                    $isIzin = $item instanceof App\Models\Izin;
                                    $statusBadge = [
                                        'pending' => 'badge-warning',
                                        'approved' => 'badge-success',
                                        'rejected' => 'badge-danger',
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="type-badge {{ $isIzin ? 'izin' : 'cuti' }}">
                                            <i class="fas {{ $isIzin ? 'fa-user-clock' : 'fa-calendar-day' }} mr-1"></i>
                                            {{ $isIzin ? 'IZIN' : 'CUTI' }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong
                                            style="color: #212529; font-size: 0.95rem;">{{ $item->karyawan->nama_lengkap ?? '-' }}
</strong>
                                        <br>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            <i class="fas fa-briefcase mr-1"></i>
                                            {{ $item->karyawan->jabatan->nama_jabatan ?? '-' }}

                                        </small>
                                    </td>
                                    <td style="font-weight: 500; color: #495057;">{{ $item->karyawan->nip ?? '-'}}</td>
                                    <td>
                                        @if ($isIzin)
                                            <span
                                                class="badge badge-{{ $item->tipe_izin === 'sakit' ? 'danger' : 'info' }}"
                                                style="font-size: 0.85rem; padding: 6px 12px;">
                                                <i
                                                    class="fas fa-{{ $item->tipe_izin === 'sakit' ? 'heartbeat' : 'user-clock' }} mr-1"></i>
                                                {{ ucfirst($item->tipe_izin) }}
                                            </span>
                                        @else
                                            @php
                                                $jenisCutiConfig = [
                                                    'tahunan' => ['color' => 'primary', 'icon' => 'calendar-alt'],
                                                    'sakit' => ['color' => 'danger', 'icon' => 'heartbeat'],
                                                    'melahirkan' => ['color' => 'info', 'icon' => 'baby'],
                                                    'menikah' => ['color' => 'success', 'icon' => 'heart'],
                                                    'khusus' => ['color' => 'warning', 'icon' => 'star'],
                                                ];
                                                $config = $jenisCutiConfig[$item->jenis_cuti] ?? [
                                                    'color' => 'secondary',
                                                    'icon' => 'calendar',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $config['color'] }}"
                                                style="font-size: 0.85rem; padding: 6px 12px;">
                                                <i class="fas fa-{{ $config['icon'] }} mr-1"></i>
                                                {{ ucfirst($item->jenis_cuti) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small style="font-weight: 500; color: #495057;">
                                            <i class="fas fa-calendar-alt mr-1 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                                            <br>
                                            <i class="fas fa-arrow-right mr-1 text-muted"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary" style="font-size: 0.85rem; padding: 6px 12px;">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            {{ $item->jumlah_hari }} Hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-status {{ $statusBadge[$item->status_approval] }}"
                                            style="font-size: 0.85rem; padding: 8px 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i
                                                class="fas fa-{{ $item->status_approval === 'pending' ? 'clock' : ($item->status_approval === 'approved' ? 'check-circle' : 'times-circle') }} mr-1"></i>
                                            {{ $item->status_approval === 'pending' ? 'PENDING' : ($item->status_approval === 'approved' ? 'DISETUJUI' : 'DITOLAK') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ $isIzin ? route('admin.pengajuan.show-izin', $item->id_izin) : route('admin.pengajuan.show-cuti', $item->id_cuti) }}"
                                                class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($item->status_approval === 'pending')
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="confirmApprove('{{ $isIzin ? 'izin' : 'cuti' }}', '{{ $isIzin ? $item->id_izin : $item->id_cuti }}', ''{{ $item->karyawan->nama_lengkap ?? '-' }}'
')"
                                                    title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                                    data-target="#rejectModal"
                                                    data-type="{{ $isIzin ? 'izin' : 'cuti' }}"
                                                    data-id="{{ $isIzin ? $item->id_izin : $item->id_cuti }}"
                                                    data-name="{{ optional($item->karyawan)->nama_lengkap ?? '-' }}"
 title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                            <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required
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
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#pengajuanTable').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data pengajuan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                columnDefs: [{
                        orderable: false,
                        targets: [8]
                    },
                    {
                        className: "text-center",
                        targets: [0, 1, 6, 7, 8]
                    }
                ]
            });
        });

        // Approve confirmation
        function confirmApprove(type, id, name) {
            if (confirm(
                    `Apakah Anda yakin ingin menyetujui pengajuan ${type} dari ${name}?\n\nPresensi akan otomatis dibuat untuk periode yang diajukan.`
                )) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = type === 'izin' ?
                    '{{ route('admin.pengajuan.approve-izin', ':id') }}'.replace(':id', id) :
                    '{{ route('admin.pengajuan.approve-cuti', ':id') }}'.replace(':id', id);

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
        $('#rejectModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const type = button.data('type');
            const id = button.data('id');
            const name = button.data('name');

            const actionUrl = type === 'izin' ?
                '{{ route('admin.pengajuan.reject-izin', ':id') }}'.replace(':id', id) :
                '{{ route('admin.pengajuan.reject-cuti', ':id') }}'.replace(':id', id);

            $('#rejectForm').attr('action', actionUrl);
            $('#rejectName').text(name);
            $('#alasan_penolakan').val('');
        });
    </script>
@endpush
