<?php

namespace Tests\Feature;

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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveAppTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Divisi $redaksi;
    protected Divisi $konten;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Setup Admin
        $this->admin = User::create([
            'name' => 'HRD SKM Amanat',
            'email' => 'admin@amanat.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Setup Member
        $this->member = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'calon_anggota',
        ]);

        ProfilCalonAnggota::create([
            'user_id' => $this->member->id,
            'nim' => '2108096001',
            'prodi' => 'Ilmu Komunikasi',
            'seleksi_administrasi' => 'lolos',
            'tes_tulis_wawancara' => 'lolos',
            'cakruma' => 'lolos',
            'pilihan_divisi_awal' => 'Redaksi',
        ]);

        // Setup Divisi
        $this->redaksi = Divisi::create(['nama' => 'Redaksi', 'deskripsi' => 'Divisi Kepenulisan']);
        $this->konten = Divisi::create(['nama' => 'Konten', 'deskripsi' => 'Divisi Visual']);

        // Setup 7 Kriteria
        $kriterias = [
            ['kode' => 'K1', 'nama' => 'Kepenulisan', 'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif'],
            ['kode' => 'K2', 'nama' => 'Kepekaan Isu', 'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif'],
            ['kode' => 'K3', 'nama' => 'Kreativitas', 'aspek' => 'Aspek 1. Kompetensi Teknis & Kreatif'],
            ['kode' => 'K4', 'nama' => 'Wawasan Sosial', 'aspek' => 'Aspek 2. Komunikasi & Sosial'],
            ['kode' => 'K5', 'nama' => 'Public Speaking', 'aspek' => 'Aspek 2. Komunikasi & Sosial'],
            ['kode' => 'K6', 'nama' => 'Kedisiplinan', 'aspek' => 'Aspek 3. Sikap & Perilaku'],
            ['kode' => 'K7', 'nama' => 'Karakteristik', 'aspek' => 'Aspek 3. Sikap & Perilaku'],
        ];

        foreach ($kriterias as $k) {
            $crit = Kriteria::create($k);
            ProfilTarget::create([
                'divisi_id' => $this->redaksi->id,
                'kriteria_id' => $crit->id,
                'nilai_target' => 85,
                'faktor' => ($crit->kode === 'K1' || $crit->kode === 'K2' || $crit->kode === 'K4') ? 'core' : 'secondary',
            ]);
            ProfilTarget::create([
                'divisi_id' => $this->konten->id,
                'kriteria_id' => $crit->id,
                'nilai_target' => 80,
                'faktor' => ($crit->kode === 'K3' || $crit->kode === 'K2' || $crit->kode === 'K5') ? 'core' : 'secondary',
            ]);
        }
    }

    public function test_all_member_views_render_successfully(): void
    {
        $this->actingAs($this->member);

        $this->get(route('member.dashboard'))->assertStatus(200)->assertSee('Andi Pratama');
        
        // Test with penugasan present
        $tugas = Penugasan::create([
            'judul' => 'Liputan Berita Kampus',
            'jenis' => 'Kepenulisan',
            'deskripsi' => 'Buat berita minimal 300 kata',
            'deadline' => Carbon::now()->addDays(2),
            'dibuat_oleh' => $this->admin->id,
        ]);

        $this->get(route('member.dashboard'))
            ->assertStatus(200)
            ->assertSee('Liputan Berita Kampus');

        $this->get(route('member.profil'))->assertStatus(200)->assertSee('Data Pribadi');
        $this->get(route('member.penugasan'))->assertStatus(200)->assertSee('Daftar Tugas Aktif');
        $this->get(route('member.presensi'))->assertStatus(200)->assertSee('Presensi Hari Ini');
        $this->get(route('member.magang-spesialis'))->assertStatus(200)->assertSee('Pilihan Divisi Magang Spesialis');
        $this->get(route('member.pengumuman'))->assertStatus(200)->assertSee('Pengumuman');
        $this->get(route('member.info-pendaftaran'))->assertStatus(200)->assertSee('Tahapan Rekrutmen Anggota');
        $this->get(route('member.hasil-rekomendasi'))->assertStatus(200);
    }

    public function test_all_admin_views_render_successfully(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.dashboard'))->assertStatus(200)->assertSee('Dashboard Pengurus');
        $this->get(route('admin.calon-anggota'))->assertStatus(200)->assertSee('Data Calon Anggota');
        $this->get(route('admin.mapping-spesialis'))->assertStatus(200)->assertSee('Data Magang Spesialis');
        $this->get(route('admin.kegiatan'))->assertStatus(200)->assertSee('Kegiatan');
        $this->get(route('admin.penugasan'))->assertStatus(200)->assertSee('Penugasan');
        $this->get(route('admin.konfigurasi-kriteria'))->assertStatus(200)->assertSee('Konfigurasi Standar Profile Matching');
        $this->get(route('admin.input-nilai'))->assertStatus(200)->assertSee('Nilai Evaluasi');
        $this->get(route('admin.hasil-rekomendasi'))->assertStatus(200)->assertSee('Hasil Perangkingan');
        $this->get(route('admin.hasil-rekomendasi.cetak'))->assertStatus(200)->assertSee('BERITA ACARA REKAPITULASI');
        $this->get(route('admin.pengumuman'))->assertStatus(200)->assertSee('Buat Pengumuman Baru');
    }

    public function test_member_can_upload_pdf_documents(): void
    {
        $this->actingAs($this->member);

        $file = UploadedFile::fake()->create('my_cv.pdf', 500, 'application/pdf');

        $response = $this->post(route('member.profil.upload'), [
            'cv' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('berkas', [
            'user_id' => $this->member->id,
            'jenis_berkas' => 'Curriculum Vitae (CV)',
            'nama_file' => 'my_cv.pdf',
        ]);
    }

    public function test_member_can_submit_assignment_pdf(): void
    {
        $this->actingAs($this->member);

        $tugas = Penugasan::create([
            'judul' => 'Menulis Berita Demo',
            'deadline' => Carbon::now()->addDays(2),
            'dibuat_oleh' => $this->admin->id,
        ]);

        $file = UploadedFile::fake()->create('straight_news.pdf', 600, 'application/pdf');

        $response = $this->post(route('member.penugasan.upload', $tugas->id), [
            'file_tugas' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pengumpulan_tugas', [
            'penugasan_id' => $tugas->id,
            'user_id' => $this->member->id,
            'status' => 'terkumpul',
        ]);
    }

    public function test_admin_can_input_scores_and_trigger_profile_matching(): void
    {
        $this->actingAs($this->admin);

        $kriterias = Kriteria::all();
        $nilaiData = [];
        foreach ($kriterias as $k) {
            $nilaiData[$this->member->id][$k->id] = 88;
        }

        $response = $this->post(route('admin.input-nilai.proses'), [
            'nilai' => $nilaiData,
        ]);

        $response->assertRedirect(route('admin.hasil-rekomendasi'));
        $this->assertDatabaseHas('hasil_profile_matching', [
            'user_id' => $this->member->id,
            'rekomendasi' => true,
        ]);
    }
}
