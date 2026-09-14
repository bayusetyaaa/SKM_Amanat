@extends('layouts/contentNavbarLayout')

@section('title', 'Pengumuman')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Pengumuman</h4>
        <p class="text-muted mb-0">Informasi resmi, surat edaran, dan instruksi kegiatan seleksi dari Pengurus SKM Amanat.</p>
    </div>

    <!-- Announcement List -->
    <div class="col-12">
        <div class="d-flex flex-column gap-3">
            @forelse($pengumumans as $pengumuman)
                <div class="card shadow-sm border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bell"></i></span>
                                </div>
                                <div>
                                    <h5 class="card-title fw-bold text-heading mb-0">{{ $pengumuman->judul }}</h5>
                                    <small class="text-muted">
                                        <i class="bx bx-calendar me-1"></i> {{ $pengumuman->created_at->translatedFormat('d F Y, H:i') }} WIB &bull; Oleh: <strong>{{ $pengumuman->penulis }}</strong>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-3 mt-2">
                            {{ Str::limit($pengumuman->isi, 200) }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            @if($pengumuman->lampiran_path)
                                <span class="badge bg-label-secondary">
                                    <i class="bx bx-paperclip me-1"></i> Ada Lampiran Dokumen
                                </span>
                            @else
                                <span></span>
                            @endif
                            <a href="{{ route('member.pengumuman.show', $pengumuman->id) }}" class="btn btn-sm btn-primary fw-bold">
                                Baca Selengkapnya <i class="bx bx-right-arrow-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="bx bx-bell-off fs-1 mb-2"></i>
                        <h6 class="fw-bold">Belum Ada Pengumuman</h6>
                        <p class="mb-0 small">Belum ada publikasi pengumuman baru dari panitia rekrutmen.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
