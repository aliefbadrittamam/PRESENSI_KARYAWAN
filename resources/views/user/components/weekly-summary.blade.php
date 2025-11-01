{{-- File: resources/views/user/components/weekly-summary.blade.php --}}
<div class="summary-card">
    <h3 class="summary-title">1 Minggu Terakhir</h3>
    
    @if($presensiMingguIni->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="fw-semibold">Tanggal</th>
                        <th scope="col" class="fw-semibold">Masuk</th>
                        <th scope="col" class="fw-semibold">Keluar</th>
                        <th scope="col" class="fw-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($presensiMingguIni as $presensi)
                        <tr>
                            <td class="fw-medium">
                                {{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($presensi->jam_masuk)
                                    <span class="badge bg-success-subtle text-success">
                                        {{ date('H:i', strtotime($presensi->jam_masuk)) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($presensi->jam_keluar)
                                    <span class="badge bg-info-subtle text-info">
                                        {{ date('H:i', strtotime($presensi->jam_keluar)) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($presensi->status_kehadiran) {
                                        'hadir' => 'success',
                                        'terlambat' => 'warning',
                                        'izin' => 'info',
                                        'sakit' => 'secondary',
                                        'cuti' => 'primary',
                                        'alpha' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    {{ ucfirst($presensi->status_kehadiran) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times d-block mb-3"></i>
            <p class="mb-0">Belum ada data absensi minggu ini</p>
        </div>
    @endif
</div>