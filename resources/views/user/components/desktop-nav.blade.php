<!-- Desktop navigation -->
{{-- File: resources/views/user/components/desktop-nav.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-light bg-white desktop-nav">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('karyawan.dashboard') }}">
            <i class="fas fa-building me-2"></i>
            Sistem Presensi
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}"
                        href="{{ route('karyawan.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('presensi.create', 'presensi.store') ? 'active' : '' }}"
                        href="{{ route('presensi.create') }}">
                        <i class="fas fa-camera"></i>
                        <span>Absen</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.cuti.*') ? 'active' : '' }}"
                        href="{{ route('user.cuti.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Cuti</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('presensi.history') ? 'active' : '' }}"
                        href="{{ route('presensi.history') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('karyawan.profile') ? 'active' : '' }}"
                        href="{{ route('karyawan.profile') }}">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>