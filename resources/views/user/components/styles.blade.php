{{-- File: resources/views/user/components/styles.blade.php --}}
<style>
    :root {
        /* 🌿 Warna Utama (Sage Green) */
        --primary-sage: #9DC183;
        --secondary-sage: #6FA976;

        /* 🌼 Warna Aksen */
        --accent-cream: #FDF6EC;
        /* background lembut */
        --accent-gold: #E4C988;
        /* untuk icon / highlight */

        /* 🪴 Warna Netral & Teks */
        --light-gray: #F5F7FA;
        --medium-gray: #A0AEC0;
        --dark-gray: #4A5568;

        /* 🌸 Warna Sekunder Pelengkap */
        --soft-pink: #F2C6B4;
        /* aksen feminin lembut */
        --soft-blue: #BFD8D2;
        /* variasi lembut untuk balance */

        /* 🌚 Shadow */
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }


    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(180deg, var(--primary-sage) 0%, var(--secondary-sage) 100%);
        min-height: 100vh;
        padding-bottom: 80px;
    }

    /* Desktop Navigation */
    .desktop-nav {
        display: none;
        background: white;
        box-shadow: var(--card-shadow);
        margin-bottom: 0;
    }

    .desktop-nav .navbar-nav .nav-link {
        color: var(--dark-gray);
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .desktop-nav .navbar-nav .nav-link:hover,
    .desktop-nav .navbar-nav .nav-link.active {
        background: var(--light-gray);
        color: var(--primary-sage);
    }

    .desktop-nav .navbar-nav .nav-link i {
        font-size: 1.1rem;
    }

    /* Header */
    .header {
        background: transparent;
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .menu-icon {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        transition: transform 0.2s;
    }

    .menu-icon:hover {
        transform: scale(1.05);
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
        transition: transform 0.2s;
    }

    .profile-icon:hover {
        transform: scale(1.05);
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
        background: var(--accent-cream);
        ;
    }

    .greeting,
    .work-time,




    .user-name {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-gray);
        margin-bottom: 0;
    }

    .work-time {
        text-align: right;
        color: var(--medium-gray);
        font-size: 0.9rem;
    }

    .work-time-label {
        font-size: 0.85rem;
        margin-bottom: 0.2rem;
    }

    .time-range {
        font-weight: 600;
        color: #1F2937;
        font-size: 1rem;
    }

    /* Action Menu */
    .action-menu {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-top: 2rem;
        padding: 0 0.5rem;
    }

    .action-item {
        text-align: center;
        text-decoration: none;
        transition: transform 0.2s;
    }

    .action-item:hover {
        transform: translateY(-3px);
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

    .icon-absen {
        background: linear-gradient(135deg, #7AB97A 0%, #9DC183 100%);
    }

    /* Hijau alami */
    .icon-izin {
        background: linear-gradient(135deg, #E4C988 0%, #F2D479 100%);
    }

    /* Gold lembut */
    .icon-cuti {
        background: linear-gradient(135deg, #BFD8D2 0%, #A4C3B2 100%);
    }

    /* Aqua sage */
    .icon-history {
        background: linear-gradient(135deg, #A0B9A8 0%, #7DAE9C 100%);
    }

    /* Cool sage */
    .icon-profil {
        background: linear-gradient(135deg, #F2C6B4 0%, #E7BBA6 100%);
    }

    /* Pink lembut */


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
        transition: transform 0.2s;
    }

    .status-card:hover {
        transform: translateY(-2px);
    }

    .status-card-masuk {
        background: linear-gradient(135deg, #A4C3B2 0%, #9DC183 100%);
        color: white;
    }

    .status-card-pulang {
        background: linear-gradient(135deg, #BFD8D2 0%, #A4C3B2 100%);
        color: white;
    }

    .status-card.present {
        background: linear-gradient(135deg, #6FA976 0%, #9DC183 100%);
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

    /* Attendance Section */
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
        margin: 0;
    }

    .month-selector {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.9rem;
        backdrop-filter: blur(10px);
        cursor: pointer;
    }

    .month-selector:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.3);
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
        transition: transform 0.2s;
    }

    .attendance-item:hover {
        transform: translateY(-2px);
    }

    .attendance-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .icon-hadir {
        background: rgba(94, 114, 228, 0.1);
        color: #5E72E4;
    }

    .icon-izin-status {
        background: rgba(38, 166, 154, 0.1);
        color: #26A69A;
    }

    .icon-sakit {
        background: rgba(139, 149, 168, 0.1);
        color: #8B95A8;
    }

    .icon-terlambat {
        background: rgba(255, 79, 126, 0.1);
        color: #FF4F7E;
    }

    .attendance-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 0.2rem;
    }

    .attendance-count {
        font-size: 0.85rem;
        color: var(--medium-gray);
    }

    /* Summary Card */
    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        margin: 1rem;
        box-shadow: var(--card-shadow);
    }

    .summary-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1F2937;
        margin-bottom: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--dark-gray);
    }

    .empty-state i {
        font-size: 3rem;
        opacity: 0.3;
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
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
        z-index: 1000;
    }

    .nav-items {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 600px;
        margin: 0 auto;
    }

    .nav-item {
        text-align: center;
        text-decoration: none;
        color: var(--dark-gray);
        transition: color 0.2s;
        flex: 1;
    }

    .nav-item.active {
        color: var(--primary-sage);
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

    /* Sidebar */
    .offcanvas {
        max-width: 280px;
    }

    .sidebar-menu-item {
        padding: 0.75rem 1rem;
        color: #1F2937;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-radius: 8px;
        transition: background 0.2s;
    }

    .sidebar-menu-item:hover {
        background: var(--light-gray);
        color: var(--primary-sage);
    }

    .sidebar-menu-item i {
        width: 20px;
        text-align: center;
    }

    /* Desktop Responsive */
    @media (min-width: 768px) {
        body {
            background: linear-gradient(180deg, var(--primary-sage) 0%, var(--secondary-sage) 50%);
            padding-bottom: 2rem;
        }

        .desktop-nav {
            display: block !important;
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

        .summary-card {
            max-width: 900px;
            margin: 1.5rem auto;
        }

        .bottom-nav {
            display: none;
        }
    }

    /* Print styles */
    @media print {

        .header,
        .bottom-nav,
        .desktop-nav,
        .action-menu {
            display: none;
        }
    }
</style>
