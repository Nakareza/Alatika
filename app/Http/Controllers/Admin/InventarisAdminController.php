<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class InventarisAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventaris::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('is_borrowable')) {
            $query->where('is_borrowable', filter_var($request->is_borrowable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('lokasi_simpan', 'like', "%{$search}%")
                    ->orWhere('kondisi', 'like', "%{$search}%");
            });
        }

        $inventaris = $query
            ->orderBy('kategori')
            ->orderBy('nama_alat')
            ->paginate(20)
            ->appends($request->query());

        $stats = [
            'total_item' => Inventaris::count(),
            'total_borrowable' => Inventaris::where('is_borrowable', true)->count(),
            'total_non_borrowable' => Inventaris::where('is_borrowable', false)->count(),
            'total_stok' => (int) Inventaris::sum('jumlah_stok'),
        ];

        $kategoriOptions = Inventaris::query()
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.inventaris.index', compact('inventaris', 'stats', 'kategoriOptions'));
    }
}
