<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Alat;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::where('status', 'tersedia')
            ->orderBy('nama')
            ->get();
            
        return view('dosen.alat.index', compact('alat'));
    }
}
