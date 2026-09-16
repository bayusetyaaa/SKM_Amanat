@extends('layouts/commonMaster')

@section('title', 'Pendaftaran Akun - SKM Amanat')

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

    .authentication-wrapper.authentication-basic .authentication-inner {
      max-width: 580px !important;
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
        <!-- Register Card -->
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
              <h4 class="mb-1 fw-bold text-heading">Pendaftaran Akun Cakruma</h4>
              <p class="text-muted small mb-0">Lengkapi data diri untuk membuat akun pendaftaran calon kru magang SKM
                Amanat</p>
            </div>

            <!-- Flash / Error Messages -->
            @if ($errors->any())
              <div class="alert alert-danger py-2 px-3 mb-3 small">
                <ul class="mb-0 ps-3">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- Register Form -->
            <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
              @csrf

              <!-- Nama Lengkap -->
              <div class="mb-3">
                <label for="name" class="form-label fw-semibold text-heading">Nama Lengkap <span
                    class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-user"></i></span>
                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Nama lengkap sesuai KTP / KTM" required autofocus />
                </div>
              </div>

              <!-- NIM & Program Studi -->
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label for="nim" class="form-label fw-semibold text-heading">NIM <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-badge"></i></span>
                    <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim') }}"
                      placeholder="Contoh: 2108096001" required />
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="prodi" class="form-label fw-semibold text-heading">Program Studi <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-book"></i></span>
                    <input type="text" class="form-control" id="prodi" name="prodi" value="{{ old('prodi') }}"
                      placeholder="Contoh: Ilmu Komunikasi" required />
                  </div>
                </div>
              </div>

              <!-- Angkatan & No WhatsApp -->
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label for="angkatan" class="form-label fw-semibold text-heading">Tahun Angkatan</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                    <input type="text" class="form-control" id="angkatan" name="angkatan"
                      value="{{ old('angkatan', date('Y')) }}" placeholder="{{ date('Y') }}" />
                  </div>
                </div>
                <div class="col-sm-6">
                  <label for="no_hp" class="form-label fw-semibold text-heading">Nomor WhatsApp</label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                      placeholder="081234567890" />
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold text-heading">Alamat Email Aktif <span
                    class="text-danger">*</span></label>
                <div class="input-group input-group-merge">
                  <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                  <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                    placeholder="nama@email.com" required />
                </div>
              </div>

              <!-- Password & Konfirmasi -->
              <div class="row g-3 mb-4">
                <div class="col-sm-6 form-password-toggle">
                  <label class="form-label fw-semibold text-heading" for="password">Kata Sandi <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                    <input type="password" id="password" class="form-control" name="password"
                      placeholder="Min. 6 karakter" required />
                    <span class="input-group-text cursor-pointer" onclick="togglePass('password', 'iconPass1')"><i
                        id="iconPass1" class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="col-sm-6 form-password-toggle">
                  <label class="form-label fw-semibold text-heading" for="password_confirmation">Ulangi Sandi <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                    <input type="password" id="password_confirmation" class="form-control"
                      name="password_confirmation" placeholder="Ulangi kata sandi" required />
                    <span class="input-group-text cursor-pointer"
                      onclick="togglePass('password_confirmation', 'iconPass2')"><i id="iconPass2"
                        class="bx bx-hide"></i></span>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100 fw-bold py-2 shadow-sm" type="submit">
                  Daftar Sekarang
                </button>
              </div>
            </form>

            <!-- Link to Login -->
            <p class="text-center mb-0 small text-muted">
              <span>Sudah memiliki akun?</span>
              <a href="{{ route('login') }}" class="text-primary fw-bold ms-1">
                <span>Masuk di sini</span>
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
      function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('bx-hide');
          icon.classList.add('bx-show');
        } else {
          input.type = 'password';
          icon.classList.remove('bx-show');
          icon.classList.add('bx-hide');
        }
      }
    </script>
  @endpush
@endsection
