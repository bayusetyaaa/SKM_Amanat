<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoPendaftaranController extends Controller
{
    public function index()
    {
        return view('user.info_pendaftaran');
    }
}
