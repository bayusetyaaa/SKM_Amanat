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

        // Rekomendasi Redaksi & Konten (HANYA dari anggota yang LOLOS seluruh tahapan seleksi)
        $countRedaksi = HasilProfileMatching::where('rekomendasi', true)
            ->whereHas('divisi', function ($q) {
                $q->where('nama', 'Redaksi');
            })
            ->whereHas('user', function ($qu) {
                $qu->where('role', 'calon_anggota')
                   ->whereHas('profil', function ($qp) {
                       $qp->where('seleksi_administrasi', 'lolos')
                          ->where('tes_tulis_wawancara', 'lolos')
                          ->where('cakruma', 'lolos');
                   })
                   ->whereDoesntHave('berkas', function ($qb) {
                       $qb->where('status', 'ditolak');
                   });
            })
            ->count();

        $countKonten = HasilProfileMatching::where('rekomendasi', true)
            ->whereHas('divisi', function ($q) {
                $q->where('nama', 'Konten');
            })
            ->whereHas('user', function ($qu) {
                $qu->where('role', 'calon_anggota')
                   ->whereHas('profil', function ($qp) {
                       $qp->where('seleksi_administrasi', 'lolos')
                          ->where('tes_tulis_wawancara', 'lolos')
                          ->where('cakruma', 'lolos');
                   })
                   ->whereDoesntHave('berkas', function ($qb) {
                       $qb->where('status', 'ditolak');
                   });
            })
            ->count();

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
