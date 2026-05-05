@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')

    @if(session('success'))
    <div class="mb-4 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background:#F5F8FF;border-bottom:1px solid #EBF3FD;">
                        <th class="py-4 px-6 text-sm font-semibold whitespace-nowrap" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Kode/Tanggal</th>
                        <th class="py-4 px-6 text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Alat</th>
                        <th class="py-4 px-6 text-sm font-semibold whitespace-nowrap" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Deadline / Kembali</th>
                        <th class="py-4 px-6 text-sm font-semibold text-center" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Status</th>
                        <th class="py-4 px-6 text-sm font-semibold text-center" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Aksi / Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $p)
                    <tr class="transition-colors" style="border-bottom:1px solid #EBF3FD;"
                        onmouseover="this.style.background='#F5F8FF'"
                        onmouseout="this.style.background='white'">
                        <td class="py-4 px-6 text-sm">
                            <div class="font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $p->kode_peminjaman }}</div>
                            <div style="color:#94a3b8;">{{ $p->updated_at->format('d M Y H:i') }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-sm" style="color:#1E2B4A;">{{ $p->alat->nama }}</div>
                            <div class="text-xs mt-0.5" style="color:#94a3b8;">{{ $p->jumlah }} unit</div>
                        </td>
                        <td class="py-4 px-6 text-sm">
                            <div class="font-medium" style="color:#1E2B4A;">{{ $p->tanggal_kembali->format('d M Y') }}</div>
                            @if($p->tanggal_dikembalikan && $p->status === 'selesai')
                                <div class="text-xs mt-1" style="color:#059669;">Kembali: {{ $p->tanggal_dikembalikan->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="badge {{ $p->status_config['color'] }}">{{ $p->status_label }}</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-center">
                            @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)
                                <a href="{{ $p->foto_bukti_url }}" target="_blank"
                                   class="text-xs font-semibold hover:opacity-70 transition-opacity"
                                   style="color:#185FA5;">
                                    <i class="fas fa-image mr-1"></i> Lihat Foto Bukti
                                </a>
                            @elseif($p->status === 'dipinjam')
                                <span class="text-xs" style="color:#94a3b8;">
                                    Gunakan <br>
                                    <code class="px-1.5 py-0.5 rounded text-xs font-mono"
                                          style="background:#EBF3FD;color:#185FA5;">
                                        /kembali {{ $p->kode_peminjaman }}
                                    </code><br> di Telegram
                                </span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
                                 style="background:#EBF3FD;">
                                <i class="fas fa-inbox text-xl" style="color:#B5D4F4;"></i>
                            </div>
                            <p class="text-sm font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                Belum ada riwayat peminjaman
                            </p>
                            <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                               class="inline-flex items-center gap-2 mt-2 btn btn-primary text-xs">
                                <i class="fas fa-plus"></i> Ajukan Peminjaman
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection