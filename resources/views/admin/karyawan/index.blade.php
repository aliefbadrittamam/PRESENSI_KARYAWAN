@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('icon', 'fa-users')

@section('content')
<div class="row g-2">
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>Daftar Karyawan
                    </h6>
                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Tambah Karyawan
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm bg-dark text-white border-secondary" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm bg-dark text-white border-secondary" id="filterJabatan">
                            <option value="">Semua Jabatan</option>
                            @foreach(\App\Models\Jabatan::all() as $jab)
                                <option value="{{ $jab->id_jabatan }}">{{ $jab->nama_jabatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm bg-dark text-white border-secondary" id="filterFakultas">
                            <option value="">Semua Fakultas</option>
                            @foreach(\App\Models\Fakultas::all() as $fak)
                                <option value="{{ $fak->id_fakultas }}">{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary btn-sm w-100" id="resetFilter">
                            <i class="fas fa-redo me-1"></i>Reset Filter
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="karyawanTable" class="table table-hover table-dark table-striped mb-0">
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
                                        @if($k->foto)
                                            <img src="{{ asset('storage/' . $k->foto) }}" 
                                                 class="rounded-circle" 
                                                 width="45" 
                                                 height="45" 
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
                                        @if($k->status_aktif)
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
                                               class="btn btn-info" 
                                               data-bs-toggle="tooltip" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <!-- Edit -->
                                            <a href="{{ route('admin.karyawan.edit', $k->id_karyawan) }}" 
                                               class="btn btn-warning" 
                                               data-bs-toggle="tooltip" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- QR Code -->
                                            <button type="button" 
                                                    class="btn btn-primary btn-qrcode" 
                                                    data-id="{{ $k->id_karyawan }}"
                                                    data-nip="{{ $k->nip }}"
                                                    data-nama="{{ $k->nama_lengkap }}"
                                                    data-token="{{ $k->user->barcode_token ?? '' }}"
                                                    data-bs-toggle="tooltip" 
                                                    title="Generate QR Code">
                                                <i class="fas fa-qrcode"></i>
                                            </button>
                                            
                                            <!-- Hapus -->
                                            <form action="{{ route('admin.karyawan.destroy', $k->id_karyawan) }}" 
                                                  method="POST" 
                                                  class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger" 
                                                        data-bs-toggle="tooltip" 
                                                        title="Hapus">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
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
    
    /* Button Group */
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Filter */
    .form-select, .form-control {
        background-color: #2d3236 !important;
        border-color: #4a5056 !important;
        color: #fff !important;
    }
    
    .form-select:focus, .form-control:focus {
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
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
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
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
  
    $('#karyawanTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "language": {
            "search": "Cari:",
            "emptyTable": "Tidak ada data karyawan"
        }
    });
    
    
    // Delete confirmation
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        
        if(confirm('Apakah Anda yakin ingin menghapus karyawan ini? Data yang terhapus tidak dapat dikembalikan.')) {
            this.submit();
        }
    });
    
    // QR Code Generator
    let currentQRCode = null;
    let currentQRUrl = '';
    
    $('.btn-qrcode').on('click', function() {
        const nip = $(this).data('nip');
        const nama = $(this).data('nama');
        const token = $(this).data('token');
        
        if(!token) {
            alert('Token tidak tersedia untuk karyawan ini');
            return;
        }
        
        // Set modal content
        $('#qrNip').text(nip);
        $('#qrNama').text(nama);
        
        // Generate QR URL - Otomatis menyesuaikan dengan environment saat ini
     
        // Ini akan otomatis detect protocol (http/https) dan domain yang sedang diakses
        const baseUrl = window.location.origin;
        currentQRUrl = `${baseUrl}/barcode-login/${token}`;
        
   
        
        console.log('QR Code URL:', currentQRUrl); // Untuk debugging
        console.log('Environment:', window.location.hostname); // Untuk cek environment
        
        // Display URL in modal
        $('#qrUrl').text(currentQRUrl);
        
        // Show modal
        $('#qrcodeModal').modal('show');
        
        // Clear previous QR code
        $('#qrcodeContainer').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
        
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
    
    // Download QR Code
    $('#downloadQR').on('click', function() {
        const canvas = $('#qrcodeContainer canvas')[0];
        if(canvas) {
            const url = canvas.toDataURL("image/png");
            const link = document.createElement('a');
            link.download = `QRCode_${$('#qrNip').text()}.png`;
            link.href = url;
            link.click();
        } else {
            alert('QR Code belum dibuat');
        }
    });
    
    // Print QR Code
    $('#printQR').on('click', function() {
        const nip = $('#qrNip').text();
        const nama = $('#qrNama').text();
        const canvas = $('#qrcodeContainer canvas')[0];
        
        if(canvas) {
            const url = canvas.toDataURL("image/png");
            
            const printWindow = window.open('', '', 'width=600,height=600');
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
                        h3 { margin: 10px 0; }
                        .badge { 
                            background: #007bff; 
                            color: white; 
                            padding: 5px 10px; 
                            border-radius: 5px;
                            display: inline-block;
                            margin: 10px 0;
                        }
                        img { 
                            margin: 20px 0;
                            border: 2px solid #ddd;
                            padding: 10px;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 12px;
                            color: #666;
                        }
                    </style>
                </head>
                <body>
                    <h2>QR Code Login Karyawan</h2>
                    <h3>${nama}</h3>
                    <div class="badge">NIP: ${nip}</div>
                    <br>
                    <img src="${url}" alt="QR Code">
                    <div class="footer">
                        <p>Scan QR Code untuk login</p>
                        <p>Sistem Presensi UNI - ${new Date().toLocaleDateString('id-ID')}</p>
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
            alert('QR Code belum dibuat');
        }
    });
    
    // Reset modal when closed
    $('#qrcodeModal').on('hidden.bs.modal', function() {
        $('#qrcodeContainer').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
        currentQRCode = null;
        currentQRUrl = '';
    });
    
    // Filter functionality
    $('#filterStatus, #filterJabatan, #filterFakultas').on('change', function() {
        applyFilters();
    });
    
    $('#resetFilter').on('click', function() {
        $('#filterStatus').val('');
        $('#filterJabatan').val('');
        $('#filterFakultas').val('');
        applyFilters();
    });
    
    function applyFilters() {
        const status = $('#filterStatus').val();
        const jabatan = $('#filterJabatan').val();
        const fakultas = $('#filterFakultas').val();
        
        $('#karyawanTable tbody tr').each(function() {
            let show = true;
            
            // Filter by status
            if(status !== '') {
                const rowStatus = $(this).find('td:eq(7) .badge').hasClass('bg-success') ? '1' : '0';
                if(rowStatus !== status) show = false;
            }
            
            // You can add more complex filtering here if needed
            
            $(this).toggle(show);
        });
    }
    
    // Fade in animation
    $('.card').hide().fadeIn(400);
});
</script>
@endpush