@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="row g-4">
    <!-- Form Buat Pengumuman -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-bell text-primary me-2"></i> Buat Pengumuman Baru
                </h5>
                <small class="text-muted">Publikasikan info penting kepada calon anggota atau pengurus</small>
            </div>
            <div class="card-body pt-4">
                <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Target Audience -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Target Penerima Pengumuman <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_audience[]" value="semua" id="audSemua">
                                <label class="form-check-label" for="audSemua">Semua</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_audience[]" value="calon_anggota" checked id="audCakruma">
                                <label class="form-check-label" for="audCakruma">Calon Anggota</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_audience[]" value="pengurus" id="audPengurus">
                                <label class="form-check-label" for="audPengurus">Pengurus</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="judul" placeholder="Contoh: Jadwal Wawancara & Pelatihan Tahap II" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Informasi <span class="text-danger">*</span></label>
                        <textarea name="isi" rows="4" placeholder="Tuliskan isi pengumuman lengkap..." required class="form-control"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Unggah File Lampiran (Opsional)</label>
                        <input type="file" name="lampiran" class="form-control">
                        <small class="text-muted">PDF / Gambar maks 5MB</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-send me-1"></i> Publikasikan Pengumuman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Pengumuman -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-list-ul text-primary me-2"></i> Daftar Pengumuman Terbit
                </h5>
                <span class="badge bg-label-primary">{{ $pengumumans->count() }} Info</span>
            </div>
            <div class="card-body pt-4">
                <div class="d-flex flex-column gap-3">
                    @forelse($pengumumans as $index => $p)
                        <div class="card border shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-label-info text-capitalize">{{ $p->target_audience }}</span>
                                            <small class="text-muted"><i class="bx bx-calendar me-1"></i> {{ $p->created_at->translatedFormat('d M Y, H:i') }} WIB</small>
                                        </div>
                                        <h6 class="mb-1 fw-bold text-heading">{{ $p->judul }}</h6>
                                        <p class="small text-muted mb-2 text-truncate" style="max-width: 400px;">{{ $p->isi }}</p>
                                        @if($p->lampiran_nama || $p->lampiran_path)
                                            <a href="{{ route('pengumuman.lampiran', $p->id) }}" target="_blank" class="badge bg-label-primary">
                                                <i class="bx bx-paperclip me-1"></i> Lihat Lampiran
                                            </a>
                                        @endif
                                    </div>
                                    <div>
                                        <form action="{{ route('admin.pengumuman.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted border border-dashed rounded">
                            <i class="bx bx-bell-off fs-1 text-secondary mb-2"></i>
                            <p class="mb-0">Belum ada pengumuman yang tersimpan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Links -->
                @if($pengumumans->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $pengumumans->firstItem() }} - {{ $pengumumans->lastItem() }} dari {{ $pengumumans->total() }} pengumuman
                    </small>
                    <div>
                        {{ $pengumumans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
