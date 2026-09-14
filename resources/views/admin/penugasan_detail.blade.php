@extends('layouts/contentNavbarLayout')

@section('title', 'Detail & Penilaian Penugasan')

@section('content')
<div class="row g-4">
    <!-- Back Header -->
    <div class="col-12 d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.penugasan') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Penugasan
        </a>
        <span class="badge bg-label-info">
            Deadline: {{ $penugasan->deadline->translatedFormat('d F Y, H:i') }} WIB
        </span>
    </div>

    <!-- Info Penugasan Card -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                    <span class="badge bg-label-primary text-capitalize">{{ str_replace('_', ' ', $penugasan->jenis) }}</span>
                    @foreach($penugasan->indikator_labels as $kode => $label)
                        <span class="badge bg-label-info">{{ $label }}</span>
                    @endforeach
                </div>
                <h4 class="card-title fw-bold text-heading mb-2">{{ $penugasan->judul }}</h4>
                <div class="d-flex flex-wrap gap-4 text-muted small mb-3">
                    <span><i class="bx bx-calendar me-1 text-primary"></i> Tenggat: <strong>{{ $penugasan->deadline->translatedFormat('l, d F Y, H:i') }} WIB</strong></span>
                    <span><i class="bx bx-file me-1 text-primary"></i> Pengumpulan: <strong>{{ $penugasan->pengumpulanTugas->count() }} / {{ $allMembers->count() }} Anggota</strong></span>
                </div>
                @if($penugasan->deskripsi)
                    <div class="p-3 bg-lighter rounded small border">
                        <strong>Instruksi & Petunjuk Tugas:</strong><br>
                        <span class="text-muted">{{ $penugasan->deskripsi }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0 fw-bold text-heading">
                    <i class="bx bx-file text-primary me-2"></i> Pengumpulan Berkas PDF Anggota
                </h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Anggota</th>
                            <th>Berkas Tugas PDF</th>
                            <th class="text-center">Waktu Kirim</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Nilai (1-100)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @php $pengumpulanKeyed = $penugasan->pengumpulanTugas->keyBy('user_id'); @endphp
                        @foreach($allMembers as $index => $m)
                            @php $pt = $pengumpulanKeyed->get($m->id); @endphp
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-heading">{{ $m->name }}</div>
                                    <small class="text-muted">{{ $m->profil->nim ?? '-' }} &bull; {{ $m->profil->prodi ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($pt)
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bx bxs-file-pdf text-danger fs-4"></i>
                                            <div>
                                                <div class="fw-semibold small text-truncate" style="max-width: 200px;">{{ $pt->nama_file }}</div>
                                                <a href="{{ route('penugasan.file', $pt->id) }}" target="_blank" class="small text-primary">
                                                    <i class="bx bx-download me-1"></i> Buka / Unduh
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">Belum mengunggah</span>
                                    @endif
                                </td>
                                <td class="text-center small">
                                    {{ $pt ? $pt->submitted_at->translatedFormat('d M, H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if($pt)
                                        <span class="badge {{ $pt->status === 'dinilai' ? 'bg-label-success' : ($pt->status === 'terlambat' ? 'bg-label-danger' : 'bg-label-info') }}">
                                            {{ ucfirst($pt->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Kosong</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pt && $pt->nilai !== null)
                                        <span class="badge bg-label-primary fs-6 fw-bold">{{ $pt->nilai }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($pt)
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalBeriNilai"
                                            data-pengumpulan-id="{{ $pt->id }}"
                                            data-user-name="{{ $m->name }}"
                                            data-file-name="{{ $pt->nama_file }}"
                                            data-nilai="{{ $pt->nilai ?? '' }}"
                                            data-feedback="{{ $pt->feedback ?? '' }}"
                                        >
                                            <i class="bx bx-edit me-1"></i> {{ $pt->status === 'dinilai' ? 'Edit Nilai' : 'Beri Nilai' }}
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Beri Nilai Bootstrap 5 Native -->
    <div class="modal fade" id="modalBeriNilai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-heading">
                        <i class="bx bx-check-shield text-primary me-2"></i> Penilaian Tugas PDF
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formBeriNilai" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Info Ringkas Peserta -->
                        <div class="card bg-lighter shadow-none border mb-3">
                            <div class="card-body p-3">
                                <div class="small text-muted mb-1">Nama Anggota: <strong id="modalNamaPeserta" class="text-heading">-</strong></div>
                                <div class="small text-muted">Berkas Unggahan: <strong id="modalNamaFile" class="text-primary">-</strong></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nilai Tugas (Skala 1 - 100) <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="nilai" 
                                id="modalInputNilai"
                                min="0" 
                                max="100" 
                                required 
                                class="form-control form-control-lg fw-bold text-center"
                                placeholder="Contoh: 85"
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan Evaluasi / Feedback Redaksi</label>
                            <textarea 
                                name="feedback" 
                                id="modalInputFeedback"
                                rows="3" 
                                placeholder="Masukkan catatan evaluasi penulisan atau arahan revisi..."
                                class="form-control"
                            ></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-save me-1"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalBeriNilai = document.getElementById('modalBeriNilai');
    if (modalBeriNilai) {
        modalBeriNilai.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const pengumpulanId = button.getAttribute('data-pengumpulan-id');
            const userName = button.getAttribute('data-user-name');
            const fileName = button.getAttribute('data-file-name');
            const nilai = button.getAttribute('data-nilai');
            const feedback = button.getAttribute('data-feedback');

            const form = document.getElementById('formBeriNilai');
            form.action = "{{ url('admin/penugasan/pengumpulan') }}/" + pengumpulanId + "/nilai";

            document.getElementById('modalNamaPeserta').textContent = userName || '-';
            document.getElementById('modalNamaFile').textContent = fileName || '-';
            document.getElementById('modalInputNilai').value = nilai || '';
            document.getElementById('modalInputFeedback').value = feedback || '';
        });
    }
});
</script>
@endsection
