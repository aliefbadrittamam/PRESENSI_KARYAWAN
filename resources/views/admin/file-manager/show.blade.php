@extends('layouts.app')

@section('title', 'Detail File')
@section('icon', 'fa-file')

@section('content')
<div class="row g-2 mb-2">
    <!-- Header Navigation -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <nav aria-label="breadcrumb" class="mb-0">
                        <ol class="breadcrumb mb-0 bg-transparent p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.file-manager.index') }}" class="text-white">
                                    <i class="fas fa-home"></i> Root
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.file-manager.index', ['dir' => $directory]) }}" class="text-white">
                                    {{ $directory }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white-50">
                                {{ $fileInfo['name'] }}
                            </li>
                        </ol>
                    </nav>
                    <a href="{{ route('admin.file-manager.index', ['dir' => $directory]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview -->
    <div class="col-md-8">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-image me-2"></i>Preview File
                </h6>
            </div>
            <div class="card-body text-center bg-dark" style="min-height: 400px;">
                @if(in_array(strtolower($fileInfo['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    <img src="{{ $fileInfo['url'] }}" alt="{{ $fileInfo['name'] }}" class="img-fluid" style="max-height: 600px;">
                @elseif(strtolower($fileInfo['extension']) == 'pdf')
                    <iframe src="{{ $fileInfo['url'] }}" style="width: 100%; height: 600px; border: none;"></iframe>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                        @if(in_array(strtolower($fileInfo['extension']), ['doc', 'docx']))
                            <i class="fas fa-file-word fa-5x text-primary mb-3"></i>
                        @elseif(in_array(strtolower($fileInfo['extension']), ['xls', 'xlsx']))
                            <i class="fas fa-file-excel fa-5x text-success mb-3"></i>
                        @else
                            <i class="fas fa-file fa-5x text-secondary mb-3"></i>
                        @endif
                        <p class="text-white-50">Preview tidak tersedia untuk tipe file ini</p>
                        <a href="{{ route('admin.file-manager.download', ['path' => urlencode($fileInfo['path'])]) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Download File
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- File Information -->
    <div class="col-md-4">
        <!-- Basic Info -->
        <div class="card border-0 mb-2">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informasi File
                </h6>
            </div>
            <div class="card-body p-3">
                <table class="table table-sm table-dark mb-0">
                    <tr>
                        <td class="text-white-50" width="40%">Nama File</td>
                        <td class="text-white"><strong>{{ $fileInfo['name'] }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Ukuran</td>
                        <td class="text-white">{{ number_format($fileInfo['size'] / 1024, 2) }} KB</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Format</td>
                        <td><span class="badge bg-info">{{ strtoupper($fileInfo['extension']) }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Tipe MIME</td>
                        <td class="text-white-50 small">{{ $fileInfo['mime_type'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-white-50">Dimodifikasi</td>
                        <td class="text-white">{{ date('d M Y H:i', $fileInfo['modified']) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Additional Info -->
        @if(!empty($additionalInfo))
        <div class="card border-0 mb-2">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Detail Tambahan
                </h6>
            </div>
            <div class="card-body p-3">
                @if($additionalInfo['type'] == 'karyawan')
                    <table class="table table-sm table-dark mb-0">
                        <tr>
                            <td class="text-white-50" width="40%">Nama</td>
                            <td class="text-white"><strong>{{ $additionalInfo['nama_lengkap'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">NIP</td>
                            <td class="text-white">{{ $additionalInfo['nip'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Departemen</td>
                            <td class="text-white">{{ $additionalInfo['departemen'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Jabatan</td>
                            <td class="text-white">{{ $additionalInfo['jabatan'] }}</td>
                        </tr>
                    </table>
                @elseif($additionalInfo['type'] == 'izin')
                    <table class="table table-sm table-dark mb-0">
                        <tr>
                            <td class="text-white-50" width="40%">Tipe</td>
                            <td><span class="badge bg-info">Izin</span></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Jenis Izin</td>
                            <td class="text-white">{{ $additionalInfo['tipe_izin'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Karyawan</td>
                            <td class="text-white"><strong>{{ $additionalInfo['karyawan'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">NIP</td>
                            <td class="text-white">{{ $additionalInfo['nip'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Periode</td>
                            <td class="text-white">
                                {{ \Carbon\Carbon::parse($additionalInfo['tanggal_mulai'])->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($additionalInfo['tanggal_selesai'])->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Keterangan</td>
                            <td class="text-white-50 small">{{ $additionalInfo['keterangan'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Status</td>
                            <td>
                                @if($additionalInfo['status'] == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($additionalInfo['status'] == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                @elseif($additionalInfo['type'] == 'cuti')
                    <table class="table table-sm table-dark mb-0">
                        <tr>
                            <td class="text-white-50" width="40%">Tipe</td>
                            <td><span class="badge bg-warning text-dark">Cuti</span></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Jenis Cuti</td>
                            <td class="text-white">{{ $additionalInfo['jenis_cuti'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Karyawan</td>
                            <td class="text-white"><strong>{{ $additionalInfo['karyawan'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">NIP</td>
                            <td class="text-white">{{ $additionalInfo['nip'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Periode</td>
                            <td class="text-white">
                                {{ \Carbon\Carbon::parse($additionalInfo['tanggal_mulai'])->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($additionalInfo['tanggal_selesai'])->format('d M Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Keterangan</td>
                            <td class="text-white-50 small">{{ $additionalInfo['keterangan'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Status</td>
                            <td>
                                @if($additionalInfo['status'] == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($additionalInfo['status'] == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                @elseif($additionalInfo['type'] == 'presensi')
                    <table class="table table-sm table-dark mb-0">
                        <tr>
                            <td class="text-white-50" width="40%">Tipe Absen</td>
                            <td>
                                <span class="badge {{ $additionalInfo['tipe_absen'] == 'Masuk' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $additionalInfo['tipe_absen'] }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Tanggal</td>
                            <td class="text-white">{{ \Carbon\Carbon::parse($additionalInfo['tanggal'])->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-white-50">Karyawan</td>
                            <td class="text-white"><strong>{{ $additionalInfo['karyawan'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-white-50">NIP</td>
                            <td class="text-white">{{ $additionalInfo['nip'] }}</td>
                        </tr>
                        @if(isset($additionalInfo['jam_masuk']))
                        <tr>
                            <td class="text-white-50">Jam Masuk</td>
                            <td class="text-white">{{ $additionalInfo['jam_masuk'] ?? '-' }}</td>
                        </tr>
                        @endif
                        @if(isset($additionalInfo['jam_keluar']))
                        <tr>
                            <td class="text-white-50">Jam Keluar</td>
                            <td class="text-white">{{ $additionalInfo['jam_keluar'] ?? '-' }}</td>
                        </tr>
                        @endif
                        @if(isset($additionalInfo['status_kehadiran']))
                        <tr>
                            <td class="text-white-50">Status</td>
                            <td>
                                @if($additionalInfo['status_kehadiran'] == 'hadir')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($additionalInfo['status_kehadiran'] == 'terlambat')
                                    <span class="badge bg-warning text-dark">Terlambat</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($additionalInfo['status_kehadiran']) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                @endif
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-cog me-2"></i>Aksi
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ $fileInfo['url'] }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-external-link-alt me-2"></i>Buka di Tab Baru
                    </a>
                    <a href="{{ route('admin.file-manager.download', ['path' => urlencode($fileInfo['path'])]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download me-2"></i>Download File
                    </a>
                    @if($directory != 'foto-karyawan')
                        <button onclick="deleteFile('{{ $fileInfo['path'] }}', '{{ addslashes($fileInfo['name']) }}')" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-2"></i>Hapus File
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.card').hide().fadeIn(400);
});

function deleteFile(path, name) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus file:<br><strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        background: '#2d3236',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.file-manager.delete") }}',
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    path: path
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        background: '#2d3236',
                        color: '#fff'
                    }).then(() => {
                        window.location.href = '{{ route("admin.file-manager.index", ["dir" => $directory]) }}';
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        icon: 'error',
                        background: '#2d3236',
                        color: '#fff'
                    });
                }
            });
        }
    });
}
</script>
@endpush

@push('css')
<style>
    .breadcrumb {
        background-color: transparent !important;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }
    
    .card-body {
        background-color: #2d3236 !important;
    }
    
    .table-dark {
        --bs-table-bg: transparent !important;
    }
    
    .table-dark td {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endpush