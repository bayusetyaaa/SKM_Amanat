@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Kegiatan Magang')

@section('content')
<div class="row g-4">
    <!-- Form Tambah Kegiatan -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-calendar-plus text-primary me-2"></i> Tambah Kegiatan Baru
                </h5>
                <small class="text-muted">Jadwalkan agenda pelatihan, liputan, atau pertemuan magang</small>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('admin.kegiatan.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis" required class="form-select">
                            <option value="" disabled selected>Pilih Jenis Kegiatan</option>
                            <option value="Pelatihan & Workshop">Pelatihan & Workshop</option>
                            <option value="Liputan Lapangan">Liputan Lapangan</option>
                            <option value="Rapat Redaksi">Rapat Redaksi</option>
                            <option value="Seleksi & Wawancara">Seleksi & Wawancara</option>
                            <option value="Evaluasi Magang">Evaluasi Magang</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" placeholder="Contoh: Workshop Kepenulisan Feature Berita" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tempat / Lokasi</label>
                        <input type="text" name="tempat" placeholder="Contoh: Sekretariat SKM Amanat / Zoom" class="form-control">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Tanggal & Jam Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tanggal_waktu" required class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Batas Waktu Selesai</label>
                            <input type="datetime-local" name="tanggal_waktu_selesai" class="form-control" title="Batas akhir waktu presensi dibuka">
                            <small class="text-muted" style="font-size: 0.75rem;">Opsional (Batas presensi)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi / Agenda (Opsional)</label>
                        <textarea name="deskripsi" rows="3" placeholder="Tuliskan catatan atau instruksi bagi peserta..." class="form-control"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-save me-1"></i> Simpan Kegiatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Kegiatan Aktif -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-list-ul text-primary me-2"></i> Daftar Kegiatan & Presensi
                </h5>
                <span class="badge bg-label-primary">{{ $kegiatans->total() }} Kegiatan</span>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($kegiatans as $index => $kegiatan)
                        <div class="card border shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <span class="badge bg-label-secondary">{{ $kegiatan->jenis }}</span>
                                            <span class="badge bg-label-dark font-monospace fw-bold px-2 py-1" title="Token Presensi Anggota">
                                                <i class="bx bx-key me-1 text-primary"></i> Token: <strong>{{ $kegiatan->token_presensi }}</strong>
                                            </span>
                                            @if($kegiatan->isPresensiOpen())
                                                <span class="badge bg-label-success"><i class="bx bx-radio-circle-marked me-1"></i> Presensi Buka</span>
                                            @elseif($kegiatan->tanggal_waktu && now()->lt($kegiatan->tanggal_waktu))
                                                <span class="badge bg-label-warning">Belum Buka</span>
                                            @else
                                                <span class="badge bg-label-secondary">Presensi Tutup</span>
                                            @endif
                                        </div>
                                        <h6 class="mb-1 fw-bold text-heading">{{ $kegiatan->nama }}</h6>
                                        <div class="small text-muted d-flex flex-wrap gap-2 mt-2">
                                            <span><i class="bx bx-time me-1"></i> {{ $kegiatan->waktu_formatted }}</span>
                                            <span>&bull;</span>
                                            <span><i class="bx bx-map me-1"></i> {{ $kegiatan->tempat ?? 'Sekretariat' }}</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top">
                                            <span class="badge bg-label-success fw-semibold"><i class="bx bx-user-check me-1"></i> Hadir: {{ $kegiatan->hadir_count ?? 0 }}</span>
                                            <span class="badge bg-label-warning fw-semibold"><i class="bx bx-time-five me-1"></i> Izin: {{ $kegiatan->izin_count ?? 0 }}</span>
                                            <span class="badge bg-label-info fw-semibold"><i class="bx bx-plus-medical me-1"></i> Sakit: {{ $kegiatan->sakit_count ?? 0 }}</span>
                                            <span class="badge bg-label-secondary"><i class="bx bx-group me-1"></i> Total: {{ $totalCakruma }} Cakruma</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-2 mt-sm-0">
                                        <a href="{{ route('admin.kegiatan.detail', $kegiatan->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show me-1"></i> Rekap Presensi
                                        </a>
                                        <form action="{{ route('admin.kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted border border-dashed rounded">
                            <i class="bx bx-calendar-x fs-1 text-secondary mb-2"></i>
                            <p class="mb-0">Belum ada agenda kegiatan yang ditambahkan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Links -->
                @if($kegiatans->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $kegiatans->firstItem() }} - {{ $kegiatans->lastItem() }} dari {{ $kegiatans->total() }} kegiatan
                    </small>
                    <div>
                        {{ $kegiatans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
