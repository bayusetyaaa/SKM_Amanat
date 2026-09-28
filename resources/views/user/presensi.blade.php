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
                    <div class="card bg-lighter border shadow-none p-3 mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-label-info">{{ $kegiatanHariIni->jenis }}</span>
                            @if($kegiatanHariIni->isPresensiOpen())
                                <span class="badge bg-label-success"><i class="bx bx-radio-circle-marked me-1"></i> Presensi Dibuka</span>
                            @elseif($kegiatanHariIni->tanggal_waktu && now()->lt($kegiatanHariIni->tanggal_waktu))
                                <span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> Belum Dibuka</span>
                            @else
                                <span class="badge bg-label-secondary"><i class="bx bx-lock-alt me-1"></i> Presensi Ditutup</span>
                            @endif
                        </div>
                        <h5 class="fw-bold text-heading mb-2">{{ $kegiatanHariIni->nama }}</h5>
                        
                        <div class="small text-muted mb-2 d-flex flex-column gap-1">
                            <div><i class="bx bx-time me-1 text-primary"></i> <strong>Waktu:</strong> {{ $kegiatanHariIni->waktu_formatted }}</div>
                            @if($kegiatanHariIni->tempat)
                                <div><i class="bx bx-map me-1 text-primary"></i> <strong>Tempat:</strong> {{ $kegiatanHariIni->tempat }}</div>
                            @endif
                        </div>

                        @if($kegiatanHariIni->deskripsi)
                            <div class="bg-white p-2 rounded border text-muted small mb-0">
                                {{ $kegiatanHariIni->deskripsi }}
                            </div>
                        @endif
                    </div>

                    <div>
                        @if($presensiHariIni)
                            @if($presensiHariIni->status === 'Hadir')
                                <div class="alert alert-success d-flex align-items-start gap-2 mb-0 py-3">
                                    <i class="bx bx-check-circle fs-3 text-success"></i>
                                    <div>
                                        <div class="fw-bold">Kehadiran Terkonfirmasi (Hadir)</div>
                                        <small class="d-block text-muted">Waktu Presensi: {{ $presensiHariIni->waktu_hadir ? $presensiHariIni->waktu_hadir->format('H:i') . ' WIB' : '-' }}</small>
                                        <small class="text-muted">{{ $presensiHariIni->keterangan }}</small>
                                    </div>
                                </div>
                            @elseif($presensiHariIni->status === 'Izin')
                                <div class="alert alert-warning d-flex align-items-start gap-2 mb-0 py-3">
                                    <i class="bx bx-time-five fs-3 text-warning"></i>
                                    <div>
                                        <div class="fw-bold">Permohonan Izin Tercatat</div>
                                        <small class="d-block text-muted">Alasan/Keterangan: {{ $presensiHariIni->keterangan ?? 'Izin tidak hadir' }}</small>
                                    </div>
                                </div>
                            @elseif($presensiHariIni->status === 'Sakit')
                                <div class="alert alert-info d-flex align-items-start gap-2 mb-0 py-3">
                                    <i class="bx bx-plus-medical fs-3 text-info"></i>
                                    <div>
                                        <div class="fw-bold">Keterangan Sakit Tercatat</div>
                                        <small class="d-block text-muted">Alasan/Keterangan: {{ $presensiHariIni->keterangan ?? 'Sedang sakit' }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-secondary d-flex align-items-center gap-2 mb-0 py-3">
                                    <i class="bx bx-x-circle fs-3"></i>
                                    <div>
                                        <div class="fw-bold">Status: {{ ucfirst($presensiHariIni->status) }}</div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Nav Tabs Pilihan Presensi / Izin / Sakit -->
                            <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-hadir">
                                        <i class="bx bx-check-double me-1"></i> Hadir (Token)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link fw-semibold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-izin-sakit">
                                        <i class="bx bx-envelope me-1"></i> Izin / Sakit
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content p-0">
                                <!-- Tab 1: Hadir via Token -->
                                <div class="tab-pane fade show active" id="tab-hadir" role="tabpanel">
                                    @if(!$kegiatanHariIni->isPresensiOpen())
                                        @if($kegiatanHariIni->tanggal_waktu && now()->lt($kegiatanHariIni->tanggal_waktu))
                                            <div class="alert alert-warning py-2 px-3 small mb-0">
                                                <i class="bx bx-time me-1"></i> Presensi belum dibuka. Dibuka mulai pukul <strong>{{ $kegiatanHariIni->tanggal_waktu->format('H:i') }} WIB</strong>.
                                            </div>
                                        @else
                                            <div class="alert alert-secondary py-2 px-3 small mb-0">
                                                <i class="bx bx-lock-alt me-1"></i> Batas waktu presensi telah berakhir pada pukul <strong>{{ $kegiatanHariIni->tanggal_waktu_selesai ? $kegiatanHariIni->tanggal_waktu_selesai->format('H:i') . ' WIB' : 'selesai' }}</strong>.
                                            </div>
                                        @endif
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

                                <!-- Tab 2: Form Izin / Sakit -->
                                <div class="tab-pane fade" id="tab-izin-sakit" role="tabpanel">
                                    <form action="{{ route('member.presensi.izin-sakit', $kegiatanHariIni->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-heading">Jenis Permohonan <span class="text-danger">*</span></label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="statusIzin" value="Izin" checked>
                                                    <label class="form-check-label fw-semibold text-warning" for="statusIzin">
                                                        <i class="bx bx-time-five me-1"></i> Izin
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="statusSakit" value="Sakit">
                                                    <label class="form-check-label fw-semibold text-info" for="statusSakit">
                                                        <i class="bx bx-plus-medical me-1"></i> Sakit
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-heading" for="keteranganIzinSakit">Alasan / Keterangan <span class="text-danger">*</span></label>
                                            <textarea name="keterangan" id="keteranganIzinSakit" rows="3" required class="form-control" placeholder="Contoh: Sakit demam dan beristirahat / Izin ada kegiatan praktikum kuliah"></textarea>
                                            <small class="text-muted d-block mt-1">Sertakan alasan yang jelas untuk verifikasi panitia.</small>
                                        </div>

                                        <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm text-dark">
                                            <i class="bx bx-send me-1"></i> Kirim Permohonan Izin / Sakit
                                        </button>
                                    </form>
                                </div>
                            </div>
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
                <span class="badge bg-label-secondary">{{ $riwayatPresensi->total() }} Tercatat</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Agenda</th>
                            <th>Jadwal Pelaksanaan</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($riwayatPresensi as $index => $p)
                            <tr>
                                <td><strong>{{ $riwayatPresensi->firstItem() + $index }}</strong></td>
                                <td>
                                    <span class="fw-bold text-heading">{{ $p->kegiatan->nama ?? 'Kegiatan' }}</span>
                                    <div class="small text-muted">{{ $p->kegiatan->jenis ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="small">{{ $p->kegiatan ? $p->kegiatan->waktu_formatted : '-' }}</div>
                                    @if($p->waktu_hadir)
                                        <small class="text-muted"><i class="bx bx-time me-1"></i> Dicatat: {{ $p->waktu_hadir->format('H:i') }} WIB</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(strtolower($p->status) === 'hadir')
                                        <span class="badge bg-label-success"><i class="bx bx-check me-1"></i> Hadir</span>
                                    @elseif(strtolower($p->status) === 'izin')
                                        <span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i> Izin</span>
                                    @elseif(strtolower($p->status) === 'sakit')
                                        <span class="badge bg-label-info"><i class="bx bx-plus-medical me-1"></i> Sakit</span>
                                    @else
                                        <span class="badge bg-label-danger"><i class="bx bx-x me-1"></i> Tidak Hadir</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted text-wrap" style="max-width: 180px; display: inline-block;">{{ $p->keterangan ?? '-' }}</small>
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

            <!-- Pagination Links -->
            @if($riwayatPresensi->hasPages())
            <div class="card-footer py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    Menampilkan {{ $riwayatPresensi->firstItem() }} - {{ $riwayatPresensi->lastItem() }} dari {{ $riwayatPresensi->total() }} riwayat
                </small>
                <div>
                    {{ $riwayatPresensi->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
