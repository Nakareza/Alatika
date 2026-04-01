<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::orderBy('stok_tersedia', 'desc')
            ->orderBy('nama')
            ->get();
            
        return view('mahasiswa.alat.index', compact('alat'));
    }

    public function waitlist($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->stok_tersedia > 0) {
            return redirect()->back()->with('error', 'Alat masih tersedia, Anda bisa langsung meminjamnya.');
        }

        \App\Models\Waitlist::firstOrCreate([
            'user_id' => auth()->id(),
            'alat_id' => $id,
            'status' => 'waiting'
        ]);

        return redirect()->back()->with('success', 'Berhasil masuk daftar tunggu! Anda akan dikabari via Telegram saat alat dikembalikan.');
    }
}
