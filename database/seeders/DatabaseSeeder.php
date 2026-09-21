<?php

namespace Database\Seeders;

use App\Models\Berkas;
use App\Models\Divisi;
use App\Models\HasilProfileMatching;
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
use App\Services\ProfileMatchingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // 0. Bersihkan seluruh tabel terkait data calon anggota & admin secara aman
        HasilProfileMatching::query()->delete();
        NilaiEvaluasi::query()->delete();
        PengumpulanTugas::query()->delete();
        Presensi::query()->delete();
        Berkas::query()->delete();
        ProfilCalonAnggota::query()->delete();
        Pengumuman::query()->delete();
        Penugasan::query()->delete();
        Kegiatan::query()->delete();
        ProfilTarget::query()->delete();
        Kriteria::query()->delete();
        Divisi::query()->delete();
        User::query()->delete();

        // 1. Akun Admin / Pengurus (4 Akun Sesuai Permintaan)
        $adminBayu = User::create([
            'name' => 'Bayu Setyawan',
            'email' => 'bayusetya123456789@gmail.com',
            'password' => Hash::make('bayu.793'),
            'role' => 'admin',
        ]);

        $adminAlfarizy = User::create([
            'name' => 'Moehammad Alfarizy',
            'email' => 'moehammad.alfarizy@amanat.id',
            'password' => Hash::make('alfarizy2026!'),
            'role' => 'admin',
        ]);

        $adminMeyra = User::create([
            'name' => 'Meyra Karunia Putri',
            'email' => 'meyra.karuniaputri@amanat.id',
            'password' => Hash::make('meyra2026!'),
            'role' => 'admin',
        ]);

        $adminLutfi = User::create([
            'name' => 'Lutfi Ardiansyah',
            'email' => 'lutfi.ardiansyah@amanat.id',
            'password' => Hash::make('lutfi2026!'),
            'role' => 'admin',
        ]);

        $primaryAdminId = $adminBayu->id;

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

        // 5. Penugasan Resmi (K1, K2, K3)
        $tugas1 = Penugasan::create([
            'kriteria_kode' => 'K1',
            'jenis' => 'Tugas Kepenulisan (K1)',
            'judul' => 'Tugas Menulis Berita Straight News',
            'deskripsi' => 'Buatlah berita lempang (straight news) 300-500 kata mengenai dinamika perkuliahan/kampus. Penilaian berdasarkan struktur piramida terbalik, unsur 5W+1H, fakta akurat, bahasa jurnalistik baku, dan kejelasan informasi.',
            'deadline' => Carbon::now()->subDays(4)->setTime(23, 59),
            'dibuat_oleh' => $primaryAdminId,
        ]);

        $tugas2 = Penugasan::create([
            'kriteria_kode' => 'K2',
            'jenis' => 'Tugas Analisis Isu (K2)',
            'judul' => 'Tugas Analisis Isu Jurnalistik',
            'deskripsi' => 'Lakukan analisis isu problematika publik atau kebijakan kampus. Penilaian mencakup identifikasi akar masalah, relevansi topik, pemetaan dampak sosial, dan ketajaman sudut pandang liputan.',
            'deadline' => Carbon::now()->subDays(3)->setTime(23, 59),
            'dibuat_oleh' => $primaryAdminId,
        ]);

        $tugas3 = Penugasan::create([
            'kriteria_kode' => 'K3',
            'jenis' => 'Tugas Kreativitas (K3)',
            'judul' => 'Tugas Desain Grafis & Fotografi Visual',
            'deskripsi' => 'Rancang feed infografis Instagram atau ambil 2 foto jurnalistik bercerita (photo story). Penilaian berdasarkan orisinalitas ide, komposisi visual, estetika tata letak, dan kesesuaian konsep.',
            'deadline' => Carbon::now()->subDays(1)->setTime(23, 59),
            'dibuat_oleh' => $primaryAdminId,
        ]);

        $tugasMap = [
            'K1' => $tugas1,
            'K2' => $tugas2,
            'K3' => $tugas3,
        ];

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

        // 7. Data 32 Calon Anggota & Nilai 7 Kriteria Sesuai Tabel
        $calonAnggotaTable = [
            [
                'no' => 1,
                'name' => 'Anisa Atun Maryam',
                'email' => 'anisa.atunmaryam@gmail.com',
                'prodi' => 'Psikologi',
                'scores' => ['K1' => 86, 'K2' => 84, 'K3' => 82, 'K4' => 85, 'K5' => 78, 'K6' => 88, 'K7' => 87],
            ],
            [
                'no' => 2,
                'name' => 'Friciliya Lutfiah Z.L.',
                'email' => 'friciliya.lutfiah@gmail.com',
                'prodi' => 'Ilmu Politik',
                'scores' => ['K1' => 80, 'K2' => 85, 'K3' => 78, 'K4' => 86, 'K5' => 76, 'K6' => 82, 'K7' => 81],
            ],
            [
                'no' => 3,
                'name' => 'Ahmad Dawud K.',
                'email' => 'ahmad.dawudk@gmail.com',
                'prodi' => 'Ilmu Hukum',
                'scores' => ['K1' => 78, 'K2' => 82, 'K3' => 76, 'K4' => 84, 'K5' => 75, 'K6' => 80, 'K7' => 79],
            ],
            [
                'no' => 4,
                'name' => 'Muhammad Wildan S.',
                'email' => 'm.wildans@gmail.com',
                'prodi' => 'Ilmu Hukum',
                'scores' => ['K1' => 79, 'K2' => 83, 'K3' => 77, 'K4' => 84, 'K5' => 76, 'K6' => 81, 'K7' => 80],
            ],
            [
                'no' => 5,
                'name' => 'Evelyn Atha Nasywa',
                'email' => 'evelyn.athanasywa@gmail.com',
                'prodi' => 'Psikologi',
                'scores' => ['K1' => 85, 'K2' => 84, 'K3' => 86, 'K4' => 83, 'K5' => 80, 'K6' => 87, 'K7' => 88],
            ],
            [
                'no' => 6,
                'name' => 'Firman Ade Rizqi P.',
                'email' => 'firman.aderizqi@gmail.com',
                'prodi' => 'Pendidikan Bahasa Inggris',
                'scores' => ['K1' => 82, 'K2' => 78, 'K3' => 80, 'K4' => 79, 'K5' => 83, 'K6' => 81, 'K7' => 80],
            ],
            [
                'no' => 7,
                'name' => 'Dina Uzma Azizah',
                'email' => 'dina.uzmaazizah@gmail.com',
                'prodi' => 'Ilmu Politik',
                'scores' => ['K1' => 81, 'K2' => 86, 'K3' => 79, 'K4' => 85, 'K5' => 77, 'K6' => 82, 'K7' => 83],
            ],
            [
                'no' => 8,
                'name' => 'David Setiawan',
                'email' => 'david.setiawan99@gmail.com',
                'prodi' => 'Hukum Pidana Islam',
                'scores' => ['K1' => 80, 'K2' => 82, 'K3' => 78, 'K4' => 83, 'K5' => 79, 'K6' => 84, 'K7' => 82],
            ],
            [
                'no' => 9,
                'name' => 'Moh. Asrori Abdul G.',
                'email' => 'moh.asroriabdul@gmail.com',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'scores' => ['K1' => 84, 'K2' => 85, 'K3' => 83, 'K4' => 84, 'K5' => 86, 'K6' => 82, 'K7' => 85],
            ],
            [
                'no' => 10,
                'name' => 'Fatih Rizqan',
                'email' => 'fatih.rizqan@gmail.com',
                'prodi' => 'Ilmu Al-Qur\'an dan Tafsir',
                'scores' => ['K1' => 79, 'K2' => 81, 'K3' => 77, 'K4' => 82, 'K5' => 76, 'K6' => 83, 'K7' => 80],
            ],
            [
                'no' => 11,
                'name' => 'Auliya Najwa H.',
                'email' => 'auliya.najwah@gmail.com',
                'prodi' => 'Gizi',
                'scores' => ['K1' => 77, 'K2' => 78, 'K3' => 80, 'K4' => 79, 'K5' => 75, 'K6' => 82, 'K7' => 81],
            ],
            [
                'no' => 12,
                'name' => 'Kaisa Ayyu Fida',
                'email' => 'kaisa.ayyufida@gmail.com',
                'prodi' => 'Ilmu Politik',
                'scores' => ['K1' => 80, 'K2' => 84, 'K3' => 81, 'K4' => 85, 'K5' => 78, 'K6' => 81, 'K7' => 82],
            ],
            [
                'no' => 13,
                'name' => 'Safinatul Mahsunah',
                'email' => 'safinatul.mahsunah@gmail.com',
                'prodi' => 'Ilmu Al-Qur\'an dan Tafsir',
                'scores' => ['K1' => 83, 'K2' => 82, 'K3' => 79, 'K4' => 84, 'K5' => 77, 'K6' => 86, 'K7' => 85],
            ],
            [
                'no' => 14,
                'name' => 'Oktavia Suci R.',
                'email' => 'oktavia.sucir@gmail.com',
                'prodi' => 'Manajemen',
                'scores' => ['K1' => 78, 'K2' => 79, 'K3' => 82, 'K4' => 80, 'K5' => 81, 'K6' => 83, 'K7' => 82],
            ],
            [
                'no' => 15,
                'name' => 'Farah Cahyani Putri',
                'email' => 'farah.cahyaniputri@gmail.com',
                'prodi' => 'Teknik Lingkungan',
                'scores' => ['K1' => 76, 'K2' => 80, 'K3' => 81, 'K4' => 79, 'K5' => 75, 'K6' => 82, 'K7' => 80],
            ],
            [
                'no' => 16,
                'name' => 'Muhammad Farrel A.S.',
                'email' => 'farrel.muhammadas@gmail.com',
                'prodi' => 'Teknologi Informasi',
                'scores' => ['K1' => 80, 'K2' => 78, 'K3' => 85, 'K4' => 77, 'K5' => 76, 'K6' => 83, 'K7' => 81],
            ],
            [
                'no' => 17,
                'name' => 'Putri Natasya I.',
                'email' => 'putri.natasyai@gmail.com',
                'prodi' => 'PAI',
                'scores' => ['K1' => 81, 'K2' => 80, 'K3' => 78, 'K4' => 82, 'K5' => 77, 'K6' => 84, 'K7' => 83],
            ],
            [
                'no' => 18,
                'name' => 'Adhilni Mizaniyatul I.',
                'email' => 'adhilni.mizaniyatul@gmail.com',
                'prodi' => 'Pendidikan Agama Islam',
                'scores' => ['K1' => 82, 'K2' => 81, 'K3' => 79, 'K4' => 83, 'K5' => 78, 'K6' => 85, 'K7' => 84],
            ],
            [
                'no' => 19,
                'name' => 'Fa\'iq Muhammad S.',
                'email' => 'faiq.muhammads@gmail.com',
                'prodi' => 'Teknologi Informasi',
                'scores' => ['K1' => 79, 'K2' => 78, 'K3' => 84, 'K4' => 77, 'K5' => 76, 'K6' => 82, 'K7' => 80],
            ],
            [
                'no' => 20,
                'name' => 'Ahmad Rafiuddin Izza',
                'email' => 'ahmad.rafiuddinizza@gmail.com',
                'prodi' => 'Psikologi',
                'scores' => ['K1' => 84, 'K2' => 83, 'K3' => 82, 'K4' => 85, 'K5' => 79, 'K6' => 86, 'K7' => 87],
            ],
            [
                'no' => 21,
                'name' => 'Kholifatim Muallimah',
                'email' => 'kholifatim.muallimah@gmail.com',
                'prodi' => 'Pendidikan Bahasa Inggris',
                'scores' => ['K1' => 83, 'K2' => 79, 'K3' => 80, 'K4' => 80, 'K5' => 84, 'K6' => 82, 'K7' => 81],
            ],
            [
                'no' => 22,
                'name' => 'Defina Indriani',
                'email' => 'defina.indriani@gmail.com',
                'prodi' => 'Teknologi Informasi',
                'scores' => ['K1' => 78, 'K2' => 77, 'K3' => 83, 'K4' => 76, 'K5' => 75, 'K6' => 81, 'K7' => 80],
            ],
            [
                'no' => 23,
                'name' => 'Nugrahenning Catur W.',
                'email' => 'nugrahenning.caturw@gmail.com',
                'prodi' => 'Akuntansi Syariah',
                'scores' => ['K1' => 80, 'K2' => 79, 'K3' => 81, 'K4' => 80, 'K5' => 78, 'K6' => 85, 'K7' => 82],
            ],
            [
                'no' => 24,
                'name' => 'Rosyidah Atiqoh',
                'email' => 'rosyidah.atiqoh@gmail.com',
                'prodi' => 'Psikologi',
                'scores' => ['K1' => 82, 'K2' => 81, 'K3' => 80, 'K4' => 83, 'K5' => 77, 'K6' => 84, 'K7' => 86],
            ],
            [
                'no' => 25,
                'name' => 'Oryza Rahma Dalila',
                'email' => 'oryza.rahmadalila@gmail.com',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'scores' => ['K1' => 83, 'K2' => 84, 'K3' => 85, 'K4' => 82, 'K5' => 86, 'K6' => 81, 'K7' => 84],
            ],
            [
                'no' => 26,
                'name' => 'Afrizal Anwar Zulfani',
                'email' => 'afrizal.anwarzulfani@gmail.com',
                'prodi' => 'Teknologi Informasi',
                'scores' => ['K1' => 77, 'K2' => 76, 'K3' => 82, 'K4' => 75, 'K5' => 74, 'K6' => 80, 'K7' => 79],
            ],
            [
                'no' => 27,
                'name' => 'Okta Rosi Sal\'wa',
                'email' => 'okta.rosisalwa@gmail.com',
                'prodi' => 'Teknik Lingkungan',
                'scores' => ['K1' => 76, 'K2' => 79, 'K3' => 80, 'K4' => 78, 'K5' => 75, 'K6' => 81, 'K7' => 80],
            ],
            [
                'no' => 28,
                'name' => 'Matsna Adilya R.',
                'email' => 'matsna.adilyar@gmail.com',
                'prodi' => 'Pendidikan Biologi',
                'scores' => ['K1' => 79, 'K2' => 80, 'K3' => 81, 'K4' => 82, 'K5' => 76, 'K6' => 83, 'K7' => 82],
            ],
            [
                'no' => 29,
                'name' => 'Noor Saidah',
                'email' => 'noor.saidah@gmail.com',
                'prodi' => 'Manajemen',
                'scores' => ['K1' => 78, 'K2' => 79, 'K3' => 82, 'K4' => 80, 'K5' => 79, 'K6' => 84, 'K7' => 81],
            ],
            [
                'no' => 30,
                'name' => 'Fadhil Fatin Ramadhan',
                'email' => 'fadhil.fatinramadhan@gmail.com',
                'prodi' => 'Hukum Keluarga Islam',
                'scores' => ['K1' => 80, 'K2' => 82, 'K3' => 77, 'K4' => 83, 'K5' => 76, 'K6' => 82, 'K7' => 80],
            ],
            [
                'no' => 31,
                'name' => 'Muhammad Maulana',
                'email' => 'muhammad.maulana.arch@gmail.com',
                'prodi' => 'Arsitektur',
                'scores' => ['K1' => 77, 'K2' => 78, 'K3' => 84, 'K4' => 79, 'K5' => 75, 'K6' => 81, 'K7' => 80],
            ],
            [
                'no' => 32,
                'name' => 'Nur Rofiqoh Nabila',
                'email' => 'nur.rofiqohnabila@gmail.com',
                'prodi' => 'Teknologi Informasi',
                'scores' => ['K1' => 79, 'K2' => 78, 'K3' => 83, 'K4' => 77, 'K5' => 76, 'K6' => 82, 'K7' => 81],
            ],
        ];

        $cities = ['Ngaliyan, Kota Semarang', 'Tembalang, Kota Semarang', 'Gunungpati, Kota Semarang', 'Banyumanik, Kota Semarang', 'Pedurungan, Kota Semarang', 'Mijen, Kota Semarang', 'Tugu, Kota Semarang'];

        foreach ($calonAnggotaTable as $idx => $row) {
            $user = User::create([
                'name' => $row['name'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'role' => 'calon_anggota',
            ]);

            $nim = '2408096' . str_pad($row['no'], 3, '0', STR_PAD_LEFT);
            $pilihan = ($row['scores']['K1'] >= $row['scores']['K3']) ? 'Redaksi' : 'Konten';

            ProfilCalonAnggota::create([
                'user_id' => $user->id,
                'nim' => $nim,
                'prodi' => $row['prodi'],
                'angkatan' => '2024',
                'no_hp' => '08' . rand(1111111111, 9999999999),
                'alamat' => $cities[$idx % count($cities)],
                'pilihan_divisi_awal' => $pilihan,
                'seleksi_administrasi' => 'lolos',
                'tes_tulis_wawancara' => 'lolos',
                'cakruma' => 'lolos',
                'keputusan_final' => null, // akan ditentukan via rekomendasi PM
            ]);

            // Buat berkas terverifikasi default
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

            // Simpan seluruh 7 Nilai Evaluasi (K1 s.d. K7) langsung sesuai tabel gambar
            foreach ($row['scores'] as $kode => $skor) {
                NilaiEvaluasi::create([
                    'user_id' => $user->id,
                    'kriteria_id' => $kriteriaMap[$kode],
                    'nilai_aktual' => $skor,
                    'input_oleh' => $primaryAdminId,
                ]);
            }

            // Buat pengumpulan tugas (K1, K2, K3) dengan nilai sesuai tabel
            foreach (['K1', 'K2', 'K3'] as $kode) {
                $penugasanObj = $tugasMap[$kode];
                PengumpulanTugas::create([
                    'penugasan_id' => $penugasanObj->id,
                    'user_id' => $user->id,
                    'nama_file' => $kode . '_' . str_replace([' ', '\'', '.'], '_', $user->name) . '.pdf',
                    'file_path' => 'penugasan/sample/' . strtolower($kode) . '_sample.pdf',
                    'mime_type' => 'application/pdf',
                    'ukuran_file' => 1024 * rand(400, 1200),
                    'status' => 'dinilai',
                    'nilai' => $row['scores'][$kode],
                    'feedback' => 'Hasil penugasan sangat baik dan telah memenuhi parameter indikator ' . $kode . '.',
                    'submitted_at' => Carbon::now()->subDays(rand(2, 5))->setTime(rand(14, 21), rand(10, 50)),
                ]);
            }

            // Buat presensi untuk 5 kegiatan (default Hadir)
            foreach ($kegiatansList as $keg) {
                Presensi::create([
                    'user_id' => $user->id,
                    'kegiatan_id' => $keg->id,
                    'waktu_hadir' => (clone $keg->tanggal_waktu)->subMinutes(rand(5, 20)),
                    'status' => 'Hadir',
                    'keterangan' => 'Hadir tepat waktu dan aktif berpartisipasi dalam agenda kegiatan.',
                ]);
            }
        }

        // 8. Pengumuman
        Pengumuman::create([
            'judul' => 'Hasil Seleksi Administrasi Calon Kru Magang 2026',
            'target_audience' => 'semua',
            'isi' => "Assalamu'alaikum Wr. Wb.\n\nSelamat kepada seluruh Calon Kru Magang SKM Amanat periode 2026 yang telah dinyatakan lolos verifikasi administrasi dan kelengkapan dokumen. Tahapan berikutnya adalah Pelatihan Kepenulisan dan Tes Tertulis yang akan diselenggarakan sesuai jadwal tertera di agenda kegiatan.\n\nHarap mempersiapkan diri dengan baik dan senantiasa memantau menu Penugasan serta Presensi di portal ini.\n\nWassalamu'alaikum Wr. Wb.",
            'penulis' => 'HRD SKM Amanat',
            'dibuat_oleh' => $primaryAdminId,
        ]);

        Pengumuman::create([
            'judul' => 'Persiapan Tes Tertulis & Wawancara Eksklusif',
            'target_audience' => 'calon_anggota',
            'isi' => "Diberitahukan kepada seluruh Calon Anggota bahwa sesi tes wawancara akan fokus pada penggalian wawasan sosial, public speaking, serta psikotes karakteristik. Harap membawa alat tulis dan hadir 15 menit sebelum jadwal sesi dimulai.",
            'penulis' => 'BPH SKM Amanat',
            'dibuat_oleh' => $primaryAdminId,
        ]);

        Pengumuman::create([
            'judul' => 'Workshop Fotografi & Tata Visual Media Digital',
            'target_audience' => 'semua',
            'isi' => "Workshop spesialisasi visual akan dilaksanakan akhir pekan ini dengan narasumber praktisi fotografer media nasional. Seluruh kru magang diwajibkan mengikutinya sebagai bekal penugasan liputan lapangan.",
            'penulis' => 'Redaktur Foto & Desain',
            'dibuat_oleh' => $primaryAdminId,
        ]);

        // 9. Jalankan Kalkulasi Profile Matching & Ranking untuk seluruh 32 Calon Anggota
        $pmService = new ProfileMatchingService();
        $pmService->calculateAndRankAll();
    }
}
