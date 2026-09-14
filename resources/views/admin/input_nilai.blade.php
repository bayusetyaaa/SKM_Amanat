@extends('layouts/contentNavbarLayout')

@section('title', 'Nilai Evaluasi')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">
                        <i class="bx bx-list-check text-primary me-2"></i> Rekapitulasi Nilai Evaluasi Calon Kru Magang
                    </h5>
                    <small class="text-muted">Data nilai kompetensi 7 kriteria terintegrasi dari hasil penugasan, presensi kegiatan, dan uji wawancara</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary">
                        Skala Nilai 1 - 100
                    </span>
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-printer me-1"></i> Cetak Nilai
                    </button>
                </div>
            </div>

            <div class="card-body pt-4">
                <form action="{{ route('admin.input-nilai.proses') }}" method="POST">
                    @csrf

                    <!-- Matrix Evaluation Table (Read-Only / Fixed Values) -->
                    <div class="table-responsive text-nowrap border rounded mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="min-width: 220px;">Nama Lengkap</th>
                                    @foreach($kriterias as $k)
                                        <th class="text-center" style="min-width: 110px;">
                                            <span class="badge bg-label-primary fw-bold mb-1">{{ $k->kode }}</span>
                                            <div class="fw-bold text-heading text-sm">{{ $k->nama }}</div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($calonAnggotas as $ca)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                                        {{ strtoupper(substr($ca->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-heading">{{ $ca->name }}</h6>
                                                    <small class="text-muted">NIM: {{ $ca->profil->nim ?? '-' }} &bull; {{ $ca->profil->prodi ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($kriterias as $k)
                                            @php
                                                $val = $nilaiMatrix[$ca->id][$k->id] ?? null;
                                            @endphp
                                            <td class="text-center p-3">
                                                @if($val !== null && $val !== '')
                                                    <span class="badge bg-label-success fs-6 fw-bold px-3 py-1">
                                                        {{ $val }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary text-muted">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 1 + $kriterias->count() }}" class="text-center py-5 text-muted">
                                            <i class="bx bx-user-x fs-1 text-secondary mb-2"></i>
                                            <p class="mb-0">Belum ada calon anggota terdaftar.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Keterangan 7 Kriteria & Sumber Nilai -->
                    <div class="card bg-lighter border shadow-none mb-4">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-heading mb-3">
                                <i class="bx bx-info-circle text-primary me-1"></i> Parameter Indikator & Sumber Penilaian 7 Kriteria:
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K1</span> Kepenulisan</span>
                                            <span class="badge bg-label-info">Tugas</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Struktur straight news, unsur berita, fakta, bahasa jurnalistik, dan kejelasan informasi.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-file me-1"></i>Sumber: Tugas kepenulisan</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K2</span> Kepekaan Isu</span>
                                            <span class="badge bg-label-info">Tugas</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Identifikasi, relevansi, permasalahan, dampak, dan sudut pandang isu.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-analyse me-1"></i>Sumber: Tugas analisis isu</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K3</span> Kreativitas</span>
                                            <span class="badge bg-label-info">Tugas</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Orisinalitas ide, komposisi, pengembangan konsep, kesesuaian, dan kualitas visual.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-image me-1"></i>Sumber: Tugas kreatif</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K4</span> Wawasan Sosial</span>
                                            <span class="badge bg-label-warning">Wawancara</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Pemahaman masalah sosial, pandangan, kondisi masyarakat, dan argumentasi.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-conversation me-1"></i>Sumber: Nilai wawancara wawasan sosial</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K5</span> Public Speaking</span>
                                            <span class="badge bg-label-warning">Wawancara</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Kejelasan komunikasi, penyampaian gagasan, artikulasi, intonasi, penguasaan materi, dan respons terhadap pertanyaan.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-microphone me-1"></i>Sumber: Nilai keseluruhan wawancara</div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded bg-white h-100 shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K6</span> Kedisiplinan</span>
                                            <span class="badge bg-label-success">Presensi & Tugas</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Kehadiran, ketepatan waktu, ketepatan pengumpulan tugas, dan konsistensi kegiatan.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-check-square me-1"></i>Sumber: Data presensi dan rekam tugas</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 border rounded bg-white shadow-2xs">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-heading fs-6"><span class="badge bg-label-primary me-1">K7</span> Karakteristik</span>
                                            <span class="badge bg-label-warning">Psikotes</span>
                                        </div>
                                        <div class="text-muted mb-2"><strong>Parameter/Indikator:</strong> Tanggung jawab, kerja sama, respons terhadap masukan, inisiatif, dan konsistensi sikap.</div>
                                        <div class="text-primary fw-semibold"><i class="bx bx-user me-1"></i>Sumber: Nilai wawancara psikotes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="d-flex justify-content-center">
                        <button 
                            type="submit" 
                            class="btn btn-primary btn-lg fw-bold px-5 shadow-sm"
                        >
                            <i class="bx bx-calculator me-2"></i> Proses Perhitungan Profile Matching
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
