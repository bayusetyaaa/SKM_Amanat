<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Rekomendasi & Penetapan Divisi Magang - SKM Amanat</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 sm:p-10 text-slate-900">
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-sm border border-slate-200">
        <!-- Print Trigger Toolbar -->
        <div class="no-print flex justify-between items-center mb-8 pb-4 border-b border-slate-200">
            <button onclick="window.history.back()" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                &larr; Kembali
            </button>
            <button onclick="window.print()" class="px-5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-black transition-colors">
                Cetak Dokumen Laporan
            </button>
        </div>

        <!-- Kop Surat SKM Amanat -->
        <div class="text-center border-b-2 border-slate-900 pb-6 mb-8">
            <h1 class="text-xl font-extrabold uppercase tracking-wide text-slate-900">
                SURAT KABAR MAHASISWA (SKM) AMANAT
            </h1>
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mt-0.5">
                LEMBAGA PERS MAHASISWA UIN WALISONGO SEMARANG
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Sekretariat: Gedung PKM Lantai 2, Kampus 3 UIN Walisongo, Jl. Prof. Dr. Hamka, Tambakaji, Ngaliyan, Semarang
            </p>
        </div>

        <!-- Report Title -->
        <div class="text-center mb-8">
            <h3 class="text-base font-extrabold uppercase text-slate-900 underline underline-offset-4">
                BERITA ACARA REKAPITULASI PENILAIAN PROFILE MATCHING & PENETAPAN DIVISI MAGANG
            </h3>
            <p class="text-xs text-slate-600 mt-1">
                Tahun Kepengurusan {{ date('Y') }} / {{ date('Y') + 1 }}
            </p>
        </div>

        <!-- Table -->
        <div class="border border-slate-900 rounded-lg overflow-hidden mb-8">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-100 border-b border-slate-900 font-bold text-slate-900">
                    <tr>
                        <th class="p-3 border-r border-slate-900 text-center w-12">Rank</th>
                        <th class="p-3 border-r border-slate-900">Nama Calon Anggota</th>
                        <th class="p-3 border-r border-slate-900">NIM / Prodi</th>
                        <th class="p-3 border-r border-slate-900 text-center">NCF (60%)</th>
                        <th class="p-3 border-r border-slate-900 text-center">NSF (40%)</th>
                        <th class="p-3 border-r border-slate-900 text-center">Skor Total (PV)</th>
                        <th class="p-3 text-center">Penetapan Divisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @foreach($hasilRankings as $index => $item)
                        @php $u = $item->user; @endphp
                        <tr>
                            <td class="p-3 border-r border-slate-900 text-center font-bold">{{ $item->ranking ?? ($index + 1) }}</td>
                            <td class="p-3 border-r border-slate-900 font-bold">{{ $u->name }}</td>
                            <td class="p-3 border-r border-slate-900">{{ $u->profil->nim ?? '-' }} ({{ $u->profil->prodi ?? '-' }})</td>
                            <td class="p-3 border-r border-slate-900 text-center font-medium">{{ number_format($item->ncf, 2) }}</td>
                            <td class="p-3 border-r border-slate-900 text-center font-medium">{{ number_format($item->nsf, 2) }}</td>
                            <td class="p-3 border-r border-slate-900 text-center font-extrabold">{{ number_format($item->nilai_total, 2) }}</td>
                            <td class="p-3 text-center font-bold uppercase">
                                {{ $u->profil->keputusan_final ?? $item->divisi->nama }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 gap-8 text-xs text-slate-800 pt-8 mt-12">
            <div class="text-center">
                <p>Mengetahui,</p>
                <p class="font-bold mt-1">Pemimpin Umum SKM Amanat</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase">( ........................................ )</p>
                <p class="text-[11px] text-slate-500">NIM. ....................................</p>
            </div>

            <div class="text-center">
                <p>Semarang, {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold mt-1">Kepala Divisi HRD / PSDM</p>
                <div class="h-20"></div>
                <p class="font-bold underline uppercase">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-slate-500">Pengurus SKM Amanat</p>
            </div>
        </div>
    </div>
</body>
</html>
