<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin | Sistem Presensi</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Google Font: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

  <style>
    /* Menggunakan font Inter yang clean */
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-light">

  <div class="login-container min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="login-card card shadow-lg border-0 rounded-4 w-100" style="max-width: 420px;">
      <div class="login-header text-center p-4 pb-3">
        <h2 class="fw-bold text-dark"><b>Sistem</b> <span class="text-primary">Presensi</span></h2>
        <p class="text-primary fw-medium fs-5 mb-0">Login Admin</p>
      </div>
      
      <div class="login-card-body p-4 pt-3">

        <!-- Menampilkan pesan error jika ada -->
        @if($errors->any())
          <div class="alert alert-danger rounded-3" role="alert">
            {{ $errors->first() }}
          </div>
        @endif
        
        <!-- Form Login -->
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
              <!-- Tombol untuk toggle password -->
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

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  <!-- Script untuk toggle password -->
  <script>
    // Menunggu halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function () {
      
      // Ambil elemen yang diperlukan
      const passwordInput = document.getElementById('password');
      const toggleButton = document.getElementById('togglePassword');
      const toggleIcon = document.getElementById('toggleIcon');

      // Pastikan semua elemen ada
      if (passwordInput && toggleButton && toggleIcon) {
        
        // Tambahkan event listener 'click' ke tombol
        toggleButton.addEventListener('click', function () {
          
          // Cek tipe input saat ini
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          // Ganti ikon berdasarkan tipe
          if (type === 'password') {
            // Jika tipe adalah password (tersembunyi), tampilkan ikon 'mata'
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
          } else {
            // Jika tipe adalah text (terlihat), tampilkan ikon 'mata coret'
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
          }
        });
      }
    });
  </script>

</body>
</html>

