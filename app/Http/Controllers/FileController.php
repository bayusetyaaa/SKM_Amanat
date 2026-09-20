<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * View or download a Berkas file stored in database.
     */
    public function viewBerkas($id)
    {
        $berkas = Berkas::withFileData()->findOrFail($id);
        $user = Auth::user();

        // Authorization: Admin or the owner of the document
        if ($user->role !== 'admin' && $berkas->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses ke berkas ini.');
        }

        if (!empty($berkas->file_data)) {
            $binary = base64_decode($berkas->file_data);
            $mime = $berkas->mime_type ?: 'application/pdf';
            $filename = $berkas->nama_file ?: 'berkas.pdf';

            return response($binary, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
        }

        // Fallback if file was saved locally
        if ($berkas->file_path && Storage::disk('public')->exists($berkas->file_path)) {
            return response()->file(Storage::disk('public')->path($berkas->file_path));
        }

        abort(404, 'File berkas tidak ditemukan.');
    }

    /**
     * View or download an assignment submission (PengumpulanTugas) stored in database.
     */
    public function viewPengumpulanTugas($id)
    {
        $pengumpulan = PengumpulanTugas::withFileData()->findOrFail($id);
        $user = Auth::user();

        // Authorization: Admin or the owner of the submission
        if ($user->role !== 'admin' && $pengumpulan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses ke file penugasan ini.');
        }

        if (!empty($pengumpulan->file_data)) {
            $binary = base64_decode($pengumpulan->file_data);
            $mime = $pengumpulan->mime_type ?: 'application/pdf';
            $filename = $pengumpulan->nama_file ?: 'penugasan.pdf';

            return response($binary, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
        }

        // Fallback if file was saved locally
        if ($pengumpulan->file_path && Storage::disk('public')->exists($pengumpulan->file_path)) {
            return response()->file(Storage::disk('public')->path($pengumpulan->file_path));
        }

        abort(404, 'File penugasan tidak ditemukan.');
    }

    /**
     * View or download an announcement attachment stored in database.
     */
    public function viewPengumumanLampiran($id)
    {
        $pengumuman = Pengumuman::withLampiranData()->findOrFail($id);

        if (!empty($pengumuman->lampiran_data)) {
            $binary = base64_decode($pengumuman->lampiran_data);
            $mime = $pengumuman->lampiran_mime ?: 'application/pdf';
            $filename = $pengumuman->lampiran_nama ?: 'lampiran.pdf';

            return response($binary, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
        }

        // Fallback if file was saved locally
        if ($pengumuman->lampiran_path && Storage::disk('public')->exists($pengumuman->lampiran_path)) {
            return response()->file(Storage::disk('public')->path($pengumuman->lampiran_path));
        }

        abort(404, 'Lampiran pengumuman tidak ditemukan.');
    }
}
