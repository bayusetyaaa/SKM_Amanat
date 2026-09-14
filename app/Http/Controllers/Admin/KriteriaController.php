<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Models\Kriteria;
use App\Models\ProfilTarget;
use App\Services\ProfileMatchingService;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    public function index(Request $request)
    {
        $divisis = Divisi::all();
        $selectedDivisiId = $request->get('divisi_id', $divisis->first()->id ?? 1);
        $selectedDivisi = Divisi::findOrFail($selectedDivisiId);

        $kriterias = Kriteria::all();
        $targets = ProfilTarget::where('divisi_id', $selectedDivisiId)
            ->get()
            ->keyBy('kriteria_id');

        // Kelompokkan kriteria berdasarkan aspek
        $aspekGroups = $kriterias->groupBy('aspek');

        return view('admin.konfigurasi_kriteria', compact('divisis', 'selectedDivisi', 'aspekGroups', 'targets'));
    }

    public function updateTarget(Request $request)
    {
        $request->validate([
            'divisi_id' => 'required|exists:divisi,id',
            'targets' => 'required|array',
            'targets.*.target' => 'required|numeric|min:1|max:100',
            'targets.*.faktor' => 'required|in:core,secondary',
        ]);

        $divisiId = $request->divisi_id;

        foreach ($request->targets as $kriteriaId => $data) {
            ProfilTarget::updateOrCreate(
                [
                    'divisi_id' => $divisiId,
                    'kriteria_id' => $kriteriaId,
                ],
                [
                    'nilai_target' => $data['target'],
                    'faktor' => $data['faktor'],
                ]
            );
        }

        // Jalankan ulang Profile Matching agar langsung terupdate
        $pmService = new ProfileMatchingService();
        $pmService->calculateAndRankAll();

        return back()->with('success', 'Konfigurasi target nilai dan faktor berhasil diperbarui.');
    }
}
