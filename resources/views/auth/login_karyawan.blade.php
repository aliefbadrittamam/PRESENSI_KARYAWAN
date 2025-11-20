<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Karyawan | Sistem Presensi</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-light">
    <div id="vanta-bg">

        <div class="login-container min-vh-100 d-flex align-items-center justify-content-center p-3">
            <div class="login-card card shadow-lg border-0 rounded-4 w-100" style="max-width: 420px;">
                <div class="login-header text-center p-4 pb-3">
                    <h2 class="fw-bold text-dark"><b>Sistem</b> <span class="text-primary">Presensi</span></h2>
                    <p class="text-primary fw-medium fs-5 mb-0">Login Karyawan</p>
                </div>

                <div class="login-card-body p-4 pt-3">

                    <!-- ✅ Pesan Sukses -->
                    @if (session('login_success'))
                        <div class="alert alert-success rounded-3" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('login_success') }}
                        </div>
                    @endif

                    <!-- ⚠️ Pesan Error dengan Tipe -->
                    @if (session('error_message'))
                        <div class="alert alert-danger rounded-3" role="alert">
                            <strong>
                                @switch(session('error_type'))
                                    @case('nip_not_found')
                                        <i class="bi bi-person-x-fill me-2"></i>
                                    @break

                                    @case('unlinked_user')
                                        <i class="bi bi-link-45deg me-2"></i>
                                    @break

                                    @case('wrong_role')
                                        <i class="bi bi-shield-exclamation me-2"></i>
                                    @break

                                    @case('invalid_credentials')
                                        <i class="bi bi-lock-fill me-2"></i>
                                    @break

                                    @case('no_password')
                                        <i class="bi bi-key-fill me-2"></i>
                                    @break

                                    @default
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                @endswitch
                            </strong>
                            {{ session('error_message') }}
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form action="{{ route('login.karyawan.submit') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-lg mb-3">
                            <div class="alert alert-info alert-dismissible fade show rounded-3 mb-3" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                                <strong>Belum punya password?</strong><br>
                                Silakan login menggunakan <strong>QR Code</strong> yang telah diberikan
                                sesuai NIP Anda. Setelah login, Anda dapat mengatur password di halaman
                                <strong>Profil</strong>.
                            </div>


                            <span class="input-group-text" id="nip-icon"><i class="bi bi-person-vcard"></i></span>
                            <input type="text" name="nip" class="form-control" placeholder="NIP" aria-label="NIP"
                                aria-describedby="nip-icon" value="{{ old('nip') }}" required autofocus>
                        </div>

                        <div class="input-group input-group-lg mb-4">
                            <span class="input-group-text" id="pass-icon"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Kata Sandi" aria-label="Kata Sandi" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-medium py-3">Masuk</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('barcode.scanner') }}" class="btn btn-outline-primary">
                            <i class="bi bi-qr-code-scan"></i> Login via Barcode
                        </a>
                    </div>

                    <p class="alternative-link text-center mt-4">
                        <a href="{{ route('login.admin') }}" class="text-decoration-none text-secondary fw-medium">
                            Masuk sebagai Admin
                        </a>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Three.js & Vanta.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/tengbao/vanta@latest/dist/vanta.net.min.js"></script>
    <script>
        VANTA.NET({
            el: "#vanta-bg",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            color: 0x000000,
            backgroundColor: 0xffffff,
            points: 12.0,
            maxDistance: 20.0,
            spacing: 18.0
        });
    </script>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <!-- Toggle Password Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput && toggleButton && toggleIcon) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'password') {
                        toggleIcon.classList.remove('bi-eye-slash-fill');
                        toggleIcon.classList.add('bi-eye-fill');
                    } else {
                        toggleIcon.classList.remove('bi-eye-fill');
                        toggleIcon.classList.add('bi-eye-slash-fill');
                    }
                });
            }
        });
    </script>

</body>

</html>
