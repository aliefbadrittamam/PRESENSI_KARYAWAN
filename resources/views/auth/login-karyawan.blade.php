<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan - Sistem Presensi UNI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
<div class="card shadow-lg p-4" style="width: 400px;">
    <h3 class="text-center mb-3 text-success">Login Karyawan</h3>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ url('/login/karyawan') }}">
        @csrf
        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input id="nip" type="text" class="form-control" name="nip" required autofocus placeholder="Masukkan NIP Anda">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input id="password" type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Masuk</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login.admin') }}">Masuk sebagai Admin</a>
    </div>
</div>
</body>
</html>
