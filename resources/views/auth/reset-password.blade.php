@extends('layouts/commonMaster')

@section('title', 'Atur Ulang Sandi - SKM Amanat')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
    body {
        background: radial-gradient(100% 100% at 50% 0%, rgba(105, 108, 255, 0.12) 0%, rgba(245, 245, 249, 0.95) 50%, #f5f5f9 100%) !important;
        background-attachment: fixed !important;
        background-repeat: no-repeat !important;
        min-height: 100vh;
    }
    .authentication-wrapper.authentication-basic .authentication-inner::before,
    .authentication-wrapper.authentication-basic .authentication-inner::after {
        display: none !important;
    }
    .auth-card {
        border-radius: 1rem !important;
        box-shadow: 0 10px 30px 0 rgba(67, 89, 113, 0.1) !important;
        border: 1px solid rgba(67, 89, 113, 0.12) !important;
        background: #ffffff !important;
    }
    .form-control:focus {
        border-color: #696cff !important;
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.15) !important;
    }
</style>
@endsection

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Reset Password Card -->
            <div class="card auth-card">
                <div class="card-body p-4 p-sm-5">
                    <!-- Brand / Logo Header -->
                    <div class="app-brand justify-content-center mb-4 flex-column text-center">
                        <div class="p-2 mb-2 bg-lighter rounded-3 shadow-xs border d-inline-flex align-items-center justify-content-center" style="width: 68px; height: 68px;">
                            <img src="{{ asset('images/logo-amanat.png') }}" alt="Logo SKM Amanat" class="img-fluid" style="max-height: 52px;">
                        </div>
                        <h4 class="mb-1 fw-bold text-heading">Atur Ulang Sandi</h4>
                        <p class="text-muted small mb-0">Masukkan OTP dan kata sandi baru untuk <strong>{{ $email }}</strong></p>
                    </div>

                    <!-- Flash / Error Messages -->
                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                            <i class="bx bx-error-circle fs-5"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                            <i class="bx bx-info-circle fs-5"></i>
                            <div>{{ session('warning') }}</div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                            <i class="bx bx-check-circle fs-5"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    <!-- Form Reset Password -->
                    <form id="formAuthentication" class="mb-3" action="{{ route('reset-password') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        
                        <div class="mb-3">
                            <label for="otp" class="form-label fw-semibold text-heading">Kode OTP (6 Digit)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-key"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control text-center fw-bold fs-4" 
                                    id="otp" 
                                    name="otp" 
                                    placeholder="••••••" 
                                    maxlength="6"
                                    autofocus 
                                    required 
                                />
                            </div>
                            <div class="form-text text-muted small">
                                Masukkan 6-digit kode OTP yang dikirim ke email <strong>{{ $email }}</strong>.
                            </div>
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label fw-semibold text-heading" for="password">Kata Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                <input 
                                    type="password" 
                                    id="password" 
                                    class="form-control" 
                                    name="password" 
                                    placeholder="············" 
                                    required 
                                />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password', 'togglePassIcon')"><i id="togglePassIcon" class="bx bx-hide"></i></span>
                            </div>
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label fw-semibold text-heading" for="password_confirmation">Konfirmasi Sandi Baru</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    class="form-control" 
                                    name="password_confirmation" 
                                    placeholder="············" 
                                    required 
                                />
                                <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPassIcon')"><i id="toggleConfirmPassIcon" class="bx bx-hide"></i></span>
                            </div>
                        </div>

                        <div class="mb-3 pt-2">
                            <button class="btn btn-primary d-grid w-100 fw-bold py-2 shadow-sm" type="submit">
                                Atur Ulang Kata Sandi
                            </button>
                        </div>
                    </form>

                    <!-- Form Kirim Ulang OTP Reset -->
                    <form action="{{ route('resend-reset-otp') }}" method="POST" class="text-center mb-3">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <p class="mb-0 small text-muted">
                            Tidak menerima kode OTP? 
                            <button type="submit" class="btn btn-link btn-sm p-0 fw-semibold text-primary text-decoration-none">
                                Kirim Ulang Kode
                            </button>
                        </p>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center small text-muted">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            Kembali ke login
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Card -->
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passInput = document.getElementById(inputId);
        const passIcon = document.getElementById(iconId);
        if (passInput.type === 'password') {
            passInput.type = 'text';
            passIcon.classList.remove('bx-hide');
            passIcon.classList.add('bx-show');
        } else {
            passInput.type = 'password';
            passIcon.classList.remove('bx-show');
            passIcon.classList.add('bx-hide');
        }
    }
</script>
@endpush
@endsection
