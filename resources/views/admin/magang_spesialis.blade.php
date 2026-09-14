@extends('layouts/contentNavbarLayout')

@section('title', 'Data Magang Spesialis')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">
                        <i class="bx bx-briefcase text-primary me-2"></i> Data Magang Spesialis
                    </h5>
                    <small class="text-muted">Perbandingan skor akhir Profile Matching (Redaksi vs Konten) dan penetapan divisi magang</small>
                </div>
                <div>
                    <span class="badge bg-label-primary fs-6 px-3 py-2">
                        Total: {{ $anggotaList->count() }} Anggota
                    </span>
                </div>
            </div>

            <div class="card-body pt-4">
                <!-- Search & Filter Bar -->
                <form action="{{ route('admin.mapping-spesialis') }}" method="GET" class="row g-2 align-items-center mb-4">
                    <div class="col-12 col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari nama atau NIM..." 
                                class="form-control"
                            >
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label fw-semibold mb-0 text-nowrap small">Divisi Pilihan:</label>
                            <select name="divisi_pilihan" onchange="this.form.submit()" class="form-select">
                                <option value="Semua Pilihan" {{ request('divisi_pilihan') === 'Semua Pilihan' ? 'selected' : '' }}>Semua Pilihan</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->nama }}" {{ request('divisi_pilihan') === $divisi->nama ? 'selected' : '' }}>
                                        Divisi {{ $divisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label fw-semibold mb-0 text-nowrap small">Divisi Akhir:</label>
                            <select name="divisi_akhir" onchange="this.form.submit()" class="form-select">
                                <option value="Semua Divisi Akhir" {{ request('divisi_akhir') === 'Semua Divisi Akhir' ? 'selected' : '' }}>Semua Divisi Akhir</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->nama }}" {{ request('divisi_akhir') === $divisi->nama ? 'selected' : '' }}>
                                        Divisi {{ $divisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="bx bx-printer me-1"></i> Cetak
                        </button>
                    </div>
                </form>

                <!-- Tabel Data Magang Spesialis -->
                <div class="table-responsive text-nowrap border rounded">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama Lengkap</th>
                                <th class="text-center" style="width: 140px;">Skor Akhir Redaksi</th>
                                <th class="text-center" style="width: 140px;">Skor Akhir Konten</th>
                                <th class="text-center" style="width: 160px;">Pilihan Divisi Magang</th>
                                <th class="text-center" style="min-width: 240px;">Hasil Akhir Divisi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($anggotaList as $index => $anggota)
                                @php
                                    $hasilRedaksi = $anggota->hasilProfileMatching->firstWhere('divisi.nama', 'Redaksi');
                                    $hasilKonten = $anggota->hasilProfileMatching->firstWhere('divisi.nama', 'Konten');
                                    $pilihanAwal = $anggota->profil->pilihan_divisi_awal ?? null;
                                    $rekomendasiPm = $anggota->hasilProfileMatching->firstWhere('rekomendasi', true)->divisi->nama ?? null;
                                    $hasilAkhir = in_array($anggota->profil->keputusan_final ?? '', ['Redaksi', 'Konten']) 
                                        ? $anggota->profil->keputusan_final 
                                        : $rekomendasiPm;
                                @endphp
                                <tr>
                                    <!-- 1. No -->
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>

                                    <!-- 2. Nama Lengkap -->
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                                    {{ strtoupper(substr($anggota->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-heading">{{ $anggota->name }}</h6>
                                                <small class="text-muted">NIM: {{ $anggota->profil->nim ?? '-' }} &bull; {{ $anggota->profil->prodi ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 3. Skor Akhir Redaksi -->
                                    <td class="text-center">
                                        @if($hasilRedaksi && $hasilRedaksi->nilai_total !== null)
                                            <span class="badge bg-label-primary fs-6 px-3 py-1 fw-bold">
                                                {{ number_format($hasilRedaksi->nilai_total, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary text-muted">Belum Dihitung</span>
                                        @endif
                                    </td>

                                    <!-- 4. Skor Akhir Konten -->
                                    <td class="text-center">
                                        @if($hasilKonten && $hasilKonten->nilai_total !== null)
                                            <span class="badge bg-label-info fs-6 px-3 py-1 fw-bold">
                                                {{ number_format($hasilKonten->nilai_total, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary text-muted">Belum Dihitung</span>
                                        @endif
                                    </td>

                                    <!-- 5. Pilihan Divisi Magang -->
                                    <td class="text-center">
                                        @if($pilihanAwal)
                                            <span class="badge {{ $pilihanAwal === 'Redaksi' ? 'bg-label-primary' : 'bg-label-info' }} px-3 py-2 fw-semibold">
                                                <i class="bx {{ $pilihanAwal === 'Redaksi' ? 'bx-news' : 'bx-paint' }} me-1"></i>
                                                {{ $pilihanAwal }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">Belum Memilih</span>
                                        @endif
                                    </td>

                                    <!-- 6. Hasil Akhir Divisi & Tombol Edit (1 Kolom) -->
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            @if($hasilAkhir)
                                                <span class="badge {{ $hasilAkhir === 'Redaksi' ? 'bg-success' : 'bg-info' }} text-white fs-6 px-3 py-2 shadow-sm">
                                                    <i class="bx bx-award me-1"></i> Divisi {{ $hasilAkhir }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-warning px-3 py-2">Menunggu Keputusan</span>
                                            @endif

                                            <!-- Tombol Pop-up Edit Modal -->
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary shadow-none"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal{{ $anggota->id }}"
                                                title="Ubah Hasil Akhir Divisi"
                                            >
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>
                                        </div>

                                        <!-- Pop-up Modal Bootstrap 5 Native -->
                                        <div class="modal fade text-start" id="editModal{{ $anggota->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content shadow-lg border-0">
                                                    <div class="modal-header border-bottom py-3">
                                                        <h5 class="modal-title fw-bold text-heading d-flex align-items-center mb-0">
                                                            <i class="bx bx-edit text-primary fs-4 me-2"></i> Ubah Hasil Akhir Divisi Magang
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <form action="{{ route('admin.mapping-spesialis.update', $anggota->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <!-- Profil Singkat Anggota -->
                                                            <div class="card bg-lighter border shadow-none mb-3">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex align-items-center gap-3">
                                                                        <div class="avatar avatar-md">
                                                                            <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">
                                                                                {{ strtoupper(substr($anggota->name, 0, 2)) }}
                                                                            </span>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0 fw-bold text-heading">{{ $anggota->name }}</h6>
                                                                            <small class="text-muted">
                                                                                NIM: {{ $anggota->profil->nim ?? '-' }} &bull; {{ $anggota->profil->prodi ?? '-' }}
                                                                            </small>
                                                                            <div class="small mt-1">
                                                                                Minat Awal: <span class="badge bg-label-secondary">{{ $pilihanAwal ?? 'Belum Memilih' }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Pilihan Divisi (Redaksi / Konten) -->
                                                            <label class="form-label fw-bold text-heading mb-2">Pilih Penetapan Divisi Spesialis: <span class="text-danger">*</span></label>
                                                            <div class="row g-2">
                                                                <!-- Opsi Divisi Redaksi -->
                                                                <div class="col-6">
                                                                    <label class="card border p-3 cursor-pointer h-100 mb-0 shadow-none" for="radioRedaksi{{ $anggota->id }}">
                                                                        <div class="form-check d-flex align-items-start gap-2 ps-0 mb-0">
                                                                            <input 
                                                                                class="form-check-input ms-0 me-2" 
                                                                                type="radio" 
                                                                                name="keputusan_final" 
                                                                                value="Redaksi" 
                                                                                id="radioRedaksi{{ $anggota->id }}"
                                                                                {{ ($hasilAkhir === 'Redaksi' || !$hasilAkhir) ? 'checked' : '' }}
                                                                                required
                                                                            >
                                                                            <div>
                                                                                <div class="fw-bold text-heading">Divisi Redaksi</div>
                                                                                <small class="text-muted d-block" style="font-size: 0.75rem;">Kepenulisan berita & analisis isu</small>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>

                                                                <!-- Opsi Divisi Konten -->
                                                                <div class="col-6">
                                                                    <label class="card border p-3 cursor-pointer h-100 mb-0 shadow-none" for="radioKonten{{ $anggota->id }}">
                                                                        <div class="form-check d-flex align-items-start gap-2 ps-0 mb-0">
                                                                            <input 
                                                                                class="form-check-input ms-0 me-2" 
                                                                                type="radio" 
                                                                                name="keputusan_final" 
                                                                                value="Konten" 
                                                                                id="radioKonten{{ $anggota->id }}"
                                                                                {{ $hasilAkhir === 'Konten' ? 'checked' : '' }}
                                                                                required
                                                                            >
                                                                            <div>
                                                                                <div class="fw-bold text-heading">Divisi Konten</div>
                                                                                <small class="text-muted d-block" style="font-size: 0.75rem;">Desain visual, foto & media kreatif</small>
                                                                            </div>
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer border-top py-3">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                <i class="bx bx-x me-1"></i> Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                                                <i class="bx bx-check me-1"></i> Simpan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bx bx-user-x fs-1 text-secondary mb-2"></i>
                                        <p class="mb-0">Tidak ada data anggota magang yang sesuai dengan kriteria pencarian / filter.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



