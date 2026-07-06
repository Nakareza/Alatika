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
        
        // Ambil total stok_tersedia per nama alat
        $totalStokTersedia = Alat::selectRaw('nama, SUM(stok_tersedia) as total_tersedia')
            ->groupBy('nama')
            ->pluck('total_tersedia', 'nama')
            ->all();

        // Format data sesuai dengan yang diharapkan view
        $pengajuan = $keranjang->map(function($item) use ($totalStokTersedia) {
            $totalAvail = $totalStokTersedia[$item->alat->nama] ?? 0;
            return [
                'alat_id' => $item->alat_id,
                'nama' => $item->alat->nama,
                'kode' => $item->alat->kode,
                'jumlah' => $item->jumlah,
                'stok_max' => $totalAvail,
                'program_studi' => $item->alat->program_studi,
            ];
        })->values()->all();

        // Ambil semua alat untuk dropdown dengan grouping nama
        $alat = Alat::selectRaw('MIN(id) as id, nama, MAX(kode) as kode, MAX(kategori) as kategori, MAX(program_studi) as program_studi, SUM(stok_total) as stok_total, SUM(stok_tersedia) as stok_tersedia, MAX(lokasi) as lokasi, MAX(status) as status, MAX(kondisi) as kondisi')
            ->groupBy('nama')
            ->get();
        
        // Kelompokkan alat by kategori: exclude special tools from normal categories and push 'Alat Khusus'
        $kategori = $alat->whereNull('program_studi')
            ->pluck('kategori')
            ->push('Alat Khusus')
            ->unique()
            ->filter()
            ->sort()
            ->values();

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

        // Check if any item requires Kaprodi/Kalab approval (special tool)
        $hasKhususItem = false;
        foreach ($items as $item) {
            $alat = Alat::findOrFail($item['alat_id']);
            if ($alat->program_studi !== null) {
                $hasKhususItem = true;
                break;
            }
        }

        // If there is a special tool, validate file input
        $suratKeteranganPath = null;
        if ($hasKhususItem) {
            $request->validate([
                'surat_keterangan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5000',
            ], [
                'surat_keterangan.required' => 'Surat Keterangan wajib dilampirkan untuk pengajuan Alat Khusus.',
                'surat_keterangan.max' => 'Ukuran file Surat Keterangan tidak boleh melebihi 5MB.',
            ]);

            if ($request->hasFile('surat_keterangan')) {
                $suratKeteranganPath = $request->file('surat_keterangan')->store('surat_keterangan', 'public');
            }
        }

        foreach ($items as $item) {
            $repAlat = Alat::findOrFail($item['alat_id']);

            // Validate total available stock for this tool name
            $totalTersedia = Alat::where('nama', $repAlat->nama)->sum('stok_tersedia');
            if ($totalTersedia < $item['jumlah']) {
                return back()->with(
                    'error',
                    'Stok alat "' . $repAlat->nama . '" tidak mencukupi.'
                );
            }

            // Find all available tools with the same name, ordered by ID
            $alatsToBorrow = Alat::where('nama', $repAlat->nama)
                ->where('stok_tersedia', '>', 0)
                ->orderBy('id')
                ->get();

            $remaining = $item['jumlah'];
            foreach ($alatsToBorrow as $a) {
                if ($remaining <= 0) break;
                $borrowQty = min($remaining, $a->stok_tersedia);

                $peminjaman = Peminjaman::create([
                    'kode_peminjaman' => Peminjaman::generateKode(),
                    'user_id' => Auth::id(),
                    'alat_id' => $a->id,
                    'jumlah' => $borrowQty,
                    'tanggal_pinjam' => $request->tanggal_pinjam,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'keperluan' => $request->keperluan,
                    'status' => 'pending',
                    'surat_keterangan' => $suratKeteranganPath,
                ]);

                $remaining -= $borrowQty;

                // Notify Admin
                $admins = User::where('role', 'admin')
                    ->whereNotNull('telegram_chat_id')
                    ->get();

                foreach ($admins as $admin) {
                    $telegram->notifyNewRequest($admin, [
                        'peminjam_nama' => Auth::user()->name,
                        'peminjam_role' => 'mahasiswa',
                        'alat' => $a->nama,
                        'jumlah' => $borrowQty,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }

                // If it is a special tool, also notify Kalab
                if ($a->program_studi !== null) {
                    $kalabs = User::where('role', 'kalab')
                        ->whereNotNull('telegram_chat_id')
                        ->get();
                    foreach ($kalabs as $kalab) {
                        $telegram->notifyNewRequest($kalab, [
                            'peminjam_nama' => Auth::user()->name,
                            'peminjam_role' => 'mahasiswa',
                            'alat' => $a->nama . ' (Alat Khusus)',
                            'jumlah' => $borrowQty,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
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

        $totalTersedia = Alat::where('nama', $alat->nama)->sum('stok_tersedia');

        $pengajuan[] = [
            'alat_id' => $alat->id,
            'nama' => $alat->nama,
            'kode' => $alat->kode,
            'jumlah' => $request->jumlah,
            'stok_max' => $totalTersedia,
        ];

        session(['pengajuan' => $pengajuan]);

        return redirect()
            ->route('mahasiswa.peminjaman.ajukan')
            ->with('success', 'Alat berhasil ditambahkan.');
    }

    public function kembalikan(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['dipinjam', 'disetujui'])
            ->firstOrFail();

        $request->validate([
            'foto_bukti_kembali' => 'required|image|mimes:jpg,jpeg,png|max:5000',
        ]);

        if ($request->hasFile('foto_bukti_kembali')) {
            $file = $request->file('foto_bukti_kembali');
            $fileName = 'bukti-' . strtolower($peminjaman->kode_peminjaman) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $savePath = 'bukti-pengembalian/' . $fileName;

            \Illuminate\Support\Facades\Storage::disk('public')->put($savePath, file_get_contents($file));

            $peminjaman->update([
                'status' => 'menunggu_verifikasi',
                'foto_bukti_kembali' => $savePath,
                'tanggal_dikembalikan' => now(),
            ]);

            return redirect()
                ->route('mahasiswa.peminjaman.riwayat')
                ->with('success', 'Pengajuan pengembalian berhasil dikirim. Silakan serahkan alat ke laboratorium.');
        }

        return back()->with('error', 'Gagal mengunggah foto bukti pengembalian.');
    }
}