<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;

class InventarisAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('stok')) {

            switch ($request->stok) {

                case 'tersedia':
                    $query->where('stok_tersedia', '>', 0);
                    break;

                case 'dipinjam':
                    $query->whereColumn(
                        'stok_tersedia',
                        '<',
                        'stok_total'
                    );
                    break;

                case 'habis':
                    $query->where('stok_tersedia', 0);
                    break;
            }
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $alat = $query
            ->orderBy('nama')
            ->paginate(10)
            ->appends($request->query());

        $stats = [

           'total_alat' => Alat::count(),

            'total_stok' => Alat::sum('stok_total'),

            'total_tersedia' => Alat::sum('stok_tersedia'),

            'total_dipinjam' => Alat::get()
             ->sum(fn ($alat) => $alat->stok_total - $alat->stok_tersedia),

        ];

        $kategoriOptions = Alat::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view(
            'admin.inventaris.index',
            compact(
                'alat',
                'stats',
                'kategoriOptions'
            )
        );
    }
    public function create()
    {
        return view('admin.inventaris.create');
    }
    public function edit(Alat $alat)
    {
        return view('admin.inventaris.edit', compact('alat'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:100|unique:alat,kode',
            'kategori' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'stok_total' => 'required|integer|min:0',
        ]);

        $validated['stok_tersedia'] = $validated['stok_total'];
        $validated['status'] = 'tersedia';

        Alat::create([
            ...$validated,
            'stok_tersedia' => $validated['stok_total'],
            'status' => 'tersedia',
        ]);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Inventaris berhasil ditambahkan.');
    }
    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => "required|string|max:100|unique:alat,kode,{$alat->id}",
            'kategori' => 'required|string|max:100',
            'lokasi' => 'required|string|max:255',
            'stok_total' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $alat->update($validated);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Inventaris berhasil diperbarui.');
    }
    public function destroy(Alat $alat)
    {
    }
}
