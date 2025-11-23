@extends('layouts.app')

@section('title', 'Daftar Lokasi Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-map-marker-alt"></i> Daftar Lokasi Presensi
                        </h4>
                        <a href="{{ route('admin.lokasi-presensi.create') }}" class="btn btn-light">
                            <i class="fas fa-plus"></i> Tambah Lokasi
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="lokasiTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Lokasi</th>
                                    <th>Jenis</th>
                                    <th>Fakultas</th>
                                    <th>Koordinat</th>
                                    <th>Radius</th>
                                    <th>Waktu Operasional</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lokasiList as $index => $lokasi)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $lokasi->nama_lokasi }}</strong>
                                        @if($lokasi->keterangan)
                                        <br><small class="text-muted">{{ Str::limit($lokasi->keterangan, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst($lokasi->jenis_lokasi) }}
                                        </span>
                                    </td>
                                    <td>{{ $lokasi->fakultas->nama_fakultas ?? '-' }}</td>
                                    <td>
                                        <small>
                                            <strong>Lat:</strong> {{ $lokasi->latitude }}<br>
                                            <strong>Long:</strong> {{ $lokasi->longitude }}
                                        </small>
                                        <br>
                                        <a href="https://www.google.com/maps?q={{ $lokasi->latitude }},{{ $lokasi->longitude }}" 
                                           target="_blank" class="btn btn-xs btn-outline-primary mt-1">
                                            <i class="fas fa-map"></i> Lihat Map
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $lokasi->radius_meter }} m</span>
                                    </td>
                                    <td>
                                        @if($lokasi->waktu_operasional_mulai && $lokasi->waktu_operasional_selesai)
                                        <small>
                                            {{ substr($lokasi->waktu_operasional_mulai, 0, 5) }} - 
                                            {{ substr($lokasi->waktu_operasional_selesai, 0, 5) }}
                                        </small>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($lokasi->status_aktif)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.lokasi-presensi.show', $lokasi->id_lokasi) }}" 
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.lokasi-presensi.edit', $lokasi->id_lokasi) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $lokasi->id_lokasi }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-{{ $lokasi->id_lokasi }}" 
                                              action="{{ route('admin.lokasi-presensi.destroy', $lokasi->id_lokasi) }}" 
                                              method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada lokasi presensi.</p>
                                        <a href="{{ route('admin.lokasi-presensi.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Lokasi Pertama
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Koordinat GPS:</strong> Dapat diambil dari aplikasi maps atau GPS device</li>
                        <li><strong>Radius:</strong> Jarak maksimal dalam meter dari titik koordinat yang masih dianggap valid untuk presensi</li>
                        <li><strong>Jenis Lokasi:</strong> Kategori lokasi (kantor, gedung, lab, dll)</li>
                        <li><strong>Waktu Operasional:</strong> Jam buka/tutup lokasi (opsional)</li>
                        <li><strong>Status Aktif:</strong> Hanya lokasi aktif yang dapat digunakan untuk presensi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#lokasiTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});

function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush