{{-- resources/views/dashboard/karyawan.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Karyawan - {{ $karyawan->nama_lengkap }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-pink: #9DC183;
            --secondary-pink: #6fa976;
            --light-gray: #F5F7FA;
            --dark-gray: #7D8AA3;
            --card-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, var(--primary-pink) 0%, var(--secondary-pink) 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Header */
        .header {
            background: transparent;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menu-icon {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .profile-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-icon i {
            color: var(--dark-gray);
            font-size: 1.3rem;
        }

        /* Welcome Card */
        .welcome-card {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            margin: 1rem;
            box-shadow: var(--card-shadow);
        }

        .greeting {
            color: var(--dark-gray);
            font-size: 0.95rem;
            margin-bottom: 0.3rem;
        }

        .user-name {
            font-size: 2rem;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 1.5rem;
        }

        .work-time {
            text-align: right;
            color: var(--dark-gray);
            font-size: 0.9rem;
        }

        .work-time-label {
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }

        .time-range {
            font-weight: 600;
            color: #1F2937;
        }

        /* Action Menu */
        .action-menu {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-item {
            text-align: center;
            text-decoration: none;
        }

        .action-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            transition: transform 0.2s;
        }

        .action-icon:active {
            transform: scale(0.95);
        }

        .action-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .action-label {
            font-size: 0.85rem;
            color: #1F2937;
            font-weight: 500;
        }

        .icon-absen { background: linear-gradient(135deg, #9DC183 0%, #9DC183 100%); }
        .icon-izin { background: linear-gradient(135deg, #FFA726 0%, #FFB74D 100%); }
        .icon-cuti { background: linear-gradient(135deg, #5C6BC0 0%, #7986CB 100%); }
        .icon-history { background: linear-gradient(135deg, #26A69A 0%, #4DB6AC 100%); }
        .icon-profil { background: linear-gradient(135deg, #FFCA28 0%, #FFD54F 100%); }

        /* Status Cards */
        .status-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem;
        }

        .status-card {
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .status-card-masuk {
            background: linear-gradient(135deg, var(--primary-pink) 0%, var(--secondary-pink) 100%);
            color: white;
        }

        .status-card-pulang {
            background: #8B95A8;
            color: white;
        }

        .status-card.present {
            background: linear-gradient(135deg, #26A69A 0%, #4DB6AC 100%);
        }

        .status-label {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }

        .status-value {
            font-size: 1.3rem;
            font-weight: 700;
        }

        /* Attendance Summary */
        .attendance-section {
            margin: 1.5rem 1rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        .month-selector {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }

        .attendance-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
        }

        .attendance-item {
            background: white;
            border-radius: 16px;
            padding: 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: var(--card-shadow);
        }

        .attendance-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-hadir { background: rgba(94, 114, 228, 0.1); color: #5E72E4; }
        .icon-izin-status { background: rgba(38, 166, 154, 0.1); color: #26A69A; }
        .icon-sakit { background: rgba(139, 149, 168, 0.1); color: #8B95A8; }
        .icon-terlambat { background: rgba(255, 79, 126, 0.1); color: #FF4F7E; }

        .attendance-info h6 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.2rem;
        }

        .attendance-count {
            font-size: 0.85rem;
            color: var(--dark-gray);
        }

        /* Last Week Summary */
        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 1rem;
            margin: 1rem;
            box-shadow: var(--card-shadow);
        }

        .summary-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 1rem;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            padding: 0.8rem 0;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.08);
            z-index: 1000;
        }

        .nav-items {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .nav-item {
            text-align: center;
            text-decoration: none;
            color: var(--dark-gray);
            transition: color 0.2s;
            flex: 1;
        }

        .nav-item.active {
            color: var(--primary-pink);
        }

        .nav-item i {
            font-size: 1.4rem;
            display: block;
            margin-bottom: 0.3rem;
        }

        .nav-label {
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Desktop Responsive */
        @media (min-width: 768px) {
            body {
                background: linear-gradient(180deg, var(--primary-pink) 0%, var(--secondary-pink) 50%);
            }

            .container-desktop {
                max-width: 1200px;
                margin: 0 auto;
            }

            .welcome-card {
                margin: 2rem auto;
                max-width: 900px;
            }

            .action-menu {
                max-width: 600px;
                margin: 2rem auto;
            }

            .status-cards {
                max-width: 600px;
                margin: 1.5rem auto;
            }

            .attendance-section {
                max-width: 900px;
                margin: 2rem auto;
            }

            .attendance-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .bottom-nav {
                display: none;
            }

            .desktop-nav {
                display: block;
                background: white;
                padding: 1rem 2rem;
                box-shadow: var(--card-shadow);
                margin-bottom: 2rem;
            }

            .desktop-nav .nav-items {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                gap: 2rem;
            }

            .desktop-nav .nav-item {
                flex: initial;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                border-radius: 8px;
            }

            .desktop-nav .nav-item:hover {
                background: var(--light-gray);
            }

            .desktop-nav .nav-item i {
                font-size: 1.2rem;
                margin-bottom: 0;
            }

            .desktop-nav .nav-label {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 767px) {
            .desktop-nav {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Desktop Navigation -->
    <nav class="desktop-nav">
        <div class="nav-items">
            <a href="{{ route('user.dashboard') }}" class="nav-item active">
                <i class="fas fa-home"></i>
                <span class="nav-label">Home</span>
            </a>
            <a href="{{ route('presensi.create') }}" class="nav-item">
                <i class="fas fa-camera"></i>
                <span class="nav-label">Absen</span>
            </a>
            <a href="{{ route('cuti.index') }}" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span class="nav-label">Cuti</span>
            </a>
            <a href="{{ route('presensi.history') }}" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span class="nav-label">History</span>
            </a>
            <a href="{{ route('karyawan.profile') }}" class="nav-item">
                <i class="fas fa-user"></i>
                <span class="nav-label">Profil</span>
            </a>
        </div>
    </nav>

    <div class="container-desktop">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <button class="menu-icon" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="{{ route('karyawan.profile') }}" class="profile-icon">
                    @if($karyawan->foto)
                        <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_lengkap }}">
                    @else
                        <i class="fas fa-user"></i>
                    @endif
                </a>
            </div>
        </header>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="greeting">Selamat {{ $greeting }}</p>
                    <h1 class="user-name">{{ $karyawan->nama_lengkap }}</h1>
                </div>
                <div class="work-time">
                    <div class="work-time-label">Jam Kerja</div>
                    @if($shift)
                        <div class="time-range">
                            {{ date('H:i', strtotime($shift->jam_mulai)) }} - {{ date('H:i', strtotime($shift->jam_selesai)) }}
                        </div>
                    @else
                        <div class="time-range">-</div>
                    @endif
                </div>
            </div>

            <!-- Action Menu -->
            <div class="action-menu">
                <a href="{{ route('presensi.create') }}" class="action-item">
                    <div class="action-icon icon-absen">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="action-label">Absen</div>
                </a>
                <a href="{{ route('izin.create') }}" class="action-item">
                    <div class="action-icon icon-izin">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="action-label">Izin</div>
                </a>
                <a href="{{ route('cuti.index') }}" class="action-item">
                    <div class="action-icon icon-cuti">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="action-label">Cuti</div>
                </a>
                <a href="{{ route('presensi.history') }}" class="action-item">
                    <div class="action-icon icon-history">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="action-label">History</div>
                </a>
                <a href="{{ route('karyawan.profile') }}" class="action-item">
                    <div class="action-icon icon-profil">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="action-label">Profil</div>
                </a>
            </div>
        </div>

        <!-- Status Cards -->
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

        <!-- Attendance Summary -->
        <section class="attendance-section">
            <div class="section-header">
                <h2 class="section-title">Absensi Bulan</h2>
                <select class="month-selector" id="monthFilter">
                    @foreach($months as $monthData)
                        <option value="{{ $monthData['value'] }}" {{ $monthData['selected'] ? 'selected' : '' }}>
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
                        <h6>Hadir</h6>
                        <div class="attendance-count">{{ $rekapBulan->jumlah_hadir ?? 0 }} Hari</div>
                    </div>
                </div>

                <div class="attendance-item">
                    <div class="attendance-icon icon-izin-status">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="attendance-info">
                        <h6>Izin</h6>
                        <div class="attendance-count">{{ $rekapBulan->jumlah_izin ?? 0 }} Hari</div>
                    </div>
                </div>

                <div class="attendance-item">
                    <div class="attendance-icon icon-sakit">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div class="attendance-info">
                        <h6>Sakit</h6>
                        <div class="attendance-count">{{ $rekapBulan->jumlah_sakit ?? 0 }} Hari</div>
                    </div>
                </div>

                <div class="attendance-item">
                    <div class="attendance-icon icon-terlambat">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="attendance-info">
                        <h6>Terlambat</h6>
                        <div class="attendance-count">{{ $rekapBulan->jumlah_terlambat ?? 0 }} hari</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Last Week Summary -->
        <div class="summary-card">
            <h3 class="summary-title">1 Minggu Terakhir</h3>
            @if($presensiMingguIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presensiMingguIni as $presensi)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($presensi->tanggal_presensi)->format('d/m/Y') }}</td>
                                    <td>{{ $presensi->jam_masuk ? date('H:i', strtotime($presensi->jam_masuk)) : '-' }}</td>
                                    <td>{{ $presensi->jam_keluar ? date('H:i', strtotime($presensi->jam_keluar)) : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $presensi->status_kehadiran == 'hadir' ? 'success' : ($presensi->status_kehadiran == 'terlambat' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($presensi->status_kehadiran) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted text-center py-3">
                    <i class="fas fa-calendar-times fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">Belum ada data absensi minggu ini</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <div class="nav-items">
            <a href="{{ route('user.dashboard') }}" class="nav-item active">
                <i class="fas fa-home"></i>
                <span class="nav-label">Home</span>
            </a>
            <a href="{{ route('presensi.create') }}" class="nav-item">
                <i class="fas fa-camera"></i>
                <span class="nav-label">Absen</span>
            </a>
            <a href="{{ route('cuti.index') }}" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span class="nav-label">Cuty</span>
            </a>
            <a href="{{ route('presensi.history') }}" class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span class="nav-label">History</span>
            </a>
            <a href="{{ route('karyawan.profile') }}" class="nav-item">
                <i class="fas fa-user"></i>
                <span class="nav-label">Profil</span>
            </a>
        </div>
    </nav>

    <!-- Sidebar Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="{{ route('user.dashboard') }}" class="text-decoration-none d-block p-2">
                        <i class="fas fa-home me-2"></i> Dashboard
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('karyawan.profile') }}" class="text-decoration-none d-block p-2">
                        <i class="fas fa-user me-2"></i> Profil Saya
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('presensi.history') }}" class="text-decoration-none d-block p-2">
                        <i class="fas fa-history me-2"></i> Riwayat Presensi
                    </a>
                </li>
                <li class="mb-2">
                    <a href="#" class="text-decoration-none d-block p-2" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Month filter change
        document.getElementById('monthFilter').addEventListener('change', function() {
            window.location.href = '{{ route('user.dashboard') }}?month=' + this.value;
        });

        // Active navigation handling
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            }
        });
    </script>
</body>
</html>