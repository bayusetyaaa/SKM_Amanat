<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\ProfilCalonAnggota;
use App\Models\ProfilTarget;
use App\Models\User;
use App\Services\ProfileMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SkmAmanatTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_matching_gap_and_score_calculation(): void
    {
        // 1. Create Divisi & Kriteria
        $divisiRedaksi = Divisi::create(['nama' => 'Redaksi']);
        $divisiKonten = Divisi::create(['nama' => 'Konten']);

        $k1 = Kriteria::create(['kode' => 'K1', 'nama' => 'Kepenulisan']);
        $k2 = Kriteria::create(['kode' => 'K2', 'nama' => 'Kepekaan Isu']);

        // Redaksi: K1 (target 90, core), K2 (target 80, secondary)
        ProfilTarget::create([
            'divisi_id' => $divisiRedaksi->id,
            'kriteria_id' => $k1->id,
            'nilai_target' => 90,
            'faktor' => 'core',
        ]);
        ProfilTarget::create([
            'divisi_id' => $divisiRedaksi->id,
            'kriteria_id' => $k2->id,
            'nilai_target' => 80,
            'faktor' => 'secondary',
        ]);

        // User aktual: K1 = 85 (gap = -5 -> bobot = 95), K2 = 80 (gap = 0 -> bobot = 100)
        $user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'password' => Hash::make('password'),
            'role' => 'calon_anggota',
        ]);

        NilaiEvaluasi::create(['user_id' => $user->id, 'kriteria_id' => $k1->id, 'nilai_aktual' => 85]);
        NilaiEvaluasi::create(['user_id' => $user->id, 'kriteria_id' => $k2->id, 'nilai_aktual' => 80]);

        $pmService = new ProfileMatchingService();
        $calc = $pmService->calculateForUser($user->id);

        $this->assertArrayHasKey('results', $calc);
        $redaksiResult = $calc['results'][$divisiRedaksi->id];

        // NCF = 95, NSF = 100
        // Total = (0.6 * 95) + (0.4 * 100) = 57 + 40 = 97.0
        $this->assertEquals(95.0, $redaksiResult['ncf']);
        $this->assertEquals(100.0, $redaksiResult['nsf']);
        $this->assertEquals(97.0, $redaksiResult['nilai_total']);
    }

    public function test_login_and_role_redirection(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_member_cannot_access_admin_dashboard(): void
    {
        $member = User::create([
            'name' => 'Member Test',
            'email' => 'member@test.com',
            'password' => Hash::make('secret123'),
            'role' => 'calon_anggota',
        ]);

        $response = $this->actingAs($member)->get('/admin/dashboard');
        $response->assertRedirect('/member/dashboard');
    }
}
