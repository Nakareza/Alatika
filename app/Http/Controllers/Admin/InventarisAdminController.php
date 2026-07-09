<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Kategori;
use App\Models\ToolSet;
use App\Models\ToolSetDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->paginate(10, ['*'], 'alat_page')
            ->appends($request->query());

        // Load active peminjaman with user info for each alat
        $alatIds = $alat->pluck('id');
        $activePeminjaman = Peminjaman::with('user')
            ->whereIn('alat_id', $alatIds)
            ->where('status', 'dipinjam')
            ->get()
            ->groupBy('alat_id');

        // ToolSet Query
        $toolSetQuery = ToolSet::with('details');

        if ($request->filled('search')) {
            $search = $request->search;
            $toolSetQuery->where(function ($q) use ($search) {
                $q->where('nama_tool_set', 'like', "%{$search}%")
                    ->orWhere('kode_tool_set', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $toolSetQuery->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_kategori', $request->kategori);
            });
        }

        if ($request->filled('stok')) {
            switch ($request->stok) {
                case 'tersedia':
                    $toolSetQuery->where('stok_tersedia', '>', 0);
                    break;
                case 'dipinjam':
                    $toolSetQuery->whereColumn('stok_tersedia', '<', 'stok');
                    break;
                case 'habis':
                    $toolSetQuery->where('stok_tersedia', 0);
                    break;
            }
        }

        $toolSets = $toolSetQuery
            ->orderBy('nama_tool_set')
            ->paginate(10, ['*'], 'toolset_page')
            ->appends($request->query());

        $toolSetIds = $toolSets->pluck('id');
        $activePeminjamanToolSet = Peminjaman::with('user')
            ->whereIn('borrowable_id', $toolSetIds)
            ->where('borrowable_type', 'App\Models\ToolSet')
            ->where('status', 'dipinjam')
            ->get()
            ->groupBy('borrowable_id');

        $stats = [
            'total_alat' => Alat::count(),
            'total_stok' => Alat::sum('stok_total'),
            'total_tersedia' => Alat::sum('stok_tersedia'),
            'total_dipinjam' => Peminjaman::where('status', 'dipinjam')->sum('jumlah'),
            
            'total_tool_sets' => ToolSet::count(),
            'total_tool_sets_stok' => ToolSet::sum('stok'),
            'total_tool_sets_tersedia' => ToolSet::sum('stok_tersedia'),
        ];

        // Fetch category options from Kategori model
        $kategoriOptions = Kategori::orderBy('nama_kategori')
            ->pluck('nama_kategori')
            ->unique()
            ->values();

        return view(
            'admin.inventaris.index',
            compact(
                'alat',
                'toolSets',
                'stats',
                'kategoriOptions',
                'activePeminjaman',
                'activePeminjamanToolSet'
            )
        );
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('admin.inventaris.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        if ($request->type === 'toolset') {
            $request->validate([
                'nama_tool_set' => 'required|string|max:255',
                'kode_tool_set' => 'required|string|max:100|unique:tool_sets,kode_tool_set',
                'kategori_id' => 'required|exists:kategoris,id',
                'stok' => 'required|integer|min:0',
                'lokasi' => 'nullable|string|max:255',
                'kondisi' => 'required|string|in:baik,rusak,perlu_pengecekan',
                'keterangan' => 'nullable|string',
                'tahun' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'components' => 'required|array|min:1',
                'components.*.nama_komponen' => 'required|string|max:255',
                'components.*.jumlah' => 'required|integer|min:1',
                'components.*.satuan' => 'required|string|max:50',
                'components.*.keterangan' => 'nullable|string|max:255',
            ]);

            DB::beginTransaction();
            try {
                $toolSet = ToolSet::create([
                    'nama_tool_set' => $request->nama_tool_set,
                    'kode_tool_set' => $request->kode_tool_set,
                    'kategori_id' => $request->kategori_id,
                    'stok' => $request->stok,
                    'stok_tersedia' => $request->stok,
                    'lokasi' => $request->lokasi,
                    'kondisi' => $request->kondisi,
                    'keterangan' => $request->keterangan,
                    'tahun' => $request->tahun,
                ]);

                foreach ($request->components as $comp) {
                    ToolSetDetail::create([
                        'tool_set_id' => $toolSet->id,
                        'nama_komponen' => $comp['nama_komponen'],
                        'jumlah' => $comp['jumlah'],
                        'satuan' => $comp['satuan'],
                        'keterangan' => $comp['keterangan'] ?? null,
                    ]);
                }

                DB::commit();
                return redirect()
                    ->route('admin.alat')
                    ->with('success', 'Tool Set berhasil ditambahkan.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Gagal menyimpan Tool Set: ' . $e->getMessage())->withInput();
            }
        } else {
            $request->validate([
                'nama' => 'required|string|max:255',
                'kode' => 'required|string|max:100|unique:alat,kode',
                'kategori_id' => 'required|string',
                'kategori_baru' => 'required_if:kategori_id,__new|nullable|string|max:255',
                'lokasi' => 'nullable|string|max:255',
                'stok_total' => 'required|integer|min:0',
                'program_studi' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'kondisi' => 'required|string|in:baik,rusak,perlu_pengecekan',
            ]);

            if ($request->kategori_id === '__new') {
                $kategoriModel = Kategori::firstOrCreate([
                    'nama_kategori' => $request->kategori_baru
                ]);
                $kategoriId = $kategoriModel->id;
            } else {
                $kategoriId = $request->kategori_id;
                $kategoriModel = Kategori::findOrFail($kategoriId);
            }

            Alat::create([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'kategori_id' => $kategoriId,
                'kategori' => $kategoriModel->nama_kategori,
                'lokasi' => $request->lokasi,
                'stok_total' => $request->stok_total,
                'stok_tersedia' => $request->stok_total,
                'stok_maintenance' => 0,
                'status' => 'tersedia',
                'kondisi' => $request->kondisi,
                'deskripsi' => $request->deskripsi,
                'program_studi' => $request->program_studi ?: null,
            ]);

            return redirect()
                ->route('admin.alat')
                ->with('success', 'Alat berhasil ditambahkan.');
        }
    }

    public function edit(Alat $alat)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $activeBorrowedCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        return view('admin.inventaris.edit', compact('alat', 'activeBorrowedCount', 'kategoris'));
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
            'kategori_id' => 'required|string',
            'kategori_baru' => 'required_if:kategori_id,__new|nullable|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'program_studi' => 'nullable|string|max:255',
            'stok_maintenance' => "required|integer|min:0|max:{$maxMaintenance}",
            'kondisi' => 'required|string|in:baik,rusak,perlu_pengecekan',
        ], [
            'stok_maintenance.max' => "Jumlah alat di-maintenance tidak boleh melebihi stok yang tersedia (maksimal: {$maxMaintenance} karena {$activeBorrowedCount} sedang dipinjam).",
        ]);

        if ($request->kategori_id === '__new') {
            $kategoriModel = Kategori::firstOrCreate([
                'nama_kategori' => $request->kategori_baru
            ]);
            $kategoriId = $kategoriModel->id;
        } else {
            $kategoriId = $request->kategori_id;
            $kategoriModel = Kategori::findOrFail($kategoriId);
        }

        $stokMaint = (int) $request->stok_maintenance;
        $newTersedia = $alat->stok_total - $activeBorrowedCount - $stokMaint;
        $status = ($stokMaint === $alat->stok_total) ? 'maintenance' : 'tersedia';
        $programStudi = $request->program_studi ?: null;

        $alat->update([
            'nama' => $request->nama,
            'kode' => $request->kode,
            'kategori_id' => $kategoriId,
            'kategori' => $kategoriModel->nama_kategori,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'program_studi' => $programStudi,
            'stok_maintenance' => $stokMaint,
            'stok_tersedia' => $newTersedia,
            'status' => $status,
            'kondisi' => $request->kondisi,
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
        $borrowedCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        if ($newStatus === 'maintenance') {
            if ($borrowedCount === $alat->stok_total) {
                return redirect()->back()->with('error', 'Alat sedang dipinjam sehingga status tidak dapat diubah menjadi Maintenance.');
            }

            $alat->stok_maintenance = $alat->stok_total - $borrowedCount;
            $alat->stok_tersedia = 0;
            $alat->status = ($alat->stok_maintenance === $alat->stok_total) ? 'maintenance' : 'tersedia';
            $alat->save();
        } else {
            $alat->stok_maintenance = 0;
            $alat->stok_tersedia = $alat->stok_total - $borrowedCount;
            $alat->status = 'tersedia';
            $alat->save();
        }

        return redirect()->back()->with('success', 'Status alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat)
    {
        $activeCount = Peminjaman::where('alat_id', $alat->id)
            ->where('status', 'dipinjam')
            ->count();

        if ($activeCount > 0) {
            return redirect()
                ->route('admin.alat')
                ->with('error', 'Alat tidak dapat dihapus karena sedang dipinjam (' . $activeCount . ' peminjaman aktif).');
        }

        \App\Models\Waitlist::where('alat_id', $alat->id)->delete();
        $alat->delete();

        return redirect()
            ->route('admin.alat')
            ->with('success', 'Alat "' . $alat->nama . '" berhasil dihapus.');
    }

    // ===================================================
    // TOOLSET CRUD METHODS
    // ===================================================

    public function editToolSet($id)
    {
        $toolSet = ToolSet::with('details')->findOrFail($id);
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $activeBorrowedCount = Peminjaman::where('borrowable_type', 'App\Models\ToolSet')
            ->where('borrowable_id', $toolSet->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        return view('admin.inventaris.edit-toolset', compact('toolSet', 'activeBorrowedCount', 'kategoris'));
    }

    public function updateToolSet(Request $request, $id)
    {
        $toolSet = ToolSet::findOrFail($id);
        $activeBorrowedCount = Peminjaman::where('borrowable_type', 'App\Models\ToolSet')
            ->where('borrowable_id', $toolSet->id)
            ->where('status', 'dipinjam')
            ->sum('jumlah');

        $request->validate([
            'nama_tool_set' => 'required|string|max:255',
            'kode_tool_set' => "required|string|max:100|unique:tool_sets,kode_tool_set,{$toolSet->id}",
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => "required|integer|min:{$activeBorrowedCount}",
            'lokasi' => 'nullable|string|max:255',
            'kondisi' => 'required|string|in:baik,rusak,perlu_pengecekan',
            'keterangan' => 'nullable|string',
            'tahun' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'components' => 'required|array|min:1',
            'components.*.nama_komponen' => 'required|string|max:255',
            'components.*.jumlah' => 'required|integer|min:1',
            'components.*.satuan' => 'required|string|max:50',
            'components.*.keterangan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $stokTersedia = $request->stok - $activeBorrowedCount;

            $toolSet->update([
                'nama_tool_set' => $request->nama_tool_set,
                'kode_tool_set' => $request->kode_tool_set,
                'kategori_id' => $request->kategori_id,
                'stok' => $request->stok,
                'stok_tersedia' => $stokTersedia,
                'lokasi' => $request->lokasi,
                'kondisi' => $request->kondisi,
                'keterangan' => $request->keterangan,
                'tahun' => $request->tahun,
            ]);

            ToolSetDetail::where('tool_set_id', $toolSet->id)->delete();

            foreach ($request->components as $comp) {
                ToolSetDetail::create([
                    'tool_set_id' => $toolSet->id,
                    'nama_komponen' => $comp['nama_komponen'],
                    'jumlah' => $comp['jumlah'],
                    'satuan' => $comp['satuan'],
                    'keterangan' => $comp['keterangan'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()
                ->route('admin.alat')
                ->with('success', 'Tool Set berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui Tool Set: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyToolSet($id)
    {
        $toolSet = ToolSet::findOrFail($id);

        $activeCount = Peminjaman::where('borrowable_type', 'App\Models\ToolSet')
            ->where('borrowable_id', $toolSet->id)
            ->where('status', 'dipinjam')
            ->count();

        if ($activeCount > 0) {
            return redirect()
                ->route('admin.alat')
                ->with('error', 'Tool Set tidak dapat dihapus karena sedang dipinjam (' . $activeCount . ' peminjaman aktif).');
        }

        DB::beginTransaction();
        try {
            ToolSetDetail::where('tool_set_id', $toolSet->id)->delete();
            $toolSet->delete();
            DB::commit();

            return redirect()
                ->route('admin.alat')
                ->with('success', 'Tool Set "' . $toolSet->nama_tool_set . '" berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.alat')
                ->with('error', 'Gagal menghapus Tool Set: ' . $e->getMessage());
        }
    }
}
