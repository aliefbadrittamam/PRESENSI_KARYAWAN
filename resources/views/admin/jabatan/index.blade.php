@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>📋 Data Jabatan</h4>
            <a href="{{ route('admin.jabatan.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Jabatan
            </a>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary text-center">
    <tr>
        <th width="5%">#</th>
        <th>Kode Jabatan</th>
        <th>Nama Jabatan</th>
        <th>Jenis Jabatan</th>
        <th>Keterangan</th>
        <th width="15%">Aksi</th>
    </tr>
                </thead>
                <tbody>
                    @forelse($jabatan as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->kode_jabatan }}</td>
                            <td>{{ $item->nama_jabatan }}</td>
                            <td>{{ ucfirst($item->jenis_jabatan) }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.jabatan.edit', $item->id_jabatan) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.jabatan.destroy', $item->id_jabatan) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus jabatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data jabatan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end mt-3">
                {{ $jabatan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
