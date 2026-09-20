<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::withCount(['presensi' => function ($q) {
            $q->where('status', 'Hadir');
        }])->orderByDesc('tanggal_waktu')->paginate(10)->withQueryString();

        $totalCakruma = User::where('role', 'calon_anggota')->count();

        return view('admin.kegiatan', compact('kegiatans', 'totalCakruma'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:100',
            'nama' => 'required|string|max:150',
            'tempat' => 'nullable|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal_waktu' => 'required|date',
        ]);

        Kegiatan::create($validated);

        return back()->with('success', 'Kegiatan baru berhasil ditambahkan.');
    }

    public function detail($id)
    {
        $kegiatan = Kegiatan::with(['presensi.user.profil'])->findOrFail($id);
        $users = User::where('role', 'calon_anggota')->with('profil')->get();

        return view('admin.kegiatan_detail', compact('kegiatan', 'users'));
    }

    public function regenerateToken($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $newToken = strtoupper(\Illuminate\Support\Str::random(6));
        $kegiatan->update(['token_presensi' => $newToken]);

        return back()->with('success', "Token presensi untuk '{$kegiatan->nama}' berhasil diacak ulang menjadi: {$newToken}");
    }

    public function updatePresensi(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Hadir,Izin,Tidak Hadir',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);

        if ($request->status === 'Tidak Hadir') {
            Presensi::where('kegiatan_id', $kegiatan->id)
                ->where('user_id', $request->user_id)
                ->delete();
        } else {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'user_id' => $request->user_id],
                [
                    'status' => $request->status,
                    'waktu_hadir' => $request->status === 'Hadir' ? Carbon::now() : null,
                    'keterangan' => $request->keterangan ?? ('Presensi dicatat oleh admin (' . $request->status . ')'),
                ]
            );
        }

        return back()->with('success', 'Status presensi dan nilai kedisiplinan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return back()->with('success', 'Kegiatan berhasil dihapus.');
    }
}
