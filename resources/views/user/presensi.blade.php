@extends('layouts/contentNavbarLayout')

@section('title', 'Presensi Kegiatan Calon Anggota')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Presensi Kegiatan</h4>
        <p class="text-muted mb-0">Lakukan konfirmasi kehadiran kegiatan pembekalan, workshop, atau agenda kepanitiaan rekrutmen hari ini.</p>
    </div>

    <!-- Section 1: PRESENSI HARI INI -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-calendar-event text-primary me-2"></i> Presensi Hari Ini
                </h5>
                <span class="badge bg-label-primary">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="card-body pt-4">
                @if($kegiatanHariIni)
                    <div class="card bg-lighter border shadow-none p-4 mb-4">
                        <span class="badge bg-label-info mb-2 w-auto d-inline-block">{{ $kegiatanHariIni->jenis }}</span>
                        <h5 class="fw-bold text-heading mb-2">{{ $kegiatanHariIni->nama }}</h5>
                        
                        <div class="small text-muted mb-3 d-flex flex-column gap-1">
                            <div><i class="bx bx-time me-1"></i> <strong>Waktu:</strong> {{ $kegiatanHariIni->tanggal_waktu->translatedFormat('d F Y - H:i') }} WIB</div>
                            @if($kegiatanHariIni->tempat)
                                <div><i class="bx bx-map me-1"></i> <strong>Tempat:</strong> {{ $kegiatanHariIni->tempat }}</div>
                            @endif
                        </div>

                        @if($kegiatanHariIni->deskripsi)
                            <div class="bg-white p-3 rounded border text-muted small mb-0">
                                {{ $kegiatanHariIni->deskripsi }}
                            </div>
                        @endif
                    </div>

                    <div>
                        @if($presensiHariIni)
                            <div class="alert alert-success d-flex align-items-center gap-2 mb-0 py-3">
                                <i class="bx bx-check-circle fs-3 text-success"></i>
                                <div>
                                    <div class="fw-bold">Kehadiran Terkonfirmasi!</div>
                                    <small>Waktu Presensi: {{ $presensiHariIni->waktu_hadir->format('H:i') }} WIB</small>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('member.presensi.submit', $kegiatanHariIni->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-heading">Masukkan Token Presensi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-lighter"><i class="bx bx-key"></i></span>
                                        <input type="text" name="token_presensi" class="form-control text-uppercase fw-bold text-center" placeholder="Contoh: A8K9X2" required maxlength="10" autocomplete="off" style="letter-spacing: 2px;">
                                    </div>
                                    <small class="text-muted d-block mt-1">Dapatkan token presensi dari panitia/instruktur saat kegiatan berlangsung.</small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                                    <i class="bx bx-check-double me-1"></i> Konfirmasi Kehadiran
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bx bx-calendar-x fs-1 mb-2"></i>
                        <h6 class="fw-bold">Tidak Ada Agenda Terjadwal</h6>
                        <p class="mb-0 small">Tidak ada kegiatan yang memerlukan presensi kehadiran pada hari ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 2: RIWAYAT KEHADIRAN -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-history text-primary me-2"></i> Riwayat Kehadiran
                </h5>
                <span class="badge bg-label-secondary">{{ $riwayatPresensi->count() }} Tercatat</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Agenda</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Waktu Hadir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($riwayatPresensi as $p)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <span class="fw-bold text-heading">{{ $p->kegiatan->nama }}</span>
                                    <div class="small text-muted">{{ $p->kegiatan->jenis }}</div>
                                </td>
                                <td>{{ $p->kegiatan->tanggal_waktu->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    @if($p->waktu_hadir)
                                        <span class="badge bg-label-info">{{ $p->waktu_hadir->format('H:i') }} WIB</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($p->status) === 'hadir')
                                        <span class="badge bg-label-success">Hadir</span>
                                    @elseif(strtolower($p->status) === 'izin')
                                        <span class="badge bg-label-warning">Izin</span>
                                    @else
                                        <span class="badge bg-label-danger">Tidak Hadir</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Belum ada riwayat kehadiran kegiatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
