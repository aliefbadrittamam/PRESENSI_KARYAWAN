{{-- File: resources/views/user/components/status-cards.blade.php --}}
<div class="status-cards">
    <div class="status-card {{ $presensiHariIni && $presensiHariIni->jam_masuk ? 'present' : 'status-card-masuk' }}">
        <div class="status-label">Absen Masuk</div>
        <div class="status-value">
            @if($presensiHariIni && $presensiHariIni->jam_masuk)
                {{ date('H:i', strtotime($presensiHariIni->jam_masuk)) }}
            @else
                Belum absen
            @endif
        </div>
    </div>
    
    <div class="status-card {{ $presensiHariIni && $presensiHariIni->jam_keluar ? 'present' : 'status-card-pulang' }}">
        <div class="status-label">Absen Pulang</div>
        <div class="status-value">
            @if($presensiHariIni && $presensiHariIni->jam_keluar)
                {{ date('H:i', strtotime($presensiHariIni->jam_keluar)) }}
            @else
                Belum Absen
            @endif
        </div>
    </div>
</div>