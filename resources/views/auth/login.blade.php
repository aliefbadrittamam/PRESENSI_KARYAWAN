<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin | Sistem Presensi</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      overflow: hidden;
      background-color: #ffffff; /* tetap putih */
    }

    /* Area background interaktif */
    #vanta-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }

    .login-container {
      position: relative;
      z-index: 10;
    }

    /* Biar form-nya tetap kontras dengan putih tapi tidak terlalu tebal */
    .login-card {
      background-color: rgba(255,255,255,0.95);
      backdrop-filter: blur(4px);
    }
  </style>
</head>
<body>

  <!-- Background jaring hitam -->
  <div id="vanta-bg"></div>

  <div class="login-container min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="login-card card shadow-lg border-0 rounded-4 w-100" style="max-width: 420px;">
      <div class="login-header text-center p-4 pb-3">
        <h2 class="fw-bold text-dark"><b>Sistem</b> <span class="text-primary">Presensi</span></h2>
        <p class="text-primary fw-medium fs-5 mb-0">Login Admin</p>
      </div>
      
      <div class="login-card-body p-4 pt-3">

        @if($errors->any())
          <div class="alert alert-danger rounded-3" role="alert">
            {{ $errors->first() }}
          </div>
        @endif
        
        <form method="POST" action="{{ url('/login/admin') }}">
          @csrf
          
          <div class="mb-3">
            <label for="email" class="form-label fw-medium">Email Admin</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text" id="email-icon"><i class="bi bi-envelope-fill"></i></span>
              <input id="email" type="email" class="form-control" name="email" placeholder="nama@email.com" aria-label="Email Admin" aria-describedby="email-icon" required autofocus>
            </div>
          </div>
          
          <div class="mb-4">
             <label for="password" class="form-label fw-medium">Kata Sandi</label>
            <div class="input-group input-group-lg">
              <span class="input-group-text" id="pass-icon"><i class="bi bi-lock-fill"></i></span>
              <input id="password" type="password" class="form-control" name="password" placeholder="Kata Sandi" aria-label="Kata Sandi" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye-fill" id="toggleIcon"></i>
              </button>
            </div>
          </div>
          
          <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg fw-medium py-3">Masuk</button>
          </div>
        </form>

        <p class="alternative-link text-center mt-4">
          <a href="{{ route('login.karyawan') }}" class="text-decoration-none text-secondary fw-medium">Masuk sebagai Karyawan</a>
        </p>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- THREE.js + VANTA.NET -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/tengbao/vanta@latest/dist/vanta.net.min.js"></script>
  <script>
    VANTA.NET({
      el: "#vanta-bg",
      mouseControls: true,
      touchControls: true,
      gyroControls: false,
      color: 0x000000,           // partikel & garis warna hitam
      backgroundColor: 0xffffff, // background putih
      points: 12.0,
      maxDistance: 20.0,
      spacing: 18.0
    });
  </script>

  <!-- Toggle password -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const passwordInput = document.getElementById('password');
      const toggleButton = document.getElementById('togglePassword');
      const toggleIcon = document.getElementById('toggleIcon');

      if (passwordInput && toggleButton && toggleIcon) {
        toggleButton.addEventListener('click', function () {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          toggleIcon.classList.toggle('bi-eye-fill');
          toggleIcon.classList.toggle('bi-eye-slash-fill');
        });
      }
    });
  </script>

</body>
</html>
