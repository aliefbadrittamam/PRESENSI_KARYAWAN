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

    /* Container QR Code agar tidak terpotong */
    #qrcodeContainer {
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #qrcodeContainer canvas,
    #qrcodeContainer img {
        max-width: 100%;
        height: auto;
    }
</style>
@endpush

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
                                        <a href="{{ route('karyawan.show', $item->id_karyawan) }}"
                                            class="btn btn-info btn-modern" data-bs-toggle="tooltip" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('karyawan.edit', $item->id_karyawan) }}"
                                            class="btn btn-warning btn-modern" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                            class="btn btn-secondary btn-modern" 
                                            onclick="showQRCodeModal('{{ addslashes($item->nama_lengkap) }}', '{{ $item->nip }}', '{{ $item->user->barcode_token ?? '' }}')"
                                            data-bs-toggle="tooltip" 
                                            title="Lihat QR Code">
                                            <i class="fas fa-qrcode"></i>
                                        </button>

                                        <form action="{{ route('karyawan.destroy', $item->id_karyawan) }}" method="POST"
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
                                    <a href="{{ route('karyawan.create') }}" class="btn btn-primary-modern btn-modern">
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

{{-- <!-- Modal QR Code - DIPINDAHKAN KE LUAR @section --> --}}
<div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrcodeModalLabel">
                    <i class="fas fa-qrcode me-2"></i>QR Code Karyawan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h5 id="namaKaryawan" class="mb-1"></h5>
                <small id="nipKaryawan" class="text-muted d-block mb-3"></small>

                <div id="qrcodeContainer" class="p-3 border rounded bg-light d-inline-block"></div>

                <div class="mt-4">
                    <button class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                    <button class="btn btn-outline-primary me-2" id="printQRCode">
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
<script>
    // Inisialisasi tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inisialisasi Bootstrap Modal
    const qrcodeModalEl = document.getElementById('qrcodeModal');
    let qrcodeModal = null;

    // State
    let currentQRCode = null;

    // Function untuk show modal dengan QR Code
    function showQRCodeModal(nama, nip, qrcodeToken) {
        // Inisialisasi modal jika belum
        if (!qrcodeModal) {
            qrcodeModal = new bootstrap.Modal(qrcodeModalEl, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        }

        // Set data karyawan
        document.getElementById('namaKaryawan').textContent = nama || '—';
        document.getElementById('nipKaryawan').textContent = nip ? ('NIP: ' + nip) : '';

        const qrcodeContainer = document.getElementById('qrcodeContainer');
        qrcodeContainer.innerHTML = '';

        // Validasi token
        if (!qrcodeToken || qrcodeToken.trim() === '') {
            qrcodeContainer.innerHTML = '<div class="text-danger p-3">QR Code token tidak tersedia.<br>Silakan generate token terlebih dahulu.</div>';
            currentQRCode = null;
            qrcodeModal.show();
            return;
        }

        // Generate QR Code
        try {
            currentQRCode = new QRCode(qrcodeContainer, {
                text: qrcodeToken,
                width: 256,
                height: 256,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            // Tunggu sebentar untuk memastikan QR Code sudah di-render
            setTimeout(function() {
                qrcodeModal.show();
            }, 100);
            
        } catch (err) {
            console.error('QRCode error:', err);
            qrcodeContainer.innerHTML = '<div class="text-danger p-3">Gagal generate QR Code.<br>Token tidak valid.</div>';
            currentQRCode = null;
            qrcodeModal.show();
        }
    }

    // Clean up saat modal ditutup
    qrcodeModalEl.addEventListener('hidden.bs.modal', function () {
        const qrcodeContainer = document.getElementById('qrcodeContainer');
        qrcodeContainer.innerHTML = '';
        currentQRCode = null;
    });

    // Download QR Code sebagai PNG
    document.getElementById('downloadQRCode').addEventListener('click', function () {
        const qrcodeContainer = document.getElementById('qrcodeContainer');
        const canvas = qrcodeContainer.querySelector('canvas');
        
        if (!canvas) {
            alert('Tidak ada QR Code untuk diunduh.');
            return;
        }

        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            if (!blob) {
                alert('Gagal mengkonversi QR Code.');
                return;
            }
            
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            const nipText = document.getElementById('nipKaryawan').textContent.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_-]/g, '') || 'karyawan';
            a.download = `qrcode_${nipText}.png`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
        }, 'image/png');
    });

    // Print QR Code
    document.getElementById('printQRCode').addEventListener('click', function () {
        const qrcodeContainer = document.getElementById('qrcodeContainer');
        const canvas = qrcodeContainer.querySelector('canvas');
        
        if (!canvas) {
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

        const qrcodeDataUrl = canvas.toDataURL('image/png');

        printWindow.document.open();
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
                <head>
                    <title>Cetak QR Code</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        body { 
                            font-family: Arial, sans-serif; 
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            padding: 20px;
                        }
                        .box { 
                            text-align: center;
                            padding: 30px; 
                            border: 2px solid #ddd;
                            border-radius: 10px;
                            background: white;
                            max-width: 400px;
                        }
                        img { 
                            width: 256px;
                            height: 256px;
                            margin: 15px auto;
                            display: block;
                        }
                        h3 { 
                            margin: 0 0 8px 0; 
                            font-size: 20px;
                            color: #333;
                        }
                        p { 
                            margin: 0 0 20px 0; 
                            color: #666;
                            font-size: 14px;
                        }
                        @media print {
                            body {
                                padding: 0;
                            }
                            .box {
                                border: none;
                                box-shadow: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="box">
                        <h3>${nama}</h3>
                        <p>${nip}</p>
                        <img src="${qrcodeDataUrl}" alt="QR Code">
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function(){ 
                                window.print();
                                // Optional: close window after print
                                // setTimeout(function(){ window.close(); }, 500);
                            }, 300);
                        }
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    });
</script>
@endpush