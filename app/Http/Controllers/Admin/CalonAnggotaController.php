<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\ProfilCalonAnggota;
use App\Models\User;
use Illuminate\Http\Request;

class CalonAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'calon_anggota')->with(['profil', 'berkas']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('profil', function ($qp) use ($search) {
                      $qp->where('nim', 'like', "%{$search}%")
                         ->orWhere('prodi', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'lolos') {
                $query->whereHas('profil', function ($q) {
                    $q->where('seleksi_administrasi', 'lolos')
                      ->where('tes_tulis_wawancara', 'lolos')
                      ->where('cakruma', 'lolos');
                });
            } elseif ($status === 'tidak_lolos') {
                $query->where(function ($q) {
                    $q->whereHas('profil', function ($qp) {
                        $qp->where('seleksi_administrasi', 'tidak_lolos')
                           ->orWhere('tes_tulis_wawancara', 'tidak_lolos')
                           ->orWhere('cakruma', 'tidak_lolos');
                    })->orWhereHas('berkas', function ($qb) {
                        $qb->where('status', 'ditolak');
                    });
                });
            } elseif ($status === 'menunggu_verifikasi' || $status === 'menunggu') {
                $query->whereHas('berkas', function ($q) {
                    $q->where('status', '!=', 'diverifikasi');
                })->whereDoesntHave('berkas', function ($q) {
                    $q->where('status', 'ditolak');
                })->whereHas('profil', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('seleksi_administrasi', '!=', 'tidak_lolos')->orWhereNull('seleksi_administrasi');
                    })->where(function ($sub) {
                        $sub->where('tes_tulis_wawancara', '!=', 'tidak_lolos')->orWhereNull('tes_tulis_wawancara');
                    })->where(function ($sub) {
                        $sub->where('cakruma', '!=', 'tidak_lolos')->orWhereNull('cakruma');
                    });
                });
            } elseif ($status === 'proses_seleksi') {
                $query->whereDoesntHave('berkas', function ($q) {
                    $q->where('status', '!=', 'diverifikasi');
                })->whereHas('profil', function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNull('tes_tulis_wawancara')
                            ->orWhereNull('cakruma');
                    })->where(function ($sub) {
                        $sub->where('seleksi_administrasi', '!=', 'tidak_lolos')->orWhereNull('seleksi_administrasi');
                    })->where(function ($sub) {
                        $sub->where('tes_tulis_wawancara', '!=', 'tidak_lolos')->orWhereNull('tes_tulis_wawancara');
                    })->where(function ($sub) {
                        $sub->where('cakruma', '!=', 'tidak_lolos')->orWhereNull('cakruma');
                    });
                });
            }
        }

        $calonAnggotas = $query->orderBy('name')->get();

        return view('admin.calon_anggota', compact('calonAnggotas'));
    }

    public function verifikasiBerkas(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diverifikasi,ditolak,menunggu',
            'catatan' => 'nullable|string',
        ]);

        $berkas = Berkas::findOrFail($id);
        
        // Jika status diverifikasi (valid), otomatis kosongkan catatan
        $catatan = $request->status === 'diverifikasi' ? null : $request->catatan;

        $berkas->update([
            'status' => $request->status,
            'catatan' => $catatan,
        ]);

        // Cek status seluruh berkas calon anggota ini
        $user = User::with('berkas')->findOrFail($berkas->user_id);
        $userBerkas = $user->berkas;

        $hasCv = $userBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'cv') || str_contains(strtolower($b->jenis_berkas), 'curriculum'));
        $hasEsai = $userBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'esai') || str_contains(strtolower($b->jenis_berkas), 'essay'));
        $hasFoto = $userBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'foto'));
        $hasKarya = $userBerkas->first(fn($b) => str_contains(strtolower($b->jenis_berkas), 'karya') || str_contains(strtolower($b->jenis_berkas), 'portofolio'));

        $allValid = ($hasCv && $hasCv->status === 'diverifikasi') &&
                    ($hasEsai && $hasEsai->status === 'diverifikasi') &&
                    ($hasFoto && $hasFoto->status === 'diverifikasi') &&
                    ($hasKarya && $hasKarya->status === 'diverifikasi');

        $hasDitolak = $userBerkas->contains('status', 'ditolak');

        if ($allValid) {
            ProfilCalonAnggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'seleksi_administrasi' => 'lolos',
                ]
            );
            $msgExtra = " Semua dokumen telah terverifikasi (valid), tahap 1 (Seleksi Administrasi) otomatis Lolos.";
        } elseif ($hasDitolak) {
            ProfilCalonAnggota::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'seleksi_administrasi' => 'tidak_lolos',
                    'tes_tulis_wawancara' => null,
                    'cakruma' => null,
                ]
            );
            $msgExtra = " Terdapat dokumen ditolak, tahap 1 (Seleksi Administrasi) diset Tidak Lolos (Perlu Perbaikan).";
        } else {
            $msgExtra = "";
        }

        return back()->with('success', "Status berkas '{$berkas->jenis_berkas}' berhasil diperbarui menjadi {$request->status}.{$msgExtra}");
    }

    public function updateStatusSeleksi(Request $request, $userId)
    {
        $statusAdm = $request->input('seleksi_administrasi'); // 'lolos', 'tidak_lolos', or null
        $statusTes = $request->input('tes_tulis_wawancara');  // 'lolos', 'tidak_lolos', or null
        $statusCak = $request->input('cakruma');              // 'lolos', 'tidak_lolos', or null

        // Aturan:
        // 1. Jika seleksi_administrasi !== 'lolos': tahap berikutnya otomatis null dan tidak lolos
        // 2. Jika seleksi_administrasi diubah ke lolos dari sebelumnya tidak lolos, tahap selanjutnya null
        // 3. Jika tes_tulis_wawancara !== 'lolos': cakruma otomatis null
        // 4. Calon hanya lolos apabila cakruma 'lolos'
        if ($statusAdm === 'tidak_lolos') {
            $statusTes = null;
            $statusCak = null;
            $finalText = 'Tidak Lolos Seleksi Administrasi';
        } elseif ($statusAdm === 'lolos') {
            if ($statusTes === 'tidak_lolos') {
                $statusCak = null;
                $finalText = 'Tidak Lolos Tes Tulis & Wawancara';
            } elseif ($statusTes === 'lolos') {
                if ($statusCak === 'lolos') {
                    $finalText = 'Lolos Seleksi Calon Kru Magang (Cakruma)';
                } elseif ($statusCak === 'tidak_lolos') {
                    $finalText = 'Tidak Lolos Penetapan Cakruma';
                } else {
                    $statusCak = null;
                    $finalText = 'Dalam Proses Penetapan Cakruma';
                }
            } else {
                $statusTes = null;
                $statusCak = null;
                $finalText = 'Dalam Proses Tes Tulis & Wawancara';
            }
        } else {
            $statusAdm = null;
            $statusTes = null;
            $statusCak = null;
            $finalText = 'Menunggu Seleksi Administrasi';
        }

        ProfilCalonAnggota::updateOrCreate(
            ['user_id' => $userId],
            [
                'seleksi_administrasi' => $statusAdm,
                'tes_tulis_wawancara' => $statusTes,
                'cakruma' => $statusCak,
            ]
        );

        $user = User::findOrFail($userId);
        return back()->with('success', "Keputusan tahapan seleksi untuk {$user->name} berhasil disimpan.");
    }
}
