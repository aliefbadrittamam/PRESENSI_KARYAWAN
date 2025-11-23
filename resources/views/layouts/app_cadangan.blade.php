<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi UNI - @yield('title')</title>
    
    <!-- Bootstrap 5 Dark Theme -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-dark-5/1.1.3/bootstrap-dark.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry"></script>
    
    <style>
        /* Tambahan style untuk camera */
        #cameraPreview {
            width: 100%;
            height: 300px;
            background: #2c3e50;
            border-radius: 10px;
            overflow: hidden;
        }
        
        #capturedImage {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
        }
        
        .webcam-container {
            position: relative;
        }
        
        .capture-btn {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }
        
        #map {
            height: 300px;
            width: 100%;
            border-radius: 10px;
        }
        
        .location-status {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        
        .location-acquired {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .location-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    
        :root {
            --primary-blue: #1a73e8;
            --dark-blue: #0d47a1;
            --darker-blue: #0a2e6b;
            --light-blue: #e8f0fe;
            --accent-blue: #4285f4;
        }
        
        .bg-primary-blue {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)) !important;
        }
        
        .bg-dark-blue {
            background-color: var(--darker-blue) !important;
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .nav-link {
            color: #e9ecef !important;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1) !important;
            color: white !important;
            transform: translateX(5px);
        }
        
        .card-modern {
            background: linear-gradient(145deg, #2c3e50, #34495e);
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .btn-modern {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            border: none;
        }
        
        .btn-primary-modern:hover {
            background: linear-gradient(135deg, var(--dark-blue), var(--darker-blue));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
        }
        
        .table-modern {
            background: #2c3e50;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table-modern th {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
            font-weight: 600;
            padding: 15px 12px;
        }
        
        .table-modern td {
            border-color: #34495e;
            padding: 12px;
            vertical-align: middle;
        }
        
        .table-modern tbody tr {
            transition: background-color 0.3s ease;
        }
        
        .table-modern tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .badge-modern {
            border-radius: 6px;
            font-weight: 500;
            padding: 6px 12px;
        }
        
        .form-control-modern {
            background-color: #34495e;
            border: 1px solid #4a6572;
            border-radius: 8px;
            color: #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
            background-color: #2c3e50;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(26, 115, 232, 0.25);
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(145deg, #3498db, #2980b9);
            border-radius: 12px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .collapse .nav-link {
            padding-left: 2rem !important;
            font-size: 0.9rem;
        }

        .collapse .nav-link:hover {
            background-color: rgba(255,255,255,0.05) !important;
            transform: translateX(3px);
        }
    </style>
</head>
<body class="d-flex bg-dark">
    <!-- Sidebar -->
    <div class="sidebar col-md-3 col-lg-2 p-0">
        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white">
            <!-- Logo -->
            <div class="d-flex align-items-center pb-4 mb-4 border-bottom w-100">
                <i class="fas fa-fingerprint fa-2x me-2 text-warning"></i>
                <span class="fs-4 fw-bold">Presensi UNI</span>
            </div>

            <!-- Navigation -->
            <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100" id="menu">
                <li class="nav-item w-100">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link px-3 align-middle">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <!-- Presensi Dropdown -->
                <li class="nav-item w-100">
                    <a class="nav-link px-3 align-middle dropdown-toggle" data-bs-toggle="collapse" href="#presensiCollapse" role="button">
                        <i class="fas fa-calendar-check me-2"></i> Presensi
                    </a>
                    <div class="collapse" id="presensiCollapse">
                        <ul class="nav nav-pills flex-column ms-3">
                            <li class="nav-item w-100">
                                <a href="{{ route('admin.presensi.index') }}" class="nav-link px-3 align-middle">
                                    <i class="fas fa-list me-2"></i> Data Presensi
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="{{ route('presensi.create') }}" class="nav-link px-3 align-middle">
                                    <i class="fas fa-plus me-2"></i> Tambah Presensi
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="{{ route('admin.shift.index') }}" class="nav-link px-3 align-middle">
                                    <i class="fas fa-clock me-2"></i> Shift Kerja
                                </a>
                            </li>
                            <li class="nav-item w-100">
                                <a href="{{ route('lokasi.index') }}" class="nav-link px-3 align-middle">
                                    <i class="fas fa-map-marker-alt me-2"></i> Lokasi Presensi
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item w-100">
                    <a href="{{ route('admin.fakultas.index') }}" class="nav-link px-3 align-middle">
                        <i class="fas fa-university me-2"></i> Fakultas
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a href="{{ route('admin.departemen.index') }}" class="nav-link px-3 align-middle">
                        <i class="fas fa-building me-2"></i> Departemen
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a href="{{ route('admin.jabatan.index') }}" class="nav-link px-3 align-middle">
                        <i class="fas fa-briefcase me-2"></i> Jabatan
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a href="{{ route('admin.karyawan.index') }}" class="nav-link px-3 align-middle">
                        <i class="fas fa-users me-2"></i> Karyawan
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 ms-sm-auto px-4 py-3 bg-dark">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h4 class="text-white mb-0">
                <i class="fas @yield('icon', 'fa-cog') me-2"></i>
                @yield('title', 'Dashboard')
            </h4>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" 
                   id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/mdo.png" alt="hugenerd" width="32" height="32" class="rounded-circle me-2">
                    <span class="d-none d-sm-inline">
                        {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    @auth
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                    @else
                    <li>
                        <a class="dropdown-item" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>

        <!-- Content -->
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>