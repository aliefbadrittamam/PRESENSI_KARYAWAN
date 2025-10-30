@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0 rounded-4">
        <div class="card-body text-center">
            <h3 class="mb-3 text-primary">Selamat Datang, {{ $user->name ?? $user->nama_lengkap }}</h3>
            <p class="text-muted mb-4">
                Anda berhasil login menggunakan <strong>QR Code</strong>.
            </p>

            <div class="row justify-content-center mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-light shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold">Informasi Akun</h5>
                            <ul class="list-unstyled text-start mt-3">
                                <li><strong>Nama:</strong> {{ $user->nama_lengkap ?? $user->name }}</li>
                                <li><strong>Email:</strong> {{ $user->email ?? '-' }}</li>
                                <li><strong>NIP:</strong> {{ $user->nip ?? '-' }}</li>
                                <li><strong>Role:</strong> {{ ucfirst($user->role ?? 'User') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <a href="{{ route('logout') }}" 
               class="btn btn-danger"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection
