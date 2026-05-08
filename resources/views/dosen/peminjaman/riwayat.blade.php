@extends('layouts.dosen')

@section('title', 'Riwayat Peminjaman')

@section('content')

@if(session('success'))
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="space-y-6">

    {{-- Header --}}
    <div class="card p-6">
        <div class="flex items-start justify-between gap-4">

            <div>
                <h2 class="text-xl font-bold mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                    Riwayat Peminjaman
                </h2>

                <p class="text-sm"
                   style="color:#94a3b8;">
                    Daftar semua pengajuan peminjaman alat laboratorium
                </p>
            </div>

            <div class="hidden md:flex w-14 h-14 rounded-2xl items-center justify-center"
                 style="background:#EBF3FD;">
                <i class="fas fa-clock-rotate-left text-xl"
                   style="color:#185FA5;"></i>
            </div>

        </div>
    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                {{-- Table Head --}}
                <thead style="background:#F8FBFF;">

                    <tr class="border-b"
                        style="border-color:#EBF3FD;">

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide whitespace-nowrap"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Kode / Tanggal
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Alat
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide whitespace-nowrap"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Deadline
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Aksi / Info
                        </th>

                    </tr>

                </thead>

                {{-- Table Body --}}
                <tbody>

                    @forelse($riwayat as $p)

                    <tr class="transition-colors hover:bg-[#F8FBFF]"
                        style="border-bottom:1px solid #F1F5F9;">

                        {{-- Kode --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-bold"
                               style="color:#1E2B4A;">
                                {{ $p->kode_peminjaman }}
                            </p>

                            <p class="text-xs mt-1"
                               style="color:#94a3b8;">
                                {{ $p->updated_at->format('d M Y • H:i') }}
                            </p>

                        </td>

                        {{-- Alat --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-semibold"
                               style="color:#1E2B4A;">
                                {{ $p->alat->nama }}
                            </p>

                            <p class="text-xs mt-1"
                               style="color:#94a3b8;">
                                {{ $p->jumlah }} unit
                            </p>

                        </td>

                        {{-- Deadline --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-semibold"
                               style="color:#1E2B4A;">
                                {{ $p->tanggal_kembali->format('d M Y') }}
                            </p>

                            @if($p->tanggal_dikembalikan && $p->status === 'selesai')

                                <p class="text-xs mt-1 text-green-600">
                                    Dikembalikan:
                                    {{ $p->tanggal_dikembalikan->format('d M Y') }}
                                </p>

                            @endif

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-5 text-center">

                            <span class="badge
                                @if($p->status === 'dipinjam') badge-info
                                @elseif($p->status === 'selesai') badge-success
                                @elseif($p->status === 'ditolak') badge-danger
                                @else badge-warning
                                @endif">

                                {{ $p->status_label }}

                            </span>

                        </td>

                        {{-- Action --}}
                        <td class="px-6 py-5 text-center">

                            @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)

                                <a href="{{ $p->foto_bukti_url }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 text-xs font-semibold transition-colors"
                                   style="color:#185FA5;"
                                   onmouseover="this.style.color='#1E2B4A'"
                                   onmouseout="this.style.color='#185FA5'">

                                    <i class="fas fa-image"></i>
                                    Lihat Bukti

                                </a>

                            @elseif($p->status === 'dipinjam')

                                <div class="inline-block rounded-xl px-3 py-2 text-xs"
                                     style="background:#F8FBFF;border:1px solid #EBF3FD;">

                                    <p style="color:#64748B;">
                                        Gunakan perintah:
                                    </p>

                                    <code class="font-bold"
                                          style="color:#EF4444;">
                                        /kembali {{ $p->kode_peminjaman }}
                                    </code>

                                </div>

                            @else

                                <span class="text-xs"
                                      style="color:#CBD5E1;">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="px-6 py-16 text-center">

                            <div class="flex flex-col items-center">

                                <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-4"
                                     style="background:#F8FBFF;border:1px solid #EBF3FD;">

                                    <i class="fas fa-box-open text-3xl"
                                       style="color:#B5D4F4;"></i>

                                </div>

                                <h3 class="text-base font-bold mb-1"
                                    style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                    Belum Ada Riwayat
                                </h3>

                                <p class="text-sm mb-5"
                                   style="color:#94a3b8;">

                                    Kamu belum pernah mengajukan peminjaman alat
                                </p>

                                <a href="{{ route('dosen.peminjaman.ajukan') }}"
                                   class="btn btn-primary">

                                    <i class="fas fa-plus"></i>
                                    Ajukan Peminjaman

                                </a>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection