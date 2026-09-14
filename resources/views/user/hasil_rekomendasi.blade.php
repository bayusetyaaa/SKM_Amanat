@extends('layouts/contentNavbarLayout')

@section('title', 'Hasil Rekomendasi Divisi')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Hasil Rekomendasi Divisi</h4>
        <p class="text-muted mb-0">Hasil evaluasi kompetensi calon anggota dan rekomendasi penempatan divisi magang berbasis metode Profile Matching.</p>
    </div>

    @if($rekomendasi)
        <!-- Top Recommendation Banner Card -->
        <div class="col-12">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
                        <div>
                            <span class="badge bg-white text-primary mb-2 fw-bold">
                                <i class="bx bx-award me-1"></i> Rekomendasi Terbaik Sistem
                            </span>
                            <h2 class="text-white fw-bold mb-2">DIVISI {{ strtoupper($rekomendasi->divisi->nama) }}</h2>
                            <p class="text-white-50 mb-0 max-w-xl">
                                {{ $rekomendasi->divisi->deskripsi }}
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded p-3 text-center text-md-end min-w-150">
                            <span class="text-white-50 small text-uppercase fw-semibold d-block">Skor Akhir (Total)</span>
                            <span class="display-6 fw-bold text-white">{{ number_format($rekomendasi->nilai_total, 2) }}</span>
                            <span class="d-block small text-white-50">Skala 1 - 100</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perbandingan Skor Antar Divisi -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-heading">
                        <i class="bx bx-bar-chart-alt-2 text-primary me-2"></i> Perbandingan Nilai Akhir Per Divisi
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        @foreach($hasilList as $hasil)
                            <div class="col-12 col-md-6">
                                <div class="card border {{ $hasil->rekomendasi ? 'border-primary shadow-sm bg-label-primary-subtle' : 'shadow-none' }} h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-heading mb-0">Divisi {{ $hasil->divisi->nama }}</h5>
                                            @if($hasil->rekomendasi)
                                                <span class="badge bg-primary">
                                                    <i class="bx bx-check-circle me-1"></i> Rekomendasi Utama
                                                </span>
                                            @endif
                                        </div>

                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <div class="card bg-lighter border shadow-none p-2 mb-0">
                                                    <small class="text-muted fw-semibold d-block">NCF (60%)</small>
                                                    <span class="fw-bold text-heading fs-6">{{ number_format($hasil->ncf, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="card bg-lighter border shadow-none p-2 mb-0">
                                                    <small class="text-muted fw-semibold d-block">NSF (40%)</small>
                                                    <span class="fw-bold text-heading fs-6">{{ number_format($hasil->nsf, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="card bg-primary text-white border-0 shadow-none p-2 mb-0">
                                                    <small class="text-white-50 fw-semibold d-block">Total Skor</small>
                                                    <span class="fw-bold text-white fs-6">{{ number_format($hasil->nilai_total, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Nilai 7 Kriteria Evaluasi -->
        @if($user->nilaiEvaluasi->isNotEmpty())
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0 fw-bold text-heading">
                            <i class="bx bx-list-check text-primary me-2"></i> Rincian Nilai Evaluasi 7 Kriteria Kompetensi
                        </h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Kriteria Kompetensi</th>
                                    <th>Aspek Penilaian</th>
                                    <th class="text-center">Nilai Aktual (1-100)</th>
                                    <th class="text-center">Predikat</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($user->nilaiEvaluasi as $ne)
                                    <tr>
                                        <td><span class="badge bg-label-dark fw-bold">{{ $ne->kriteria->kode }}</span></td>
                                        <td><span class="fw-bold text-heading">{{ $ne->kriteria->nama }}</span></td>
                                        <td><span class="badge bg-label-secondary">{{ $ne->kriteria->aspek }}</span></td>
                                        <td class="text-center">
                                            <span class="fw-bold fs-6 text-heading">{{ $ne->nilai_aktual }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($ne->nilai_aktual >= 90)
                                                <span class="badge bg-label-success">Sangat Baik</span>
                                            @elseif($ne->nilai_aktual >= 75)
                                                <span class="badge bg-label-primary">Baik</span>
                                            @elseif($ne->nilai_aktual >= 60)
                                                <span class="badge bg-label-warning">Cukup</span>
                                            @else
                                                <span class="badge bg-label-danger">Perlu Pembinaan</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bx bx-hourglass fs-1 text-muted mb-3"></i>
                    <h5 class="fw-bold text-heading">Hasil Rekomendasi Belum Tersedia</h5>
                    <p class="text-muted max-w-md mx-auto mb-0">
                        Tim Pengurus & HRD SKM Amanat sedang melakukan proses penilaian evaluasi berkas, penugasan, tes tulis, dan wawancara. Silakan cek kembali secara berkala.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
