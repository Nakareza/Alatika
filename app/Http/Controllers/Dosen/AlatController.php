<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\User;

class AlatController extends Controller
{
    public function index()
    {
        $alat = Alat::orderBy('stok_tersedia', 'desc')
            ->orderBy('nama')
            ->get();
        
        $cartCount = Keranjang::where('user_id', auth()->id())->count();
            
        return view('dosen.alat.index', compact('alat', 'cartCount'));
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
