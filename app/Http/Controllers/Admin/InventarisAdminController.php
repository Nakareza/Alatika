<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
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

        // Load active peminjaman with user info for each alat
        $alatIds = $alat->pluck('id');
        $activePeminjaman = Peminjaman::with('user')
            ->whereIn('alat_id', $alatIds)
            ->where('status', 'dipinjam')
            ->get()
            ->groupBy('alat_id');

        $stats = [

           'total_alat' => Alat::count(),

            'total_stok' => Alat::sum('stok_total'),

            'total_tersedia' => Alat::sum('stok_tersedia'),

            'total_dipinjam' => Peminjaman::where('status', 'dipinjam')->sum('jumlah'),

        ];

        $kategoriOptions = Alat::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->orderBy('kategori')
            ->pluck('kategori')
            ->unique()
            ->values();

        return view(
            'admin.inventaris.index',
            compact(
                'alat',
                'stats',
                'kategoriOptions',
                'activePeminjaman'
            )
        );
    }
    public function create()
    {
        $kategoriOptions = Alat::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->unique()
            ->values();

        return view('admin.inventaris.create', compact('kategoriOptions'));
    }
    public function edit(Alat $alat)
    {
        return view('admin.inventaris.edit', compact('alat'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:100|unique:alat,kode',
            'kategori' => 'required|string|max:100',
            'kategori_baru' => 'nullable|string|max:100',
            'lokasi' => 'required|string|max:255',
            'stok_total' => 'required|integer|min:0',
        ]);

        // If user chose "new category", use kategori_baru value
        $kategori = $request->kategori === '__new'
            ? $request->kategori_baru
            : $request->kategori;

        if (empty($kategori)) {
            return back()->withErrors(['kategori' => 'Kategori wajib diisi.'])->withInput();
        }

        Alat::create([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'kategori' => $kategori,
            'lokasi' => $request->lokasi,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_total,
            'status' => 'tersedia',
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat berhasil ditambahkan.');
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
            'status' => 'required|string|in:tersedia,maintenance',
        ]);

        // Adjust stok_tersedia when stok_total changes
        $oldStokTotal = $alat->stok_total;
        $newStokTotal = (int) $validated['stok_total'];
        $diff = $newStokTotal - $oldStokTotal;

        if ($diff != 0) {
            $newTersedia = $alat->stok_tersedia + $diff;
            // Never let stok_tersedia go below 0 or above stok_total
            $validated['stok_tersedia'] = max(0, min($newStokTotal, $newTersedia));
        }

        $alat->update($validated);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat berhasil diperbarui.');
    }
    public function destroy(Alat $alat)
    {
        // Prevent deletion if alat has active (dipinjam) peminjaman
        $activeCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->count();

        if ($activeCount > 0) {
            return redirect()
                ->route('admin.alat')
                ->with('error', 'Alat tidak dapat dihapus karena sedang dipinjam (' . $activeCount . ' peminjaman aktif).');
        }

        // Clean up waitlists
        \App\Models\Waitlist::where('alat_id', $alat->id)->delete();

        // Delete the alat
        $alat->delete();

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat "' . $alat->nama . '" berhasil dihapus.');
    }
}
