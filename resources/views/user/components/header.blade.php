{{-- File: resources/views/user/components/header.blade.php --}}
<header class="header">
    <div class="d-flex justify-content-between align-items-center">
        <button class="menu-icon" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="fas fa-bars"></i>
        </button>
        
        <a href="{{ route('karyawan.profile') }}" class="profile-icon">
            @if($karyawan->foto)
                <img src="{{ asset('public/' . $karyawan->foto) }}" 
                     alt="{{ $karyawan->nama_lengkap }}"
                     class="img-fluid">
            @else
                <i class="fas fa-user"></i>
            @endif
        </a>
    </div>
</header>