@extends('layouts.app')

@section('title', 'Ubah Password Karyawan')
@section('icon', 'fa-key')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Info Karyawan -->
            <div class="card border-0 mb-3">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Informasi Karyawan
                    </h6>
                </div>
                <div class="card-body bg-dark">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            @if ($karyawan->foto)
                                <img src="{{ asset('public/' . $karyawan->foto) }}" 
                                    class="rounded-circle"
                                    width="120" height="120"
                                    style="object-fit: cover; border: 3px solid #007bff;"
                                    alt="{{ $karyawan->nama_lengkap }}">
                            @else
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 120px; height: 120px; border: 3px solid #007bff;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-dark table-borderless mb-0">
                                <tr>
                                    <td width="150"><strong>NIP</strong></td>
                                    <td>: {{ $karyawan->nip }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Lengkap</strong></td>
                                    <td>: {{ $karyawan->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: {{ $karyawan->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: {{ $karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Departemen</strong></td>
                                    <td>: {{ $karyawan->departemen->nama_departemen ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Change Password -->
            <div class="card border-0">
                <div class="card-header bg-dark text-white border-0 py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </h6>
                </div>
                <div class="card-body bg-dark">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Password minimal 8 karakter. Pastikan password yang Anda masukkan aman dan mudah diingat oleh karyawan.
                    </div>

                    <form action="{{ route('admin.karyawan.update-password', $karyawan->id_karyawan) }}" method="POST">
                        @csrf
                        
                        <!-- Password Baru -->
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Password Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                    name="password" 
                                    id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="Minimal 8 karakter"
                                    required
                                    minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="form-group mb-3">
                            <label class="form-label">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation" 
                                    class="form-control" 
                                    placeholder="Ulangi password baru"
                                    required
                                    minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Match Indicator -->
                        <div id="passwordMatchIndicator" class="alert" style="display: none;"></div>

                        <!-- Generate Random Password -->
                        <div class="mb-4">
                            <button type="button" class="btn btn-info btn-sm" id="generatePassword">
                                <i class="fas fa-random me-1"></i>Generate Password Acak
                            </button>
                            <small class="text-muted ms-2">Klik untuk membuat password acak yang kuat</small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .card {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control, .form-select {
            background-color: #2d3236 !important;
            border-color: #4a5056 !important;
            color: #fff !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: #343a40 !important;
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }

        .input-group .btn-outline-secondary {
            background-color: #2d3236;
            border-color: #4a5056;
            color: #fff;
        }

        .input-group .btn-outline-secondary:hover {
            background-color: #343a40;
            border-color: #007bff;
            color: #fff;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle Password Visibility
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            $('#togglePasswordConfirmation').on('click', function() {
                const passwordField = $('#password_confirmation');
                const icon = $(this).find('i');
                
                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Check Password Match
            $('#password, #password_confirmation').on('keyup', function() {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                const indicator = $('#passwordMatchIndicator');
                
                if (confirmation.length > 0) {
                    if (password === confirmation) {
                        indicator
                            .removeClass('alert-danger')
                            .addClass('alert-success')
                            .html('<i class="fas fa-check-circle me-1"></i>Password cocok!')
                            .show();
                    } else {
                        indicator
                            .removeClass('alert-success')
                            .addClass('alert-danger')
                            .html('<i class="fas fa-times-circle me-1"></i>Password tidak cocok!')
                            .show();
                    }
                } else {
                    indicator.hide();
                }
            });

            // Generate Random Password
            $('#generatePassword').on('click', function() {
                const length = 12;
                const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
                let password = "";
                
                for (let i = 0; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }
                
                $('#password').val(password);
                $('#password_confirmation').val(password);
                
                // Show passwords
                $('#password, #password_confirmation').attr('type', 'text');
                $('#togglePassword i, #togglePasswordConfirmation i')
                    .removeClass('fa-eye')
                    .addClass('fa-eye-slash');
                
                // Trigger match check
                $('#password_confirmation').trigger('keyup');
                
                // Show alert
                alert('Password acak telah dibuat:\n\n' + password + '\n\nSalin dan simpan password ini!');
            });

            // Form validation
            $('form').on('submit', function(e) {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('Password minimal 8 karakter!');
                    return false;
                }
                
                if (password !== confirmation) {
                    e.preventDefault();
                    alert('Password dan konfirmasi password tidak cocok!');
                    return false;
                }
                
                return confirm('Apakah Anda yakin ingin mengubah password karyawan ini?');
            });
        });
    </script>
@endpush