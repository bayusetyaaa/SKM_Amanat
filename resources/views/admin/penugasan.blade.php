@extends('layouts/contentNavbarLayout')

@section('title', 'Kelola Penugasan & Nilai Wawancara')

@section('content')
<div class="row g-4">
    <!-- Header Card -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="card-title fw-bold mb-1 text-heading">
                            <i class="bx bx-task text-primary me-2"></i> Penugasan Magang & Penilaian Wawancara
                        </h4>
                        <p class="text-muted mb-0 small">
                            Kelola instruksi tugas magang calon anggota dan input skor penilaian wawancara (Wawasan Sosial, Public Speaking, dan Psikotes Karakteristik).
                        </p>
                    </div>
                    <!-- Navigation Pills -->
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-penugasan">
                                <i class="bx bx-file me-1"></i> Daftar Penugasan
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link fw-bold" role="tab" data-bs-toggle="tab" data-bs-target="#tab-wawancara">
                                <i class="bx bx-conversation me-1"></i> Nilai Wawancara
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="col-12">
        <div class="tab-content p-0">
            <!-- TAB 1: PENUGASAN MAGANG -->
            <div class="tab-pane fade show active" id="tab-penugasan" role="tabpanel">
                <div class="row g-4">
                    <!-- Form Tambah Penugasan -->
                    <div class="col-12 col-lg-5">
                        <div class="card shadow-sm h-100">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0 fw-bold text-heading">
                                    <i class="bx bx-plus-circle text-primary me-2"></i> Buat Penugasan Baru
                                </h5>
                                <small class="text-muted">Buat tugas liputan/penulisan PDF untuk calon anggota</small>
                            </div>
                            <div class="card-body pt-4">
                                <form action="{{ route('admin.penugasan.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Kategori Penugasan <span class="text-danger">*</span></label>
                                        <select name="jenis" id="selectKategoriPenugasan" required class="form-select" onchange="toggleIndikatorCakruma(this.value)">
                                            <option value="penugasan cakruma" selected>Penugasan Cakruma</option>
                                            <option value="penugasan magang redaksi">Penugasan Magang Redaksi</option>
                                            <option value="penugasan magang konten">Penugasan Magang Konten</option>
                                            <option value="penugasan magang semua">Penugasan Magang Semua</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">Tentukan target peserta dan alur penilaian tugas.</small>
                                    </div>

                                    <!-- Indikator Kriteria untuk Penugasan Cakruma -->
                                    <div class="mb-3 p-3 bg-lighter rounded border" id="wrapperIndikatorCakruma">
                                        <label class="form-label fw-bold text-heading d-block mb-1">
                                            <i class="bx bx-check-shield text-primary me-1"></i> Indikator Evaluasi Profile Matching:
                                        </label>
                                        <small class="text-muted d-block mb-2">Pilih indikator yang dinilai dari tugas ini (bisa pilih lebih dari 1 atau dikosongkan):</small>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="indikator_kriteria[]" value="K1" id="indK1">
                                                <label class="form-check-label fw-semibold" for="indK1">
                                                    <span class="badge bg-label-primary me-1">K1</span> Kepenulisan <small class="text-muted font-normal">(Struktur straight news, bahasa jurnalistik)</small>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="indikator_kriteria[]" value="K2" id="indK2">
                                                <label class="form-check-label fw-semibold" for="indK2">
                                                    <span class="badge bg-label-info me-1">K2</span> Kepekaan Isu <small class="text-muted font-normal">(Identifikasi & sudut pandang isu)</small>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="indikator_kriteria[]" value="K3" id="indK3">
                                                <label class="form-check-label fw-semibold" for="indK3">
                                                    <span class="badge bg-label-warning me-1">K3</span> Kreativitas <small class="text-muted font-normal">(Orisinalitas ide & kualitas visual)</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul Penugasan <span class="text-danger">*</span></label>
                                        <input type="text" name="judul" placeholder="Contoh: Menulis Berita Straight News / Analisis Isu Kampus" required class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tenggat Waktu (Deadline) <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="deadline" required class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Deskripsi & Petunjuk Teknis</label>
                                        <textarea name="deskripsi" rows="4" placeholder="Instruksi penulisan, format PDF, jumlah kata minimal, dll..." class="form-control"></textarea>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary fw-bold">
                                            <i class="bx bx-save me-1"></i> Publikasikan Penugasan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Penugasan Aktif -->
                    <div class="col-12 col-lg-7">
                        <div class="card shadow-sm h-100">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold text-heading">
                                    <i class="bx bx-list-check text-primary me-2"></i> Daftar Penugasan Aktif
                                </h5>
                                <span class="badge bg-label-primary">{{ $penugasans->count() }} Tugas</span>
                            </div>
                            <div class="card-body pt-4">
                                <div class="d-flex flex-column gap-3">
                                    @forelse($penugasans as $index => $penugasan)
                                        <div class="card border shadow-none">
                                            <div class="card-body p-3">
                                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
                                                    <div>
                                                        <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                                            <span class="badge bg-label-secondary text-capitalize">{{ str_replace('_', ' ', $penugasan->jenis) }}</span>
                                                            @foreach($penugasan->indikator_labels as $kode => $label)
                                                                <span class="badge bg-label-info">{{ $label }}</span>
                                                            @endforeach
                                                        </div>
                                                        <h6 class="mb-1 fw-bold text-heading">{{ $penugasan->judul }}</h6>
                                                        <div class="small text-muted d-flex flex-wrap gap-3 mt-2">
                                                            <span><i class="bx bx-time me-1"></i> Deadline: {{ $penugasan->deadline->translatedFormat('d M Y, H:i') }} WIB</span>
                                                            <span class="text-primary fw-semibold"><i class="bx bx-file me-1"></i> Terkumpul: {{ $penugasan->pengumpulan_tugas_count }} Berkas PDF</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1 mt-2 mt-sm-0">
                                                        <a href="{{ route('admin.penugasan.detail', $penugasan->id) }}" class="btn btn-sm btn-outline-primary fw-bold">
                                                            <i class="bx bx-show me-1"></i> Detail & Beri Nilai
                                                        </a>
                                                        <form action="{{ route('admin.penugasan.destroy', $penugasan->id) }}" method="POST" onsubmit="return confirm('Hapus penugasan ini?');" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted border border-dashed rounded">
                                            <i class="bx bx-task-x fs-1 text-secondary mb-2"></i>
                                            <p class="mb-0">Belum ada penugasan yang dibuat.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PENILAIAN WAWANCARA & PSIKOTES -->
            <div class="tab-pane fade" id="tab-wawancara" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h5 class="card-title mb-0 fw-bold text-heading">
                                <i class="bx bx-microphone text-primary me-2"></i> Input Nilai Wawancara & Psikotes Calon Anggota
                            </h5>
                            <small class="text-muted">Penilaian aspek wawancara: K4 (Wawasan Sosial), K5 (Public Speaking), dan K7 (Karakteristik/Psikotes)</small>
                        </div>
                        <span class="badge bg-label-primary fs-6 px-3 py-2">
                            Total: {{ $calonAnggotas->count() }} Pendaftar
                        </span>
                    </div>

                    <div class="card-body pt-4">
                        <form action="{{ route('admin.penugasan.wawancara-massal') }}" method="POST">
                            @csrf

                            <!-- Tabel Input Wawancara -->
                            <div class="table-responsive text-nowrap border rounded mb-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3" style="width: 50px;">No</th>
                                            <th style="min-width: 220px;">Nama Calon Anggota</th>
                                            <th class="text-center" style="min-width: 150px;">
                                                <div class="fw-bold text-heading">K4. Wawasan Sosial</div>
                                                <small class="text-muted">Wawancara Sosial (1-100)</small>
                                            </th>
                                            <th class="text-center" style="min-width: 150px;">
                                                <div class="fw-bold text-heading">K5. Public Speaking</div>
                                                <small class="text-muted">Komunikasi Lisan (1-100)</small>
                                            </th>
                                            <th class="text-center" style="min-width: 150px;">
                                                <div class="fw-bold text-heading">K7. Karakteristik</div>
                                                <small class="text-muted">Wawancara Psikotes (1-100)</small>
                                            </th>
                                            <th class="text-center" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @forelse($calonAnggotas as $index => $ca)
                                            @php
                                                $k4Id = $kriteriaWawancara['K4']->id ?? null;
                                                $k5Id = $kriteriaWawancara['K5']->id ?? null;
                                                $k7Id = $kriteriaWawancara['K7']->id ?? null;

                                                $valK4 = $ca->nilaiEvaluasi->firstWhere('kriteria_id', $k4Id)->nilai_aktual ?? '';
                                                $valK5 = $ca->nilaiEvaluasi->firstWhere('kriteria_id', $k5Id)->nilai_aktual ?? '';
                                                $valK7 = $ca->nilaiEvaluasi->firstWhere('kriteria_id', $k7Id)->nilai_aktual ?? '';

                                                $candidateData = [
                                                    'id' => $ca->id,
                                                    'name' => $ca->name,
                                                    'nim' => $ca->profil->nim ?? '-',
                                                    'prodi' => $ca->profil->prodi ?? '-',
                                                    'nilai_k4' => $valK4,
                                                    'nilai_k5' => $valK5,
                                                    'nilai_k7' => $valK7,
                                                ];
                                            @endphp
                                            <tr>
                                                <td class="ps-3 fw-bold">{{ $loop->iteration }}</td>
                                                <td>
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

                                                <!-- Input K4 Wawasan Sosial -->
                                                <td class="text-center">
                                                    <input 
                                                        type="number" 
                                                        name="nilai[{{ $ca->id }}][nilai_k4]" 
                                                        value="{{ $valK4 }}" 
                                                        min="0" 
                                                        max="100" 
                                                        placeholder="1-100" 
                                                        class="form-control form-control-sm text-center fw-bold mx-auto"
                                                        style="max-width: 90px;"
                                                    >
                                                </td>

                                                <!-- Input K5 Public Speaking -->
                                                <td class="text-center">
                                                    <input 
                                                        type="number" 
                                                        name="nilai[{{ $ca->id }}][nilai_k5]" 
                                                        value="{{ $valK5 }}" 
                                                        min="0" 
                                                        max="100" 
                                                        placeholder="1-100" 
                                                        class="form-control form-control-sm text-center fw-bold mx-auto"
                                                        style="max-width: 90px;"
                                                    >
                                                </td>

                                                <!-- Input K7 Karakteristik / Psikotes -->
                                                <td class="text-center">
                                                    <input 
                                                        type="number" 
                                                        name="nilai[{{ $ca->id }}][nilai_k7]" 
                                                        value="{{ $valK7 }}" 
                                                        min="0" 
                                                        max="100" 
                                                        placeholder="1-100" 
                                                        class="form-control form-control-sm text-center fw-bold mx-auto"
                                                        style="max-width: 90px;"
                                                    >
                                                </td>

                                                <!-- Tombol Edit Modal -->
                                                <td class="text-center">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalInputWawancara"
                                                        data-user-id="{{ $ca->id }}"
                                                        data-user-name="{{ $ca->name }}"
                                                        data-user-nim="{{ $ca->profil->nim ?? '-' }}"
                                                        data-user-prodi="{{ $ca->profil->prodi ?? '-' }}"
                                                        data-nilai-k4="{{ $valK4 }}"
                                                        data-nilai-k5="{{ $valK5 }}"
                                                        data-nilai-k7="{{ $valK7 }}"
                                                        title="Beri Nilai Wawancara"
                                                    >
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    Belum ada calon anggota terdaftar.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <i class="bx bx-save me-1"></i> Simpan Seluruh Nilai Wawancara
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Input Nilai Wawancara Per Calon Anggota Bootstrap 5 Native -->
    <div class="modal fade" id="modalInputWawancara" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold text-heading">
                        <i class="bx bx-conversation text-primary me-2"></i> Form Nilai Wawancara & Psikotes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formInputWawancara" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <!-- Info Candidate -->
                        <div class="card bg-lighter shadow-none border mb-3">
                            <div class="card-body p-3">
                                <div class="fw-bold text-heading" id="modalWawancaraNama">-</div>
                                <small class="text-muted">
                                    NIM: <span id="modalWawancaraNim">-</span> &bull; 
                                    Prodi: <span id="modalWawancaraProdi">-</span>
                                </small>
                            </div>
                        </div>

                        <!-- K4 Wawasan Sosial -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <span class="badge bg-label-primary me-1">K4</span> Wawasan Sosial (Skala 1 - 100)
                            </label>
                            <input 
                                type="number" 
                                name="nilai_k4" 
                                id="modalInputK4"
                                min="0" 
                                max="100" 
                                placeholder="Contoh: 85" 
                                class="form-control form-control-lg fw-bold text-center"
                            >
                            <div class="form-text">Pemahaman isu sosial kampus, kebijakan universitas, dan argumentasi.</div>
                        </div>

                        <!-- K5 Public Speaking -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <span class="badge bg-label-primary me-1">K5</span> Public Speaking & Komunikasi (Skala 1 - 100)
                            </label>
                            <input 
                                type="number" 
                                name="nilai_k5" 
                                id="modalInputK5"
                                min="0" 
                                max="100" 
                                placeholder="Contoh: 80" 
                                class="form-control form-control-lg fw-bold text-center"
                            >
                            <div class="form-text">Kejelasan komunikasi, penyampaian gagasan, artikulasi, dan intonasi saat wawancara.</div>
                        </div>

                        <!-- K7 Karakteristik / Psikotes -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <span class="badge bg-label-primary me-1">K7</span> Karakteristik & Psikotes (Skala 1 - 100)
                            </label>
                            <input 
                                type="number" 
                                name="nilai_k7" 
                                id="modalInputK7"
                                min="0" 
                                max="100" 
                                placeholder="Contoh: 88" 
                                class="form-control form-control-lg fw-bold text-center"
                            >
                            <div class="form-text">Tanggung jawab, kerja sama tim, integritas etika, dan kesesuaian budaya SKM Amanat.</div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bx bx-save me-1"></i> Simpan Nilai Wawancara
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleIndikatorCakruma(val) {
    const wrapper = document.getElementById('wrapperIndikatorCakruma');
    if (wrapper) {
        if (val === 'penugasan cakruma') {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('selectKategoriPenugasan');
    if (select) {
        toggleIndikatorCakruma(select.value);
    }

    const modalWawancara = document.getElementById('modalInputWawancara');
    if (modalWawancara) {
        modalWawancara.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;

            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const userNim = button.getAttribute('data-user-nim');
            const userProdi = button.getAttribute('data-user-prodi');
            const scoreK4 = button.getAttribute('data-nilai-k4');
            const scoreK5 = button.getAttribute('data-nilai-k5');
            const scoreK7 = button.getAttribute('data-nilai-k7');

            const form = document.getElementById('formInputWawancara');
            form.action = "{{ url('admin/penugasan/wawancara') }}/" + userId;

            document.getElementById('modalWawancaraNama').textContent = userName || '-';
            document.getElementById('modalWawancaraNim').textContent = userNim || '-';
            document.getElementById('modalWawancaraProdi').textContent = userProdi || '-';
            document.getElementById('modalInputK4').value = scoreK4 || '';
            document.getElementById('modalInputK5').value = scoreK5 || '';
            document.getElementById('modalInputK7').value = scoreK7 || '';
        });
    }
});
</script>
@endsection
