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
              <h4 class="card-title text-white mb-1 fw-bold">Dashboard Pengurus &amp; HRD SKM Amanat</h4>
              <p class="card-text text-white-50 mb-0">
                Kelola seleksi calon anggota, konfigurasi kriteria Profile Matching, penugasan, presensi, serta penetapan
                divisi magang Redaksi &amp; Konten secara objektif dan terukur.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <!-- 1. Total Pendaftar -->
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading text-uppercase fs-tiny fw-medium">Total Pendaftar</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-primary">{{ $totalPendaftar }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-primary">
                <i class="bx bx-group bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Total Cakruma Aktif -->
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading text-uppercase fs-tiny fw-medium">Total Cakruma Aktif</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-warning">{{ $totalCakrumaAktif }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-warning">
                <i class="bx bx-user-check bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 3. Rekomendasi Redaksi -->
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading text-uppercase fs-tiny fw-medium">Rekomendasi Redaksi</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-success">{{ $countRedaksi }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-success">
                <i class="bx bx-pen bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 4. Rekomendasi Konten -->
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span class="text-heading text-uppercase fs-tiny fw-medium">Rekomendasi Konten</span>
              <div class="d-flex align-items-center my-1">
                <h3 class="mb-0 me-2 fw-bold text-info">{{ $countKonten }}</h3>
              </div>
            </div>
            <div class="avatar">
              <span class="avatar-initial rounded bg-label-info">
                <i class="bx bx-video bx-sm"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Seluruh Anggota & Divisi Rekomendasi -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
          <h5 class="card-title mb-0 fw-bold">
            <i class="bx bx-trophy me-1 text-primary"></i> Rekomendasi Divisi Seluruh Anggota
          </h5>
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Filter tabs -->
            <div class="btn-group btn-group-sm" role="group" id="filterDivisi">
              <button type="button" class="btn btn-outline-secondary active" data-filter="semua">Semua</button>
              <button type="button" class="btn btn-outline-success" data-filter="Redaksi">
                <i class="bx bx-pen me-1"></i>Redaksi
                <span class="badge bg-success ms-1">{{ $countRedaksi }}</span>
              </button>
              <button type="button" class="btn btn-outline-info" data-filter="Konten">
                <i class="bx bx-video me-1"></i>Konten
                <span class="badge bg-info ms-1">{{ $countKonten }}</span>
              </button>
              <button type="button" class="btn btn-outline-secondary" data-filter="belum">Belum Dihitung</button>
            </div>
            <!-- Live search -->
            <input type="text" id="searchAnggota" class="form-control form-control-sm"
              placeholder="Cari nama / NIM..." style="width: 200px;">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="tabelAnggota">
            <thead class="table-light">
              <tr>
                <th class="text-center" style="width:50px">#</th>
                <th>Nama Anggota</th>
                <th>NIM / Prodi</th>
                <th class="text-center">Divisi Rekomendasi</th>
                <th class="text-center">Nilai PM</th>
                <th class="text-center">Status Seleksi</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody id="tabelAnggotaBody">
              @php $rankNo = 1; @endphp
              @forelse($semuaAnggota as $ca)
                @php
                  $admStatus   = $ca->profil->seleksi_administrasi ?? null;
                  $tesStatus   = $ca->profil->tes_tulis_wawancara ?? null;
                  $cakStatus   = $ca->profil->cakruma ?? null;
                  $hasRejected = $ca->berkas->some(fn($b) => $b->status === 'ditolak');
                  $isLolos     = $admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos' && !$hasRejected;
                  $isTidakLolos = $admStatus === 'tidak_lolos' || $tesStatus === 'tidak_lolos' || $cakStatus === 'tidak_lolos' || $hasRejected;
                  $rec = $ca->hasilProfileMatching->firstWhere('rekomendasi', true);
                  $divisiNama = $rec?->divisi?->nama ?? null;
                @endphp
                <tr
                  data-divisi="{{ $divisiNama ?? 'belum' }}"
                  data-nama="{{ strtolower($ca->name) }}"
                  data-nim="{{ strtolower($ca->profil->nim ?? '') }}"
                >
                  <td class="text-center">
                    @if($rec && $isLolos)
                      <span class="fw-bold text-{{ $divisiNama === 'Redaksi' ? 'success' : 'info' }}">{{ $rankNo++ }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <div class="fw-bold text-heading">{{ $ca->name }}</div>
                    <small class="text-muted">{{ $ca->email }}</small>
                  </td>
                  <td>
                    <div class="fw-semibold">{{ $ca->profil->nim ?? '-' }}</div>
                    <small class="text-muted">{{ $ca->profil->prodi ?? '-' }}</small>
                  </td>
                  <td class="text-center">
                    @if($isTidakLolos)
                      <span class="badge bg-label-danger">Tidak Lolos Seleksi</span>
                    @elseif(!$isLolos)
                      <span class="badge bg-label-warning">Seleksi Belum Selesai</span>
                    @elseif($rec && $divisiNama)
                      <span class="badge {{ $divisiNama === 'Redaksi' ? 'bg-label-success' : 'bg-label-info' }} fw-bold px-2 py-1">
                        <i class="bx {{ $divisiNama === 'Redaksi' ? 'bx-pen' : 'bx-video' }} me-1"></i>{{ $divisiNama }}
                      </span>
                    @else
                      <span class="badge bg-label-secondary">Belum Dihitung</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($rec && $isLolos)
                      <span class="fw-bold text-heading">{{ number_format($rec->nilai_total, 2) }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td class="text-center">
                    @if($isTidakLolos)
                      <span class="badge bg-label-danger">Tidak Lolos</span>
                    @elseif($admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos')
                      <span class="badge bg-label-success">Lolos Semua</span>
                    @elseif($admStatus === 'lolos')
                      <span class="badge bg-label-info">Adm ✓</span>
                    @else
                      <span class="badge bg-label-secondary">Pendaftar</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <a href="{{ route('admin.calon-anggota', ['search' => $ca->name]) }}" class="btn btn-xs btn-primary">
                      <i class="bx bx-show me-1"></i>Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada data calon anggota.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer text-muted small text-end py-2 px-3">
          Total {{ $semuaAnggota->count() }} anggota
          &bull; Redaksi: <strong class="text-success">{{ $countRedaksi }}</strong>
          &bull; Konten: <strong class="text-info">{{ $countKonten }}</strong>
        </div>
      </div>
    </div>
  </div>

  @push('page-scripts')
  <script>
    document.querySelectorAll('#filterDivisi button').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.querySelectorAll('#filterDivisi button').forEach(function(b) {
          b.classList.remove('active');
        });
        this.classList.add('active');
        filterTable();
      });
    });

    document.getElementById('searchAnggota').addEventListener('input', filterTable);

    function filterTable() {
      var filter = (document.querySelector('#filterDivisi .active') || {}).dataset.filter || 'semua';
      var search = document.getElementById('searchAnggota').value.toLowerCase().trim();
      var rows   = document.querySelectorAll('#tabelAnggotaBody tr[data-nama]');
      rows.forEach(function(row) {
        var divisi = row.dataset.divisi || '';
        var nama   = row.dataset.nama   || '';
        var nim    = row.dataset.nim    || '';
        var matchDivisi = filter === 'semua'
          || (filter === 'belum' && divisi === 'belum')
          || divisi === filter;
        var matchSearch = search === '' || nama.includes(search) || nim.includes(search);
        row.style.display = (matchDivisi && matchSearch) ? '' : 'none';
      });
    }
  </script>
  @endpush
@endsection
