<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index()
    {
        $pengembalian = Peminjaman::with(['user', 'alat'])
            ->whereIn('status', ['dipinjam', 'menunggu_verifikasi', 'selesai'])
            ->orderByRaw("FIELD(status, 'menunggu_verifikasi', 'dipinjam', 'selesai')")
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return view('admin.pengembalian.index', compact('pengembalian'));
    }

    public function verify(Request $request, $id, TelegramService $telegram)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        
        // Return stok
        $alat = $peminjaman->alat;
        if ($request->kondisi_kembali === 'baik' || $request->kondisi_kembali === 'rusak_ringan') {
            $alat->stok_tersedia += $peminjaman->jumlah;
        } else {
            // Jika rusak berat, stok tidak dikembalikan ke stok tersedia
            // (tergantung business logic lab, biasanya dikurangi dari stok total juga / masuk maintenance)
            $alat->stok_total -= $peminjaman->jumlah;
        }
        $alat->save();

        $peminjaman->update([
            'status' => 'selesai',
            'kondisi_kembali' => $request->kondisi_kembali,
            'catatan_kondisi' => $request->catatan_kondisi,
            // Jika belum ada foto karena offline return, kita biarkan null atau bisa upload admin manual
        ]);

        // Send notif
        $telegram->notifyReturnVerified($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
        ]);

        return redirect()->back()->with('success', 'Pengembalian berhasil diverifikasi');
    }
}
