<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function riwayat()
    {
        $user = Auth::user();
        $riwayat = $user->peminjaman()
            ->with('alat')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('mahasiswa.peminjaman.riwayat', compact('riwayat'));
    }

    public function ajukan()
    {
        $alat = Alat::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->get();

        return view('mahasiswa.peminjaman.ajukan', compact('alat'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'items' => 'required|array|min:1',

            'items.*.alat_id' => 'required|exists:alat,id',
            'items.*.jumlah' => 'required|integer|min:1',

            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',

            'keperluan' => 'required|string|max:500'
        ]);

        foreach ($request->items as $item) {

            $alat = Alat::findOrFail($item['alat_id']);

            // cek stok
            if ($alat->stok_tersedia < $item['jumlah']) {

                return back()->with(
                    'error',
                    'Stok alat "' . $alat->nama . '" tidak mencukupi.'
                );
            }

            // simpan peminjaman
            $peminjaman = Peminjaman::create([
                'kode_peminjaman' => Peminjaman::generateKode(),

                'user_id' => Auth::id(),

                'alat_id' => $alat->id,

                'jumlah' => $item['jumlah'],

                'tanggal_pinjam' => $request->tanggal_pinjam,

                'tanggal_kembali' => $request->tanggal_kembali,

                'keperluan' => $request->keperluan,

                'status' => 'pending'
            ]);

            // notif admin
            $admins = User::where('role', 'admin')
                ->whereNotNull('telegram_chat_id')
                ->get();

            foreach ($admins as $admin) {

                $telegram->notifyNewRequest($admin, [
                    'peminjam_nama' => Auth::user()->name,
                    'peminjam_role' => 'mahasiswa',
                    'alat' => $alat->nama,
                    'jumlah' => $item['jumlah'],
                    'kode' => $peminjaman->kode_peminjaman,
                ]);
            }
        }

        return redirect()
            ->route('mahasiswa.peminjaman.riwayat')
            ->with(
                'success',
                'Peminjaman berhasil diajukan dan sedang menunggu persetujuan.'
            );
    }
}