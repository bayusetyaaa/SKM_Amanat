<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\HasilProfileMatching;
use App\Models\Kegiatan;
use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendaftar = User::where('role', 'calon_anggota')->count();

        // Total Cakruma Aktif (semua anggota kecuali yang tidak lolos)
        $totalCakrumaAktif = User::where('role', 'calon_anggota')
            ->whereDoesntHave('profil', function ($q) {
                $q->where('seleksi_administrasi', 'tidak_lolos')
                  ->orWhere('tes_tulis_wawancara', 'tidak_lolos')
                  ->orWhere('cakruma', 'tidak_lolos');
            })
            ->whereDoesntHave('berkas', function ($q) {
                $q->where('status', 'ditolak');
            })
            ->count();

        // Rekomendasi Redaksi & Konten sesuai dengan Divisi Akhir anggota (Keputusan Final atau Rekomendasi PM)
        $calonLolos = User::where('role', 'calon_anggota')
            ->whereHas('profil', function ($q) {
                $q->where('seleksi_administrasi', 'lolos')
                  ->where('tes_tulis_wawancara', 'lolos')
                  ->where('cakruma', 'lolos');
            })
            ->whereDoesntHave('berkas', function ($q) {
                $q->where('status', 'ditolak');
            })
            ->with(['profil', 'hasilProfileMatching.divisi'])
            ->get();

        $countRedaksi = 0;
        $countKonten = 0;

        foreach ($calonLolos as $u) {
            $recPm = $u->hasilProfileMatching->firstWhere('rekomendasi', true)?->divisi?->nama;
            $divisiAkhir = in_array($u->profil->keputusan_final ?? '', ['Redaksi', 'Konten'])
                ? $u->profil->keputusan_final
                : $recPm;

            if ($divisiAkhir === 'Redaksi') {
                $countRedaksi++;
            } elseif ($divisiAkhir === 'Konten') {
                $countKonten++;
            }
        }


        $cakrumaTerbaru = User::where('role', 'calon_anggota')
            ->with(['profil', 'berkas', 'hasilProfileMatching.divisi'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPendaftar',
            'totalCakrumaAktif',
            'countRedaksi',
            'countKonten',
            'cakrumaTerbaru'
        ));
    }
}
