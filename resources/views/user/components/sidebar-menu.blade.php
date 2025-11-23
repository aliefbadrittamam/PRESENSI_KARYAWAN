<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">
            <i class="fas fa-bars me-2"></i>
            Menu
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="{{ route('karyawan.dashboard') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home me-3"></i>
                Dashboard
            </a>
            
            <a href="{{ route('presensi.create') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('presensi.create') ? 'active' : '' }}">
                <i class="fas fa-camera me-3"></i>
                Absen Sekarang
            </a>
            
            <a href="{{ route('presensi.history') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('presensi.history') ? 'active' : '' }}">
                <i class="fas fa-history me-3"></i>
                Riwayat Presensi
            </a>
            
            <a href="{{ route('user.izin.create') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('user.izin.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list me-3"></i>
                Ajukan Izin
            </a>
            
            <a href="{{ route('user.cuti.index') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('user.cuti.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-3"></i>
                Cuti
            </a>
            
            <a href="{{ route('karyawan.profile') }}" 
               class="list-group-item list-group-item-action border-0 py-3 {{ request()->routeIs('karyawan.profile') ? 'active' : '' }}">
                <i class="fas fa-user me-3"></i>
                Profil Saya
            </a>
            
            <div class="border-top my-2"></div>
            
            <a href="#" 
               class="list-group-item list-group-item-action border-0 py-3 text-danger"
               onclick="event.preventDefault(); if(confirm('Yakin ingin logout?')) document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-3"></i>
                Logout
            </a>
        </div>
        
        <div class="p-3 text-center text-muted small border-top mt-auto">
            <p class="mb-0">Sistem Presensi v1.0</p>
            <p class="mb-0">&copy; {{ date('Y') }}</p>
        </div>
    </div>
</div>