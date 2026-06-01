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
            ->paginate(12)
            ->appends($request->query());

        $stats = [
            'total_alat' => Alat::count(),

            'total_tersedia' => Alat::where('status', 'tersedia')
                ->count(),

            'total_dipinjam' => Alat::whereColumn(
                'stok_tersedia',
                '<',
                'stok_total'
            )->count(),

            'total_maintenance' => Alat::where(
                'status',
                'maintenance'
            )->count(),
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
}
