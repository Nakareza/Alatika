@extends('layouts.kalab')

@section('title', 'Riwayat Peminjaman')

@section('content')

    {{-- Header Card --}}
    <div class="card p-6 lg:p-8 mb-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#EBF3FD] text-[#185FA5] text-sm font-semibold mb-4">
                    <i class="fas fa-history"></i>
                    Riwayat Global
                </div>

                <h1 class="text-2xl lg:text-3xl font-extrabold mb-2"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Riwayat Peminjaman Laboratorium
                </h1>

                <p class="text-sm text-slate-500">
                    Daftar seluruh aktivitas peminjaman alat laboratorium.
                </p>
            </div>

            <div class="hidden lg:flex items-center justify-center w-20 h-20 rounded-2xl bg-[#F5F8FF] border border-[#D4E6F8]">
                <i class="fas fa-clock-rotate-left text-3xl text-[#378ADD]"></i>
            </div>

        </div>

    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden">

        {{-- Table Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b"
             style="border-color:#EBF3FD;">

            <div>

                <h2 class="text-lg font-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Data Riwayat
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Semua data peminjaman yang pernah dilakukan.
                </p>

            </div>

            <div class="badge badge-info">
                <i class="fas fa-database mr-2"></i>
                {{ $peminjaman->count() }} Data
            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-[#F8FBFF] border-b"
                       style="border-color:#EBF3FD;">

                    <tr>

                        <th class="py-4 px-6 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            Peminjam
                        </th>

                        <th class="py-4 px-6 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            Alat / Jumlah
                        </th>

                        <th class="py-4 px-6 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="py-4 px-6 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                            Update Terakhir
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y"
                       style="divide-color:#EBF3FD;">

                    @forelse($peminjaman as $p)

                        <tr class="hover:bg-[#F8FBFF] transition-all duration-200">

                            {{-- User --}}
                            <td class="py-5 px-6">

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-full bg-[#1E2B4A] text-white flex items-center justify-center font-bold text-sm shadow-sm">

                                        {{ strtoupper(substr($p->user->name, 0, 2)) }}

                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-[#1E2B4A]">
                                            {{ $p->user->name }}
                                        </p>

                                        <p class="text-xs text-slate-500 capitalize">
                                            {{ $p->user->role }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- Alat --}}
                            <td class="py-5 px-6">

                                <div>

                                    <p class="text-sm font-semibold text-[#1E2B4A]">
                                        {{ $p->alat->nama }}
                                    </p>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $p->jumlah }} unit
                                    </p>

                                </div>

                            </td>

                            {{-- Status --}}
                            <td class="py-5 px-6">

                                @php
                                    $statusClass = match($p->status) {
                                        'disetujui', 'selesai' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'ditolak' => 'badge-danger',
                                        default => 'badge-info'
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }}">

                                    <i class="fas 
                                        {{ $p->status == 'disetujui' || $p->status == 'selesai' ? 'fa-check-circle' : 
                                           ($p->status == 'ditolak' ? 'fa-times-circle' : 'fa-clock') }} mr-2">
                                    </i>

                                    {{ $p->status_label }}

                                </span>

                            </td>

                            {{-- Updated --}}
                            <td class="py-5 px-6">

                                <div class="flex items-center gap-2 text-sm text-slate-600">

                                    <i class="fas fa-calendar-alt text-[#378ADD]"></i>

                                    {{ $p->updated_at->format('d M Y H:i') }}

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="py-14 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="w-20 h-20 rounded-full bg-[#F5F8FF] flex items-center justify-center mb-4 border border-[#D4E6F8]">

                                        <i class="fas fa-inbox text-3xl text-[#A0BBCC]"></i>

                                    </div>

                                    <h3 class="text-lg font-bold text-[#1E2B4A] mb-1"
                                        style="font-family:'Plus Jakarta Sans',sans-serif;">

                                        Belum Ada Riwayat
                                    </h3>

                                    <p class="text-sm text-slate-500">
                                        Data peminjaman akan muncul di sini.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection