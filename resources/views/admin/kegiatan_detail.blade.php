@extends('layouts/contentNavbarLayout')

@section('title', 'Rekap Presensi Kegiatan')

@section('content')
<div class="row g-4">
    <!-- Back Button and Header -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.kegiatan') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Kegiatan
        </a>
        <span class="badge bg-label-info">
            {{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }} WIB
        </span>
    </div>

    <!-- Detail Kegiatan Card -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-label-primary">{{ $kegiatan->jenis }}</span>
                            @if($kegiatan->isPresensiOpen())
                                <span class="badge bg-label-success"><i class="bx bx-radio-circle-marked me-1"></i> Presensi Buka</span>
                            @elseif($kegiatan->tanggal_waktu && now()->lt($kegiatan->tanggal_waktu))
                                <span class="badge bg-label-warning">Belum Buka</span>
                            @else
                                <span class="badge bg-label-secondary">Presensi Tutup</span>
                            @endif
                        </div>
                        <h4 class="card-title fw-bold text-heading mb-1">{{ $kegiatan->nama }}</h4>
                    </div>
                    <!-- Token Presensi Box -->
                    <div class="d-flex align-items-center gap-2 bg-lighter p-2 px-3 rounded border">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">TOKEN PRESENSI ANGGOTA:</small>
                            <span class="fs-4 fw-bold font-monospace text-primary letter-spacing-1">{{ $kegiatan->token_presensi }}</span>
                        </div>
                        <form action="{{ route('admin.kegiatan.regenerate-token', $kegiatan->id) }}" method="POST" class="ms-2" onsubmit="return confirm('Acak ulang token presensi untuk kegiatan ini?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Acak Ulang Token">
                                <i class="bx bx-refresh"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-4 text-muted small mb-3">
                    <span><i class="bx bx-map me-1 text-primary"></i> Tempat: <strong>{{ $kegiatan->tempat ?? 'Sekretariat SKM Amanat' }}</strong></span>
                    <span><i class="bx bx-time me-1 text-primary"></i> Waktu Pelaksanaan: <strong>{{ $kegiatan->waktu_formatted }}</strong></span>
                </div>

                <!-- Summary Bar Presensi -->
                @php
                    $countHadir = $kegiatan->presensi->where('status', 'Hadir')->count();
                    $countIzin = $kegiatan->presensi->where('status', 'Izin')->count();
                    $countSakit = $kegiatan->presensi->where('status', 'Sakit')->count();
                    $countBelum = max(0, $users->count() - $kegiatan->presensi->count());
                @endphp
                <div class="row g-2 pt-2 border-top">
                    <div class="col-6 col-sm-3">
                        <div class="p-2 rounded bg-lighter text-center border">
                            <small class="text-muted d-block">Hadir</small>
                            <strong class="text-success fs-5">{{ $countHadir }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-2 rounded bg-lighter text-center border">
                            <small class="text-muted d-block">Izin</small>
                            <strong class="text-warning fs-5">{{ $countIzin }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-2 rounded bg-lighter text-center border">
                            <small class="text-muted d-block">Sakit</small>
                            <strong class="text-info fs-5">{{ $countSakit }}</strong>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-2 rounded bg-lighter text-center border">
                            <small class="text-muted d-block">Belum Presensi</small>
                            <strong class="text-secondary fs-5">{{ $countBelum }}</strong>
                        </div>
                    </div>
                </div>

                @if($kegiatan->deskripsi)
                    <div class="p-3 bg-lighter rounded small border mt-3">
                        <strong>Deskripsi / Catatan Agenda:</strong><br>
                        {{ $kegiatan->deskripsi }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Rekap Presensi -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-check-double text-primary me-2"></i> Rekap Presensi Peserta
                </h5>
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-printer me-1"></i> Cetak Presensi
                </button>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Anggota</th>
                            <th>NIM / Program Studi</th>
                            <th class="text-center">Waktu Presensi</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                            <th class="text-center d-print-none" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php $presensiKeyed = $kegiatan->presensi->keyBy('user_id'); @endphp
                        @foreach($users as $index => $u)
                            @php $p = $presensiKeyed->get($u->id); @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-heading">{{ $u->name }}</div>
                                    <small class="text-muted">{{ $u->email }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $u->profil->nim ?? '-' }}</div>
                                    <small class="text-muted">{{ $u->profil->prodi ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    {{ ($p && $p->waktu_hadir) ? $p->waktu_hadir->format('H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($p)
                                        @if($p->status === 'Hadir')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check me-1"></i> Hadir
                                            </span>
                                        @elseif($p->status === 'Izin')
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-time-five me-1"></i> Izin
                                            </span>
                                        @elseif($p->status === 'Sakit')
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-plus-medical me-1"></i> Sakit
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-x me-1"></i> {{ ucfirst($p->status) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">
                                            Belum Presensi
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $p->keterangan ?? '-' }}</small>
                                </td>
                                <td class="text-center d-print-none">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-xs btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            Ubah
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <form action="{{ route('admin.kegiatan.presensi', $kegiatan->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                <input type="hidden" name="status" value="Hadir">
                                                <button type="submit" class="dropdown-item text-success"><i class="bx bx-check me-1"></i> Tandai Hadir</button>
                                            </form>
                                            <form action="{{ route('admin.kegiatan.presensi', $kegiatan->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                <input type="hidden" name="status" value="Izin">
                                                <button type="submit" class="dropdown-item text-warning"><i class="bx bx-time-five me-1"></i> Tandai Izin</button>
                                            </form>
                                            <form action="{{ route('admin.kegiatan.presensi', $kegiatan->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                <input type="hidden" name="status" value="Sakit">
                                                <button type="submit" class="dropdown-item text-info"><i class="bx bx-plus-medical me-1"></i> Tandai Sakit</button>
                                            </form>
                                            @if($p)
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.kegiatan.presensi', $kegiatan->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                <input type="hidden" name="status" value="Tidak Hadir">
                                                <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-1"></i> Reset Presensi</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
