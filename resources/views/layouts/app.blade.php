<!DOCTYPE html>
<html lang="id">

<head>
    <style>

    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presensi UNI - @yield('title', 'home')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AdminLTE CSS -->
    {{-- <link rel="stylesheet" href="{{ secure_asset('vendor/adminlte/dist/css/adminlte.min.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- WebcamJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <!-- Google Maps -->
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=geometry"></script> --}}

    <style>
        /* AdminLTE3 Color Scheme */
        :root {
            --primary: #007bff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --sidebar-dark-bg: #343a40;
            --sidebar-dark-hover-bg: rgba(255, 255, 255, .1);
            --sidebar-dark-color: #c2c7d0;
            --sidebar-dark-active-color: #fff;
        }

        /* Sidebar Customization */
        .main-sidebar {
            background-color: var(--sidebar-dark-bg) !important;
        }

        .brand-link {
            border-bottom: 1px solid #4b545c;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: var(--primary);
            color: white;
        }

        .nav-sidebar>.nav-item>.nav-link {
            color: var(--sidebar-dark-color);
        }

        .nav-sidebar>.nav-item>.nav-link:hover {
            background-color: var(--sidebar-dark-hover-bg);
            color: var(--sidebar-dark-active-color);
        }

        /* Improved Icon Styling */
        .nav-icon {
            width: 1.5rem;
            text-align: center;
            margin-right: 0.5rem;
        }

        .nav-treeview .nav-icon {
            font-size: 0.8rem;
            width: 1.2rem;
        }

        /* Logout Button Styling */
        .sidebar-logout {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            padding: 0 10px;
        }

        .sidebar-logout .nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            background-color: var(--danger);
            border-radius: 5px;
            margin: 0 5px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .sidebar-logout .nav-link:hover {
            background-color: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .sidebar-logout .nav-icon {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Adjust sidebar content to accommodate logout button */
        .sidebar {
            padding-bottom: 70px;
        }

        /* Fix Dropdown Behavior */
        .dropdown-menu {
            z-index: 1030 !important;
        }

        .navbar-nav .dropdown-menu {
            position: absolute !important;
        }

        /* Card Modern Styling */
        .card-modern {
            border: none;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .card-modern .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            background-color: rgba(0, 0, 0, .03);
        }

        /* Button Modern */
        .btn-modern {
            border-radius: .25rem;
        }

        /* Table Modern */
        .table-modern {
            border-radius: .25rem;
            overflow: hidden;
        }

        .table-modern th {
            background-color: rgba(0, 0, 0, .03);
            border-bottom: 2px solid #dee2e6;
        }

        /* Form Control Modern */
        .form-control-modern {
            border-radius: .25rem;
        }

        /* Stats Card */
        .stats-card {
            border-radius: .25rem;
            padding: 20px;
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

        /* Badge Modern */
        .badge-modern {
            border-radius: .25rem;
            padding: .375rem .75rem;
        }

        /* Camera Styles */
        #cameraPreview {
            width: 100%;
            height: 300px;
            border-radius: .25rem;
            overflow: hidden;
        }

        #capturedImage {
            max-width: 100%;
            max-height: 300px;
            border-radius: .25rem;
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

        /* Map Styles */
        #map {
            height: 300px;
            width: 100%;
            border-radius: .25rem;
        }

        .location-status {
            padding: 10px;
            border-radius: .25rem;
            margin-bottom: 10px;
        }

        /* User Panel Customization */
        .user-panel .info {
            color: var(--sidebar-dark-color);
        }

        .user-panel .info a {
            color: var(--sidebar-dark-active-color);
        }

        /* Content Background */
        .content-wrapper {
            background-color: #f4f6f9;
        }

        /* Breadcrumb Customization */
        .breadcrumb {
            background-color: transparent;
        }

        /* Alert Customization */
        .alert {
            border-radius: .25rem;
        }

        /* Nav Treeview Customization */
        .nav-treeview .nav-link {
            padding-left: 3rem !important;
        }

        /* Brand Logo Custom */
        .brand-text {
            font-weight: 400;
        }

        /* Main Header Custom */
        .navbar-white {
            background-color: #fff !important;
            border-bottom: 1px solid #dee2e6;
        }

        /* Dark Mode Adjustments */
        .dark-mode .content-wrapper {
            background-color: #343a40;
        }

        .dark-mode .card {
            background-color: #454d55;
            color: #fff;
            border-color: #4b545c;
        }

        .dark-mode .card-header {
            background-color: rgba(255, 255, 255, .03);
            border-bottom-color: #4b545c;
        }

        .dark-mode .table {
            color: #fff;
        }

        .dark-mode .table th,
        .dark-mode .table td {
            border-color: #4b545c;
        }

        .dark-mode .form-control {
            background-color: #454d55;
            border-color: #4b545c;
            color: #fff;
        }

        .dark-mode .breadcrumb-item.active {
            color: #adb5bd;
        }

        .dark-mode .content-header h1 {
            color: #fff;
        }

        .dark-mode .main-footer {
            background-color: #343a40;
            border-top: 1px solid #4b545c;
            color: #fff;
        }

        .dark-mode .main-header.navbar {
            background-color: #2f3237 !important;
            border-bottom: 1px solid #3a3d41 !important;
        }

        .dark-mode .main-header .nav-link,
        .dark-mode .navbar-nav .nav-item>a {
            color: #ffffff !important;
        }

        .dark-mode .main-header .nav-link:hover {
            background-color: #3a3d41 !important;
            color: #ffffff !important;
        }

        .dark-mode .navbar .dropdown-menu {
            background-color: #2f3237 !important;
            border: 1px solid #3a3d41 !important;
        }

        .dark-mode .navbar .dropdown-item {
            color: #ffffff !important;
        }

        .dark-mode .navbar .dropdown-item:hover {
            background-color: #3a3d41 !important;
            color: #ffffff !important;
        }
    </style>

    @stack('css')
</head>

<body class="hold-transition sidebar-mini layout-fixed dark-mode">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <i class="fas fa-fingerprint fa-3x text-warning animation__shake"></i>
            <div class="mt-2 text-muted">Loading Presensi UNI...</div>
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <i class="far fa-bell"></i>
                        <span class="badge badge-warning navbar-badge">15</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-users mr-2"></i> 8 new presensi
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                    </div>
                </li>

                <!-- User Account Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                        <i class="far fa-user"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Guest' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header text-center">
                            <div class="image">
                                <img src="https://github.com/mdo.png" alt="User Avatar"
                                    class="img-size-50 img-circle mr-3">
                            </div>
                            <p class="mb-0 mt-2">{{ Auth::user()->name ?? 'Guest' }}</p>
                            <small>{{ Auth::user()->email ?? '' }}</small>
                        </span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        @auth
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @else
                            <a class="dropdown-item" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt mr-2"></i> Login
                            </a>
                        @endauth
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <i class="fas fa-fingerprint brand-image img-circle elevation-3"></i>
                <span class="brand-text font-weight-light">Presensi UNI</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="https://github.com/mdo.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name ?? 'Guest' }}</a>
                        <small class="text-success">
                            <i class="fas fa-circle text-success mr-1"></i>
                            Online
                        </small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}"
                                class="nav-link {{ Request::is('admin/dashboard') || Request::is('admin/home') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Master Data -->
                        <li
                            class="nav-item {{ Request::is('admin/fakultas*', 'admin/departemen*', 'admin/jabatan*', 'admin/karyawan*') ? 'menu-open' : '' }}">
                            <a href="#"
                                class="nav-link {{ Request::is('admin/fakultas*', 'admin/departemen*', 'admin/jabatan*', 'admin/karyawan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-database"></i>
                                <p>
                                    Master Data
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('admin.fakultas.index') }}"
                                        class="nav-link {{ Request::is('admin/fakultas*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-university"></i>
                                        <p>Fakultas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.departemen.index') }}"
                                        class="nav-link {{ Request::is('admin/departemen*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-building"></i>
                                        <p>Departemen</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.jabatan.index') }}"
                                        class="nav-link {{ Request::is('admin/jabatan*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-briefcase"></i>
                                        <p>Jabatan</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('admin.karyawan.index') }}"
                                        class="nav-link {{ Request::is('admin/karyawan*') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-users"></i>
                                        <p>Karyawan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Presensi -->
<!-- Presensi -->
<li class="nav-item {{ Request::is('admin/presensi*', 'admin/lokasi-presensi*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('admin/presensi*', 'admin/lokasi-presensi*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calendar-check"></i>
        <p>
            Presensi
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.lokasi-presensi.index') }}"
                class="nav-link {{ Request::is('admin/lokasi-presensi*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-map-marker-alt text-success"></i>
                <p>Lokasi Presensi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.presensi.rekap') }}"
                class="nav-link {{ Request::is('admin/presensi') || Request::is('admin/presensi/rekap*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar text-info"></i>
                <p>Rekap Presensi</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.presensi.monitoring') }}"
                class="nav-link {{ Request::is('admin/presensi/monitoring*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-desktop text-warning"></i>
                <p>Monitoring Presensi</p>
            </a>
        </li>
    </ul>
</li>


                        <!-- Tambahkan menu File Manager -->
                        <li class="nav-item">
                            <a href="{{ route('admin.file-manager.index') }}"
                                class="nav-link {{ Request::is('admin/file-manager*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-folder-open"></i>
                                <p>File Manager</p>
                            </a>
                        </li>
                        <!-- Pengajuan Izin & Cuti -->
                        <li class="nav-item">
                            <a href="{{ route('admin.pengajuan.index') }}"
                                class="nav-link {{ Request::is('admin/pengajuan*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Pengajuan
                                    @php
                                        $totalPending =
                                            App\Models\Izin::where('status_approval', 'pending')->count() +
                                            App\Models\Cuti::where('status_approval', 'pending')->count();
                                    @endphp
                                    @if ($totalPending > 0)
                                        <span class="right badge badge-warning">{{ $totalPending }}</span>
                                    @endif
                                </p>
                            </a>
                        </li>

                        
                    </ul>

                    <!-- Logout Button at Bottom of Sidebar -->
                    <div class="sidebar-logout">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                            <li class="nav-item">
                                @auth
                                    <a class="nav-link" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                                        <i class="nav-icon fas fa-sign-out-alt"></i>
                                        <p>Logout</p>
                                    </a>
                                    <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                @else
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="nav-icon fas fa-sign-in-alt"></i>
                                        <p>Login</p>
                                    </a>
                                @endauth
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">
                                <i class="fas @yield('icon', 'fa-cog') me-2"></i>
                                @yield('title', 'Dashboard')
                            </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('title', 'Dashboard')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Notifications -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="icon fas fa-ban"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">Sistem Presensi UNI</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE App -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    @stack('scripts')

    <script>
        $(document).ready(function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Initialize DataTables
            $('.datatable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Berikutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Fix dropdown behavior
            $(document).on('click', function(e) {
                $('.dropdown-menu').each(function() {
                    // Hide dropdown if click is outside
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $(
                            '.dropdown-toggle').has(e.target).length === 0) {
                        $(this).parent().removeClass('show');
                        $(this).removeClass('show');
                    }
                });
            });

            // Proper dropdown toggle
            $('.dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $this = $(this);
                var $parent = $this.parent();
                var $menu = $this.next('.dropdown-menu');

                // Close other dropdowns
                $('.dropdown-menu').not($menu).removeClass('show');
                $('.dropdown').not($parent).removeClass('show');

                // Toggle current dropdown
                $parent.toggleClass('show');
                $menu.toggleClass('show');
            });

            // Close dropdown when clicking on dropdown item
            $('.dropdown-item').on('click', function() {
                $(this).closest('.dropdown-menu').removeClass('show');
                $(this).closest('.dropdown').removeClass('show');
            });
        });
    </script>
</body>

</html>
