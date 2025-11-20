@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('icon', 'fa-users')

@section('content')
    <div class="row g-2">
        <!-- Filter Card -->
        <div class="col-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter me-2"></i>Filter Karyawan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.karyawan.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <!-- Search Nama/NIP -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Cari Nama/NIP</label>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                        placeholder="Nama atau NIP" value="{{ request('search') }}">
                                </div>
                            </div>

                            <!-- Jabatan -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Jabatan</label>
                                    <select name="jabatan" class="form-control form-control-sm">
                                        <option value="">Semua Jabatan</option>
                                        @foreach ($jabatanList as $j)
                                            <option value="{{ $j->id_jabatan }}"
                                                {{ request('jabatan') == $j->id_jabatan ? 'selected' : '' }}>
                                                {{ $j->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Departemen -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Departemen</label>
                                    <select name="departemen" class="form-control form-control-sm">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemenList as $d)
                                            <option value="{{ $d->id_departemen }}"
                                                {{ request('departemen') == $d->id_departemen ? 'selected' : '' }}>
                                                {{ $d->nama_departemen }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Fakultas -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Fakultas</label>
                                    <select name="fakultas" class="form-control form-control-sm">
                                        <option value="">Semua Fakultas</option>
                                        @foreach ($fakultasList as $f)
                                            <option value="{{ $f->id_fakultas }}"
                                                {{ request('fakultas') == $f->id_fakultas ? 'selected' : '' }}>
                                                {{ $f->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Status Aktif -->
                            <div class="col-md-2">
                                <div class="form-group mb-3">
                                    <label class="small mb-1">Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non-Aktif
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="small mb-1 d-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-sm me-1">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-users me-2"></i>Daftar Karyawan
                            <span class="badge bg-primary ms-2">{{ $karyawan->total() }} karyawan</span>
                        </h6>
                        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Tambah Karyawan
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-dark table-striped mb-0">
                            <thead class="bg-gradient-primary">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th class="text-center" style="width: 80px;">Foto</th>
                                    <th style="width: 120px;">NIP</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jabatan</th>
                                    <th>Departemen</th>
                                    <th>Fakultas</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($karyawan as $index => $k)
                                    <tr>
                                        <td class="text-center">{{ $karyawan->firstItem() + $index }}</td>
                                        <td class="text-center">
                                            @if ($k->foto)
                                                <img src="{{ asset('public/' . $k->foto) }}" class="rounded-circle"
                                                    width="45" height="45"
                                                    style="object-fit: cover; border: 2px solid #007bff;"
                                                    alt="{{ $k->nama_lengkap }}">
                                            @else
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto"
                                                    style="width: 45px; height: 45px; border: 2px solid #007bff;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $k->nip }}</strong></td>
                                        <td>{{ $k->nama_lengkap }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $k->jabatan->nama_jabatan ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $k->departemen->nama_departemen ?? '-' }}</td>
                                        <td>{{ $k->fakultas->nama_fakultas ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($k->status_aktif)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Non-Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <!-- Lihat -->
                                                <a href="{{ route('admin.karyawan.show', $k->id_karyawan) }}"
                                                    class="btn btn-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Edit -->
                                                <a href="{{ route('admin.karyawan.edit', $k->id_karyawan) }}"
                                                    class="btn btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- QR Code -->
                                                <button type="button" class="btn btn-primary btn-qrcode"
                                                    data-id="{{ $k->id_karyawan }}" data-nip="{{ $k->nip }}"
                                                    data-nama="{{ $k->nama_lengkap }}"
                                                    data-token="{{ $k->user->barcode_token ?? '' }}"
                                                    data-bs-toggle="tooltip" title="Lihat QR Code Login">
                                                    <i class="fas fa-qrcode"></i>
                                                </button>

                                                <!-- Change Password -->
                                                <a href="{{ route('admin.karyawan.change-password', $k->id_karyawan) }}" 
                                                class="btn btn-success" 
                                                data-bs-toggle="tooltip" 
                                                title="Ubah Password">
                                                    <i class="fas fa-key"></i>
                                                </a>

                                                <!-- Hapus -->
                                                <form action="{{ route('admin.karyawan.destroy', $k->id_karyawan) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada data karyawan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Menampilkan {{ $karyawan->firstItem() ?? 0 }} - {{ $karyawan->lastItem() ?? 0 }}
                            dari {{ $karyawan->total() }} data
                        </div>
                        <div>
                            {{ $karyawan->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div class="modal fade" id="qrcodeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode me-2"></i>QR Code Login Karyawan
                    </h5>

                    <div class="d-flex gap-2">
                        <!-- Minimize button -->
                        <button type="button" class="btn btn-sm btn-secondary" id="minimizeQRModal" title="Minimize">
                            <i class="fas fa-window-minimize"></i>
                        </button>

                        <!-- Close button -->
                        {{-- <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button> --}}
                    </div>
                </div>
                <div class="modal-body text-center bg-white">
                    <div class="p-4">
                        <h6 class="text-dark mb-3">
                            <strong id="qrNama"></strong>
                        </h6>
                        <div class="badge bg-primary mb-3">
                            NIP: <span id="qrNip"></span>
                        </div>

                        <!-- QR Code Container -->
                        <div id="qrcodeContainer" class="d-flex justify-content-center align-items-center my-3"
                            style="min-height: 250px;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <!-- URL Display -->
                        <div class="alert alert-secondary small mb-2" role="alert">
                            <i class="fas fa-link me-1"></i>
                            <strong>URL:</strong>
                            <span id="qrUrl" class="text-break"></span>
                        </div>

                        <div class="alert alert-info small mb-0" role="alert">
                            <i class="fas fa-info-circle me-1"></i>
                            Scan QR Code ini untuk login sebagai karyawan
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary justify-content-center">
                    <button type="button" class="btn btn-primary btn-sm" id="downloadQR">
                        <i class="fas fa-download me-1"></i>Download QR Code
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="printQR">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@push('css')
    <style>
        /* Dark Theme Table */
        .table-dark {
            --bs-table-bg: #2d3236;
            --bs-table-striped-bg: #343a40;
            --bs-table-hover-bg: #3a3d41;
            color: #fff;
        }

        .table-dark thead {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        /* Form Label Compact */
        .form-label.small {
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            margin-bottom: 0.25rem !important;
        }

        /* Button Group */
        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        /* Filter */
        .form-select,
        .form-control {
            background-color: #2d3236 !important;
            border-color: #4a5056 !important;
            color: #fff !important;
        }

        .form-select:focus,
        .form-control:focus {
            background-color: #343a40 !important;
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }

        .form-select option {
            background-color: #2d3236 !important;
        }

        /* Pagination */
        .pagination {
            --bs-pagination-bg: #2d3236;
            --bs-pagination-border-color: #4a5056;
            --bs-pagination-hover-bg: #343a40;
            --bs-pagination-hover-border-color: #007bff;
            --bs-pagination-focus-bg: #343a40;
            --bs-pagination-active-bg: #007bff;
            --bs-pagination-active-border-color: #007bff;
        }

        /* Modal */
        .modal-content.bg-dark {
            border: 1px solid #4a5056;
        }

        /* Badge */
        .badge {
            padding: 0.375rem 0.75rem;
            font-weight: 500;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- QRCode.js Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Delete confirmation
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();

                if (confirm(
                        'Apakah Anda yakin ingin menghapus karyawan ini? Data yang terhapus tidak dapat dikembalikan.'
                    )) {
                    this.submit();
                }
            });

            // QR Code Generator
            let currentQRCode = null;
            let currentQRUrl = '';
            let qrModalInstance = null;

            $('.btn-qrcode').on('click', function() {
                const nip = $(this).data('nip');
                const nama = $(this).data('nama');
                const token = $(this).data('token');

                if (!token) {
                    alert('Token tidak tersedia untuk karyawan ini');
                    return;
                }

                // Set modal content
                $('#qrNip').text(nip);
                $('#qrNama').text(nama);

                // Generate QR URL - Otomatis menyesuaikan dengan environment saat ini
                const baseUrl = window.location.origin;
                currentQRUrl = `${baseUrl}/barcode-login/${token}`;

                console.log('QR Code URL:', currentQRUrl);
                console.log('Environment:', window.location.hostname);

                // Display URL in modal
                $('#qrUrl').text(currentQRUrl);

                // Show modal using Bootstrap 5
                qrModalInstance = new bootstrap.Modal(document.getElementById('qrcodeModal'));
                qrModalInstance.show();

                // Clear previous QR code
                $('#qrcodeContainer').html(
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                );

                // Generate new QR code with delay for modal animation
                setTimeout(function() {
                    $('#qrcodeContainer').html('');

                    currentQRCode = new QRCode(document.getElementById("qrcodeContainer"), {
                        text: currentQRUrl,
                        width: 250,
                        height: 250,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }, 300);
            });

            // Minimize Modal Function
            $('#minimizeQRModal').on('click', function() {
                if (qrModalInstance) {
                    // Minimize effect: slide down and hide
                    $('#qrcodeModal .modal-dialog').animate({
                        opacity: 0,
                        marginTop: '100vh'
                    }, 300, function() {
                        qrModalInstance.hide();
                        // Reset position after hide
                        setTimeout(function() {
                            $('#qrcodeModal .modal-dialog').css({
                                opacity: 1,
                                marginTop: ''
                            });
                        }, 300);
                    });
                }
            });

            // Download QR Code with complete information (like print)
            $('#downloadQR').on('click', function() {
                const nip = $('#qrNip').text();
                const nama = $('#qrNama').text();
                const canvas = $('#qrcodeContainer canvas')[0];

                if (canvas) {
                    const qrImageUrl = canvas.toDataURL("image/png");

                    // Create a temporary canvas to draw the complete image
                    const tempCanvas = document.createElement('canvas');
                    const ctx = tempCanvas.getContext('2d');

                    // Set canvas size (similar to print layout)
                    tempCanvas.width = 600;
                    tempCanvas.height = 700;

                    // Fill white background
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

                    // Draw header
                    ctx.fillStyle = '#000000';
                    ctx.font = 'bold 24px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('QR Code Login Karyawan', tempCanvas.width / 2, 50);

                    // Draw employee name
                    ctx.font = 'bold 20px Arial';
                    ctx.fillText(nama, tempCanvas.width / 2, 90);

                    // Draw NIP badge background
                    ctx.fillStyle = '#007bff';
                    ctx.fillRect(tempCanvas.width / 2 - 80, 105, 160, 35);

                    // Draw NIP text
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '16px Arial';
                    ctx.fillText(`NIP: ${nip}`, tempCanvas.width / 2, 128);

                    // Draw QR Code image
                    const qrImage = new Image();
                    qrImage.onload = function() {
                        // Draw border for QR code
                        ctx.strokeStyle = '#dddddd';
                        ctx.lineWidth = 4;
                        ctx.strokeRect(tempCanvas.width / 2 - 135, 165, 270, 270);

                        // Draw QR code
                        ctx.drawImage(qrImage, tempCanvas.width / 2 - 125, 175, 250, 250);

                        // Draw footer
                        ctx.fillStyle = '#666666';
                        ctx.font = '14px Arial';
                        ctx.fillText('Scan QR Code untuk login', tempCanvas.width / 2, 480);

                        const currentDate = new Date().toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });
                        ctx.font = '12px Arial';
                        ctx.fillText(`Sistem Presensi UNI - ${currentDate}`, tempCanvas.width / 2, 510);

                        // Convert to blob and download
                        tempCanvas.toBlob(function(blob) {
                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.download = `QRCode_${nip}_${nama.replace(/\s+/g, '_')}.png`;
                            link.href = url;
                            link.click();
                            URL.revokeObjectURL(url);
                        });
                    };
                    qrImage.src = qrImageUrl;
                } else {
                    alert('QR Code belum dibuat. Silakan tunggu beberapa saat.');
                }
            });

            // Print QR Code
            $('#printQR').on('click', function() {
                const nip = $('#qrNip').text();
                const nama = $('#qrNama').text();
                const canvas = $('#qrcodeContainer canvas')[0];

                if (canvas) {
                    const url = canvas.toDataURL("image/png");

                    const printWindow = window.open('', '', 'width=600,height=700');
                    printWindow.document.write(`
                <html>
                <head>
                    <title>QR Code - ${nip}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            text-align: center;
                            padding: 20px;
                        }
                        h2 { 
                            margin: 10px 0; 
                            font-size: 24px;
                            color: #000;
                        }
                        h3 { 
                            margin: 10px 0;
                            font-size: 20px;
                            color: #000;
                        }
                        .badge { 
                            background: #007bff; 
                            color: white; 
                            padding: 8px 15px; 
                            border-radius: 5px;
                            display: inline-block;
                            margin: 10px 0;
                            font-size: 16px;
                        }
                        img { 
                            margin: 20px 0;
                            border: 4px solid #ddd;
                            padding: 10px;
                            background: white;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 14px;
                            color: #666;
                        }
                        .footer p {
                            margin: 5px 0;
                        }
                        @media print {
                            body { margin: 0; padding: 20px; }
                        }
                    </style>
                </head>
                <body>
                    <h2>QR Code Login Karyawan</h2>
                    <h3>${nama}</h3>
                    <div class="badge">NIP: ${nip}</div>
                    <br>
                    <img src="${url}" alt="QR Code" width="250" height="250">
                    <div class="footer">
                        <p><strong>Scan QR Code untuk login</strong></p>
                        <p>Sistem Presensi UNI - ${new Date().toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        })}</p>
                    </div>
                </body>
                </html>
            `);
                    printWindow.document.close();
                    printWindow.focus();

                    setTimeout(function() {
                        printWindow.print();
                        printWindow.close();
                    }, 250);
                } else {
                    alert('QR Code belum dibuat. Silakan tunggu beberapa saat.');
                }
            });

            // Reset modal when closed
            $('#qrcodeModal').on('hidden.bs.modal', function() {
                $('#qrcodeContainer').html(
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                );
                currentQRCode = null;
                currentQRUrl = '';
                qrModalInstance = null;
            });

            // Card collapse functionality
            $('[data-card-widget="collapse"]').on('click', function() {
                const $btn = $(this);
                const $icon = $btn.find('i');
                const $card = $btn.closest('.card');
                const $cardBody = $card.find('.card-body');

                $cardBody.slideToggle(300, function() {
                    if ($cardBody.is(':visible')) {
                        $icon.removeClass('fa-plus').addClass('fa-minus');
                    } else {
                        $icon.removeClass('fa-minus').addClass('fa-plus');
                    }
                });
            });

            // Fade in animation
            $('.card').hide().fadeIn(400);
            });

           

    </script>
@endpush