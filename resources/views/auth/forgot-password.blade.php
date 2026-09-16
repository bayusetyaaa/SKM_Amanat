@extends('layouts/commonMaster')

@section('title', 'Lupa Sandi - SKM Amanat')

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
            <!-- Forgot Password Card -->
            <div class="card auth-card">
                <div class="card-body p-4 p-sm-5">
                    <!-- Brand / Logo Header -->
                    <div class="app-brand justify-content-center mb-4 flex-column text-center">
                        <div class="p-2 mb-2 bg-lighter rounded-3 shadow-xs border d-inline-flex align-items-center justify-content-center" style="width: 68px; height: 68px;">
                            <img src="{{ asset('images/logo-amanat.png') }}" alt="Logo SKM Amanat" class="img-fluid" style="max-height: 52px;">
                        </div>
                        <h4 class="mb-1 fw-bold text-heading">Lupa Sandi? 🔒</h4>
                        <p class="text-muted small mb-0">Masukkan email Anda dan kami akan mengirimkan instruksi untuk mengatur ulang kata sandi Anda</p>
                    </div>

                    <!-- Flash / Error Messages -->
                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                            <i class="bx bx-error-circle fs-5"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <!-- Form Forgot Password -->
                    <form id="formAuthentication" class="mb-3" action="{{ route('forgot-password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-heading">Alamat Email</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="nama@email.com" 
                                    autofocus 
                                    required 
                                />
                            </div>
                        </div>

                        <div class="mb-3 pt-2">
                            <button class="btn btn-primary d-grid w-100 fw-bold py-2 shadow-sm" type="submit">
                                Kirim Kode OTP
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
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
@endsection
