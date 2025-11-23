{{-- File: resources/views/user/components/bottom-nav.blade.php --}}
<nav class="bottom-nav">
    <div class="nav-items">
        <a href="{{ route('karyawan.dashboard') }}" 
           class="nav-item {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span class="nav-label">Home</span>
        </a>
        
        <a href="{{ route('presensi.create') }}" 
           class="nav-item {{ request()->routeIs('presensi.create') ? 'active' : '' }}">
            <i class="fas fa-camera"></i>
            <span class="nav-label">Absen</span>
        </a>
        
        <a href="{{ route('user.cuti.index') }}" 
           class="nav-item {{ request()->routeIs('user.cuti.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span class="nav-label">Cuti</span>
        </a>
        
        <a href="{{ route('presensi.history') }}" 
           class="nav-item {{ request()->routeIs('presensi.history') ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span class="nav-label">History</span>
        </a>
        
        <a href="{{ route('karyawan.profile') }}" 
           class="nav-item {{ request()->routeIs('karyawan.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span class="nav-label">Profil</span>
        </a>
    </div>
</nav>