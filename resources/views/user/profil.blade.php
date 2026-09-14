@extends('layouts/contentNavbarLayout')

@section('title', 'Profil Saya & Berkas Persyaratan')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Profil & Berkas</h4>
        <p class="text-muted mb-0">Kelola informasi data pribadi dan unggah dokumen persyaratan seleksi penerimaan anggota SKM Amanat.</p>
    </div>

    <!-- Section 1: DATA PRIBADI -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-id-card text-primary me-2"></i> Data Pribadi
                </h5>
                <span class="badge bg-label-primary">Wajib Diisi</span>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('member.profil.update') }}" method="POST">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
                        </div>
                    </div>

                    <!-- NIM & Prodi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="nim">NIM <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-badge"></i></span>
                                <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim', $user->profil->nim ?? '') }}" required placeholder="Nomor Induk Mahasiswa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="prodi">Jurusan / Program Studi <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-book"></i></span>
                                <input type="text" class="form-control" id="prodi" name="prodi" value="{{ old('prodi', $user->profil->prodi ?? '') }}" required placeholder="Contoh: Komunikasi & Penyiaran Islam">
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="nama@email.com">
                        </div>
                    </div>

                    <!-- No WhatsApp & Angkatan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="no_hp">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                                <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->profil->no_hp ?? '') }}" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="angkatan">Tahun Angkatan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" class="form-control" id="angkatan" name="angkatan" value="{{ old('angkatan', $user->profil->angkatan ?? date('Y')) }}" placeholder="{{ date('Y') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Domisili -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="alamat">Alamat Domisili / Kost di Semarang</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Alamat tempat tinggal saat ini di Semarang">{{ old('alamat', $user->profil->alamat ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-save me-1"></i> Simpan Perubahan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section 2: BERKAS PERSYARATAN -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-folder-open text-primary me-2"></i> Berkas Persyaratan
                </h5>
                <span class="badge bg-label-info">Format PDF / Scan Asli</span>
            </div>
            <div class="card-body pt-4">
                @php
                    $allBerkas = $user->berkas;
                    $cv = $allBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'cv') || str_contains(strtolower($b->jenis_berkas), 'curriculum'));
                    $foto = $allBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'foto'));
                    $esai = $allBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'esai') || str_contains(strtolower($b->jenis_berkas), 'essay'));
                    $karya = $allBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'karya') || str_contains(strtolower($b->jenis_berkas), 'portofolio'));
                    $hasRejectedBerkas = $allBerkas->some(fn($b) => $b->status === 'ditolak' || ($b->status !== 'diverifikasi' && !empty($b->catatan)));
                @endphp

                @if($hasRejectedBerkas)
                    <div class="alert alert-danger py-3 px-3 mb-4 d-flex align-items-start gap-2 border border-danger">
                        <i class="bx bx-error-circle fs-4 text-danger mt-1 flex-shrink-0"></i>
                        <div>
                            <strong class="text-danger d-block">Perhatian: Ada Dokumen yang Perlu Diperbaiki!</strong>
                            <span class="small text-dark">Admin verifikator telah memberikan catatan perbaikan pada berkas Anda di bawah. Silakan unggah file pengganti yang sesuai.</span>
                        </div>
                    </div>
                @else
                    <div class="alert alert-primary py-2 px-3 mb-4 d-flex align-items-center gap-2">
                        <i class="bx bx-info-circle fs-5 text-primary"></i>
                        <small>Unggah dokumen format <strong>PDF/JPG/PNG</strong> dengan ukuran maksimal <strong>5 MB</strong> per file.</small>
                    </div>
                @endif

                <form action="{{ route('member.profil.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- 1. Curriculum Vitae (CV) -->
                    <div class="card border {{ $cv && ($cv->status === 'ditolak' || ($cv->status !== 'diverifikasi' && $cv->catatan)) ? 'border-danger' : 'shadow-none' }} mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold text-heading">
                                    <i class="bx bx-file text-primary me-1"></i> 1. Curriculum Vitae (CV)
                                </div>
                                <div>
                                    @if($cv)
                                        @if($cv->status === 'diverifikasi')
                                            <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Diverifikasi</span>
                                        @elseif($cv->status === 'ditolak')
                                            <span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i> Perlu Perbaikan</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Menunggu Verifikasi</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Belum Diunggah</span>
                                    @endif
                                </div>
                            </div>

                            @if($cv)
                                <div class="d-flex align-items-center gap-2 mb-2 small flex-wrap">
                                    <span class="text-success"><i class="bx bx-check-circle"></i> {{ $cv->nama_file }}</span>
                                    <a href="{{ route('berkas.file', $cv->id) }}" target="_blank" class="text-primary fw-semibold">
                                        (Lihat File)
                                    </a>
                                    @if($cv->updated_at > $cv->created_at && $cv->status === 'menunggu')
                                        <span class="badge bg-label-info ms-auto" style="font-size: 0.72rem;">
                                            <i class="bx bx-refresh me-1"></i> Telah diperbarui (Menunggu Cek Admin)
                                        </span>
                                    @endif
                                </div>

                                @if($cv->status !== 'diverifikasi' && $cv->catatan)
                                    <div class="alert alert-danger py-2 px-3 my-2 border-danger rounded-2 small">
                                        <div class="fw-bold text-danger mb-1"><i class="bx bx-message-square-error me-1"></i> Catatan Verifikator / Perbaikan:</div>
                                        <div class="text-dark">{{ $cv->catatan }}</div>
                                    </div>
                                @endif
                            @endif

                            <label class="form-label small text-muted mb-1">{{ $cv ? 'Unggah Ulang / Ganti File CV (PDF):' : 'Pilih File CV (PDF):' }}</label>
                            <input class="form-control form-control-sm" type="file" name="cv" accept=".pdf">
                        </div>
                    </div>

                    <!-- 2. Pas Foto 3x4 -->
                    <div class="card border {{ $foto && ($foto->status === 'ditolak' || ($foto->status !== 'diverifikasi' && $foto->catatan)) ? 'border-danger' : 'shadow-none' }} mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold text-heading">
                                    <i class="bx bx-image text-primary me-1"></i> 2. Pas Foto 3x4
                                </div>
                                <div>
                                    @if($foto)
                                        @if($foto->status === 'diverifikasi')
                                            <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Diverifikasi</span>
                                        @elseif($foto->status === 'ditolak')
                                            <span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i> Perlu Perbaikan</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Menunggu Verifikasi</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Belum Diunggah</span>
                                    @endif
                                </div>
                            </div>

                            @if($foto)
                                <div class="d-flex align-items-center gap-2 mb-2 small flex-wrap">
                                    <span class="text-success"><i class="bx bx-check-circle"></i> {{ $foto->nama_file }}</span>
                                    <a href="{{ route('berkas.file', $foto->id) }}" target="_blank" class="text-primary fw-semibold">
                                        (Lihat File)
                                    </a>
                                    @if($foto->updated_at > $foto->created_at && $foto->status === 'menunggu')
                                        <span class="badge bg-label-info ms-auto" style="font-size: 0.72rem;">
                                            <i class="bx bx-refresh me-1"></i> Telah diperbarui (Menunggu Cek Admin)
                                        </span>
                                    @endif
                                </div>

                                @if($foto->status !== 'diverifikasi' && $foto->catatan)
                                    <div class="alert alert-danger py-2 px-3 my-2 border-danger rounded-2 small">
                                        <div class="fw-bold text-danger mb-1"><i class="bx bx-message-square-error me-1"></i> Catatan Verifikator / Perbaikan:</div>
                                        <div class="text-dark">{{ $foto->catatan }}</div>
                                    </div>
                                @endif
                            @endif

                            <label class="form-label small text-muted mb-1">{{ $foto ? 'Unggah Ulang / Ganti Pas Foto (JPG/PNG/PDF):' : 'Pilih File Pas Foto (JPG/PNG/PDF):' }}</label>
                            <input class="form-control form-control-sm" type="file" name="pas_foto" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <!-- 3. Essai Alasan Memilih Amanat -->
                    <div class="card border {{ $esai && ($esai->status === 'ditolak' || ($esai->status !== 'diverifikasi' && $esai->catatan)) ? 'border-danger' : 'shadow-none' }} mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold text-heading">
                                    <i class="bx bx-detail text-primary me-1"></i> 3. Esai Alasan Memilih Amanat
                                </div>
                                <div>
                                    @if($esai)
                                        @if($esai->status === 'diverifikasi')
                                            <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Diverifikasi</span>
                                        @elseif($esai->status === 'ditolak')
                                            <span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i> Perlu Perbaikan</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Menunggu Verifikasi</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Belum Diunggah</span>
                                    @endif
                                </div>
                            </div>

                            @if($esai)
                                <div class="d-flex align-items-center gap-2 mb-2 small flex-wrap">
                                    <span class="text-success"><i class="bx bx-check-circle"></i> {{ $esai->nama_file }}</span>
                                    <a href="{{ route('berkas.file', $esai->id) }}" target="_blank" class="text-primary fw-semibold">
                                        (Lihat File)
                                    </a>
                                    @if($esai->updated_at > $esai->created_at && $esai->status === 'menunggu')
                                        <span class="badge bg-label-info ms-auto" style="font-size: 0.72rem;">
                                            <i class="bx bx-refresh me-1"></i> Telah diperbarui (Menunggu Cek Admin)
                                        </span>
                                    @endif
                                </div>

                                @if($esai->status !== 'diverifikasi' && $esai->catatan)
                                    <div class="alert alert-danger py-2 px-3 my-2 border-danger rounded-2 small">
                                        <div class="fw-bold text-danger mb-1"><i class="bx bx-message-square-error me-1"></i> Catatan Verifikator / Perbaikan:</div>
                                        <div class="text-dark">{{ $esai->catatan }}</div>
                                    </div>
                                @endif
                            @endif

                            <label class="form-label small text-muted mb-1">{{ $esai ? 'Unggah Ulang / Ganti File Esai (PDF):' : 'Pilih File Esai (PDF):' }}</label>
                            <input class="form-control form-control-sm" type="file" name="esai" accept=".pdf">
                        </div>
                    </div>

                    <!-- 4. Karya Pribadi -->
                    <div class="card border {{ $karya && ($karya->status === 'ditolak' || ($karya->status !== 'diverifikasi' && $karya->catatan)) ? 'border-danger' : 'shadow-none' }} mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-bold text-heading">
                                    <i class="bx bx-news text-primary me-1"></i> 4. Karya Pribadi (Artikel / Portofolio)
                                </div>
                                <div>
                                    @if($karya)
                                        @if($karya->status === 'diverifikasi')
                                            <span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Diverifikasi</span>
                                        @elseif($karya->status === 'ditolak')
                                            <span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i> Perlu Perbaikan</span>
                                        @else
                                            <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Menunggu Verifikasi</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Belum Diunggah</span>
                                    @endif
                                </div>
                            </div>

                            @if($karya)
                                <div class="d-flex align-items-center gap-2 mb-2 small flex-wrap">
                                    <span class="text-success"><i class="bx bx-check-circle"></i> {{ $karya->nama_file }}</span>
                                    <a href="{{ route('berkas.file', $karya->id) }}" target="_blank" class="text-primary fw-semibold">
                                        (Lihat File)
                                    </a>
                                    @if($karya->updated_at > $karya->created_at && $karya->status === 'menunggu')
                                        <span class="badge bg-label-info ms-auto" style="font-size: 0.72rem;">
                                            <i class="bx bx-refresh me-1"></i> Telah diperbarui (Menunggu Cek Admin)
                                        </span>
                                    @endif
                                </div>

                                @if($karya->status !== 'diverifikasi' && $karya->catatan)
                                    <div class="alert alert-danger py-2 px-3 my-2 border-danger rounded-2 small">
                                        <div class="fw-bold text-danger mb-1"><i class="bx bx-message-square-error me-1"></i> Catatan Verifikator / Perbaikan:</div>
                                        <div class="text-dark">{{ $karya->catatan }}</div>
                                    </div>
                                @endif
                            @endif

                            <label class="form-label small text-muted mb-1">{{ $karya ? 'Unggah Ulang / Ganti Karya Pribadi (PDF):' : 'Pilih File Karya Pribadi (PDF):' }}</label>
                            <input class="form-control form-control-sm" type="file" name="karya" accept=".pdf">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-upload me-1"></i> Unggah Dokumen Berkas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
