<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Keranjang;
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

        $riwayat = Peminjaman::with('alat')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('mahasiswa.peminjaman.riwayat', compact('riwayat'));
    }

    public function ajukan()
    {
        // Baca dari tabel Keranjang
        $keranjang = Keranjang::with('alat')
            ->where('user_id', Auth::id())
            ->get();
        
        // Format data sesuai dengan yang diharapkan view
        $pengajuan = $keranjang->map(function($item) {
            return [
                'alat_id' => $item->alat_id,
                'nama' => $item->alat->nama,
                'kode' => $item->alat->kode,
                'jumlah' => $item->jumlah,
                'stok_max' => $item->alat->stok_tersedia,
            ];
        })->values()->all();

        // Ambil semua alat untuk dropdown
        $alat = Alat::all();
        
        // Kelompokkan alat by kategori
        $kategori = $alat->groupBy('kategori')->keys();

        // Load keperluan options from config
        $keperluanOptions = static::getKeperluanOptions();

        return view('mahasiswa.peminjaman.ajukan', compact('pengajuan', 'alat', 'kategori', 'keperluanOptions'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.alat_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $items = $request->items;

        if (empty($items)) {
            return back()->with(
                'error',
                'Belum ada alat yang ditambahkan ke daftar pengajuan.'
            );
        }

        foreach ($items as $item) {
            $alat = Alat::findOrFail($item['alat_id']);

            if ($alat->stok_tersedia < $item['jumlah']) {
                return back()->with(
                    'error',
                    'Stok alat "' . $alat->nama . '" tidak mencukupi.'
                );
            }

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

        // Kosongkan keranjang setelah sukses
        Keranjang::where('user_id', Auth::id())->delete();

        return redirect()
            ->route('mahasiswa.peminjaman.riwayat')
            ->with(
                'success',
                'Peminjaman berhasil diajukan dan sedang menunggu persetujuan.'
            );
    }

    public function tambahPengajuan(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $pengajuan = session()->get('pengajuan', []);

        $pengajuan[] = [
            'alat_id' => $alat->id,
            'nama' => $alat->nama,
            'kode' => $alat->kode,
            'jumlah' => $request->jumlah,
            'stok_max' => $alat->stok_tersedia,
        ];

        session(['pengajuan' => $pengajuan]);

        return redirect()
            ->route('mahasiswa.peminjaman.ajukan')
            ->with('success', 'Alat berhasil ditambahkan.');
    }
}