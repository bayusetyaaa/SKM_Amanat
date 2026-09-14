<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\Penugasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenugasanController extends Controller
{
    private function checkIsNotTidakLolos($user): bool
    {
        $user->loadMissing(['profil', 'berkas']);
        $admStatus = $user->profil->seleksi_administrasi ?? null;
        $tesStatus = $user->profil->tes_tulis_wawancara ?? null;
        $cakStatus = $user->profil->cakruma ?? null;
        $hasRejected = $user->berkas->some(fn($b) => $b->status === 'ditolak');

        return !($hasRejected || $admStatus === 'tidak_lolos' || $tesStatus === 'tidak_lolos' || $cakStatus === 'tidak_lolos');
    }

    public function index()
    {
        $user = Auth::user();

        if (!$this->checkIsNotTidakLolos($user)) {
            return redirect()->route('member.dashboard')
                ->with('error', 'Akses Ditolak: Menu Penugasan tidak tersedia untuk akun yang tidak lolos seleksi.');
        }

        // Ambil semua penugasan beserta data pengumpulan user yang sedang login
        $penugasans = Penugasan::with(['pengumpulanUser' => function ($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->orderByDesc('deadline')->get();

        // Pisahkan tugas aktif dan riwayat tugas
        $tugasAktif = $penugasans->filter(function ($tugas) {
            $pengumpulan = $tugas->pengumpulanUser->first();
            return !$pengumpulan || $pengumpulan->status !== 'dinilai';
        });

        $riwayatTugas = $penugasans->filter(function ($tugas) {
            $pengumpulan = $tugas->pengumpulanUser->first();
            return $pengumpulan && $pengumpulan->status === 'dinilai';
        });

        return view('user.penugasan', compact('tugasAktif', 'riwayatTugas'));
    }

    public function uploadTugas(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $user = Auth::user();

        $request->validate([
            'file_tugas' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file_tugas.required' => 'Silakan pilih file PDF hasil penugasan Anda.',
            'file_tugas.mimes' => 'Format file tugas wajib PDF.',
            'file_tugas.max' => 'Ukuran file tugas maksimal 5MB.',
        ]);

        $file = $request->file('file_tugas');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();
        $fileData = base64_encode($file->get());

        // Cek apakah pengumpulan lewat deadline
        $now = Carbon::now();
        $status = $now->isAfter($penugasan->deadline) ? 'terlambat' : 'terkumpul';

        // Cari pengumpulan sebelumnya jika ada
        $existing = PengumpulanTugas::where('penugasan_id', $penugasan->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        PengumpulanTugas::updateOrCreate(
            [
                'penugasan_id' => $penugasan->id,
                'user_id' => $user->id,
            ],
            [
                'nama_file' => $originalName,
                'file_data' => $fileData,
                'file_path' => null,
                'mime_type' => $mimeType,
                'ukuran_file' => $size,
                'status' => $status,
                'submitted_at' => $now,
            ]
        );

        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncUserScores($user);

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        $msg = ($status === 'terlambat')
            ? 'Tugas berhasil diunggah (Catatan: Pengumpulan tercatat melewati tenggat waktu).'
            : 'Tugas berhasil diunggah tepat waktu!';

        return back()->with('success', $msg);
    }
}
