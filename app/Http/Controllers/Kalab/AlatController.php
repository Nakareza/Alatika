<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        // Hanya menampilkan alat Prodi D3 TI / D4 TRK
        $query = Alat::where('program_studi', 'D3 TI / D4 TRK');

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

        // Statistik (hanya alat Prodi TI/TRK)
        $prodiQuery = Alat::where('program_studi', 'D3 TI / D4 TRK');
        $totalStok = (clone $prodiQuery)->sum('stok_total');
        $totalTersedia = (clone $prodiQuery)->sum('stok_tersedia');
        $totalMaintenance = (clone $prodiQuery)->sum('stok_maintenance');
        $totalDipinjam = max(0, $totalStok - $totalTersedia - $totalMaintenance);

        $stats = [
            'total' => $totalStok,
            'tersedia' => $totalTersedia,
            'dipinjam' => $totalDipinjam,
            'maintenance' => $totalMaintenance,
        ];

        // Ambil list kategori unik untuk dropdown (hanya dari Prodi TI/TRK)
        $kategoriOptions = Alat::query()
            ->where('program_studi', 'D3 TI / D4 TRK')
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori')
            ->sort();

        return view(
            'kalab.alat.index',
            compact('alat', 'stats', 'kategoriOptions')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:100|unique:alat,kode',
            'kategori' => 'required|string|max:100',
            'tahun_pengadaan' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'stok_total' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Alat::create([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'kategori' => $request->kategori,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'stok_total' => $request->stok_total,
            'stok_tersedia' => $request->stok_total,
            'stok_maintenance' => 0,
            'status' => 'tersedia',
            'deskripsi' => $request->deskripsi,
            'kondisi' => 'baik',
            'program_studi' => 'D3 TI / D4 TRK', // Automatically set to special tools
        ]);

        return back()->with(
            'success',
            'Data inventaris alat khusus berhasil ditambahkan.'
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

    public function destroy(Alat $alat)
    {
        // Prevent deletion if alat has active (dipinjam) peminjaman
        $activeCount = \App\Models\Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->count();

        if ($activeCount > 0) {
            return back()->with('error', 'Alat tidak dapat dihapus karena sedang dipinjam (' . $activeCount . ' peminjaman aktif).');
        }

        // Clean up waitlists
        \App\Models\Waitlist::where('alat_id', $alat->id)->delete();

        // Delete the alat
        $alat->delete();

        return back()->with('success', 'Alat "' . $alat->nama . '" berhasil dihapus.');
    }
}