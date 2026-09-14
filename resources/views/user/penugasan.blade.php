@extends('layouts/contentNavbarLayout')

@section('title', 'Penugasan Calon Anggota')

@section('content')
<div class="row g-4">
    <!-- Header Title -->
    <div class="col-12">
        <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Cakruma /</span> Penugasan</h4>
        <p class="text-muted mb-0">Kerjakan dan unggah berkas penugasan kepenulisan, liputan lapangan, atau penugasan media lainnya.</p>
    </div>

    <!-- Section 1: DAFTAR TUGAS AKTIF -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-task text-primary me-2"></i> Daftar Tugas Aktif
                </h5>
                <span class="badge bg-label-primary">{{ $tugasAktif->count() }} Tugas Berjalan</span>
            </div>
            <div class="card-body pt-4">
                <div class="row g-4">
                    @forelse($tugasAktif as $tugas)
                        @php
                            $pengumpulan = $tugas->pengumpulanUser->first();
                        @endphp
                        <div class="col-12 col-lg-6">
                            <div class="card border h-100 shadow-none">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                        <span class="badge bg-label-secondary text-capitalize">{{ str_replace('_', ' ', $tugas->jenis) }}</span>
                                        @foreach($tugas->indikator_labels as $kode => $label)
                                            <span class="badge bg-label-info">{{ $label }}</span>
                                        @endforeach
                                    </div>
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <h5 class="card-title fw-bold text-heading mb-0">{{ $tugas->judul }}</h5>
                                        @if($pengumpulan)
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle me-1"></i> Sudah Dikumpulkan
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-time-five me-1"></i> Belum Mengumpulkan
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <div class="small text-muted mb-1">
                                            <i class="bx bx-calendar-event me-1"></i> <strong>Tenggat Waktu:</strong> {{ $tugas->deadline->translatedFormat('d F Y, H:i') }} WIB
                                        </div>
                                        @if($pengumpulan)
                                            <div class="small text-muted mb-1">
                                                <i class="bx bx-file me-1"></i> <strong>File:</strong> {{ $pengumpulan->nama_file }}
                                                <a href="{{ route('penugasan.file', $pengumpulan->id) }}" target="_blank" class="ms-1 text-primary fw-semibold">(Lihat)</a>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="bx bx-time me-1"></i> <strong>Waktu Submit:</strong> {{ $pengumpulan->submitted_at->translatedFormat('d M Y, H:i') }} WIB
                                            </div>
                                        @endif
                                    </div>

                                    @if($tugas->deskripsi)
                                        <div class="bg-light p-3 rounded mb-3 small text-muted">
                                            <strong>Instruksi Tugas:</strong><br>
                                            {{ $tugas->deskripsi }}
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-end pt-2 border-top">
                                        <button 
                                            type="button" 
                                            class="btn btn-sm {{ $pengumpulan ? 'btn-outline-primary' : 'btn-primary' }} fw-bold btn-open-upload"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalUploadTugas"
                                            data-tugas-id="{{ $tugas->id }}"
                                            data-tugas-judul="{{ $tugas->judul }}"
                                        >
                                            <i class="bx bx-upload me-1"></i> {{ $pengumpulan ? 'Unggah Ulang PDF' : 'Unggah Tugas PDF' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-check-double fs-1 mb-2"></i>
                                <h6 class="fw-bold">Tidak ada tugas aktif yang sedang berjalan</h6>
                                <p class="mb-0 small">Semua penugasan saat ini telah selesai atau belum ada tugas baru dari panitia.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: RIWAYAT TUGAS -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-history text-primary me-2"></i> Riwayat Tugas & Nilai
                </h5>
                <span class="badge bg-label-secondary">{{ $riwayatTugas->count() }} Tugas Selesai</span>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Judul Tugas</th>
                            <th>Waktu Submit</th>
                            <th>Berkas PDF</th>
                            <th>Nilai (1-100)</th>
                            <th>Feedback Pengurus</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($riwayatTugas as $index => $tugas)
                            @php
                                $pengumpulan = $tugas->pengumpulanUser->first();
                            @endphp
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <span class="fw-bold text-heading">{{ $tugas->judul }}</span>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                        <span class="badge bg-label-secondary text-capitalize" style="font-size: 0.7rem;">{{ str_replace('_', ' ', $tugas->jenis) }}</span>
                                        @foreach($tugas->indikator_labels as $kode => $label)
                                            <span class="badge bg-label-info" style="font-size: 0.7rem;">{{ $label }}</span>
                                        @endforeach
                                    </div>
                                    <div class="small text-muted mt-1">Deadline: {{ $tugas->deadline->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    @if($pengumpulan && $pengumpulan->submitted_at)
                                        {{ $pengumpulan->submitted_at->translatedFormat('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($pengumpulan)
                                        <a href="{{ route('penugasan.file', $pengumpulan->id) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                            <i class="bx bx-file me-1"></i> Unduh File
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($pengumpulan && $pengumpulan->nilai !== null)
                                        <span class="badge {{ $pengumpulan->nilai >= 80 ? 'bg-label-success' : ($pengumpulan->nilai >= 60 ? 'bg-label-warning' : 'bg-label-danger') }} fs-6 fw-bold">
                                            {{ $pengumpulan->nilai }} / 100
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pengumpulan && $pengumpulan->feedback)
                                        <span class="small text-muted" title="{{ $pengumpulan->feedback }}">{{ Str::limit($pengumpulan->feedback, 35) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-success">Selesai</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada riwayat tugas yang telah dinilai atau berakhir tenggatnya.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Tugas PDF -->
<div class="modal fade" id="modalUploadTugas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalUploadTugasTitle">
                    <i class="bx bx-upload text-primary me-2"></i> Unggah Hasil Pengerjaan Tugas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUploadTugas" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Judul Tugas</label>
                        <h6 class="fw-bold text-heading" id="modalTugasJudulDisplay">-</h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="file_tugas">Pilih Berkas PDF Hasil Tugas <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="file_tugas" name="file_tugas" accept=".pdf" required>
                        <div class="form-text">Format dokumen harus berupa <strong>PDF</strong> dengan batas maksimal <strong>5 MB</strong>.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bx bx-send me-1"></i> Kirim Tugas PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadButtons = document.querySelectorAll('.btn-open-upload');
        const formUpload = document.getElementById('formUploadTugas');
        const modalJudul = document.getElementById('modalTugasJudulDisplay');

        uploadButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const tugasId = this.getAttribute('data-tugas-id');
                const tugasJudul = this.getAttribute('data-tugas-judul');

                modalJudul.textContent = tugasJudul;
                formUpload.action = '{{ url("member/penugasan") }}/' + tugasId + '/upload';
            });
        });
    });
</script>
@endpush
@endsection
