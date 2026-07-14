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
            ];
        })->values()->all();

        // Ambil semua alat untuk dropdown dengan grouping nama (hanya Alat Khusus)
        $alat = Alat::where('program_studi', 'D3 TI / D4 TRK')
            ->selectRaw('MIN(id) as id, nama, MAX(kode) as kode, MAX(kategori) as kategori, MAX(program_studi) as program_studi, SUM(stok_total) as stok_total, SUM(stok_tersedia) as stok_tersedia, MAX(lokasi) as lokasi, MAX(status) as status, MAX(kondisi) as kondisi')
            ->groupBy('nama')
            ->get();
        
        // Kelompokkan alat by kategori
        $kategori = $alat->groupBy('kategori')->keys();

        // Load keperluan options from config
        $keperluanOptions = static::getKeperluanOptions();

        return view('dosen.peminjaman.ajukan', compact('pengajuan', 'alat', 'kategori', 'keperluanOptions'));
    }

    public function store(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:500',
            'surat_keterangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5000',
        ]);

        $suratKeteranganPath = null;
        if ($request->hasFile('surat_keterangan')) {
            $suratKeteranganPath = $request->file('surat_keterangan')->store('surat_keterangan', 'public');
        }

        foreach ($request->items as $item) {
            $repAlat = Alat::findOrFail($item['alat_id']);

            // Validate that the tool is a special tool
            if ($repAlat->program_studi !== 'D3 TI / D4 TRK') {
                return back()->with(
                    'error',
                    'Dosen hanya diperbolehkan meminjam alat khusus.'
                );
            }

            // Validate total available stock for this tool name
            $totalTersedia = Alat::where('nama', $repAlat->nama)->sum('stok_tersedia');
            if ($totalTersedia < $item['jumlah']) {
                return back()->with(
                    'error',
                    "Stok {$repAlat->nama} tidak mencukupi."
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
                    'user_id' => auth()->id(),
                    'alat_id' => $a->id,
                    'jumlah' => $borrowQty,
                    'tanggal_pinjam' => $request->tanggal_pinjam,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'keperluan' => $request->keperluan,
                    'status' => 'pending',
                    'surat_keterangan' => $suratKeteranganPath,
                    'required_approvals' => ['kalab', 'kaprodi'],
                ]);

                $remaining -= $borrowQty;

                // notif kalab
                $kalabs = User::where('role', 'kalab')
                    ->whereNotNull('telegram_chat_id')
                    ->get();

                foreach ($kalabs as $kalab) {
                    $telegram->notifyNewRequest($kalab, [
                        'peminjam_nama' => auth()->user()->name,
                        'peminjam_role' => 'dosen',
                        'alat' => $a->nama,
                        'jumlah' => $borrowQty,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }

                if ($a->program_studi !== null) {
                    $borrowerProdi = auth()->user()->program_studi;
                    $kaprodis = User::where('role', 'kaprodi')
                        ->whereNotNull('telegram_chat_id')
                        ->get()
                        ->filter(function ($kaprodi) use ($borrowerProdi, $a) {
                            if ($borrowerProdi) {
                                $borrowerShort = str_contains($borrowerProdi, 'D3') ? 'D3' : 'D4';
                                $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                                return $borrowerShort === $kProdiShort;
                            }
                            
                            $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                            return str_contains($a->program_studi, $kProdiShort);
                        });
                    foreach ($kaprodis as $kaprodi) {
                        $telegram->notifyNewRequest($kaprodi, [
                            'peminjam_nama' => auth()->user()->name,
                            'peminjam_role' => 'dosen',
                            'alat' => $a->nama,
                            'jumlah' => $borrowQty,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
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

    public function kembalikan(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', auth()->id())
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
                ->route('dosen.riwayat')
                ->with('success', 'Pengajuan pengembalian berhasil dikirim. Silakan serahkan alat ke laboratorium.');
        }

        return back()->with('error', 'Gagal mengunggah foto bukti pengembalian.');
    }

}
