<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Keranjang;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    /**
     * Display the user's cart
     */
    public function index()
    {
        $keranjang = Keranjang::with('alat')->where('user_id', auth()->id())->get();
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

        $keranjang = Keranjang::where('user_id', auth()->id())
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
                'user_id' => auth()->id(),
                'alat_id' => $alat_id,
                'jumlah' => $request->jumlah
            ]);
        }

        return redirect()->route('mahasiswa.alat')->with('success', 'Alat berhasil ditambahkan ke keranjang.');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $keranjang = Keranjang::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
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
            'tanggal_kembali' => 'required|date|after_or_equal:today',
        ]);

        $keranjangItems = Keranjang::with('alat')->where('user_id', auth()->id())->get();

        if ($keranjangItems->isEmpty()) {
            return redirect()->route('mahasiswa.alat')->with('error', 'Keranjang Anda kosong.');
        }

        DB::beginTransaction();
        try {
            $kode = Peminjaman::generateKode();
            $alatNames = [];

            foreach ($keranjangItems as $item) {
                // Ensure stock is still available
                if ($item->alat->stok_tersedia < $item->jumlah) {
                    throw new \Exception("Stok {$item->alat->nama} tidak mencukupi saat ini.");
                }

                Peminjaman::create([
                    'kode_peminjaman' => $kode,
                    'user_id' => auth()->id(),
                    'alat_id' => $item->alat_id,
                    'jumlah' => $item->jumlah,
                    'keperluan' => $request->keperluan,
                    'tanggal_pinjam' => now(),
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'status' => 'pending',
                ]);

                // Reduce stock
                $item->alat->decrement('stok_tersedia', $item->jumlah);
                
                $alatNames[] = "{$item->alat->nama} ({$item->jumlah} unit)";
            }

            // Clear cart
            Keranjang::where('user_id', auth()->id())->delete();

            DB::commit();

            // Send notification to approvers (Kalab or Admin depending on business logic)
            // Assuming we send generic request
            $adminOrKalab = \App\Models\User::whereIn('role', ['admin', 'kalab'])->first();
            if ($adminOrKalab) {
                $telegram->notifyNewRequest($adminOrKalab, [
                    'peminjam_nama' => auth()->user()->name,
                    'peminjam_role' => auth()->user()->role,
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
