@extends('layouts/contentNavbarLayout')

@section('title', 'Peminatan Magang Spesialis')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Magang Spesialis</h4>
        <p class="text-muted mb-0">Tentukan pilihan divisi magang awal yang sesuai dengan minat bakat jurnalistik & pengelolaan media Anda.</p>
    </div>

    <!-- Divisi Cards Overview -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-briefcase text-primary me-2"></i> Pilihan Divisi Magang Spesialis
                </h5>
                <span class="badge bg-label-primary">Peminatan Awal</span>
            </div>
            <div class="card-body pt-4">
                <p class="text-muted mb-4">
                    Pilihlah salah satu divisi magang di bawah ini. Pilihan minat Anda akan dipadukan dengan hasil perhitungan algoritma Profile Matching (NCF 60% & NSF 40%) pada 7 kriteria evaluasi untuk menentukan rekomendasi akhir.
                </p>

                <form action="{{ route('member.magang-spesialis.simpan') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold" for="pilihan_divisi">Pilih Divisi Magang Spesialis <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="pilihan_divisi" name="pilihan_divisi" required>
                            <option value="" disabled {{ empty($user->profil->pilihan_divisi_awal) ? 'selected' : '' }}>-- Pilih Divisi --</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->nama }}" {{ ($user->profil->pilihan_divisi_awal ?? '') === $divisi->nama ? 'selected' : '' }}>
                                    Divisi {{ $divisi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-check me-1"></i> Simpan Pilihan Minat Divisi
                        </button>
                    </div>
                </form>

                @if($user->profil && $user->profil->pilihan_divisi_awal)
                    <div class="alert alert-info mt-4 d-flex align-items-center gap-2 mb-0">
                        <i class="bx bx-info-circle fs-4"></i>
                        <div>
                            Pilihan minat aktif Anda saat ini: <strong>Divisi {{ $user->profil->pilihan_divisi_awal }}</strong>.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info Divisi Available -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-info-circle text-primary me-2"></i> Mengenal Divisi
                </h5>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex flex-column gap-3">
                    @foreach($divisis as $divisi)
                        <div class="card border shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="avatar avatar-sm">
                                        <span class="avatar-initial rounded {{ $divisi->nama === 'Redaksi' ? 'bg-label-primary' : 'bg-label-info' }}">
                                            <i class="bx {{ $divisi->nama === 'Redaksi' ? 'bx-news' : 'bx-paint' }}"></i>
                                        </span>
                                    </div>
                                    <h6 class="fw-bold text-heading mb-0">Divisi {{ $divisi->nama }}</h6>
                                </div>
                                <p class="small text-muted mb-0">
                                    {{ $divisi->deskripsi }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
