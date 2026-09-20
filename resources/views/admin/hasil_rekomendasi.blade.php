@extends('layouts/contentNavbarLayout')

@section('title', 'Hasil Perangkingan & Rekomendasi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">
                        <i class="bx bx-trophy text-primary me-2"></i> Hasil Perangkingan Profile Matching & Penetapan Divisi
                    </h5>
                    <small class="text-muted">Hasil kalkulasi kesesuaian profil kompetensi calon anggota terhadap kriteria divisi Redaksi & Konten</small>
                </div>
                <div>
                    <span class="badge bg-label-primary">
                        Metode Profile Matching
                    </span>
                </div>
            </div>

            <div class="card-body pt-4">
                <!-- Filter Bar -->
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-12 col-md-6">
                        <form action="{{ route('admin.hasil-rekomendasi') }}" method="GET" class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 fw-semibold text-nowrap">Filter Divisi:</label>
                            <select name="divisi" onchange="this.form.submit()" class="form-select">
                                <option value="Semua Divisi" {{ request('divisi') === 'Semua Divisi' ? 'selected' : '' }}>Semua Rekomendasi Divisi</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->nama }}" {{ request('divisi') === $divisi->nama ? 'selected' : '' }}>
                                        Divisi {{ $divisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Perangkingan Table & Penetapan Form -->
                <form action="{{ route('admin.hasil-rekomendasi.simpan-keputusan') }}" method="POST">
                    @csrf

                    <div class="table-responsive text-nowrap border rounded mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 70px;">Rank</th>
                                    <th>Nama Anggota</th>
                                    <th class="text-center" style="width: 130px;"></th>
                                    <th class="text-center" style="width: 170px;">Skor PV Total</th>
                                    <th class="text-center" style="width: 170px;">Rekomendasi Sistem</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($hasilRankings as $index => $item)
                                    @php
                                        $user = $item->user;
                                        $keputusanCurrent = in_array($user->profil->keputusan_final ?? '', ['Redaksi', 'Konten']) 
                                            ? $user->profil->keputusan_final 
                                            : $item->divisi->nama;
                                        $userCalc = $calcDetailsByUser[$user->id] ?? null;
                                    @endphp
                                    <tr>
                                        <!-- Rank Badge -->
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-label-primary fw-bold">{{ $item->ranking ?? ($hasilRankings->firstItem() + $index) }}</span>
                                        </td>

                                        <!-- Nama & Penetapan Final -->
                                        <td>
                                            <div class="fw-bold text-heading fs-6 mb-0">{{ $user->name }}</div>
                                            <small class="text-muted d-block mb-2">NIM: {{ $user->profil->nim ?? '-' }} &bull; {{ $user->profil->prodi ?? '-' }}</small>

                                            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                                                <span class="small fw-semibold text-muted">Penetapan Final:</span>
                                                <select 
                                                    name="keputusan[{{ $user->id }}]" 
                                                    class="form-select form-select-sm fw-bold"
                                                    style="max-width: 160px;"
                                                >
                                                    <option value="Redaksi" {{ $keputusanCurrent === 'Redaksi' ? 'selected' : '' }}>Divisi Redaksi</option>
                                                    <option value="Konten" {{ $keputusanCurrent === 'Konten' ? 'selected' : '' }}>Divisi Konten</option>
                                                </select>
                                            </div>
                                        </td>
                                        
                                        <!-- Tombol Rincian  -->
                                        <td class="text-center align-middle">
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary shadow-none"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rincianModal{{ $user->id }}"
                                                title="Lihat Rincian Perhitungan"
                                            >
                                                <i class="bx bx-show me-1"></i> Rincian
                                            </button>

                                            <!-- Modal Pop-up Rincian Profile Matching Per User -->
                                            <div class="modal fade text-start" id="rincianModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                    <div class="modal-content shadow-lg border-0">
                                                        <div class="modal-header border-bottom py-3">
                                                            <div>
                                                                <h5 class="modal-title fw-bold text-heading mb-0 d-flex align-items-center">
                                                                    <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i> Rincian Perhitungan Profile Matching: {{ $user->name }}
                                                                </h5>
                                                                <small class="text-muted">NIM: {{ $user->profil->nim ?? '-' }} &bull; Program Studi: {{ $user->profil->prodi ?? '-' }} &bull; Minat Awal: {{ $user->profil->pilihan_divisi_awal ?? '-' }}</small>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body p-4">
                                                            @if($userCalc && isset($userCalc['results']))
                                                                <div class="d-flex flex-column gap-4">
                                                                    @foreach($userCalc['results'] as $divisiId => $divisiData)
                                                                        <div class="card border shadow-none">
                                                                            <div class="card-header bg-light d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-2 px-3 gap-2">
                                                                                <h6 class="mb-0 fw-bold text-heading">
                                                                                    <i class="bx {{ $divisiData['divisi_nama'] === 'Redaksi' ? 'bx-news' : 'bx-paint' }} text-primary me-1"></i>
                                                                                    Divisi {{ $divisiData['divisi_nama'] }}
                                                                                </h6>
                                                                                <div class="d-flex flex-wrap gap-2">
                                                                                    <span class="badge bg-label-primary">NCF (Core 60%): {{ number_format($divisiData['ncf'], 2) }}</span>
                                                                                    <span class="badge bg-label-info">NSF (Secondary 40%): {{ number_format($divisiData['nsf'], 2) }}</span>
                                                                                    <span class="badge {{ $divisiData['divisi_nama'] === 'Redaksi' ? 'bg-success' : 'bg-primary' }} text-white">Skor Total: {{ number_format($divisiData['nilai_total'], 2) }}</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="table-responsive text-nowrap">
                                                                                <table class="table table-sm table-hover align-middle mb-0">
                                                                                    <thead class="table-light">
                                                                                        <tr>
                                                                                            <th style="width: 60px;">Kode</th>
                                                                                            <th>Kriteria Penilaian</th>
                                                                                            <th class="text-center">Faktor</th>
                                                                                            <th class="text-center">Nilai Target</th>
                                                                                            <th class="text-center">Nilai Aktual</th>
                                                                                            <th class="text-center">GAP</th>
                                                                                            <th class="text-center">Bobot Nilai</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($divisiData['details'] as $kDetail)
                                                                                            <tr>
                                                                                                <td class="fw-bold text-primary">{{ $kDetail['kode'] }}</td>
                                                                                                <td>
                                                                                                    <div class="fw-semibold">{{ $kDetail['nama'] }}</div>
                                                                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $kDetail['aspek'] }}</small>
                                                                                                </td>
                                                                                                <td class="text-center">
                                                                                                    @if($kDetail['faktor'] === 'core')
                                                                                                        <span class="badge bg-label-primary">Core Factor</span>
                                                                                                    @else
                                                                                                        <span class="badge bg-label-secondary">Secondary</span>
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td class="text-center fw-semibold">{{ $kDetail['target'] }}</td>
                                                                                                <td class="text-center fw-bold">{{ $kDetail['aktual'] }}</td>
                                                                                                <td class="text-center fw-bold {{ $kDetail['gap'] < 0 ? 'text-danger' : ($kDetail['gap'] > 0 ? 'text-primary' : 'text-success') }}">
                                                                                                    {{ $kDetail['gap'] > 0 ? '+' . $kDetail['gap'] : $kDetail['gap'] }}
                                                                                                </td>
                                                                                                <td class="text-center fw-bold text-success">{{ $kDetail['bobot'] }}</td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="text-center py-4 text-muted">
                                                                    <i class="bx bx-info-circle fs-2 mb-2"></i>
                                                                    <p>Rincian kalkulasi belum tersedia.</p>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="modal-footer border-top py-3">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bx bx-x me-1"></i> Tutup
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Skor PV Total -->
                                        <td class="text-center">
                                            <div class="fw-bold fs-5 text-primary">{{ number_format($item->nilai_total, 2) }}</div>
                                            <small class="text-muted">NCF: {{ number_format($item->ncf, 2) }} | NSF: {{ number_format($item->nsf, 2) }}</small>
                                        </td>

                                        <!-- Rekomendasi Divisi -->
                                        <td class="text-center">
                                            <span class="badge {{ $item->divisi->nama === 'Redaksi' ? 'bg-label-success' : 'bg-label-info' }} fs-6 px-3 py-2">
                                                {{ $item->divisi->nama }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bx bx-info-circle fs-2 text-secondary mb-2"></i>
                                            <p class="mb-0">Belum ada data hasil perhitungan. Silakan lakukan proses kalkulasi di menu <strong>Nilai Evaluasi</strong>.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    @if($hasilRankings->hasPages())
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $hasilRankings->firstItem() }} - {{ $hasilRankings->lastItem() }} dari {{ $hasilRankings->total() }} peringkat
                        </small>
                        <div>
                            {{ $hasilRankings->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a 
                            href="{{ route('admin.hasil-rekomendasi.cetak') }}" 
                            target="_blank"
                            class="btn btn-outline-secondary fw-bold px-4"
                        >
                            <i class="bx bx-printer me-1"></i> Cetak Berita Acara & Laporan
                        </a>
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bx bx-save me-1"></i> Simpan Penetapan Keputusan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
