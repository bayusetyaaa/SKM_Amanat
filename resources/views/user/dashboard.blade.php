@extends('layouts/contentNavbarLayout')

@section('title', 'Beranda Cakruma')

@section('content')
<div class="row g-4">
    <!-- Welcome Header Card -->
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4">
                <span class="badge bg-white text-primary mb-2 fw-bold">Portal Calon Kru Magang (Cakruma) 2026</span>
                <h4 class="card-title text-white mb-1 fw-bold">Selamat Datang, {{ $user->name }}!</h4>
                <p class="card-text text-white-50 mb-0">
                    Pantau proses seleksi pendaftaran, kumpulkan tugas penulisan/liputan, lakukan presensi kegiatan, dan lihat rekomendasi penempatan divisi magang Anda.
            </div>
        </div>
    </div>

    <!-- Alert jika ada berkas yang ditolak / perlu perbaikan -->
    @php
        $berkasDitolak = $user->berkas->filter(fn($b) => $b->status === 'ditolak' || ($b->status !== 'diverifikasi' && !empty($b->catatan)))->count();
    @endphp
    @if($berkasDitolak > 0)
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-between p-3 border-danger shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <i class="bx bx-error-circle fs-2 text-danger"></i>
                    <div>
                        <strong class="d-block text-danger fs-6">Perhatian: Terdapat {{ $berkasDitolak }} Berkas Perlu Perbaikan!</strong>
                        <span class="small text-dark">Admin verifikator telah memeriksa dokumen Anda dan memberikan catatan perbaikan. Mohon periksa dan unggah ulang berkas terkait.</span>
                    </div>
                </div>
                <a href="{{ route('member.profil') }}" class="btn btn-danger btn-sm fw-bold text-nowrap ms-3">
                    <i class="bx bx-edit me-1"></i> Perbaiki Berkas Sekarang
                </a>
            </div>
        </div>
    @endif

    @php
        $berkasList = $user->berkas;
        $isAllBerkasValid = $berkasList->count() >= 4 && $berkasList->every(fn($b) => $b->status === 'diverifikasi');
        $hasRejectedBerkas = $berkasList->some(fn($b) => $b->status === 'ditolak');

        $admStatus = $user->profil->seleksi_administrasi ?? null;
        $tesStatus = $user->profil->tes_tulis_wawancara ?? null;
        $cakStatus = $user->profil->cakruma ?? null;

        if (!$admStatus && $isAllBerkasValid) {
            $admStatus = 'lolos';
        } elseif (!$admStatus && $hasRejectedBerkas) {
            $admStatus = 'tidak_lolos';
        }

        if ($hasRejectedBerkas || $admStatus === 'tidak_lolos' || $tesStatus === 'tidak_lolos' || $cakStatus === 'tidak_lolos') {
            $userStatus = 'tidak_lolos';
        } elseif (!$isAllBerkasValid) {
            $userStatus = 'menunggu_verifikasi';
        } elseif ($admStatus === 'lolos' && $tesStatus === 'lolos' && $cakStatus === 'lolos') {
            $userStatus = 'lolos';
        } else {
            $userStatus = 'proses_seleksi';
        }
    @endphp

    <!-- Status Cards -->
    <div class="{{ $userStatus === 'tidak_lolos' ? 'col-md-6' : 'col-md-4' }}">
        @php
            $statusCardBg = match($userStatus) {
                'lolos' => 'background: linear-gradient(135deg, #71dd37 0%, #4fad1e 100%);',
                'tidak_lolos' => 'background: linear-gradient(135deg, #ff3e1d 0%, #c92305 100%);',
                'menunggu_verifikasi' => 'background: linear-gradient(135deg, #ffab00 0%, #e08700 100%);',
                default => 'background: linear-gradient(135deg, #696cff 0%, #484be2 100%);',
            };
        @endphp
        <div class="card h-100 border-0 text-white shadow" style="{{ $statusCardBg }}">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <span class="text-uppercase fs-tiny fw-semibold" style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Status Seleksi</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                            <i class="bx {{ $userStatus === 'tidak_lolos' ? 'bx-x' : 'bx-user-check' }}"></i>
                        </span>
                    </div>
                </div>
                <div>
                    @if($userStatus === 'lolos')
                        <span class="badge bg-white text-success fw-bold fs-6 px-3 py-1 shadow-sm">
                            <i class="bx bx-check-circle me-1"></i> LOLOS
                        </span>
                    @elseif($userStatus === 'tidak_lolos')
                        <span class="badge bg-white text-danger fw-bold fs-6 px-3 py-1 shadow-sm">
                            <i class="bx bx-x-circle me-1"></i> TIDAK LOLOS
                        </span>
                    @elseif($userStatus === 'menunggu_verifikasi')
                        <span class="badge bg-white text-warning fw-bold fs-6 px-3 py-1 shadow-sm">
                            <i class="bx bx-time-five me-1"></i> MENUNGGU VERIFIKASI
                        </span>
                    @else
                        <span class="badge bg-white text-primary fw-bold fs-6 px-3 py-1 shadow-sm">
                            <i class="bx bx-sync me-1"></i> PROSES SELEKSI
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="{{ $userStatus === 'tidak_lolos' ? 'col-md-6' : 'col-md-4' }}">
        <div class="card h-100 border-0 text-white shadow" style="background: linear-gradient(135deg, #03c3ec 0%, #0294b3 100%);">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <span class="text-uppercase fs-tiny fw-semibold" style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Berkas Terunggah</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);"><i class="bx bxs-file-pdf"></i></span>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="mb-0 fw-bold text-white">{{ $user->berkas->count() }}</h3>
                    <small style="color: rgba(255, 255, 255, 0.8);">dari 4 berkas wajib</small>
                </div>
                <a href="{{ route('member.profil') }}" class="small fw-semibold d-inline-block mt-2 text-white text-decoration-underline">
                    Periksa & Unggah Berkas &rarr;
                </a>
            </div>
        </div>
    </div>

    @if($userStatus !== 'tidak_lolos')
    <div class="col-md-4">
        <div class="card h-100 border-0 text-white shadow" style="{{ $userStatus === 'lolos' ? 'background: linear-gradient(135deg, #71dd37 0%, #4fad1e 100%);' : 'background: linear-gradient(135deg, #8592a3 0%, #636e7b 100%);' }}">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <span class="text-uppercase fs-tiny fw-semibold" style="color: rgba(255, 255, 255, 0.9); letter-spacing: 0.5px;">Rekomendasi Divisi</span>
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded-3 text-white shadow-sm" style="background: rgba(255, 255, 255, 0.25);">
                            <i class="bx {{ $userStatus === 'lolos' ? 'bx-trophy' : 'bx-lock-alt' }}"></i>
                        </span>
                    </div>
                </div>
                @if($userStatus !== 'lolos')
                    <div class="fw-semibold text-white">Menu Belum Dibuka</div>
                    <small class="d-block mt-1" style="color: rgba(255, 255, 255, 0.8);">
                        <i class="bx bx-info-circle me-1"></i> Rekomendasi divisi & magang spesialis hanya dapat diakses setelah dinyatakan <strong>LOLOS</strong> pada seluruh tahapan seleksi.
                    </small>
                @elseif($hasilRekomendasi)
                    <div>
                        <span class="badge bg-white text-success fw-bold fs-6 px-3 py-1 shadow-sm">
                            Divisi {{ $hasilRekomendasi->divisi->nama }}
                        </span>
                        <div class="small fw-semibold mt-1 text-white">Skor Total: {{ number_format($hasilRekomendasi->nilai_total, 2) }}</div>
                    </div>
                    <a href="{{ route('member.hasil-rekomendasi') }}" class="small fw-semibold d-inline-block mt-1 text-white text-decoration-underline">
                        Lihat Rincian Profile Matching &rarr;
                    </a>
                @else
                    <div class="fw-semibold text-white">Menunggu Penilaian Evaluasi</div>
                    <small style="color: rgba(255, 255, 255, 0.8);">Nilai sedang diproses oleh tim HRD</small>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($userStatus === 'tidak_lolos')
    <!-- Info Banner untuk Calon Anggota yang Tidak Lolos -->
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-label-danger">
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-md mx-auto mb-3 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bx bx-x fs-3"></i>
                </div>
                <h5 class="fw-bold text-danger mb-2">Pemberitahuan Kelulusan Seleksi</h5>
                <p class="text-heading mb-3 mx-auto" style="max-width: 650px;">
                    Mohon maaf, Anda dinyatakan <strong>TIDAK LOLOS</strong> pada tahapan seleksi penerimaan Calon Kru Magang (Cakruma) 2026 SKM Amanat. 
                </p>
            </div>
        </div>
    </div>
    @else
    <!-- Active Tasks & Presensi -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-task text-primary me-2"></i> Daftar Penugasan Aktif
                </h5>
                <a href="{{ route('member.penugasan') }}" class="btn btn-xs btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($tugasAktif as $tugas)
                        @php
                            $pengumpulan = $tugas->pengumpulanUser->first();
                        @endphp
                        <div class="card border shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                            <span class="badge bg-label-secondary text-capitalize" style="font-size: 0.65rem;">{{ str_replace('_', ' ', $tugas->jenis) }}</span>
                                            @foreach($tugas->indikator_labels as $kode => $label)
                                                <span class="badge bg-label-info" style="font-size: 0.65rem;">{{ $label }}</span>
                                            @endforeach
                                        </div>
                                        <h6 class="mb-1 fw-bold text-heading">{{ $tugas->judul }}</h6>
                                        <small class="text-muted"><i class="bx bx-time me-1"></i> Tenggat: {{ $tugas->deadline->translatedFormat('d M Y, H:i') }} WIB</small>
                                    </div>
                                    <div>
                                        @if($pengumpulan)
                                            <span class="badge bg-label-info">{{ ucfirst($pengumpulan->status) }}</span>
                                        @else
                                            <span class="badge bg-label-warning">Belum Mengumpulkan</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex justify-content-end">
                                    <a href="{{ route('member.penugasan') }}" class="btn btn-xs btn-primary fw-bold">
                                        <i class="bx bx-upload me-1"></i> Unggah Tugas PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Tidak ada penugasan aktif saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Presensi & Announcements -->
    <div class="col-12 col-lg-6">
        <div class="row g-4">
            <!-- Presensi -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-heading">
                            <i class="bx bx-calendar-check text-primary me-2"></i> Presensi Kegiatan Terdekat
                        </h5>
                        <a href="{{ route('member.presensi') }}" class="btn btn-xs btn-outline-secondary">Riwayat</a>
                    </div>
                    <div class="card-body pt-4">
                        @if($kegiatanTerdekat)
                            <div class="card bg-lighter border shadow-none p-3 mb-0">
                                <span class="badge bg-label-secondary mb-1 w-auto d-inline-block">{{ $kegiatanTerdekat->jenis }}</span>
                                <h6 class="fw-bold text-heading mb-1">{{ $kegiatanTerdekat->nama }}</h6>
                                <div class="small text-muted mb-3">
                                    <div><i class="bx bx-time me-1"></i> {{ $kegiatanTerdekat->tanggal_waktu->translatedFormat('l, d F Y - H:i') }} WIB</div>
                                    <div><i class="bx bx-map me-1"></i> {{ $kegiatanTerdekat->tempat ?? 'Sekretariat SKM Amanat' }}</div>
                                </div>

                                <div>
                                    @if($presensiHariIni)
                                        <div class="alert alert-success py-2 px-3 mb-0 d-flex align-items-center gap-2">
                                            <i class="bx bx-check-circle fs-5"></i>
                                            <span class="small fw-bold">Sudah Presensi ({{ $presensiHariIni->waktu_hadir->format('H:i') }} WIB)</span>
                                        </div>
                                    @else
                                        <form action="{{ route('member.presensi.submit', $kegiatanTerdekat->id) }}" method="POST">
                                            @csrf
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-key"></i></span>
                                                <input type="text" name="token_presensi" class="form-control text-uppercase fw-bold" placeholder="Token Presensi" required maxlength="10" autocomplete="off">
                                                <button type="submit" class="btn btn-primary fw-bold">
                                                    <i class="bx bx-check me-1"></i> Hadir
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                Tidak ada agenda kegiatan terjadwal hari ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pengumuman Terbaru -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-heading">
                            <i class="bx bx-bell text-primary me-2"></i> Pengumuman Terbaru
                        </h5>
                        <a href="{{ route('member.pengumuman') }}" class="btn btn-xs btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body pt-3">
                        <div class="d-flex flex-column gap-2">
                            @forelse($pengumumanTerbaru as $pengumuman)
                                <a href="{{ route('member.pengumuman.show', $pengumuman->id) }}" class="card border shadow-none p-3 text-decoration-none text-body hover-bg-light">
                                    <div class="fw-bold text-heading small">{{ $pengumuman->judul }}</div>
                                    <small class="text-muted"><i class="bx bx-calendar me-1"></i> {{ $pengumuman->created_at->translatedFormat('d M Y') }}</small>
                                </a>
                            @empty
                                <div class="text-center py-3 text-muted small">Belum ada pengumuman.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
