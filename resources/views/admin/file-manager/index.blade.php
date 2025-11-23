@extends('layouts.app')

@section('title', 'File Manager')
@section('icon', 'fa-folder-open')

@section('content')
<div class="row g-2 mb-2">
    <!-- Filter & Search Card -->
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter & Pencarian File
                    </h6>
                    @if(!empty($directory))
                        <a href="{{ route('admin.file-manager.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Root
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.file-manager.index') }}" method="GET" id="filterForm">
                    @if(!empty($directory))
                        <input type="hidden" name="dir" value="{{ $directory }}">
                    @endif
                    <div class="row g-2">
                        <!-- Search -->
                        <div class="col-md-5">
                            <label class="form-label text-white small mb-1">Cari Nama File</label>
                            <input type="text" name="search" class="form-control form-control-sm bg-dark text-white border-secondary" 
                                   placeholder="Masukkan nama file..." value="{{ $search }}">
                        </div>

                        <!-- File Type Filter -->
                        <div class="col-md-3">
                            <label class="form-label text-white small mb-1">Tipe File</label>
                            <select name="type" class="form-select form-select-sm bg-dark text-white border-secondary">
                                <option value="">Semua Tipe</option>
                                <option value="image" {{ $fileType == 'image' ? 'selected' : '' }}>Gambar (jpg, png, gif, webp)</option>
                                <option value="document" {{ $fileType == 'document' ? 'selected' : '' }}>Dokumen (pdf, doc, xls)</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100 me-1">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('admin.file-manager.index', !empty($directory) ? ['dir' => $directory] : []) }}" class="btn btn-secondary btn-sm w-100">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Storage Usage Card (Only show on root) -->
    @if(empty($directory) && !empty($storageUsage))
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Penggunaan Storage
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white border-0">
                            <div class="card-body text-center">
                                <h3 class="mb-1">{{ $storageUsage['total_formatted'] }}</h3>
                                <p class="mb-0 small">Total Storage</p>
                            </div>
                        </div>
                    </div>
                    @foreach($storageUsage['directories'] as $dir)
                    <div class="col-md-3">
                        <div class="card bg-dark text-white border-secondary">
                            <div class="card-body">
                                <h6 class="mb-1">{{ $dir['name'] }}</h6>
                                <p class="mb-1 text-white-50 small">{{ $dir['formatted'] }}</p>
                                <p class="mb-0 text-muted small">{{ $dir['files_count'] }} file(s)</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Breadcrumb -->
    @if(!empty($directory))
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-dark mb-0 p-2">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.file-manager.index') }}" class="text-white">
                        <i class="fas fa-home"></i> Root
                    </a>
                </li>
                <li class="breadcrumb-item active text-white-50" aria-current="page">
                    <i class="fas fa-folder"></i> {{ $directory }}
                </li>
            </ol>
        </nav>
    </div>
    @endif

    <!-- Directories List (Only show on root) -->
    @if(empty($directory) && count($directories) > 0)
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <h6 class="mb-0">
                    <i class="fas fa-folder me-2"></i>Direktori ({{ count($directories) }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($directories as $dir)
                    <a href="{{ route('admin.file-manager.index', ['dir' => $dir['path']]) }}" 
                       class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-folder fa-2x text-warning me-3"></i>
                            <strong>{{ $dir['name'] }}</strong>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info">{{ $dir['files_count'] }} file(s)</span>
                            <span class="badge bg-secondary">{{ number_format($dir['size'] / 1024 / 1024, 2) }} MB</span>
                            <small class="text-muted d-block">{{ date('d M Y H:i', $dir['modified']) }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Files List -->
    @if(count($files) > 0)
    <div class="col-12">
        <div class="card border-0">
            <div class="card-header bg-dark text-white border-0 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-file me-2"></i>File ({{ count($files) }})
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-white">Preview</th>
                                <th class="text-white">Nama File</th>
                                @if($directory == 'foto-karyawan')
                                    <th class="text-white">Nama Karyawan</th>
                                    <th class="text-white">NIP</th>
                                @endif
                                @if($directory == 'FilePendukung')
                                    <th class="text-white">Tipe</th>
                                    <th class="text-white">Keterangan</th>
                                @endif
                                @if($directory == 'FotoPresensi')
                                    <th class="text-white">Tanggal</th>
                                    <th class="text-white">Tipe Absen</th>
                                    <th class="text-white">Nama Karyawan</th>
                                @endif
                                <th class="text-white">Ukuran</th>
                                <th class="text-white">Format</th>
                                <th class="text-white">Tanggal Modified</th>
                                <th class="text-center text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            @php
                                // Parse info dari nama file
                                $fileName = $file['name'];
                                $namaKaryawan = '';
                                $nip = '';
                                $tanggalAbsen = '';
                                $tipeAbsen = '';
                                $tipeFile = '';
                                $keterangan = '';
                                
                                // Bersihkan URL dari domain dan duplikasi
                                $filePath = $file['path'];
                                // Hapus semua URL/domain yang ada
                                $filePath = preg_replace('#https?://[^/]+/#', '', $filePath);
                                // Hapus duplikasi public/
                                $filePath = preg_replace('#(public/)+#', 'public/', $filePath);
                                // Pastikan dimulai dengan public/
                                if (!str_starts_with($filePath, 'public/')) {
                                    $filePath = 'public/' . ltrim($filePath, '/');
                                }
                                // Generate URL yang benar
                                $fileUrl = '/' . $filePath;
                                
                                // Untuk foto-karyawan: format bisa varied
                                if($directory == 'foto-karyawan') {
                                    // Cari di database berdasarkan nama file
                                    $karyawan = \App\Models\Karyawan::where('foto', 'like', '%'.$fileName)->first();
                                    if($karyawan) {
                                        $namaKaryawan = $karyawan->nama_lengkap;
                                        $nip = $karyawan->nip;
                                    }
                                }
                                
                                // Untuk FilePendukung: format DD-MM-YYYY_HHMMSS_NamaFile(Keterangan).ext
                                if($directory == 'FilePendukung') {
                                    if(preg_match('/\d{2}-\d{2}-\d{4}_\d{6}_(.+?)\((.+?)\)/', $fileName, $matches)) {
                                        $keterangan = $matches[2] ?? '';
                                        // Cek di database izin atau cuti
                                        $izin = \App\Models\Izin::where('file_pendukung', 'like', '%'.$fileName)->first();
                                        $cuti = \App\Models\Cuti::where('file_pendukung', 'like', '%'.$fileName)->first();
                                        
                                        if($izin) {
                                            $tipeFile = 'Izin';
                                        } elseif($cuti) {
                                            $tipeFile = 'Cuti';
                                        }
                                    }
                                }
                                
                                // Untuk FotoPresensi: format masuk_YYYY-MM-DD_HHMMSS.jpg atau keluar_...
                                // atau DD-MM-YYYY_HHMMSS_NamaKaryawan(NIP).jpg
                                if($directory == 'FotoPresensi') {
                                    if(preg_match('/(masuk|keluar)_(\d{4}-\d{2}-\d{2})_\d{6}/', $fileName, $matches)) {
                                        $tipeAbsen = ucfirst($matches[1]);
                                        $tanggalAbsen = \Carbon\Carbon::parse($matches[2])->format('d M Y');
                                    } elseif(preg_match('/(\d{2}-\d{2}-\d{4})_\d{6}_(.+?)\((.+?)\)/', $fileName, $matches)) {
                                        $tanggalAbsen = \Carbon\Carbon::createFromFormat('d-m-Y', $matches[1])->format('d M Y');
                                        $namaKaryawan = $matches[2];
                                        $nip = $matches[3];
                                        
                                        // Deteksi tipe dari folder atau nama
                                        if(strpos($file['path'], '/Masuk/') !== false || strpos($fileName, 'Masuk') !== false) {
                                            $tipeAbsen = 'Masuk';
                                        } elseif(strpos($file['path'], '/Keluar/') !== false || strpos($fileName, 'Keluar') !== false) {
                                            $tipeAbsen = 'Keluar';
                                        }
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if(in_array(strtolower($file['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <a href="{{ route('admin.file-manager.show', ['path' => urlencode($file['path'])]) }}">
                                            <img src="{{ $fileUrl }}" alt="{{ $file['name'] }}" 
                                                 class="img-thumbnail"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    @elseif(strtolower($file['extension']) == 'pdf')
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                    @elseif(in_array(strtolower($file['extension']), ['doc', 'docx']))
                                        <i class="fas fa-file-word fa-2x text-primary"></i>
                                    @elseif(in_array(strtolower($file['extension']), ['xls', 'xlsx']))
                                        <i class="fas fa-file-excel fa-2x text-success"></i>
                                    @else
                                        <i class="fas fa-file fa-2x text-secondary"></i>
                                    @endif
                                </td>
                                <td class="text-white">
                                    <strong>{{ $file['name'] }}</strong>
                                </td>
                                @if($directory == 'foto-karyawan')
                                    <td class="text-white-50">{{ $namaKaryawan ?: '-' }}</td>
                                    <td class="text-white-50">{{ $nip ?: '-' }}</td>
                                @endif
                                @if($directory == 'FilePendukung')
                                    <td class="text-white-50">
                                        @if($tipeFile)
                                            <span class="badge {{ $tipeFile == 'Izin' ? 'bg-info' : 'bg-warning' }}">{{ $tipeFile }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-white-50">{{ $keterangan ?: '-' }}</td>
                                @endif
                                @if($directory == 'FotoPresensi')
                                    <td class="text-white-50">{{ $tanggalAbsen ?: '-' }}</td>
                                    <td class="text-white-50">
                                        @if($tipeAbsen)
                                            <span class="badge {{ $tipeAbsen == 'Masuk' ? 'bg-success' : 'bg-danger' }}">{{ $tipeAbsen }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-white-50">{{ $namaKaryawan ?: '-' }}</td>
                                @endif
                                <td class="text-white-50">{{ number_format($file['size'] / 1024, 2) }} KB</td>
                                <td class="text-white-50">
                                    <span class="badge bg-info">{{ strtoupper($file['extension']) }}</span>
                                </td>
                                <td class="text-white-50">{{ date('d M Y H:i', $file['modified']) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.file-manager.show', ['path' => urlencode($file['path'])]) }}" 
                                       class="btn btn-primary btn-sm me-1" data-bs-toggle="tooltip" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.file-manager.download', ['path' => urlencode($file['path'])]) }}" 
                                       class="btn btn-success btn-sm me-1" data-bs-toggle="tooltip" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @if($directory != 'foto-karyawan')
                                        <button class="btn btn-danger btn-sm" onclick="deleteFile('{{ $file['path'] }}', '{{ addslashes($file['name']) }}')" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @elseif(!empty($directory))
    <div class="col-12">
        <div class="card border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h5 class="text-white">Folder Kosong</h5>
                <p class="text-muted">Tidak ada file di direktori ini</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.card').hide().fadeIn(400);
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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
                        location.reload();
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
    .form-control, .form-select {
        background-color: #2d3236 !important;
        border-color: #4a5056 !important;
        color: #fff !important;
    }
    
    .form-control:focus, .form-select:focus {
        background-color: #343a40 !important;
        border-color: #007bff !important;
        color: #fff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    
    .form-select option {
        background-color: #2d3236 !important;
        color: #fff !important;
    }
    
    .border-secondary {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    .list-group-item {
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        transform: translateX(5px);
        background-color: #343a40 !important;
    }
    
    .breadcrumb {
        background-color: #2d3236 !important;
        border-radius: 0.25rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.5);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    table tbody tr {
        transition: all 0.2s ease;
    }
    
    table tbody tr:hover {
        background-color: #343a40 !important;
    }
</style>
@endpush