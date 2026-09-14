@extends('layouts/contentNavbarLayout')

@section('title', 'Informasi Alur Rekrutmen')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Alur Pendaftaran & Bantuan</h4>
        <p class="text-muted mb-0">Tahapan proses seleksi penerimaan Calon Kru Magang (Cakruma) dan kontak pengurus SKM Amanat.</p>
    </div>

    <!-- Section 1: ALUR REKRUTMEN -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-git-commit text-primary me-2"></i> Tahapan Rekrutmen Anggota
                </h5>
                <span class="badge bg-label-primary">7 Tahapan</span>
            </div>
            <div class="card-body pt-4">
                @php
                    $tahapan = [
                        ['no' => 1, 'icon' => 'bx-file', 'judul' => 'Registrasi & Unggah Berkas', 'desc' => 'Mengisi data diri serta mengunggah berkas persyaratan format PDF (CV, Pas Foto 3x4, Esai Alasan, dan Portofolio Karya).'],
                        ['no' => 2, 'icon' => 'bx-book-reader', 'judul' => 'Pelatihan Kepenulisan Dasar', 'desc' => 'Mengikuti pembekalan orientasi jurnalistik dasar, teknik peliputan berita, reportase, dan kode etik pers mahasiswa.'],
                        ['no' => 3, 'icon' => 'bx-edit', 'judul' => 'Tes Tulis & Wawancara', 'desc' => 'Ujian tertulis wawasan kebangsaan/isu sosial serta wawancara komprehensif terkait minat, kepribadian, dan komitmen.'],
                        ['no' => 4, 'icon' => 'bx-laptop', 'judul' => 'Workshop Amanat', 'desc' => 'Praktik langsung reportase lapangan, hunting feature, desain layout, dan produksi konten media digital.'],
                        ['no' => 5, 'icon' => 'bx-user-check', 'judul' => 'Pengumuman Lolos Kru Magang', 'desc' => 'Penetapan calon anggota yang berhak maju ke tahapan magang spesialis SKM Amanat.'],
                        ['no' => 6, 'icon' => 'bx-briefcase-alt-2', 'judul' => 'Masa Magang Spesialis', 'desc' => 'Penugasan terarah pada divisi spesialisasi: Redaksi (kepenulisan berita) atau Konten (kreatif media & desain).'],
                        ['no' => 7, 'icon' => 'bx-award', 'judul' => 'Pengumuman Lolos Pengurus Tetap', 'desc' => 'Hasil akhir perankingan Profile Matching dan pelantikan anggota tetap SKM Amanat.'],
                    ];
                @endphp

                <div class="d-flex flex-column gap-3">
                    @foreach($tahapan as $t)
                        <div class="card border shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="avatar avatar-sm flex-shrink-0">
                                        <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ $t['no'] }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-heading mb-1">{{ $t['judul'] }}</h6>
                                        <p class="text-muted small mb-0">{{ $t['desc'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: BANTUAN & KONTAK -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-support text-primary me-2"></i> Bantuan & Layanan
                </h5>
            </div>
            <div class="card-body pt-4">
                <p class="text-muted mb-4">
                    Mengalami kendala teknis saat mengunggah berkas, presensi, atau pengumpulan tugas? Tim HRD dan Panitia siap membantu Anda.
                </p>

                <div class="card bg-lighter border shadow-none p-3 mb-4 text-center">
                    <div class="avatar avatar-lg mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="bx bxl-whatsapp fs-2"></i>
                        </span>
                    </div>
                    <h6 class="fw-bold text-heading mb-1">Helpdesk WhatsApp</h6>
                    <small class="text-muted mb-3 d-block">Senin - Sabtu (08.00 - 17.00 WIB)</small>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-success fw-bold">
                        <i class="bx bxl-whatsapp me-1"></i> Hubungi WhatsApp HRD
                    </a>
                </div>

                <div class="card border shadow-none p-3">
                    <h6 class="fw-bold text-heading mb-2"><i class="bx bx-map-pin text-primary me-1"></i> Sekretariat SKM Amanat</h6>
                    <p class="small text-muted mb-0">
                        Gedung PKM Kampus 3 UIN Walisongo Semarang, Jl. Prof. Hamka, Ngaliyan, Kota Semarang.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
