<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\NilaiEvaluasi;
use App\Models\PengumpulanTugas;
use App\Models\Penugasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenugasanController extends Controller
{
    public function index()
    {
        $penugasans = Penugasan::withCount('pengumpulanTugas')->orderByDesc('created_at')->paginate(10)->withQueryString();
        
        $calonAnggotas = User::where('role', 'calon_anggota')
            ->with(['profil', 'nilaiEvaluasi'])
            ->orderBy('name')
            ->get();

        $kriteriaWawancara = Kriteria::whereIn('kode', ['K4', 'K5', 'K7'])->get()->keyBy('kode');

        return view('admin.penugasan', compact('penugasans', 'calonAnggotas', 'kriteriaWawancara'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'indikator_kriteria' => 'nullable|array',
            'indikator_kriteria.*' => 'string|in:K1,K2,K3',
        ]);

        $validated['dibuat_oleh'] = Auth::id();

        if ($request->jenis === 'penugasan cakruma') {
            $validated['indikator_kriteria'] = $request->input('indikator_kriteria', []);
        } else {
            $validated['indikator_kriteria'] = null;
        }

        Penugasan::create($validated);

        return back()->with('success', 'Penugasan baru berhasil dibuat dan dipublikasikan ke calon anggota.');
    }

    public function detail($id)
    {
        $penugasan = Penugasan::with(['pengumpulanTugas.user.profil'])->findOrFail($id);
        $allMembers = User::where('role', 'calon_anggota')->with('profil')->get();

        return view('admin.penugasan_detail', compact('penugasan', 'allMembers'));
    }

    public function beriNilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan = PengumpulanTugas::with('user', 'penugasan')->findOrFail($id);
        $pengumpulan->update([
            'nilai' => $request->nilai,
            'feedback' => $request->feedback,
            'status' => 'dinilai',
        ]);

        // Auto sync nilai ke kriteria K1 / K2 / K3 dan K6 (Kedisiplinan)
        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncUserScores($pengumpulan->user, Auth::id());

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Nilai dan evaluasi tugas berhasil disimpan, nilai kriteria & rekomendasi telah disinkronisasi.');
    }

    public function simpanNilaiWawancara(Request $request, $userId)
    {
        $request->validate([
            'nilai_k4' => 'nullable|numeric|min:0|max:100',
            'nilai_k5' => 'nullable|numeric|min:0|max:100',
            'nilai_k7' => 'nullable|numeric|min:0|max:100',
        ]);

        $adminId = Auth::id();
        $user = User::where('role', 'calon_anggota')->findOrFail($userId);
        $kriteriaMap = Kriteria::whereIn('kode', ['K4', 'K5', 'K7'])->pluck('id', 'kode');

        if ($request->filled('nilai_k4') && isset($kriteriaMap['K4'])) {
            NilaiEvaluasi::updateOrCreate(
                ['user_id' => $user->id, 'kriteria_id' => $kriteriaMap['K4']],
                ['nilai_aktual' => $request->nilai_k4, 'input_oleh' => $adminId]
            );
        }

        if ($request->filled('nilai_k5') && isset($kriteriaMap['K5'])) {
            NilaiEvaluasi::updateOrCreate(
                ['user_id' => $user->id, 'kriteria_id' => $kriteriaMap['K5']],
                ['nilai_aktual' => $request->nilai_k5, 'input_oleh' => $adminId]
            );
        }

        if ($request->filled('nilai_k7') && isset($kriteriaMap['K7'])) {
            NilaiEvaluasi::updateOrCreate(
                ['user_id' => $user->id, 'kriteria_id' => $kriteriaMap['K7']],
                ['nilai_aktual' => $request->nilai_k7, 'input_oleh' => $adminId]
            );
        }

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', "Nilai wawancara & psikotes untuk {$user->name} berhasil disimpan.");
    }

    public function simpanNilaiWawancaraMassal(Request $request)
    {
        $request->validate([
            'nilai' => 'required|array',
        ]);

        $adminId = Auth::id();
        $kriteriaMap = Kriteria::whereIn('kode', ['K4', 'K5', 'K7'])->pluck('id', 'kode');

        foreach ($request->nilai as $userId => $scores) {
            foreach (['K4' => 'nilai_k4', 'K5' => 'nilai_k5', 'K7' => 'nilai_k7'] as $kode => $key) {
                if (isset($scores[$key]) && $scores[$key] !== '' && $scores[$key] !== null) {
                    $scoreVal = max(0, min(100, (int) $scores[$key]));
                    if (isset($kriteriaMap[$kode])) {
                        NilaiEvaluasi::updateOrCreate(
                            ['user_id' => $userId, 'kriteria_id' => $kriteriaMap[$kode]],
                            ['nilai_aktual' => $scoreVal, 'input_oleh' => $adminId]
                        );
                    }
                }
            }
        }

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Seluruh nilai wawancara & psikotes calon anggota berhasil disimpan.');
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $penugasan->delete();

        $evalService = new \App\Services\EvaluasiNilaiService();
        $evalService->syncAllScores(Auth::id());

        $pmService = new \App\Services\ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }
}
