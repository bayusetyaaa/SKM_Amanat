@extends('layouts/contentNavbarLayout')

@section('title', $pengumuman->judul)

@section('content')
<div class="row g-4">
    <!-- Header Title & Back Button -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma / Pengumuman /</span> Detail</h4>
            </div>
            <a href="{{ route('member.pengumuman') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Pengumuman
            </a>
        </div>
    </div>

    <!-- Announcement Detail Card -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-bell fs-4"></i></span>
                    </div>
                    <div>
                        <h3 class="fw-bold text-heading mb-1">{{ $pengumuman->judul }}</h3>
                        <div class="small text-muted">
                            <i class="bx bx-calendar me-1"></i> {{ $pengumuman->created_at->translatedFormat('l, d F Y - H:i') }} WIB &bull; 
                            Oleh: <strong class="text-heading">{{ $pengumuman->penulis }}</strong>
                        </div>
                    </div>
                </div>

                <div class="text-heading fs-6 lh-lg mb-4" style="white-space: pre-line;">
                    {{ $pengumuman->isi }}
                </div>

                @if($pengumuman->lampiran_nama || $pengumuman->lampiran_path)
                    <div class="pt-4 mt-4 border-top">
                        <h6 class="fw-bold text-heading mb-2"><i class="bx bx-paperclip text-primary me-1"></i> Lampiran Dokumen:</h6>
                        <a href="{{ route('pengumuman.lampiran', $pengumuman->id) }}" target="_blank" class="btn btn-outline-primary fw-semibold">
                            <i class="bx bxs-file-pdf me-1"></i> Buka / Unduh Lampiran Dokumen
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
