@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('icon', 'fa-users')

@push('styles')
    <style>
        /* Modal z-index */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1060 !important;
        }

        .modal-content {
            background-color: #ffffff !important;
            text-align: right;
            border-radius: 10px;
            padding: 20px;
        }

        .modal-backdrop.show {
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Modal body centering */
        .modal-body-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
        }

        /* QR Card Container */
        .qr-download-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-card {
            background: white;
            border: 15px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 30px 25px;
            display: inline-block;
            max-width: 380px;
            text-align: right;
        }

        .qr-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 8px 0;
        }

        .qr-header p {
            font-size: 0.95rem;
            color: #7f8c8d;
            margin: 0;
        }

        #qrcodeContainer {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            border: 2px solid #ecf0f1;
        }

        #qrcodeContainer canvas,
        #qrcodeContainer img {
            display: block;
            margin: 0 auto;
        }

        /* Debug info */
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-top: 20px;
            font-size: 0.85rem;
            text-align: left;
            max-height: 150px;
            overflow-y: auto;
            width: 100%;
        }

        .debug-info strong {
            color: #495057;
            display: block;
            margin-bottom: 5px;
        }

        .debug-info small {
            display: block;
            margin: 3px 0;
            word-break: break-all;
        }

        .debug-info code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.75rem;
            color: #e74c3c;
        }

        /* Enhanced Table Styles */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .table-modern thead th:first-child {
            border-radius: 10px 0 0 0;
        }

        .table-modern thead th:last-child {
            border-radius: 0 10px 0 0;
        }

        .table-modern tbody tr {
            background: white;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border: none;
        }

        /* NIP Column Styling */
        .nip-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        /* Name Column Styling */
        .employee-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
        }

        /* Badge Enhancements */
        .badge-modern {
            padding: 0.5rem 1rem;
            font-weight: 500;
            font-size: 0.8rem;
            border-radius: 50px;
            letter-spacing: 0.3px;
        }

        .badge-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .badge-success {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        }

        .badge-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
        }

        /* Department & Faculty Styling */
        .dept-text {
            color: #4a5568;
            font-weight: 500;
        }

        .faculty-text {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Action Buttons Enhancement */
        .btn-group-sm .btn {
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }

        .btn-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(240, 147, 251, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #868f96 0%, #596164 100%);
            border: none;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(134, 143, 150, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            border: none;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(250, 112, 154, 0.4);
        }

        /* Number Column */
        .number-cell {
            font-weight: 700;
            color: #667eea;
            font-size: 1rem;
        }

        /* Empty State Enhancement */
        .empty-state {
            padding: 3rem 2rem;
        }

        .empty-state i {
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        /* Card Enhancement */
        .card-modern {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        /* Pagination Enhancement - Bootstrap 4 Compatible */
        .pagination {
            margin-bottom: 0;
            display: flex;
            padding-left: 0;
            list-style: none;
            border-radius: 0.25rem;
        }

        .page-item {
            margin: 0 3px;
        }

        .page-link {
            position: relative;
            display: block;
            padding: 0.5rem 0.75rem;
            margin-left: 0;
            line-height: 1.25;
            color: #667eea;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            z-index: 2;
            color: #fff;
            text-decoration: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .page-link:focus {
            z-index: 3;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #dee2e6;
            opacity: 0.5;
        }

        .page-item:first-child .page-link {
            margin-left: 0;
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .page-item:last-child .page-link {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        /* Dark Mode Pagination Fix */
        .dark-mode .page-link {
            background-color: #454d55;
            border-color: #4b545c;
            color: #667eea;
        }

        .dark-mode .page-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
        }

        .dark-mode .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
        }

        .dark-mode .page-item.disabled .page-link {
            background-color: #454d55;
            border-color: #4b545c;
            color: #6c757d;
        }

        /* Status Icons */
        .status-icon {
            font-size: 0.8rem;
            margin-right: 0.3rem;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-white mb-0"><i class="fas fa-users me-2"></i>Data Karyawan</h4>
        <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary-modern btn-modern">
            <i class="fas fa-plus-circle me-2"></i>Tambah Karyawan
        </a>
    </div>

    <div class="card-modern">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Fakultas</th>
                            <th class="text-center">Status</th>
                            <th width="200" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawan as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="number-cell">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <span class="nip-badge">{{ $item->nip }}</span>
                                </td>
                                <td>
                                    <span class="employee-name">{{ $item->nama_lengkap }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info badge-modern">
                                        <i class="fas fa-briefcase me-1"></i>
                                        {{ $item->jabatan->nama_jabatan }}
                                    </span>
                                </td>
                                <td>
                                    <span class="dept-text">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $item->departemen->nama_departemen }}
                                    </span>
                                </td>
                                <td>
                                    <span class="faculty-text">
                                        <i class="fas fa-university me-1"></i>
                                        {{ $item->fakultas->nama_fakultas }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->status_aktif ? 'success' : 'danger' }} badge-modern">
                                        <i class="fas fa-{{ $item->status_aktif ? 'check' : 'times' }}-circle status-icon"></i>
                                        {{ $item->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm d-flex justify-content-center" role="group">
                                        <a href="{{ route('admin.karyawan.show', $item->id_karyawan) }}"
                                            class="btn btn-info btn-modern" data-bs-toggle="tooltip" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.karyawan.edit', $item->id_karyawan) }}"
                                            class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-secondary btn-modern"
                                            onclick="showQRCodeModal('{{ addslashes($item->nama_lengkap) }}', '{{ $item->nip }}', '{{ $item->user->barcode_token ?? '' }}')"
                                            data-bs-toggle="tooltip" title="Lihat QR Code">
                                            <i class="fas fa-qrcode"></i>
                                        </button>
                                        <form action="{{ route('admin.karyawan.destroy', $item->id_karyawan) }}" method="POST"
                                            class="d-inline">
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
                                <td colspan="8" class="text-center empty-state">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p class="text-muted mb-3">Tidak ada data karyawan.</p>
                                    <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary-modern btn-modern">
                                        <i class="fas fa-plus me-2"></i>Tambah Karyawan Pertama
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($karyawan->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan {{ $karyawan->firstItem() }} - {{ $karyawan->lastItem() }} dari
                        {{ $karyawan->total() }} data
                    </div>
                    {{ $karyawan->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

{{-- Modal QR Code --}}
<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white border-0 shadow-lg rounded-4 text-end">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title w-100 text-end" id="qrcodeModalLabel">
                    <i class="fas fa-qrcode ms-2"></i> QR Code Karyawan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="qr-download-wrapper d-flex justify-content-center" id="qrDownloadWrapper">
                    <div class="qr-card bg-white border rounded-3 p-4 shadow-sm text-end">
                        <div class="qr-header mb-3">
                            <h3 id="namaKaryawan" class="mb-0">—</h3>
                            <p id="nipKaryawan" class="text-muted mb-0">—</p>
                        </div>
                        <div id="qrcodeContainer"></div>
                    </div>
                </div>

                <div id="debugInfo" class="debug-info mt-4 text-end" style="display: block;">
                    <strong>Debug Info:</strong><br>
                    <small>Token: <code id="debugToken">—</code></small><br>
                    <small>URL: <code id="debugUrl">—</code></small>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                    <button class="btn btn-outline-primary" id="printQRCode">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                    <button class="btn btn-outline-success" id="downloadQRCode">
                        <i class="fas fa-download me-1"></i> Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const qrcodeModalEl = document.getElementById('qrcodeModal');
            const qrcodeModal = new bootstrap.Modal(qrcodeModalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            qrcodeModalEl.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    try {
                        qrcodeModal.hide();
                    } catch (err) {}
                });
            });

            let currentQRCode = null;

            qrcodeModalEl.addEventListener('hidden.bs.modal', function() {
                const qrcodeContainer = document.getElementById('qrcodeContainer');
                qrcodeContainer.innerHTML = '';
                try {
                    if (currentQRCode && typeof currentQRCode.clear === 'function') {
                        currentQRCode.clear();
                    }
                } catch (e) {}
                currentQRCode = null;
                console.log('Modal closed - QR Code cleared');
            });

            window.showQRCodeModal = function(nama, nip, qrcodeToken) {
                console.log('showQRCodeModal', { nama, nip, qrcodeToken });

                document.getElementById('namaKaryawan').textContent = nama || '—';
                document.getElementById('nipKaryawan').textContent = nip ? ('NIP: ' + nip) : '—';

                const qrcodeContainer = document.getElementById('qrcodeContainer');
                const debugInfo = document.getElementById('debugInfo');
                const debugToken = document.getElementById('debugToken');
                const debugUrl = document.getElementById('debugUrl');

                qrcodeContainer.innerHTML = '';
                if (currentQRCode && typeof currentQRCode.clear === 'function') {
                    try {
                        currentQRCode.clear();
                    } catch (e) {}
                }
                currentQRCode = null;

                if (!qrcodeToken || !qrcodeToken.toString().trim()) {
                    qrcodeContainer.innerHTML =
                        '<div class="text-danger p-3"><i class="fas fa-exclamation-triangle mb-2"></i><br>QR Code token tidak tersedia.<br><small>Silakan generate token terlebih dahulu.</small></div>';
                    debugToken.textContent = 'Token kosong';
                    debugUrl.textContent = '—';
                    qrcodeModal.show();
                    return;
                }

                const baseUrl = window.location.origin;
                const loginUrl = baseUrl + '/barcode-login/' + encodeURIComponent(qrcodeToken);

                debugToken.textContent = qrcodeToken;
                debugUrl.textContent = loginUrl;

                try {
                    qrcodeContainer.innerHTML = '';

                    currentQRCode = new QRCode(qrcodeContainer, {
                        text: loginUrl,
                        width: 280,
                        height: 280,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });

                    console.log('QR Code generated:', loginUrl);

                    setTimeout(function() {
                        qrcodeModal.show();
                    }, 50);

                } catch (err) {
                    console.error('QRCode generation error:', err);
                    qrcodeContainer.innerHTML =
                        '<div class="text-danger p-3"><i class="fas fa-times-circle mb-2"></i><br>Gagal generate QR Code.<br><small>Error: ' +
                        (err.message || err) + '</small></div>';
                    qrcodeModal.show();
                }
            };

            document.getElementById('downloadQRCode').addEventListener('click', function() {
                const wrapper = document.getElementById('qrDownloadWrapper');
                if (!wrapper) {
                    alert('Tidak ada QR Code untuk diunduh.');
                    return;
                }

                const debugInfo = document.getElementById('debugInfo');
                const debugDisplay = debugInfo.style.display;
                debugInfo.style.display = 'none';

                html2canvas(wrapper, {
                    backgroundColor: '#ffffff',
                    scale: 3,
                    logging: false,
                    useCORS: true
                }).then(canvas => {
                    debugInfo.style.display = debugDisplay;

                    canvas.toBlob(function(blob) {
                        if (!blob) {
                            alert('Gagal mengkonversi QR Code.');
                            return;
                        }

                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;

                        const nipText = document.getElementById('nipKaryawan').textContent
                            .replace(/NIP:\s*/gi, '')
                            .replace(/\s+/g, '_')
                            .replace(/[^a-zA-Z0-9_-]/g, '') || 'karyawan';

                        a.download = `QRCode_${nipText}.png`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                        console.log('QR Code downloaded');
                    }, 'image/png');
                }).catch(err => {
                    debugInfo.style.display = debugDisplay;
                    console.error('html2canvas error:', err);
                    alert('Gagal download QR Code. Error: ' + (err.message || err));
                });
            });

            document.getElementById('printQRCode').addEventListener('click', function() {
                const qrcodeCanvas = document.querySelector('#qrcodeContainer canvas');
                const qrcodeImg = document.querySelector('#qrcodeContainer img');
                let dataUrl = null;

                if (qrcodeCanvas) {
                    try {
                        dataUrl = qrcodeCanvas.toDataURL('image/png');
                    } catch (e) {
                        dataUrl = null;
                    }
                } else if (qrcodeImg) {
                    dataUrl = qrcodeImg.src;
                }

                if (!dataUrl) {
                    alert('Tidak ada QR Code untuk dicetak.');
                    return;
                }

                const nama = document.getElementById('namaKaryawan').textContent || '';
                const nip = document.getElementById('nipKaryawan').textContent || '';

                const printWindow = window.open('', '_blank', 'width=800,height=600');
                if (!printWindow) {
                    alert('Pop-up diblokir. Izinkan pop-up untuk mencetak.');
                    return;
                }

                printWindow.document.open();
                printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8"/>
                    <title>Cetak QR Code - ${nama}</title>
                    <style>
                        *{box-sizing:border-box;margin:0;padding:0}
                        body{font-family:Arial,Helvetica,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:20px;background:#f5f5f5}
                        .qr-wrapper{background:#fff;padding:40px;border-radius:12px}
                        .qr-card{text-align:center;padding:30px 25px;border-radius:12px}
                        .qr-header h3{margin:0 0 8px 0;font-size:24px;color:#2c3e50}
                        .qr-header p{margin:0;color:#7f8c8d}
                        .qr-container img{width:280px;height:280px;display:block}
                        @media print{ body{background:#fff} }
                    </style>
                </head>
                <body>
                    <div class="qr-wrapper">
                        <div class="qr-card">
                            <div class="qr-header">
                                <h3>${nama}</h3>
                                <p>${nip}</p>
                            </div>
                            <div class="qr-container">
                                <img src="${dataUrl}" alt="QR Code"/>
                            </div>
                        </div>
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function(){ window.print(); }, 250);
                        };
                    <\/script>
                </body>
            </html>
        `);
                printWindow.document.close();
            });
        });
    </script>
@endpush