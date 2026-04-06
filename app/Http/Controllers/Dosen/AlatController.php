<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Alat;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::orderBy('stok_tersedia', 'desc')
            ->orderBy('nama')
            ->get();
            
        return view('dosen.alat.index', compact('alat'));
    }
}
