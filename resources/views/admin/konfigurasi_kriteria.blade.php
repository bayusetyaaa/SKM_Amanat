@extends('layouts/contentNavbarLayout')

@section('title', 'Konfigurasi Kriteria & Profil Target')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-heading">
                        <i class="bx bx-cog text-primary me-2"></i> Konfigurasi Standar Profile Matching
                    </h5>
                    <small class="text-muted">Atur nilai target profil jabatan ideal dan klasifikasi Core Factor (CF) vs Secondary Factor (SF)</small>
                </div>
                <div>
                    <span class="badge bg-label-primary">
                        Formula Bobot Linear: 100 - |Gap|
                    </span>
                </div>
            </div>

            <div class="card-body pt-4">
                <form action="{{ route('admin.konfigurasi-kriteria.update') }}" method="POST">
                    @csrf

                    <!-- Selector Divisi -->
                    <div class="card bg-lighter shadow-none border mb-4">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold text-heading mb-1">
                                <i class="bx bx-buildings me-1 text-primary"></i> Atur Profil Target untuk Divisi:
                            </label>
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <select 
                                        name="divisi_id" 
                                        id="divisi_id"
                                        onchange="window.location.href = '{{ route('admin.konfigurasi-kriteria') }}?divisi_id=' + this.value"
                                        class="form-select fw-semibold"
                                    >
                                        @foreach($divisis as $d)
                                            <option value="{{ $d->id }}" {{ $selectedDivisi->id === $d->id ? 'selected' : '' }}>
                                                Divisi {{ $d->nama }} ({{ $d->nama === 'Redaksi' ? 'Redaksi Kepenulisan' : 'Konten & Desain Visual' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Groups per Aspek -->
                    @php
                        $aspekWeights = [
                            'Aspek 1. Kompetensi Teknis & Kreatif' => '40%',
                            'Aspek 2. Komunikasi & Sosial' => '30%',
                            'Aspek 3. Sikap & Perilaku' => '30%',
                        ];
                    @endphp

                    <div class="d-flex flex-column gap-4">
                        @foreach($aspekGroups as $aspekName => $kriteriaList)
                            <div class="card border shadow-none">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                    <h6 class="mb-0 fw-bold text-heading">
                                        <i class="bx bx-folder me-1 text-primary"></i> {{ strtoupper($aspekName) }}
                                    </h6>
                                    <span class="badge bg-label-info">Bobot Aspek: {{ $aspekWeights[$aspekName] ?? '33%' }}</span>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 50px;">No</th>
                                                <th style="width: 180px;">Kriteria Evaluasi</th>
                                                <th>Parameter / Indikator</th>
                                                <th style="width: 200px;">Sumber Penilaian</th>
                                                <th class="text-center" style="width: 140px;">Nilai Target</th>
                                                <th class="text-center" style="width: 150px;">Jenis Faktor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @foreach($kriteriaList as $index => $k)
                                                @php
                                                    $targetItem = $targets->get($k->id);
                                                    $valTarget = $targetItem ? $targetItem->nilai_target : 80;
                                                    $valFaktor = $targetItem ? $targetItem->faktor : 'core';
                                                @endphp
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="fw-bold text-heading">{{ $k->nama }}</div>
                                                        <span class="badge bg-label-primary fw-bold">{{ $k->kode }}</span>
                                                    </td>
                                                    <td class="text-wrap" style="max-width: 320px;">
                                                        <small class="text-body">{{ $k->parameter ?? $k->deskripsi }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-label-secondary text-wrap text-start">
                                                            <i class="bx bx-file-blank me-1"></i>{{ $k->sumber_penilaian ?? 'Tugas / Wawancara' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <input 
                                                            type="number" 
                                                            name="targets[{{ $k->id }}][target]" 
                                                            value="{{ $valTarget }}" 
                                                            min="1" 
                                                            max="100" 
                                                            required 
                                                            class="form-control form-control-sm text-center fw-bold mx-auto"
                                                            style="max-width: 90px;"
                                                        >
                                                    </td>
                                                    <td class="text-center">
                                                        <select 
                                                            name="targets[{{ $k->id }}][faktor]" 
                                                            class="form-select form-select-sm text-center fw-bold mx-auto"
                                                            style="max-width: 140px;"
                                                        >
                                                            <option value="core" {{ $valFaktor === 'core' ? 'selected' : '' }}>Core Factor (CF)</option>
                                                            <option value="secondary" {{ $valFaktor === 'secondary' ? 'selected' : '' }}>Secondary Factor (SF)</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="bx bx-save me-1"></i> Simpan Konfigurasi Target
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
