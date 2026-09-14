@extends('layouts/contentNavbarLayout')

@section('title', 'Data Calon Anggota')

@section('content')
<div class="row g-4" x-data="{ docModalOpen: false, activeCandidate: null }">
    <!-- Header & Filter Bar -->
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-2">
            <div class="card-body py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                    <div>
                        <h4 class="card-title fw-bold mb-1 text-heading">
                            <i class="bx bx-user-check text-primary me-2"></i> Data Anggota & Kelulusan
                        </h4>
                        <p class="text-muted mb-0 small">Verifikasi berkas persyaratan dan tetapkan keputusan kelulusan calon kru magang.</p>
                    </div>
                    <div>
                        <span class="badge bg-label-primary fs-6 px-3 py-2">
                            Total: {{ $calonAnggotas->count() }} Anggota
                        </span>
                    </div>
                </div>

                <!-- Search & Filter Form -->
                <form action="{{ route('admin.calon-anggota') }}" method="GET" class="row g-2">
                    <div class="col-12 col-md-7">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari nama pendaftar, NIM, atau email..." 
                                class="form-control"
                            >
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <select name="status" onchange="this.form.submit()" class="form-select">
                            <option value="">Semua Status Seleksi</option>
                            <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' || request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="proses_seleksi" {{ request('status') === 'proses_seleksi' ? 'selected' : '' }}>Proses Seleksi</option>
                            <option value="lolos" {{ request('status') === 'lolos' ? 'selected' : '' }}>Lolos</option>
                            <option value="tidak_lolos" {{ request('status') === 'tidak_lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- List of Member Cards (Sneat Theme with Current Layout Positioning) -->
    <div class="col-12">
        <div class="d-flex flex-column gap-4">
            @forelse($calonAnggotas as $ca)
                @php
                    $berkasList = $ca->berkas;
                    $cvBerkas = $berkasList->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'cv') || str_contains(strtolower($b->jenis_berkas), 'curriculum'));
                    $esaiBerkas = $berkasList->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'esai') || str_contains(strtolower($b->jenis_berkas), 'essay'));
                    $fotoBerkas = $berkasList->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'foto'));
                    $karyaBerkas = $berkasList->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'karya') || str_contains(strtolower($b->jenis_berkas), 'portofolio'));
                    
                    // Status validitas berkas
                    $isCvValid = $cvBerkas && $cvBerkas->status === 'diverifikasi';
                    $isEsaiValid = $esaiBerkas && $esaiBerkas->status === 'diverifikasi';
                    $isFotoValid = $fotoBerkas && $fotoBerkas->status === 'diverifikasi';
                    $isKaryaValid = $karyaBerkas && $karyaBerkas->status === 'diverifikasi';

                    $allBerkasValid = $isCvValid && $isEsaiValid && $isFotoValid && $isKaryaValid;
                    $hasRejectedBerkas = ($cvBerkas && $cvBerkas->status === 'ditolak') ||
                                         ($esaiBerkas && $esaiBerkas->status === 'ditolak') ||
                                         ($fotoBerkas && $fotoBerkas->status === 'ditolak') ||
                                         ($karyaBerkas && $karyaBerkas->status === 'ditolak');

                    // 3 Kolom Status Tahapan Seleksi dari Database:
                    // 1. seleksi_administrasi ('lolos' | 'tidak_lolos' | null)
                    // 2. tes_tulis_wawancara ('lolos' | 'tidak_lolos' | null)
                    // 3. cakruma ('lolos' | 'tidak_lolos' | null)
                    $admStatus = $ca->profil->seleksi_administrasi ?? null;
                    $tesStatus = $ca->profil->tes_tulis_wawancara ?? null;
                    $cakStatus = $ca->profil->cakruma ?? null;

                    if (!$admStatus && $allBerkasValid) {
                        $admStatus = 'lolos';
                    } elseif (!$admStatus && $hasRejectedBerkas) {
                        $admStatus = 'tidak_lolos';
                    }

                    // Ketergantungan Tahap (Cascading Visibility):
                    // Step 2 hanya muncul jika Step 1 Lolos
                    $showStep2 = ($admStatus === 'lolos');
                    // Step 3 hanya muncul jika Step 1 Lolos DAN Step 2 Lolos
                    $showStep3 = ($admStatus === 'lolos' && $tesStatus === 'lolos');

                    // Status Perhitungan:
                    // 1. Jika ada berkas ditolak atau salah satu tahap 'tidak_lolos' -> TIDAK LOLOS
                    // 2. Jika berkas belum lengkap terverifikasi -> MENUNGGU VERIFIKASI
                    // 3. Jika ketiganya lolos -> LOLOS
                    // 4. Jika ada yang null -> PROSES SELEKSI
                    $isTidakLolos = $hasRejectedBerkas || ($admStatus === 'tidak_lolos') || ($tesStatus === 'tidak_lolos') || ($cakStatus === 'tidak_lolos');
                    $isMenungguVerifikasi = !$allBerkasValid && !$hasRejectedBerkas;
                    $isLolos = ($admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos');

                    // Cek jika ada berkas yang baru diunggah ulang/diperbarui oleh calon anggota
                    $hasUpdatedBerkas = $ca->berkas->some(fn($b) => $b->updated_at && $b->created_at && $b->updated_at->gt($b->created_at) && $b->status === 'menunggu');
                @endphp

                <!-- Card Anggota -->
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <!-- Top Header Row -->
                    <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <!-- Left: Avatar Icon + Nama -->
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-sm bg-label-primary rounded-circle d-flex align-items-center justify-content-center p-2">
                                <i class="bx bx-user text-primary fs-4"></i>
                            </div>
                            <div>
                                <span class="fw-bold text-heading fs-5 text-uppercase">{{ $ca->name }}</span>
                                <span class="text-muted ms-2 small d-none d-md-inline">({{ $ca->profil->prodi ?? 'Prodi Belum Diisi' }})</span>
                            </div>
                        </div>

                        <!-- Right: Status Badge + NIM -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($hasUpdatedBerkas)
                                <span class="badge bg-label-info border border-info rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center animate-pulse" title="Terdapat dokumen yang baru diunggah ulang oleh calon anggota">
                                    <i class="bx bx-bell bx-tada me-1"></i> Berkas Baru Diperbarui
                                </span>
                            @endif

                            @if($isTidakLolos)
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center">
                                    <i class="bx bx-x-circle me-1"></i> TIDAK LOLOS
                                </span>
                            @elseif($isMenungguVerifikasi)
                                <span class="badge bg-label-warning rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center">
                                    <i class="bx bx-time-five me-1"></i> MENUNGGU VERIFIKASI
                                </span>
                            @elseif($isLolos)
                                <span class="badge bg-label-success rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center">
                                    <i class="bx bx-check-circle me-1"></i> LOLOS
                                </span>
                            @else
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center">
                                    <i class="bx bx-sync me-1"></i> PROSES SELEKSI
                                </span>
                            @endif

                            <span class="badge bg-label-secondary text-secondary rounded-pill px-3 py-2 fw-bold">
                                NIM: {{ $ca->profil->nim ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body 2 Kolom -->
                    <div class="card-body p-4">
                        <div class="row g-4 align-items-stretch">
                            <!-- Kolom Kiri: CEK DOKUMEN (2x2 Pill Buttons & Verifikasi) -->
                            <div class="col-12 col-lg-6 pe-lg-4 border-end-lg">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bx bx-folder-open text-primary fs-5"></i>
                                        <span class="fw-bold text-heading text-uppercase small">CEK DOKUMEN:</span>
                                    </div>
                                    <small class="text-muted">Klik ikon <i class="bx bx-check-shield text-primary"></i> untuk verifikasi & beri catatan</small>
                                </div>

                                <div class="row g-3">
                                    <!-- 1. Curriculum Vitae (CV) -->
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-lighter d-flex flex-column gap-2 h-100 justify-content-between">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small text-heading"><i class="bx bx-file text-primary me-1"></i> CV</span>
                                                @if($cvBerkas)
                                                    @if($cvBerkas->status === 'diverifikasi')
                                                        <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check"></i> Valid</span>
                                                    @elseif($cvBerkas->status === 'ditolak')
                                                        <span class="badge bg-label-danger" style="font-size: 0.7rem;"><i class="bx bx-x"></i> Ditolak</span>
                                                    @else
                                                        <span class="badge bg-label-warning" style="font-size: 0.7rem;"><i class="bx bx-time"></i> Menunggu</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">Belum Ada</span>
                                                @endif
                                            </div>

                                            @if($cvBerkas && $cvBerkas->updated_at && $cvBerkas->created_at && $cvBerkas->updated_at->gt($cvBerkas->created_at) && $cvBerkas->status === 'menunggu')
                                                <div class="badge bg-label-info text-info border border-info text-truncate py-1 px-2" style="font-size: 0.68rem;" title="Diperbarui: {{ $cvBerkas->updated_at->format('d/m/Y H:i') }}">
                                                    <i class="bx bx-refresh me-1"></i> Baru Diperbarui
                                                </div>
                                            @endif

                                            @if($cvBerkas && $cvBerkas->status !== 'diverifikasi' && $cvBerkas->catatan)
                                                <div class="alert alert-danger p-1 mb-0 small text-truncate" style="font-size: 0.7rem;" title="{{ $cvBerkas->catatan }}">
                                                    <i class="bx bx-error-circle me-1"></i><strong>Catatan:</strong> {{ Str::limit($cvBerkas->catatan, 22) }}
                                                </div>
                                            @endif

                                            <div class="d-flex gap-1 mt-1">
                                                @if($cvBerkas)
                                                    <a href="{{ route('berkas.file', $cvBerkas->id) }}" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1 py-1 rounded-pill fw-semibold text-truncate" style="font-size: 0.75rem;">
                                                        <i class="bx bx-show me-1"></i> Lihat CV
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-primary py-1 px-2 rounded-pill shadow-none" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $cvBerkas->id }}" title="Verifikasi & Beri Catatan">
                                                        <i class="bx bx-check-shield"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 py-1 rounded-pill fw-semibold disabled" style="font-size: 0.75rem;">
                                                        <i class="bx bx-file me-1"></i> Lihat CV
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Esai -->
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-lighter d-flex flex-column gap-2 h-100 justify-content-between">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small text-heading"><i class="bx bx-edit-alt text-info me-1"></i> Essai</span>
                                                @if($esaiBerkas)
                                                    @if($esaiBerkas->status === 'diverifikasi')
                                                        <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check"></i> Valid</span>
                                                    @elseif($esaiBerkas->status === 'ditolak')
                                                        <span class="badge bg-label-danger" style="font-size: 0.7rem;"><i class="bx bx-x"></i> Ditolak</span>
                                                    @else
                                                        <span class="badge bg-label-warning" style="font-size: 0.7rem;"><i class="bx bx-time"></i> Menunggu</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">Belum Ada</span>
                                                @endif
                                            </div>

                                            @if($esaiBerkas && $esaiBerkas->updated_at && $esaiBerkas->created_at && $esaiBerkas->updated_at->gt($esaiBerkas->created_at) && $esaiBerkas->status === 'menunggu')
                                                <div class="badge bg-label-info text-info border border-info text-truncate py-1 px-2" style="font-size: 0.68rem;" title="Diperbarui: {{ $esaiBerkas->updated_at->format('d/m/Y H:i') }}">
                                                    <i class="bx bx-refresh me-1"></i> Baru Diperbarui
                                                </div>
                                            @endif

                                            @if($esaiBerkas && $esaiBerkas->status !== 'diverifikasi' && $esaiBerkas->catatan)
                                                <div class="alert alert-danger p-1 mb-0 small text-truncate" style="font-size: 0.7rem;" title="{{ $esaiBerkas->catatan }}">
                                                    <i class="bx bx-error-circle me-1"></i><strong>Catatan:</strong> {{ Str::limit($esaiBerkas->catatan, 22) }}
                                                </div>
                                            @endif

                                            <div class="d-flex gap-1 mt-1">
                                                @if($esaiBerkas)
                                                    <a href="{{ route('berkas.file', $esaiBerkas->id) }}" target="_blank" class="btn btn-sm btn-outline-info flex-grow-1 py-1 rounded-pill fw-semibold text-truncate" style="font-size: 0.75rem;">
                                                        <i class="bx bx-show me-1"></i> Baca Essai
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-info py-1 px-2 rounded-pill text-white shadow-none" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $esaiBerkas->id }}" title="Verifikasi & Beri Catatan">
                                                        <i class="bx bx-check-shield"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 py-1 rounded-pill fw-semibold disabled" style="font-size: 0.75rem;">
                                                        <i class="bx bx-edit-alt me-1"></i> Baca Essai
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Pas Foto -->
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-lighter d-flex flex-column gap-2 h-100 justify-content-between">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small text-heading"><i class="bx bx-image text-success me-1"></i> Foto</span>
                                                @if($fotoBerkas)
                                                    @if($fotoBerkas->status === 'diverifikasi')
                                                        <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check"></i> Valid</span>
                                                    @elseif($fotoBerkas->status === 'ditolak')
                                                        <span class="badge bg-label-danger" style="font-size: 0.7rem;"><i class="bx bx-x"></i> Ditolak</span>
                                                    @else
                                                        <span class="badge bg-label-warning" style="font-size: 0.7rem;"><i class="bx bx-time"></i> Menunggu</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">Belum Ada</span>
                                                @endif
                                            </div>

                                            @if($fotoBerkas && $fotoBerkas->updated_at && $fotoBerkas->created_at && $fotoBerkas->updated_at->gt($fotoBerkas->created_at) && $fotoBerkas->status === 'menunggu')
                                                <div class="badge bg-label-info text-info border border-info text-truncate py-1 px-2" style="font-size: 0.68rem;" title="Diperbarui: {{ $fotoBerkas->updated_at->format('d/m/Y H:i') }}">
                                                    <i class="bx bx-refresh me-1"></i> Baru Diperbarui
                                                </div>
                                            @endif

                                            @if($fotoBerkas && $fotoBerkas->status !== 'diverifikasi' && $fotoBerkas->catatan)
                                                <div class="alert alert-danger p-1 mb-0 small text-truncate" style="font-size: 0.7rem;" title="{{ $fotoBerkas->catatan }}">
                                                    <i class="bx bx-error-circle me-1"></i><strong>Catatan:</strong> {{ Str::limit($fotoBerkas->catatan, 22) }}
                                                </div>
                                            @endif

                                            <div class="d-flex gap-1 mt-1">
                                                @if($fotoBerkas)
                                                    <a href="{{ route('berkas.file', $fotoBerkas->id) }}" target="_blank" class="btn btn-sm btn-outline-success flex-grow-1 py-1 rounded-pill fw-semibold text-truncate" style="font-size: 0.75rem;">
                                                        <i class="bx bx-show me-1"></i> Lihat Foto
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-success py-1 px-2 rounded-pill shadow-none" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $fotoBerkas->id }}" title="Verifikasi & Beri Catatan">
                                                        <i class="bx bx-check-shield"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 py-1 rounded-pill fw-semibold disabled" style="font-size: 0.75rem;">
                                                        <i class="bx bx-image me-1"></i> Lihat Foto
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. Karya Pribadi -->
                                    <div class="col-6">
                                        <div class="border rounded-3 p-2 bg-lighter d-flex flex-column gap-2 h-100 justify-content-between">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold small text-heading"><i class="bx bx-palette text-warning me-1"></i> Karya</span>
                                                @if($karyaBerkas)
                                                    @if($karyaBerkas->status === 'diverifikasi')
                                                        <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check"></i> Valid</span>
                                                    @elseif($karyaBerkas->status === 'ditolak')
                                                        <span class="badge bg-label-danger" style="font-size: 0.7rem;"><i class="bx bx-x"></i> Ditolak</span>
                                                    @else
                                                        <span class="badge bg-label-warning" style="font-size: 0.7rem;"><i class="bx bx-time"></i> Menunggu</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">Belum Ada</span>
                                                @endif
                                            </div>

                                            @if($karyaBerkas && $karyaBerkas->updated_at && $karyaBerkas->created_at && $karyaBerkas->updated_at->gt($karyaBerkas->created_at) && $karyaBerkas->status === 'menunggu')
                                                <div class="badge bg-label-info text-info border border-info text-truncate py-1 px-2" style="font-size: 0.68rem;" title="Diperbarui: {{ $karyaBerkas->updated_at->format('d/m/Y H:i') }}">
                                                    <i class="bx bx-refresh me-1"></i> Baru Diperbarui
                                                </div>
                                            @endif

                                            @if($karyaBerkas && $karyaBerkas->status !== 'diverifikasi' && $karyaBerkas->catatan)
                                                <div class="alert alert-danger p-1 mb-0 small text-truncate" style="font-size: 0.7rem;" title="{{ $karyaBerkas->catatan }}">
                                                    <i class="bx bx-error-circle me-1"></i><strong>Catatan:</strong> {{ Str::limit($karyaBerkas->catatan, 22) }}
                                                </div>
                                            @endif

                                            <div class="d-flex gap-1 mt-1">
                                                @if($karyaBerkas)
                                                    <a href="{{ route('berkas.file', $karyaBerkas->id) }}" target="_blank" class="btn btn-sm btn-outline-warning flex-grow-1 py-1 rounded-pill fw-semibold text-truncate" style="font-size: 0.75rem;">
                                                        <i class="bx bx-show me-1"></i> Lihat Karya
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-warning py-1 px-2 rounded-pill text-white shadow-none" data-bs-toggle="modal" data-bs-target="#modalVerif{{ $karyaBerkas->id }}" title="Verifikasi & Beri Catatan">
                                                        <i class="bx bx-check-shield"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 py-1 rounded-pill fw-semibold disabled" style="font-size: 0.75rem;">
                                                        <i class="bx bx-palette me-1"></i> Lihat Karya
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: KEPUTUSAN KELULUSAN (PILIH SALAH SATU) -->
                            <div class="col-12 col-lg-6 ps-lg-4 d-flex flex-column justify-content-between">
                                <form action="{{ route('admin.calon-anggota.status', $ca->id) }}" method="POST">
                                    @csrf
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <i class="bx bx-target-lock text-primary fs-5"></i>
                                            <span class="fw-bold text-heading text-uppercase small">KEPUTUSAN KELULUSAN (3 TAHAPAN):</span>
                                        </div>

                                        <!-- Tahap 1. Seleksi Administrasi -->
                                        <div class="mb-3 p-2 bg-lighter rounded-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="fw-semibold text-heading small">1. Seleksi Administrasi</div>
                                                @if($allBerkasValid)
                                                    <span class="badge bg-label-success" style="font-size: 0.7rem;"><i class="bx bx-check-circle me-1"></i> Semua Berkas Valid</span>
                                                @elseif($hasRejectedBerkas)
                                                    <span class="badge bg-label-danger" style="font-size: 0.7rem;"><i class="bx bx-x-circle me-1"></i> Ada Berkas Ditolak</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-4 ps-2">
                                                <span class="text-primary fw-bold">=&gt;</span>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="seleksi_administrasi" 
                                                        id="adm_lolos_{{ $ca->id }}" 
                                                        value="lolos" 
                                                        onchange="handleStepTransition({{ $ca->id }}, 'adm', 'lolos')"
                                                        {{ $admStatus === 'lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label fw-semibold text-success" for="adm_lolos_{{ $ca->id }}">Lolos</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="seleksi_administrasi" 
                                                        id="adm_tidak_{{ $ca->id }}" 
                                                        value="tidak_lolos"
                                                        onchange="handleStepTransition({{ $ca->id }}, 'adm', 'tidak_lolos')"
                                                        {{ $admStatus === 'tidak_lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label text-danger" for="adm_tidak_{{ $ca->id }}">Tidak Lolos</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tahap 2. Tes Tulis & Wawancara (Hanya muncul jika Tahap 1 Lolos) -->
                                        <div id="step2_container_{{ $ca->id }}" class="{{ !$showStep2 ? 'd-none' : '' }} mb-3 p-2 bg-lighter rounded-2">
                                            <div class="fw-semibold text-heading mb-1 small">2. Tes Tulis & Wawancara</div>
                                            <div class="d-flex align-items-center gap-4 ps-2">
                                                <span class="text-primary fw-bold">=&gt;</span>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="tes_tulis_wawancara" 
                                                        id="tes_lolos_{{ $ca->id }}" 
                                                        value="lolos" 
                                                        onchange="handleStepTransition({{ $ca->id }}, 'tes', 'lolos')"
                                                        {{ $tesStatus === 'lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label fw-semibold text-success" for="tes_lolos_{{ $ca->id }}">Lolos</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="tes_tulis_wawancara" 
                                                        id="tes_tidak_{{ $ca->id }}" 
                                                        value="tidak_lolos"
                                                        onchange="handleStepTransition({{ $ca->id }}, 'tes', 'tidak_lolos')"
                                                        {{ $tesStatus === 'tidak_lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label text-danger" for="tes_tidak_{{ $ca->id }}">Tidak Lolos</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tahap 3. Penetapan Cakruma (Hanya muncul jika Tahap 1 Lolos & Tahap 2 Lolos) -->
                                        <div id="step3_container_{{ $ca->id }}" class="{{ !$showStep3 ? 'd-none' : '' }} mb-3 p-2 bg-lighter rounded-2">
                                            <div class="fw-semibold text-heading mb-1 small">3. Penetapan Cakruma</div>
                                            <div class="d-flex align-items-center gap-4 ps-2">
                                                <span class="text-primary fw-bold">=&gt;</span>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="cakruma" 
                                                        id="cak_lolos_{{ $ca->id }}" 
                                                        value="lolos" 
                                                        onchange="handleStepTransition({{ $ca->id }}, 'cak', 'lolos')"
                                                        {{ $cakStatus === 'lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label fw-semibold text-success" for="cak_lolos_{{ $ca->id }}">Lolos</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="radio" 
                                                        name="cakruma" 
                                                        id="cak_tidak_{{ $ca->id }}" 
                                                        value="tidak_lolos"
                                                        onchange="handleStepTransition({{ $ca->id }}, 'cak', 'tidak_lolos')"
                                                        {{ $cakStatus === 'tidak_lolos' ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label text-danger" for="cak_tidak_{{ $ca->id }}">Tidak Lolos</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Panel Informasi / Alur Tahapan -->
                                        <div id="step_info_{{ $ca->id }}" class="{{ $showStep3 && $cakStatus ? 'd-none' : '' }} p-3 border border-dashed rounded-2 text-center text-muted bg-lighter mb-3">
                                            <i class="bx bx-info-circle fs-4 text-secondary mb-1"></i>
                                            <div class="small text-muted" id="step_info_text_{{ $ca->id }}" style="font-size: 0.75rem;">
                                                @if(!$showStep2)
                                                    @if($admStatus === 'tidak_lolos')
                                                        <span class="text-danger fw-semibold"><i class="bx bx-x-circle me-1"></i> Tidak lolos Seleksi Administrasi. Tahap berikutnya tidak dimunculkan dan otomatis Tidak Lolos.</span>
                                                    @else
                                                        Pilih <strong>Lolos</strong> pada tahap 1 (Seleksi Administrasi) untuk membuka tahap 2 (Tes Tulis & Wawancara).
                                                    @endif
                                                @elseif(!$showStep3)
                                                    @if($tesStatus === 'tidak_lolos')
                                                        <span class="text-danger fw-semibold"><i class="bx bx-x-circle me-1"></i> Tidak lolos Tes Tulis & Wawancara. Tahap 3 (Penetapan Cakruma) tidak dimunculkan dan otomatis Tidak Lolos.</span>
                                                    @else
                                                        Pilih <strong>Lolos</strong> pada tahap 2 (Tes Tulis & Wawancara) untuk membuka tahap 3 (Penetapan Cakruma).
                                                    @endif
                                                @else
                                                    Tahap 1 & 2 Lolos. Silakan tentukan keputusan akhir pada <strong>3. Penetapan Cakruma</strong>.
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Right: SIMPAN DATA Button -->
                                    <div class="d-flex justify-content-end pt-2">
                                        <button type="submit" class="btn btn-primary rounded-pill fw-semibold px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                                            <i class="bx bx-save fs-5"></i> SIMPAN DATA
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modals Verifikasi untuk Setiap Berkas Calon Anggota Ini -->
                @foreach($ca->berkas as $b)
                    <div class="modal fade" id="modalVerif{{ $b->id }}" tabindex="-1" aria-labelledby="modalVerifLabel{{ $b->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('admin.calon-anggota.verifikasi', $b->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header border-bottom">
                                        <h5 class="modal-title fw-bold" id="modalVerifLabel{{ $b->id }}">
                                            <i class="bx bx-check-shield text-primary me-2"></i> Verifikasi {{ $b->jenis_berkas }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Detail Berkas & Calon -->
                                        <div class="p-3 bg-lighter rounded-3 mb-3 border">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Nama Calon:</span>
                                                <span class="fw-bold text-dark small">{{ $ca->name }} (NIM: {{ $ca->profil->nim ?? '-' }})</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Nama File:</span>
                                                <span class="fw-bold text-primary small text-truncate" style="max-width: 240px;">{{ $b->nama_file }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Ukuran File:</span>
                                                <span class="text-dark small">{{ round($b->ukuran_file / 1024, 1) }} KB</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Waktu Unggah Pertama:</span>
                                                <span class="text-dark small">{{ $b->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                            @if($b->updated_at > $b->created_at)
                                                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                                    <span class="text-info fw-bold small"><i class="bx bx-bell me-1"></i> Baru Diperbarui:</span>
                                                    <span class="badge bg-label-info small">{{ $b->updated_at->format('d M Y H:i') }} ({{ $b->updated_at->diffForHumans() }})</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mb-3 text-center">
                                            <a href="{{ route('berkas.file', $b->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                                                <i class="bx bx-link-external me-1"></i> Buka / Pratinjau Dokumen Asli
                                            </a>
                                        </div>

                                        <!-- Radio Pilihan Status Verifikasi -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-uppercase">Keputusan Verifikasi Berkas <span class="text-danger">*</span></label>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <input type="radio" class="btn-check" name="status" id="status_div_{{ $b->id }}" value="diverifikasi" {{ $b->status === 'diverifikasi' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success w-100 py-2 d-flex flex-column align-items-center gap-1 shadow-none rounded-3" for="status_div_{{ $b->id }}">
                                                        <i class="bx bx-check-circle fs-4"></i>
                                                        <span class="small fw-bold">Diverifikasi</span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="radio" class="btn-check" name="status" id="status_dit_{{ $b->id }}" value="ditolak" {{ $b->status === 'ditolak' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger w-100 py-2 d-flex flex-column align-items-center gap-1 shadow-none rounded-3" for="status_dit_{{ $b->id }}">
                                                        <i class="bx bx-x-circle fs-4"></i>
                                                        <span class="small fw-bold">Perlu Perbaikan</span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <input type="radio" class="btn-check" name="status" id="status_men_{{ $b->id }}" value="menunggu" {{ $b->status === 'menunggu' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-warning w-100 py-2 d-flex flex-column align-items-center gap-1 shadow-none rounded-3" for="status_men_{{ $b->id }}">
                                                        <i class="bx bx-time-five fs-4"></i>
                                                        <span class="small fw-bold">Menunggu</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Input Catatan / Catatan Perbaikan -->
                                        <div class="mb-2">
                                            <label for="catatan_{{ $b->id }}" class="form-label fw-bold small text-uppercase">Catatan / Alasan Perbaikan untuk Calon Anggota</label>
                                            <textarea class="form-control" name="catatan" id="catatan_{{ $b->id }}" rows="3" placeholder="Contoh: Format CV belum mencantumkan riwayat organisasi atau foto buram, mohon upload ulang...">{{ old('catatan', $b->catatan) }}</textarea>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bx bx-info-circle me-1"></i> Catatan ini akan langsung tampil di akun calon anggota agar mereka mengetahui bagian yang perlu diperbaiki.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4">
                                            <i class="bx bx-save me-1"></i> Simpan Verifikasi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="text-center py-5">
                    <div class="card shadow-sm p-4 text-muted">
                        <i class="bx bx-user-x fs-1 text-secondary mb-2"></i>
                        Tidak ditemukan data calon anggota yang cocok dengan filter pencarian.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
@media (min-width: 992px) {
    .border-end-lg {
        border-right: 1px solid #ebedef !important;
    }
}
.bg-lighter {
    background-color: #f8f9fa;
}
</style>

<script>
function handleStepTransition(userId, step, value) {
    const step2Container = document.getElementById('step2_container_' + userId);
    const tesLolos = document.getElementById('tes_lolos_' + userId);
    const tesTidak = document.getElementById('tes_tidak_' + userId);

    const step3Container = document.getElementById('step3_container_' + userId);
    const cakLolos = document.getElementById('cak_lolos_' + userId);
    const cakTidak = document.getElementById('cak_tidak_' + userId);

    const infoBox = document.getElementById('step_info_' + userId);
    const infoText = document.getElementById('step_info_text_' + userId);

    if (step === 'adm') {
        if (value === 'lolos') {
            // Tahap 1 diubah ke lolos:
            // Buka tahap 2 dalam keadaan unselected / null jika baru dibuka
            if (step2Container) step2Container.classList.remove('d-none');
            if (tesLolos && !tesLolos.checked && tesTidak && !tesTidak.checked) {
                // Biarkan null
            }

            // Jika tes belum lolos, sembunyikan tahap 3
            if (!tesLolos || !tesLolos.checked) {
                if (step3Container) step3Container.classList.add('d-none');
                if (cakLolos) cakLolos.checked = false;
                if (cakTidak) cakTidak.checked = false;
            }

            if (infoBox) {
                infoBox.classList.remove('d-none');
                infoText.innerHTML = 'Tahap 1 Lolos. Silakan tentukan keputusan pada <strong>2. Tes Tulis & Wawancara</strong>.';
            }
        } else {
            // Tahap 1 diubah ke tidak lolos:
            // Sembunyikan Step 2 dan Step 3, reset nilai keduanya ke null
            if (step2Container) step2Container.classList.add('d-none');
            if (tesLolos) tesLolos.checked = false;
            if (tesTidak) tesTidak.checked = false;

            if (step3Container) step3Container.classList.add('d-none');
            if (cakLolos) cakLolos.checked = false;
            if (cakTidak) cakTidak.checked = false;

            if (infoBox) {
                infoBox.classList.remove('d-none');
                infoText.innerHTML = '<span class="text-danger fw-semibold"><i class="bx bx-x-circle me-1"></i> Tidak lolos Seleksi Administrasi. Tahap berikutnya tidak dimunculkan dan otomatis Tidak Lolos.</span>';
            }
        }
    } else if (step === 'tes') {
        if (value === 'lolos') {
            // Tahap 2 diubah ke lolos:
            // Buka tahap 3 dalam keadaan unselected / null
            if (step3Container) step3Container.classList.remove('d-none');
            if (infoBox) {
                infoBox.classList.remove('d-none');
                infoText.innerHTML = 'Tahap 2 Lolos. Silakan tentukan keputusan pada <strong>3. Penetapan Cakruma</strong>.';
            }
        } else {
            // Tahap 2 Tidak Lolos:
            // Sembunyikan Step 3 dan reset ke null
            if (step3Container) step3Container.classList.add('d-none');
            if (cakLolos) cakLolos.checked = false;
            if (cakTidak) cakTidak.checked = false;

            if (infoBox) {
                infoBox.classList.remove('d-none');
                infoText.innerHTML = '<span class="text-danger fw-semibold"><i class="bx bx-x-circle me-1"></i> Tidak lolos Tes Tulis & Wawancara. Tahap 3 (Penetapan Cakruma) tidak dimunculkan dan otomatis Tidak Lolos.</span>';
            }
        }
    } else if (step === 'cak') {
        if (value === 'lolos') {
            if (infoBox) {
                infoBox.classList.add('d-none');
            }
        } else {
            if (infoBox) {
                infoBox.classList.remove('d-none');
                infoText.innerHTML = '<span class="text-danger fw-semibold"><i class="bx bx-x-circle me-1"></i> Calon anggota dinyatakan Tidak Lolos pada Penetapan Cakruma.</span>';
            }
        }
    }
}
</script>
@endsection

