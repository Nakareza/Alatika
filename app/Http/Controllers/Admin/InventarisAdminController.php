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
        // Get active peminjaman count for this specific record
        $activeBorrowedCount = \App\Models\Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        return view('admin.inventaris.edit', compact('alat', 'activeBorrowedCount'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:100|unique:alat,kode',
            'kategori' => 'required|string|max:100',
            'kategori_baru' => 'nullable|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'stok_total' => 'required|integer|min:0',
            'program_studi' => 'nullable|string|max:255',
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
            'stok_maintenance' => 0,
            'status' => 'tersedia',
            'deskripsi' => $request->deskripsi,
            'program_studi' => $request->program_studi ?: null,
        ]);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat berhasil ditambahkan.');
    }
    public function update(Request $request, Alat $alat)
    {
        $activeBorrowedCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        $maxMaintenance = $alat->stok_total - $activeBorrowedCount;

        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => "required|string|max:100|unique:alat,kode,{$alat->id}",
            'kategori' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'program_studi' => 'nullable|string|max:255',
            'stok_maintenance' => "required|integer|min:0|max:{$maxMaintenance}",
        ], [
            'stok_maintenance.max' => "Jumlah alat di-maintenance tidak boleh melebihi stok yang tersedia (maksimal: {$maxMaintenance} karena {$activeBorrowedCount} sedang dipinjam).",
        ]);

        $stokMaint = (int) $request->stok_maintenance;

        // Calculate new stok_tersedia
        $newTersedia = $alat->stok_total - $activeBorrowedCount - $stokMaint;

        // Set status to 'maintenance' if ALL units are under maintenance, else 'tersedia'
        $status = ($stokMaint === $alat->stok_total) ? 'maintenance' : 'tersedia';

        // Ensure empty string program_studi is saved as null
        $programStudi = $request->program_studi ?: null;

        $alat->update([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'kategori' => $request->kategori,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'program_studi' => $programStudi,
            'stok_maintenance' => $stokMaint,
            'stok_tersedia' => $newTersedia,
            'status' => $status,
        ]);

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:tersedia,maintenance',
        ]);

        $newStatus = $request->status;

        // Find currently active borrowed count
        $borrowedCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        if ($newStatus === 'maintenance') {
            if ($borrowedCount === $alat->stok_total) {
                return redirect()->back()->with('error', 'Alat sedang dipinjam sehingga status tidak dapat diubah menjadi Maintenance.');
            }

            // Put all remaining available units to maintenance
            $alat->stok_maintenance = $alat->stok_total - $borrowedCount;
            $alat->stok_tersedia = 0;
            $alat->status = ($alat->stok_maintenance === $alat->stok_total) ? 'maintenance' : 'tersedia';
            $alat->save();
        } else {
            // Put all units back to tersedia
            $alat->stok_maintenance = 0;
            $alat->stok_tersedia = $alat->stok_total - $borrowedCount;
            $alat->status = 'tersedia';
            $alat->save();
        }

        return redirect()->back()->with('success', 'Status alat berhasil diperbarui.');
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
