@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard Pengurus')

@section('content')
  <div class="row g-4">
    <!-- Header Card -->
    <div class="col-12">
      <div class="card bg-primary text-white shadow-sm border-0">
        <div class="card-body p-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <span class="badge bg-white text-primary mb-2 fw-bold">SPK Penempatan Divisi Magang</span>
              <h4 class="card-title text-white mb-1 fw-bold">Dashboard Pengurus & HRD SKM Amanat</h4>
              <p class="card-text text-white-50 mb-0">
                Kelola seleksi calon anggota, konfigurasi kriteria Profile Matching, penugasan, presensi, serta penetapan
                divisi magang Redaksi & Konten secara objektif dan terukur.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <!-- 1. Total Pendaftar (Full Biru) -->
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 text-white shadow"
        style="background: linear-gradient(135deg, #696cff 0%, #484be2 100%);">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-uppercase fs-tiny fw-semibold"
                style="color: rgba(255, 255, 255, 0.85); letter-spacing: 0.5px;">Total Pendaftar</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-white">{{ $totalPendaftar }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                <i class="bx bx-group bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Total Cakruma Aktif (Full Oranye/Kuning) -->
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 text-white shadow"
        style="background: linear-gradient(135deg, #ffab00 0%, #e08700 100%);">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-uppercase fs-tiny fw-semibold"
                style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Total Cakruma Aktif</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-white">{{ $totalCakrumaAktif }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                <i class="bx bx-user-check bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. Rekomendasi Redaksi (Full Hijau) -->
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 text-white shadow"
        style="background: linear-gradient(135deg, #71dd37 0%, #4fad1e 100%);">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-uppercase fs-tiny fw-semibold"
                style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Rekomendasi Redaksi</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-white">{{ $countRedaksi }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                <i class="bx bx-pen bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Rekomendasi Konten (Full Cyan) -->
    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 text-white shadow"
        style="background: linear-gradient(135deg, #03c3ec 0%, #0294b3 100%);">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-uppercase fs-tiny fw-semibold"
                style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Rekomendasi Konten</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-white">{{ $countKonten }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                <i class="bx bx-video bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Candidates Table -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom">
          <h5 class="card-title mb-0 fw-bold">
            <i class="bx bx-group me-1 text-primary"></i> Calon Anggota Terbaru & Status Rekomendasi
          </h5>
          <a href="{{ route('admin.calon-anggota') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="bx bx-chevron-right ms-1"></i>
          </a>
        </div>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Nama Lengkap</th>
                <th>NIM / Prodi</th>
                <th>Pilihan Minat</th>
                <th class="text-center">Status Berkas</th>
                <th class="text-center">Rekomendasi PM</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse($cakrumaTerbaru as $ca)
                <tr>
                  <td>
                    <div class="fw-bold text-heading">{{ $ca->name }}</div>
                    <small class="text-muted">{{ $ca->email }}</small>
                  </td>
                  <td>
                    <div class="fw-semibold">{{ $ca->profil->nim ?? '-' }}</div>
                    <small class="text-muted">{{ $ca->profil->prodi ?? '-' }}</small>
                  </td>
                  <td>
                    <span
                      class="fw-semibold text-heading">{{ $ca->profil->pilihan_divisi_awal ?? 'Belum memilih' }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-label-secondary">
                      {{ $ca->berkas->count() }}/4 Berkas
                    </span>
                  </td>
                  <td class="text-center">
                    @php
                      $admStatus = $ca->profil->seleksi_administrasi ?? null;
                      $tesStatus = $ca->profil->tes_tulis_wawancara ?? null;
                      $cakStatus = $ca->profil->cakruma ?? null;
                      $hasRejected = $ca->berkas->some(fn($b) => $b->status === 'ditolak');
                      $isLolosSemua =
                          $admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos' && !$hasRejected;

                      $rec = $ca->hasilProfileMatching->firstWhere('rekomendasi', true);
                    @endphp
                    @if (!$isLolosSemua)
                      <span class="badge bg-label-secondary text-muted" title="Belum lolos semua tahapan seleksi">Belum
                        Memenuhi Syarat</span>
                    @elseif($rec)
                      <span class="badge {{ $rec->divisi->nama === 'Redaksi' ? 'bg-label-success' : 'bg-label-info' }}">
                        {{ $rec->divisi->nama }} ({{ number_format($rec->nilai_total, 2) }})
                      </span>
                    @else
                      <span class="badge bg-label-warning">Belum dihitung</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.calon-anggota', ['search' => $ca->name]) }}" class="btn btn-xs btn-primary">
                      <i class="bx bx-show me-1"></i> Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada data calon anggota.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
