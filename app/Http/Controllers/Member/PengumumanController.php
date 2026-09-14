<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::whereIn('target_audience', ['semua', 'calon_anggota'])
            ->orderByDesc('created_at')
            ->get();

        return view('user.pengumuman', compact('pengumumans'));
    }

    public function detail($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return view('user.pengumuman_detail', compact('pengumuman'));
    }
}
