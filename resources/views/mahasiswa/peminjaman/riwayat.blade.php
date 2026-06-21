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

    <x-table title="Riwayat Peminjaman">

        <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">

            <tr>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Kode / Tanggal
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Alat
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Deadline / Kembali
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Status
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Aksi / Info
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($riwayat as $p)

            <tr class="hover:bg-[#F8FBFF] transition">

                {{-- Kode --}}
                <td class="px-6 py-5">

                    <div class="font-bold text-[#1E2B4A]">
                        {{ $p->kode_peminjaman }}
                    </div>

                    <div class="text-xs text-slate-400 mt-1">
                        {{ $p->updated_at->format('d M Y H:i') }}
                    </div>

                </td>

                {{-- Alat --}}
                <td class="px-6 py-5">

                    <div class="font-semibold text-[#1E2B4A]">
                        {{ $p->alat->nama }}
                    </div>

                    <div class="text-xs text-slate-500 mt-1">
                        {{ $p->jumlah }} Unit
                    </div>

                </td>

                {{-- Deadline --}}
                <td class="px-6 py-5">

                    <div class="font-medium text-[#1E2B4A]">
                        {{ $p->tanggal_kembali->format('d M Y') }}
                    </div>

                    @if($p->tanggal_dikembalikan && $p->status === 'selesai')

                        <div class="text-xs text-emerald-600 mt-1">
                            Kembali:
                            {{ $p->tanggal_dikembalikan->format('d M Y') }}
                        </div>

                    @endif

                </td>

                {{-- Status --}}
                <td class="px-6 py-5 text-center">

                    <span class="badge {{ $p->status_config['color'] }}">
                        {{ $p->status_label }}
                    </span>

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5 text-center">

                    @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)

                        <a href="{{ $p->foto_bukti_url }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 text-[#185FA5] hover:text-[#378ADD] font-semibold text-sm transition">

                            <i class="fas fa-image"></i>

                            Lihat Foto Bukti

                        </a>

                    @elseif($p->status === 'dipinjam')

                        <div class="flex flex-col items-center gap-1 text-xs">

                            <span class="text-slate-500">
                                Gunakan
                            </span>

                            <code class="px-2 py-1 rounded-lg bg-[#EBF3FD] text-[#185FA5] font-mono">
                                /kembali {{ $p->kode_peminjaman }}
                            </code>

                            <span class="text-slate-500">
                                di Telegram
                            </span>

                        </div>

                    @else

                        <span class="text-slate-300 text-lg">
                            —
                        </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5" class="py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div class="w-16 h-16 rounded-full bg-[#EBF3FD] flex items-center justify-center mb-4">

                            <i class="fas fa-inbox text-2xl text-[#B5D4F4]"></i>

                        </div>

                        <h3 class="font-bold text-lg text-[#1E2B4A]">

                            Belum Ada Riwayat Peminjaman

                        </h3>

                        <p class="text-slate-500 mt-1">

                            Silakan ajukan peminjaman alat terlebih dahulu.

                        </p>

                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                        class="btn btn-primary mt-5">

                            <i class="fas fa-plus"></i>

                            Ajukan Peminjaman

                        </a>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>

@endsection