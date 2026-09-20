<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::orderByDesc('created_at')->paginate(10)->withQueryString();
        return view('admin.pengumuman', compact('pengumumans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'target_audience' => 'nullable|array',
            'isi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:5120',
        ]);

        $audience = 'semua';
        if ($request->filled('target_audience')) {
            $audience = implode(',', $request->target_audience);
        }

        $lampiranPath = null;
        $lampiranData = null;
        $lampiranNama = null;
        $lampiranMime = null;

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $lampiranNama = $file->getClientOriginalName();
            $lampiranMime = $file->getClientMimeType();
            $lampiranData = base64_encode($file->get());
        }

        Pengumuman::create([
            'judul' => $validated['judul'],
            'target_audience' => $audience,
            'isi' => $validated['isi'],
            'lampiran_path' => null,
            'lampiran_data' => $lampiranData,
            'lampiran_nama' => $lampiranNama,
            'lampiran_mime' => $lampiranMime,
            'penulis' => Auth::user()->name ?? 'HRD SKM Amanat',
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Pengumuman baru berhasil dipublikasikan.');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->lampiran_path && Storage::disk('public')->exists($pengumuman->lampiran_path)) {
            Storage::disk('public')->delete($pengumuman->lampiran_path);
        }
        $pengumuman->delete();

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
