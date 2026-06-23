<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::query();

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");

            });
        }

        $alat = $query
            ->orderBy('nama', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $stats = [
            'total' => Alat::count(),

            'tersedia' => Alat::where('stok_tersedia', '>', 0)
                ->where('status', 'tersedia')
                ->count(),

            'dipinjam' => \App\Models\Peminjaman::where('status', 'dipinjam')
                ->whereHas('user', fn($q) => $q->where('role', 'dosen'))
                ->sum('jumlah'),

            'maintenance' => Alat::where('status', 'maintenance')
                ->count(),
        ];

        // Ambil list kategori unik untuk dropdown
        $kategoriOptions = Alat::query()
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori')
            ->sort();

        return view(
            'kalab.alat.index',
            compact('alat', 'stats', 'kategoriOptions')
        );
    }

    public function update(Request $request, Alat $alat)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'stok_total' => 'required|integer',
            'stok_tersedia' => 'required|integer',
            'kondisi' => 'required'
        ]);

        $alat->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_tersedia,
            'kondisi' => $request->kondisi,
        ]);

        return back()->with(
            'success',
            'Data inventaris berhasil diperbarui.'
        );
    }
}