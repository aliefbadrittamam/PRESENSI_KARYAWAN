<!-- Welcome card with actions -->
{{-- File: resources/views/user/components/welcome-card.blade.php --}}
<div class="welcome-card">
    <div class="row align-items-start g-3">
        <div class="col-7">
            <p class="greeting mb-1">Selamat {{ $greeting }}</p>
            <h1 class="user-name">{{ $karyawan->nama_lengkap }}</h1>
        </div>
        <div class="col-5">
            <div class="work-time">
                <div class="work-time-label">Jam Kerja</div>
                @if ($shift)
                    <div class="time-range">
                        {{ date('H:i', strtotime($shift->jam_mulai)) }} -
                        {{ date('H:i', strtotime($shift->jam_selesai)) }}
                    </div>
                @else
                    <div class="time-range">-</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Menu -->
   <div class="action-menu">
    {{-- Absen --}}
    <a href="{{ route('presensi.create') }}" class="action-item">
        <div class="action-icon icon-absen">
            <i class="fas fa-camera"></i>
        </div>
        <div class="action-label">Absen</div>
    </a>

    {{-- Izin --}}
    <a href="{{ route('user.izin.index') }}" class="action-item">
        <div class="action-icon icon-izin">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="action-label">Izin</div>
    </a>

    {{-- Cuti --}}
    <a href="{{ route('user.cuti.index') }}" class="action-item">
        <div class="action-icon icon-cuti">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="action-label">Cuti</div>
    </a>

    {{-- History --}}
    <a href="{{ route('presensi.history') }}" class="action-item">
        <div class="action-icon icon-history">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="action-label">History</div>
    </a>

    {{-- Profil --}}
    <a href="{{ route('karyawan.profile') }}" class="action-item">
        <div class="action-icon icon-profil">
            <i class="fas fa-user"></i>
        </div>
        <div class="action-label">Profil</div>
    </a>
</div>
  
</div>
