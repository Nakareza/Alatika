<?php

namespace App\Http\Controllers\Dosen;

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
        $riwayat = auth()->user()->peminjaman()->with('alat')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('dosen.peminjaman.riwayat', compact('riwayat'));
    }

    public function ajukan()
    {
        // Baca dari tabel Keranjang
        $keranjang = Keranjang::with('alat')
            ->where('user_id', auth()->id())
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

        // Ambil semua alat untuk dropdown (jika user ingin menambah lagi)
        $alat = Alat::all();
        
        // Kelompokkan alat by kategori
        $kategori = $alat->groupBy('kategori')->keys();

        // Load keperluan options from config
        $keperluanOptions = $this->getKeperluanOptions();

        return view('dosen.peminjaman.ajukan', compact('pengajuan', 'alat', 'kategori', 'keperluanOptions'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:500',
        ]);

        foreach ($request->items as $item) {

            $alat = Alat::findOrFail($item['alat_id']);

            if ($alat->stok_tersedia < $item['jumlah']) {

                return back()->with(
                    'error',
                    "Stok {$alat->nama} tidak mencukupi."
                );
            }

            $peminjaman = Peminjaman::create([
                'kode_peminjaman' => Peminjaman::generateKode(),
                'user_id' => auth()->id(),
                'alat_id' => $alat->id,
                'jumlah' => $item['jumlah'],
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'keperluan' => $request->keperluan,
                'status' => 'pending'
            ]);

            // notif teknisi
            $teknisi = User::where('role', 'teknisi')
                ->whereNotNull('telegram_chat_id')
                ->get();

            foreach ($teknisi as $user) {

                $telegram->notifyNewRequest($user, [
                    'peminjam_nama' => Auth::user()->name,
                    'peminjam_role' => 'dosen',
                    'alat' => $alat->nama,
                    'jumlah' => $item['jumlah'],
                    'kode' => $peminjaman->kode_peminjaman,
                ]);
            }

            // notif kalab
            $kalabs = User::where('role', 'kalab')
                ->whereNotNull('telegram_chat_id')
                ->get();

            foreach ($kalabs as $kalab) {

                $telegram->notifyNewRequest($kalab, [
                    'peminjam_nama' => auth()->user()->name,
                    'peminjam_role' => 'dosen',
                    'alat' => $alat->nama,
                    'jumlah' => $item['jumlah'],
                    'kode' => $peminjaman->kode_peminjaman,
                ]);
            }
        }

        // Kosongkan keranjang setelah sukses
        Keranjang::where('user_id', auth()->id())->delete();

        return redirect()
            ->route('dosen.riwayat')
            ->with(
                'success',
                'Pengajuan berhasil dikirim ke Teknisi dan Kepala Laboratorium.'
            );
    }

    private function getKeperluanOptions(): array
    {
        $path = storage_path('app/keperluan.json');
        if (!file_exists($path)) {
            return ['Penelitian', 'Tugas Harian', 'Pengabdian', 'Praktikum', 'Perkuliahan'];
        }
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }
}
