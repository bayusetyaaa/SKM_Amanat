<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Berkas;
use App\Models\ProfilCalonAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['profil', 'berkas']);
        $berkasList = $user->berkas->keyBy('jenis_berkas');

        return view('user.profil', compact('user', 'berkasList'));
    }

    public function updateDataPribadi(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:30',
            'prodi' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'angkatan' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        ProfilCalonAnggota::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi'],
                'no_hp' => $validated['no_hp'] ?? null,
                'angkatan' => $validated['angkatan'] ?? date('Y'),
                'alamat' => $validated['alamat'] ?? null,
            ]
        );

        return back()->with('success', 'Data pribadi berhasil diperbarui.');
    }

    public function uploadBerkas(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cv' => 'nullable|file|mimes:pdf|max:5120',
            'pas_foto' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'esai' => 'nullable|file|mimes:pdf|max:5120',
            'karya' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'mimes' => 'Format file harus berupa PDF (atau JPG/PNG untuk pas foto).',
            'max' => 'Ukuran file maksimal adalah 5MB.',
        ]);

        $configs = [
            'cv' => [
                'canonical' => 'Curriculum Vitae (CV)',
                'match' => fn($b) => str_contains(strtolower($b->jenis_berkas), 'cv') || str_contains(strtolower($b->jenis_berkas), 'curriculum'),
            ],
            'pas_foto' => [
                'canonical' => 'Pas Foto 3x4',
                'match' => fn($b) => str_contains(strtolower($b->jenis_berkas), 'foto'),
            ],
            'esai' => [
                'canonical' => 'Esai Alasan Memilih Amanat',
                'match' => fn($b) => str_contains(strtolower($b->jenis_berkas), 'esai') || str_contains(strtolower($b->jenis_berkas), 'essay'),
            ],
            'karya' => [
                'canonical' => 'Karya Pribadi (Artikel/Opini/Sastra/Jurnalistik)',
                'match' => fn($b) => str_contains(strtolower($b->jenis_berkas), 'karya') || str_contains(strtolower($b->jenis_berkas), 'portofolio'),
            ],
        ];

        $uploadedCount = 0;
        $userBerkas = Berkas::where('user_id', $user->id)->get();

        foreach ($configs as $inputName => $cfg) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType();
                $size = $file->getSize();
                $fileData = base64_encode($file->get());

                // Cari berkas yang sudah ada dengan matching fleksibel
                $existing = $userBerkas->first($cfg['match']);

                if ($existing) {
                    if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
                    $existing->update([
                        'nama_file' => $originalName,
                        'file_data' => $fileData,
                        'file_path' => null,
                        'mime_type' => $mimeType,
                        'ukuran_file' => $size,
                        'status' => 'menunggu',
                        'catatan' => null, // Reset catatan perbaikan saat upload revisi
                    ]);
                    $existing->touch(); // Pastikan timestamp updated_at diperbarui
                } else {
                    Berkas::create([
                        'user_id' => $user->id,
                        'jenis_berkas' => $cfg['canonical'],
                        'nama_file' => $originalName,
                        'file_data' => $fileData,
                        'file_path' => null,
                        'mime_type' => $mimeType,
                        'ukuran_file' => $size,
                        'status' => 'menunggu',
                        'catatan' => null,
                    ]);
                }

                $uploadedCount++;
            }
        }

        if ($uploadedCount > 0) {
            return back()->with('success', "Berhasil mengunggah {$uploadedCount} berkas persyaratan. Admin verifikator akan segera memeriksa dokumen pembaruan Anda.");
        }

        return back()->with('info', 'Silakan pilih file terlebih dahulu untuk diunggah.');
    }
}
