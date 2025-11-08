@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('icon', 'fa-users')

@push('styles')
    <style>
        /* Pastikan modal selalu di depan */
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

        /* QR Card Container - yang akan di-download */
 .qr-download-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
  }

        /* QR Card dengan border putih sebagai pembungkus */
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

        /* QR Code Container dengan background putih */
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

        /* Debug info styling */
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
                                        @if ($item->foto)
                                            <img src="{{ asset('storage/' . $item->foto) }}" class="rounded-circle me-2"
                                                width="32" height="32" alt="{{ $item->nama_lengkap }}">
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
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data karyawan.</p>
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
{{-- modal --}}
<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-white border-0 shadow-lg rounded-4 text-end">

      <!-- Header -->
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title w-100 text-end" id="qrcodeModalLabel">
          <i class="fas fa-qrcode ms-2"></i> QR Code Karyawan
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">

        {{-- Wrapper untuk download --}}
        <div class="qr-download-wrapper d-flex justify-content-center" id="qrDownloadWrapper">
          {{-- QR Card --}}
          <div class="qr-card bg-white border rounded-3 p-4 shadow-sm text-end">
            <div class="qr-header mb-3">
              <h3 id="namaKaryawan" class="mb-0">—</h3>
              <p id="nipKaryawan" class="text-muted mb-0">—</p>
            </div>
            <div id="qrcodeContainer"></div>
          </div>
        </div>

        {{-- Debug Info --}}
        <div id="debugInfo" class="debug-info mt-4 text-end" style="display: block;">
          <strong>Debug Info:</strong><br>
          <small>Token: <code id="debugToken">—</code></small><br>
          <small>URL: <code id="debugUrl">—</code></small>
        </div>

        {{-- Tombol Aksi --}}
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
            // Inisialisasi tooltip (Bootstrap)
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Element modal dan modal instance (inisialisasi sekali)
            const qrcodeModalEl = document.getElementById('qrcodeModal');
            const qrcodeModal = new bootstrap.Modal(qrcodeModalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });

            // Tombol-tombol close fallback (memanggil hide() jika data-bs-dismiss gagal)
            // Cari tombol yang menutup modal: btn-close & tombol dengan data-bs-dismiss="modal"
            qrcodeModalEl.querySelectorAll('[data-bs-dismiss="modal"], .btn-close').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Pastikan modal disembunyikan via instance
                    try {
                        qrcodeModal.hide();
                    } catch (err) {
                        /* ignore */
                    }
                });
            });

            // Variables untuk QRCode instance
            let currentQRCode = null;

            // Ketika modal benar-benar tersembunyi -> bersihkan DOM QR
            qrcodeModalEl.addEventListener('hidden.bs.modal', function() {
                const qrcodeContainer = document.getElementById('qrcodeContainer');
                qrcodeContainer.innerHTML = '';
                // Jika library QR punya method clear, panggil (safe-check)
                try {
                    if (currentQRCode && typeof currentQRCode.clear === 'function') {
                        currentQRCode.clear();
                    }
                } catch (e) {
                    // ignore
                }
                currentQRCode = null;
                console.log('Modal closed - QR Code cleared');
            });

            // Fungsi untuk menampilkan modal dan generate QR
            window.showQRCodeModal = function(nama, nip, qrcodeToken) {
                console.log('showQRCodeModal', {
                    nama,
                    nip,
                    qrcodeToken
                });

                // Set data karyawan
                document.getElementById('namaKaryawan').textContent = nama || '—';
                document.getElementById('nipKaryawan').textContent = nip ? ('NIP: ' + nip) : '—';

                const qrcodeContainer = document.getElementById('qrcodeContainer');
                const debugInfo = document.getElementById('debugInfo');
                const debugToken = document.getElementById('debugToken');
                const debugUrl = document.getElementById('debugUrl');

                // Reset container dan instance lama jika ada
                qrcodeContainer.innerHTML = '';
                if (currentQRCode && typeof currentQRCode.clear === 'function') {
                    try {
                        currentQRCode.clear();
                    } catch (e) {
                        /* ignore */
                    }
                }
                currentQRCode = null;

                // Validasi token
                if (!qrcodeToken || !qrcodeToken.toString().trim()) {
                    qrcodeContainer.innerHTML =
                        '<div class="text-danger p-3"><i class="fas fa-exclamation-triangle mb-2"></i><br>QR Code token tidak tersedia.<br><small>Silakan generate token terlebih dahulu.</small></div>';
                    debugToken.textContent = 'Token kosong';
                    debugUrl.textContent = '—';
                    qrcodeModal.show();
                    return;
                }

                // Generate URL lengkap
                const baseUrl = window.location.origin;
                const loginUrl = baseUrl + '/barcode-login/' + encodeURIComponent(qrcodeToken);

                debugToken.textContent = qrcodeToken;
                debugUrl.textContent = loginUrl;

                try {
                    // Pastikan container kosong sebelum pembuatan baru
                    qrcodeContainer.innerHTML = '';

                    // Buat QRCode
                    currentQRCode = new QRCode(qrcodeContainer, {
                        text: loginUrl,
                        width: 280,
                        height: 280,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });

                    console.log('QR Code generated:', loginUrl);

                    // Tampilkan modal segera setelah QR dibuat
                    // setTimeout kecil tetap aman untuk memberi waktu render pada beberapa browser
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

            // Download QR Code
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
                    // restore debug
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

            // Print QR Code (mendukung canvas atau img)
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

        }); // end DOMContentLoaded
    </script>
@endpush
