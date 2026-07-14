<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Models\ToolSet;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Display the user's cart
     */
    public function index()
    {
        $keranjang = Keranjang::with(['cartable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\ToolSet::class => ['details'],
                ]);
            }])
            ->where('user_id', Auth::id())
            ->get();
        return view('mahasiswa.keranjang.index', compact('keranjang'));
    }

    /**
     * Add item to cart
     */
    public function add(Request $request, $alat_id)
    {
        $alat = Alat::findOrFail($alat_id);
        
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . $alat->stok_tersedia
        ]);

        $keranjang = Keranjang::where('user_id', Auth::id())
                             ->where('alat_id', $alat_id)
                             ->first();

        if ($keranjang) {
            $newJumlah = $keranjang->jumlah + $request->jumlah;
            if ($newJumlah > $alat->stok_tersedia) {
                return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
            $keranjang->update(['jumlah' => $newJumlah]);
        } else {
            Keranjang::create([
                'user_id' => Auth::id(),
                'alat_id' => $alat_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return redirect()->route('mahasiswa.peminjaman.ajukan')->with('success', 'Alat berhasil ditambahkan ke keranjang.');
    }

    /**
     * Add ToolSet to cart
     */
    public function addToolSet(Request $request, $toolset_id)
    {
        $toolSet = ToolSet::findOrFail($toolset_id);

        $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . $toolSet->stok_tersedia
        ]);

        // Check if this ToolSet is already in the cart
        $keranjang = Keranjang::where('user_id', Auth::id())
                             ->where('cartable_type', 'App\Models\ToolSet')
                             ->where('cartable_id', $toolset_id)
                             ->first();

        if ($keranjang) {
            $newJumlah = $keranjang->jumlah + $request->jumlah;
            if ($newJumlah > $toolSet->stok_tersedia) {
                return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
            $keranjang->update(['jumlah' => $newJumlah]);
        } else {
            Keranjang::create([
                'user_id' => Auth::id(),
                'cartable_type' => 'App\Models\ToolSet',
                'cartable_id' => $toolset_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return redirect()->route('mahasiswa.peminjaman.ajukan')->with('success', 'Tool Set berhasil ditambahkan ke keranjang.');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $keranjang = Keranjang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $keranjang->delete();

        return redirect()->back()->with('success', 'Alat dihapus dari keranjang.');
    }

    /**
     * Checkout the cart
     */
    public function checkout(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'keperluan' => 'required|string|max:255',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $keranjangItems = Keranjang::with(['cartable' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\ToolSet::class => ['details'],
                ]);
            }])
            ->where('user_id', Auth::id())
            ->get();

        if ($keranjangItems->isEmpty()) {
            return redirect()->route('mahasiswa.alat')->with('error', 'Keranjang Anda kosong.');
        }

        // Validate that there is no special tool in the cart for checkout endpoint
        $hasSpecialTool = false;
        foreach ($keranjangItems as $item) {
            if ($item->cartable_type === 'App\Models\Alat') {
                $alat = Alat::find($item->cartable_id);
                if ($alat && $alat->program_studi !== null) {
                    $hasSpecialTool = true;
                    break;
                }
            }
        }
        if ($hasSpecialTool) {
            return redirect()->route('mahasiswa.peminjaman.ajukan')
                ->with('error', 'Peminjaman Alat Khusus wajib melalui form pengajuan resmi dengan melampirkan Surat Keterangan.');
        }

        DB::beginTransaction();
        try {
            $kode = Peminjaman::generateKode();
            $alatNames = [];

            foreach ($keranjangItems as $item) {
                $cartable = $item->cartable;

                // Ensure stock is still available
                if ($cartable->stok_tersedia < $item->jumlah) {
                    $itemName = $item->cartable_type === 'App\Models\ToolSet'
                        ? $cartable->nama_tool_set
                        : $cartable->nama;
                    throw new \Exception("Stok {$itemName} tidak mencukupi saat ini.");
                }

                $peminjamanData = [
                    'kode_peminjaman' => $kode,
                    'user_id' => Auth::id(),
                    'jumlah' => $item->jumlah,
                    'keperluan' => $request->keperluan,
                    'tanggal_pinjam' => $request->tanggal_pinjam,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'status' => 'pending',
                ];

                if ($item->cartable_type === 'App\Models\ToolSet') {
                    // ToolSet: use polymorphic fields
                    $peminjamanData['borrowable_type'] = 'App\Models\ToolSet';
                    $peminjamanData['borrowable_id'] = $item->cartable_id;
                    $alatNames[] = "{$cartable->nama_tool_set} ({$item->jumlah} set)";
                } else {
                    // Alat: use legacy alat_id (boot() will sync borrowable fields)
                    $peminjamanData['alat_id'] = $item->alat_id;
                    $alatNames[] = "{$cartable->nama} ({$item->jumlah} unit)";
                }

                Peminjaman::create($peminjamanData);

                // Stock is NOT decremented here — it will be decremented when Admin approves
            }

            // Clear cart
            Keranjang::where('user_id', Auth::id())->delete();

            DB::commit();

            // Send notification to approvers (Kalab or Admin depending on business logic)
            // Assuming we send generic request
            $adminOrKalab = \App\Models\User::whereIn('role', ['admin', 'kalab'])->first();
            if ($adminOrKalab) {
                $telegram->notifyNewRequest($adminOrKalab, [
                    'peminjam_nama' => Auth::user()->name,
                    'peminjam_role' => Auth::user()->role,
                    'alat' => implode(", ", $alatNames),
                    'jumlah' => 1, // multiple tools abstracted
                    'kode' => $kode,
                ]);
            }

            return redirect()->route('mahasiswa.peminjaman.riwayat')
                ->with('success', 'Peminjaman berganda berhasil diajukan dengan kode ' . $kode);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses peminjaman: ' . $e->getMessage());
        }
    }
}
