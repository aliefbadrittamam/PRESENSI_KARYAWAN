{{-- File: resources/views/user/components/attendance-summary.blade.php --}}
<section class="attendance-section">
    <div class="section-header">
        <h2 class="section-title">Absensi Bulan</h2>
        <select class="month-selector" id="monthFilter">
            @foreach($months as $monthData)
                <option value="{{ $monthData['value'] }}" {{ $monthData['selected'] ? 'selected' : '' }} class="text-black border-2">
                    {{ $monthData['label'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="attendance-grid">
        <div class="attendance-item">
            <div class="attendance-icon icon-hadir">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="attendance-info">
                <h6 class="mb-0">Hadir</h6>
                <div class="attendance-count">{{ $rekapBulan->jumlah_hadir ?? 0 }} Hari</div>
            </div>
        </div>

        <div class="attendance-item">
            <div class="attendance-icon icon-izin-status">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="attendance-info">
                <h6 class="mb-0">Izin</h6>
                <div class="attendance-count">{{ $rekapBulan->jumlah_izin ?? 0 }} Hari</div>
            </div>
        </div>

        <div class="attendance-item">
            <div class="attendance-icon icon-sakit">
                <i class="fas fa-user-injured"></i>
            </div>
            <div class="attendance-info">
                <h6 class="mb-0">Sakit</h6>
                <div class="attendance-count">{{ $rekapBulan->jumlah_sakit ?? 0 }} Hari</div>
            </div>
        </div>

        <div class="attendance-item">
            <div class="attendance-icon icon-terlambat">
                <i class="fas fa-clock"></i>
            </div>
            <div class="attendance-info">
                <h6 class="mb-0">Terlambat</h6>
                <div class="attendance-count">{{ $rekapBulan->jumlah_terlambat ?? 0 }} hari</div>
            </div>
        </div>
    </div>
</section>