@extends('layouts/commonMaster')

@section('title', 'Masuk - SKM Amanat')

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

    .demo-account-btn {
      transition: all 0.2s ease-in-out;
      border: 1px solid #e7eaf0;
      background: #f8f9fc;
    }

    .demo-account-btn:hover {
      background: #ffffff;
      border-color: #696cff;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(105, 108, 255, 0.15);
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
        <!-- Login Card -->
        <div class="card auth-card">
          <div class="card-body p-4 p-sm-5">
            <!-- Brand / Logo Header -->
            <div class="app-brand justify-content-center mb-4 flex-column text-center">
              <div
                class="p-2 mb-2 bg-lighter rounded-3 shadow-xs border d-inline-flex align-items-center justify-content-center"
                style="width: 68px; height: 68px;">
                <img src="{{ asset('images/logo-amanat.png') }}" alt="Logo SKM Amanat" class="img-fluid"
                  style="max-height: 52px;">
              </div>
              <h4 class="mb-1 fw-bold text-heading">SKM AMANAT</h4>
              <p class="text-muted small mb-0">Sistem Informasi Keanggotaan & Magang Spesialis</p>
            </div>

            <!-- Flash / Error Messages -->
            @if ($errors->any())
              <div class="alert alert-danger py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                <i class="bx bx-error-circle fs-5"></i>
                <div>{{ $errors->first() }}</div>
              </div>
            @endif

            @if (session('warning'))
              <div class="alert alert-warning py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                <i class="bx bx-info-circle fs-5"></i>
                <div>{{ session('warning') }}</div>
              </div>
            @endif

            @if (session('success'))
              <div class="alert alert-success py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                <i class="bx bx-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            @if (session('info'))
              <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                <i class="bx bx-info-circle fs-5"></i>
                <div>{{ session('info') }}</div>
              </div>
            @endif

            <!-- Form Login -->
            <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
              @csrf

              <!-- Email Input -->
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-heading">Alamat Email</label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                  <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}"
                    placeholder="nama@email.com" autofocus required />
                </div>
              </div>

              <!-- Password Input -->
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label fw-semibold text-heading mb-0" for="password">Kata Sandi</label>
                  <a href="{{ route('forgot-password') }}" class="small text-primary fw-semibold">
                    Lupa sandi?
                  </a>
                </div>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                  <input type="password" id="password" class="form-control" name="password" placeholder="············"
                    required />
                  <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()"><i
                      id="togglePassIcon" class="bx bx-hide"></i></span>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="mb-3 pt-2">
                <button class="btn btn-primary d-grid w-100 fw-bold py-2 shadow-sm" type="submit">
                  Masuk Sekarang
                </button>
              </div>
            </form>

            <!-- Link to Register -->
            <p class="text-center mb-0 small text-muted">
              <span>Belum punya akun calon anggota?</span>
              <a href="{{ route('register') }}" class="text-primary fw-bold ms-1">
                <span>Daftar Sekarang</span>
              </a>
            </p>
          </div>
        </div>
        <!-- /Card -->

        <!-- Footer Note -->
        <div class="text-center mt-3 text-muted small">
          &copy; {{ date('Y') }} <strong>SKM Amanat</strong> &bull; SPK Metode Profile Matching
        </div>
      </div>
    </div>
  </div>

  @push('page-scripts')
    <script>
      function togglePasswordVisibility() {
        const passInput = document.getElementById('password');
        const passIcon = document.getElementById('togglePassIcon');
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

      function fillCredentials(email, password) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        if (emailInput && passwordInput) {
          emailInput.value = email;
          passwordInput.value = password;

          emailInput.classList.add('is-valid');
          passwordInput.classList.add('is-valid');

          setTimeout(() => {
            emailInput.classList.remove('is-valid');
            passwordInput.classList.remove('is-valid');
          }, 1000);
        }
      }
    </script>
  @endpush
@endsection
