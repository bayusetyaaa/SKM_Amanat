<?php

namespace Database\Seeders;

use App\Models\Berkas;
use App\Models\Divisi;
use App\Models\Kegiatan;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use App\Models\Penugasan;
use App\Models\Presensi;
use App\Models\ProfilCalonAnggota;
use App\Models\ProfilTarget;
use App\Models\User;
use App\Services\EvaluasiNilaiService;
use App\Services\ProfileMatchingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Mute model event listeners during seed to prevent recursive recalculations
        Kegiatan::flushEventListeners();
        Penugasan::flushEventListeners();
        PengumpulanTugas::flushEventListeners();
        Presensi::flushEventListeners();

        // 1. Akun Admin / Pengurus
        $admin = User::create([
            'name' => 'HRD SKM Amanat',
            'email' => 'admin@amanat.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Master Divisi
        $divisiRedaksi = Divisi::create([
            'nama' => 'Redaksi',
            'deskripsi' => 'Divisi peliputan berita, kepenulisan feature, opini, investigasi, dan manajemen keredaksian Surat Kabar Mahasiswa Amanat.',
        ]);

        $divisiKonten = Divisi::create([
            'nama' => 'Konten',
            'deskripsi' => 'Divisi kreatif produksi media sosial, desain grafis, fotografi, videografi, podcast, dan manajemen platform digital SKM Amanat.',
        ]);

        // 3. 7 Kriteria Penilaian (Sesuai Indikator & Sumber)
        $kriterias = [
            [
                'kode' => 'K1',
                'nama' => 'Kepenulisan',
                'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif',
                'parameter' => 'Struktur straight news, unsur berita, fakta, bahasa jurnalistik, dan kejelasan informasi.',
                'sumber_penilaian' => 'Tugas kepenulisan',
                'deskripsi' => 'Struktur straight news, unsur berita, fakta, bahasa jurnalistik, dan kejelasan informasi.',
            ],
            [
                'kode' => 'K2',
                'nama' => 'Kepekaan Isu',
                'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif',
                'parameter' => 'Identifikasi, relevansi, permasalahan, dampak, dan sudut pandang isu.',
                'sumber_penilaian' => 'Tugas analisis isu',
                'deskripsi' => 'Identifikasi, relevansi, permasalahan, dampak, dan sudut pandang isu.',
            ],
            [
                'kode' => 'K3',
                'nama' => 'Kreativitas',
                'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif',
                'parameter' => 'Orisinalitas ide, komposisi, pengembangan konsep, kesesuaian, dan kualitas visual.',
                'sumber_penilaian' => 'Tugas kreatif',
                'deskripsi' => 'Orisinalitas ide, komposisi, pengembangan konsep, kesesuaian, dan kualitas visual.',
            ],
            [
                'kode' => 'K4',
                'nama' => 'Wawasan Sosial',
                'aspek' => 'Aspek 2. Komunikasi & Sosial',
                'parameter' => 'Pemahaman masalah sosial, pandangan, kondisi masyarakat, dan argumentasi.',
                'sumber_penilaian' => 'Nilai wawancara wawasan sosial',
                'deskripsi' => 'Pemahaman masalah sosial, pandangan, kondisi masyarakat, dan argumentasi.',
            ],
            [
                'kode' => 'K5',
                'nama' => 'Public Speaking',
                'aspek' => 'Aspek 2. Komunikasi & Sosial',
                'parameter' => 'Kejelasan komunikasi, penyampaian gagasan, artikulasi, intonasi, penguasaan materi, dan respons terhadap pertanyaan.',
                'sumber_penilaian' => 'Nilai keseluruhan wawancara',
                'deskripsi' => 'Kejelasan komunikasi, penyampaian gagasan, artikulasi, intonasi, penguasaan materi, dan respons terhadap pertanyaan.',
            ],
            [
                'kode' => 'K6',
                'nama' => 'Kedisiplinan',
                'aspek' => 'Aspek 3. Sikap & Perilaku',
                'parameter' => 'Kehadiran, ketepatan waktu, ketepatan pengumpulan tugas, dan konsistensi kegiatan.',
                'sumber_penilaian' => 'Data presensi dan rekam tugas',
                'deskripsi' => 'Kehadiran, ketepatan waktu, ketepatan pengumpulan tugas, dan konsistensi kegiatan.',
            ],
            [
                'kode' => 'K7',
                'nama' => 'Karakteristik',
                'aspek' => 'Aspek 3. Sikap & Perilaku',
                'parameter' => 'Tanggung jawab, kerja sama, respons terhadap masukan, inisiatif, dan konsistensi sikap.',
                'sumber_penilaian' => 'Nilai wawancara psikotes',
                'deskripsi' => 'Tanggung jawab, kerja sama, respons terhadap masukan, inisiatif, dan konsistensi sikap.',
            ],
        ];

        $kriteriaMap = [];
        foreach ($kriterias as $k) {
            $created = Kriteria::create($k);
            $kriteriaMap[$k['kode']] = $created->id;
        }

        // 4. Profil Target per Divisi
        // Target Divisi Redaksi
        $redaksiTargets = [
            ['kode' => 'K1', 'target' => 90, 'faktor' => 'core'],
            ['kode' => 'K2', 'target' => 85, 'faktor' => 'core'],
            ['kode' => 'K4', 'target' => 80, 'faktor' => 'core'],
            ['kode' => 'K6', 'target' => 75, 'faktor' => 'secondary'],
            ['kode' => 'K7', 'target' => 75, 'faktor' => 'secondary'],
            ['kode' => 'K3', 'target' => 70, 'faktor' => 'secondary'],
            ['kode' => 'K5', 'target' => 65, 'faktor' => 'secondary'],
        ];
        foreach ($redaksiTargets as $t) {
            ProfilTarget::create([
                'divisi_id' => $divisiRedaksi->id,
                'kriteria_id' => $kriteriaMap[$t['kode']],
                'nilai_target' => $t['target'],
                'faktor' => $t['faktor'],
            ]);
        }

        // Target Divisi Konten
        $kontenTargets = [
            ['kode' => 'K3', 'target' => 90, 'faktor' => 'core'],
            ['kode' => 'K2', 'target' => 80, 'faktor' => 'core'],
            ['kode' => 'K5', 'target' => 80, 'faktor' => 'core'],
            ['kode' => 'K6', 'target' => 75, 'faktor' => 'secondary'],
            ['kode' => 'K7', 'target' => 75, 'faktor' => 'secondary'],
            ['kode' => 'K1', 'target' => 65, 'faktor' => 'secondary'],
            ['kode' => 'K4', 'target' => 65, 'faktor' => 'secondary'],
        ];
        foreach ($kontenTargets as $t) {
            ProfilTarget::create([
                'divisi_id' => $divisiKonten->id,
                'kriteria_id' => $kriteriaMap[$t['kode']],
                'nilai_target' => $t['target'],
                'faktor' => $t['faktor'],
            ]);
        }

        // 5. Calon Anggota (Cakruma)
        $calonAnggotaData = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@gmail.com',
                'nim' => '2108096001',
                'prodi' => 'Ilmu Komunikasi',
                'angkatan' => '2024',
                'no_hp' => '081234567890',
                'alamat' => 'Ngaliyan, Kota Semarang',
                'pilihan' => 'Redaksi',
                'status' => 'lolos',
                'keputusan' => 'Redaksi',
                'nilai_tugas' => ['K1' => 88, 'K2' => 85, 'K3' => 75],
                'nilai_wawancara' => ['K4' => 82, 'K5' => 70, 'K7' => 80],
                'presensi_hadir' => [true, true, true, true, true], // 5/5 hadir (100%)
            ],
            [
                'name' => 'Citra Kirana',
                'email' => 'citra@gmail.com',
                'nim' => '2108096002',
                'prodi' => 'Komunikasi Penyiaran Islam',
                'angkatan' => '2024',
                'no_hp' => '082134567891',
                'alamat' => 'Tambakaji, Ngaliyan, Semarang',
                'pilihan' => 'Konten',
                'status' => 'lolos',
                'keputusan' => 'Konten',
                'nilai_tugas' => ['K1' => 70, 'K2' => 80, 'K3' => 88],
                'nilai_wawancara' => ['K4' => 72, 'K5' => 85, 'K7' => 78],
                'presensi_hadir' => [true, true, true, true, true], // 5/5 hadir (100%)
            ],
            [
                'name' => 'Diva Melati',
                'email' => 'diva@gmail.com',
                'nim' => '2108096003',
                'prodi' => 'Jurnalistik Islam',
                'angkatan' => '2024',
                'no_hp' => '083134567892',
                'alamat' => 'Beringin, Semarang Barat',
                'pilihan' => 'Konten',
                'status' => 'lolos',
                'keputusan' => 'Konten',
                'nilai_tugas' => ['K1' => 75, 'K2' => 82, 'K3' => 86],
                'nilai_wawancara' => ['K4' => 74, 'K5' => 84, 'K7' => 80],
                'presensi_hadir' => [true, true, true, true, true], // 5/5 hadir (100%)
            ],
            [
                'name' => 'Ghea Kirana',
                'email' => 'ghea@gmail.com',
                'nim' => '2108096004',
                'prodi' => 'Sastra Inggris',
                'angkatan' => '2024',
                'no_hp' => '085134567893',
                'alamat' => 'Jerakah, Tugu, Semarang',
                'pilihan' => 'Konten',
                'status' => 'lolos',
                'keputusan' => 'Konten',
                'nilai_tugas' => ['K1' => 68, 'K2' => 76], // K3 tidak mengumpulkan (2/3 tugas)
                'nilai_wawancara' => ['K4' => 70, 'K5' => 78, 'K7' => 76],
                'presensi_hadir' => [true, true, true, false, true], // 4/5 hadir (80%) - Izin di Pra Workshop 2
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'nim' => '2108096005',
                'prodi' => 'Teknologi Informasi',
                'angkatan' => '2024',
                'no_hp' => '087134567894',
                'alamat' => 'Sampangan, Gajahmungkur, Semarang',
                'pilihan' => 'Redaksi',
                'status' => 'menunggu',
                'keputusan' => null,
                'nilai_tugas' => ['K1' => 82, 'K2' => 78, 'K3' => 72],
                'nilai_wawancara' => ['K4' => 80, 'K5' => 68, 'K7' => 75],
                'presensi_hadir' => [true, false, true, true, true], // 4/5 hadir (80%) - Izin di Hunting Feature
            ],
        ];

        $userInstances = [];
        foreach ($calonAnggotaData as $cad) {
            $user = User::create([
                'name' => $cad['name'],
                'email' => $cad['email'],
                'password' => Hash::make('password'),
                'role' => 'calon_anggota',
            ]);

            $isLolos = ($cad['status'] ?? 'lolos') === 'lolos';
            ProfilCalonAnggota::create([
                'user_id' => $user->id,
                'nim' => $cad['nim'],
                'prodi' => $cad['prodi'],
                'angkatan' => $cad['angkatan'],
                'no_hp' => $cad['no_hp'],
                'alamat' => $cad['alamat'],
                'pilihan_divisi_awal' => $cad['pilihan'],
                'seleksi_administrasi' => $isLolos ? 'lolos' : 'tidak_lolos',
                'tes_tulis_wawancara' => $isLolos ? 'lolos' : 'tidak_lolos',
                'cakruma' => $isLolos ? 'lolos' : 'tidak_lolos',
                'keputusan_final' => $cad['keputusan'],
            ]);

            // Buat berkas default
            $jenisBerkasList = [
                'Curriculum Vitae (CV)' => 'cv_pendaftar.pdf',
                'Pas Foto 3x4' => 'pas_foto_formal.pdf',
                'Esai Alasan Memilih Amanat' => 'esai_motivasi.pdf',
                'Karya Pribadi (Artikel/Opini/Sastra/Jurnalistik)' => 'portofolio_karya.pdf',
            ];
            foreach ($jenisBerkasList as $jenis => $filename) {
                Berkas::create([
                    'user_id' => $user->id,
                    'jenis_berkas' => $jenis,
                    'nama_file' => $filename,
                    'file_path' => 'berkas/sample/' . $filename,
                    'mime_type' => 'application/pdf',
                    'ukuran_file' => 1024 * rand(300, 1500),
                    'status' => 'diverifikasi',
                    'catatan' => 'Berkas valid dan sesuai standar persyaratan SKM Amanat.',
                ]);
            }

            // Simpan nilai wawancara (K4, K5, K7)
            foreach ($cad['nilai_wawancara'] as $kode => $skor) {
                NilaiEvaluasi::create([
                    'user_id' => $user->id,
                    'kriteria_id' => $kriteriaMap[$kode],
                    'nilai_aktual' => $skor,
                    'input_oleh' => $admin->id,
                ]);
            }

            $userInstances[] = [
                'user' => $user,
                'data' => $cad,
            ];
        }

        // 6. Agenda Kegiatan (5 Kegiatan Resmi)
        $kegiatan1 = Kegiatan::create([
            'jenis' => 'Seleksi & Wawancara',
            'nama' => 'Seleksi Tes Tulis dan Wawancara',
            'tempat' => 'Ruang Teater Kampus 3 UIN Walisongo',
            'deskripsi' => 'Pelaksanaan ujian tertulis kepenulisan, analisis isu jurnalistik, serta wawancara pendalaman wawasan sosial, public speaking, dan karakteristik psikotes.',
            'tanggal_waktu' => Carbon::now()->subDays(14)->setTime(8, 30),
        ]);

        $kegiatan2 = Kegiatan::create([
            'jenis' => 'Praktik Lapangan',
            'nama' => 'Hunting Feature Kota Lama',
            'tempat' => 'Kawasan Cagar Budaya Kota Lama Semarang',
            'deskripsi' => 'Praktik liputan lapangan langsung: wawancara narasumber human interest, observasi peristiwa, dan pengambilan foto bercerita (photo story).',
            'tanggal_waktu' => Carbon::now()->subDays(10)->setTime(8, 0),
        ]);

        $kegiatan3 = Kegiatan::create([
            'jenis' => 'Pelatihan Magang',
            'nama' => 'Pelatihan Pra Workshop 1',
            'tempat' => 'Gedung PKM Lantai 2 SKM Amanat',
            'deskripsi' => 'Pembekalan materi dasar kepenulisan berita lempang (straight news), teknik reportase, kaidah kode etik jurnalistik mahasiswa, dan investigasi isu.',
            'tanggal_waktu' => Carbon::now()->subDays(7)->setTime(13, 30),
        ]);

        $kegiatan4 = Kegiatan::create([
            'jenis' => 'Pelatihan Magang',
            'nama' => 'Pelatihan Pra Workshop 2',
            'tempat' => 'Gedung PKM Lantai 2 SKM Amanat',
            'deskripsi' => 'Pembekalan materi desain visual feed media, infografis berita, fotografi jurnalistik, serta manajemen platform media digital.',
            'tanggal_waktu' => Carbon::now()->subDays(3)->setTime(13, 30),
        ]);

        $kegiatan5 = Kegiatan::create([
            'jenis' => 'Workshop & Evaluasi',
            'nama' => 'Workshop Amanat 2026',
            'tempat' => 'Auditorium Utama Kampus 3 UIN Walisongo',
            'deskripsi' => 'Puncak kegiatan magang jurnalistik SKM Amanat bersama jurnalis praktisi profesional, kurasi karya liputan, dan pengukuhan magang spesialis.',
            'tanggal_waktu' => Carbon::now()->subDay()->setTime(8, 0),
        ]);

        $kegiatansList = [$kegiatan1, $kegiatan2, $kegiatan3, $kegiatan4, $kegiatan5];

        // Buat data presensi untuk setiap kegiatan
        foreach ($userInstances as $item) {
            $u = $item['user'];
            $hadirArr = $item['data']['presensi_hadir'];

            foreach ($kegiatansList as $idx => $keg) {
                $isHadir = $hadirArr[$idx] ?? true;
                if ($isHadir) {
                    Presensi::create([
                        'user_id' => $u->id,
                        'kegiatan_id' => $keg->id,
                        'waktu_hadir' => (clone $keg->tanggal_waktu)->subMinutes(rand(5, 20)),
                        'status' => 'Hadir',
                        'keterangan' => 'Hadir tepat waktu dan aktif berpartisipasi dalam agenda kegiatan.',
                    ]);
                } else {
                    Presensi::create([
                        'user_id' => $u->id,
                        'kegiatan_id' => $keg->id,
                        'waktu_hadir' => null,
                        'status' => 'Izin',
                        'keterangan' => 'Izin tidak dapat hadir karena agenda akademik mendesak / sakit terkonfirmasi.',
                    ]);
                }
            }
        }

        // 7. Penugasan Sesuai Indikator (K1, K2, K3)
        $tugas1 = Penugasan::create([
            'kriteria_kode' => 'K1',
            'jenis' => 'Tugas Kepenulisan (K1)',
            'judul' => 'Tugas Menulis Berita Straight News',
            'deskripsi' => 'Buatlah berita lempang (straight news) 300-500 kata mengenai dinamika perkuliahan/kampus. Penilaian berdasarkan struktur piramida terbalik, unsur 5W+1H, fakta akurat, bahasa jurnalistik baku, dan kejelasan informasi.',
            'deadline' => Carbon::now()->subDays(4)->setTime(23, 59),
            'dibuat_oleh' => $admin->id,
        ]);

        $tugas2 = Penugasan::create([
            'kriteria_kode' => 'K2',
            'jenis' => 'Tugas Analisis Isu (K2)',
            'judul' => 'Tugas Analisis Isu Jurnalistik',
            'deskripsi' => 'Lakukan analisis isu problematika publik atau kebijakan kampus. Penilaian mencakup identifikasi akar masalah, relevansi topik, pemetaan dampak sosial, dan ketajaman sudut pandang liputan.',
            'deadline' => Carbon::now()->subDays(3)->setTime(23, 59),
            'dibuat_oleh' => $admin->id,
        ]);

        $tugas3 = Penugasan::create([
            'kriteria_kode' => 'K3',
            'jenis' => 'Tugas Kreativitas (K3)',
            'judul' => 'Tugas Desain Grafis & Fotografi Visual',
            'deskripsi' => 'Rancang feed infografis Instagram atau ambil 2 foto jurnalistik bercerita (photo story). Penilaian berdasarkan orisinalitas ide, komposisi visual, estetika tata letak, dan kesesuaian konsep.',
            'deadline' => Carbon::now()->subDays(1)->setTime(23, 59),
            'dibuat_oleh' => $admin->id,
        ]);

        $tugasMap = [
            'K1' => $tugas1,
            'K2' => $tugas2,
            'K3' => $tugas3,
        ];

        // Pengumpulan Tugas & Penilaian untuk setiap calon anggota
        foreach ($userInstances as $item) {
            $u = $item['user'];
            $nilaiTugas = $item['data']['nilai_tugas'];

            foreach ($nilaiTugas as $kode => $score) {
                $penugasanObj = $tugasMap[$kode];
                PengumpulanTugas::create([
                    'penugasan_id' => $penugasanObj->id,
                    'user_id' => $u->id,
                    'nama_file' => $kode . '_' . str_replace(' ', '_', $u->name) . '.pdf',
                    'file_path' => 'penugasan/sample/' . strtolower($kode) . '_sample.pdf',
                    'mime_type' => 'application/pdf',
                    'ukuran_file' => 1024 * rand(400, 1200),
                    'status' => 'dinilai',
                    'nilai' => $score,
                    'feedback' => 'Hasil penugasan sangat baik dan telah memenuhi parameter indikator ' . $kode . '.',
                    'submitted_at' => Carbon::now()->subDays(rand(2, 5))->setTime(rand(14, 21), rand(10, 50)),
                ]);
            }
        }

        // 8. Pengumuman
        Pengumuman::create([
            'judul' => 'Hasil Seleksi Administrasi Calon Kru Magang 2026',
            'target_audience' => 'semua',
            'isi' => "Assalamu'alaikum Wr. Wb.\n\nSelamat kepada seluruh Calon Kru Magang SKM Amanat periode 2026 yang telah dinyatakan lolos verifikasi administrasi dan kelengkapan dokumen. Tahapan berikutnya adalah Pelatihan Kepenulisan dan Tes Tertulis yang akan diselenggarakan sesuai jadwal tertera di agenda kegiatan.\n\nHarap mempersiapkan diri dengan baik dan senantiasa memantau menu Penugasan serta Presensi di portal ini.\n\nWassalamu'alaikum Wr. Wb.",
            'penulis' => 'HRD SKM Amanat',
            'dibuat_oleh' => $admin->id,
        ]);

        Pengumuman::create([
            'judul' => 'Persiapan Tes Tertulis & Wawancara Eksklusif',
            'target_audience' => 'calon_anggota',
            'isi' => "Diberitahukan kepada seluruh Calon Anggota bahwa sesi tes wawancara akan fokus pada penggalian wawasan sosial, public speaking, serta psikotes karakteristik. Harap membawa alat tulis dan hadir 15 menit sebelum jadwal sesi dimulai.",
            'penulis' => 'BPH SKM Amanat',
            'dibuat_oleh' => $admin->id,
        ]);

        Pengumuman::create([
            'judul' => 'Workshop Fotografi & Tata Visual Media Digital',
            'target_audience' => 'semua',
            'isi' => "Workshop spesialisasi visual akan dilaksanakan akhir pekan ini dengan narasumber praktisi fotografer media nasional. Seluruh kru magang diwajibkan mengikutinya sebagai bekal penugasan liputan lapangan.",
            'penulis' => 'Redaktur Foto & Desain',
            'dibuat_oleh' => $admin->id,
        ]);

        // 9. Jalankan Sinkronisasi Otomatis Seluruh Nilai Evaluasi (K1, K2, K3, K6: 60% Tugas + 40% Absensi)
        $evalService = new EvaluasiNilaiService();
        $evalService->syncAllScores($admin->id);

        // 10. Jalankan Kalkulasi Profile Matching Awal
        $pmService = new ProfileMatchingService();
        $pmService->calculateAndRankAll();
    }
}

