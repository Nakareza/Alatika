@extends('layouts.dosen')

@section('title', 'Keranjang Peminjaman')

@section('content')

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-2xl border px-4 py-3"
             style="background:#ECFDF5;border-color:#A7F3D0;color:#065F46;">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 flex items-center gap-2 rounded-2xl border px-4 py-3"
             style="background:#FEF2F2;border-color:#FECACA;color:#991B1B;">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <h2 class="text-2xl font-bold"
                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Keranjang Peminjaman
            </h2>

            <p class="text-sm mt-1" style="color:#94A3B8;">
                Review alat yang akan dipinjam lalu ajukan sekaligus
            </p>
        </div>

        <a href="{{ route('dosen.alat') }}"
           class="btn btn-secondary w-fit">

            <i class="fas fa-arrow-left"></i>
            Tambah Alat Lain

        </a>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Cart Items --}}
        <div class="xl:col-span-2">

            <div class="card overflow-hidden">

                {{-- Header Card --}}
                <div class="px-6 py-5 border-b flex items-center justify-between"
                     style="border-color:#EBF3FD;">

                    <div class="flex items-center gap-2">

                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             style="background:#FFF7ED;">

                            <i class="fas fa-box-open"
                               style="color:#FB923C;"></i>

                        </div>

                        <div>
                            <h3 class="font-bold text-sm"
                                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                Item Peminjaman
                            </h3>

                            <p class="text-xs mt-0.5"
                               style="color:#94A3B8;">
                                {{ count($keranjang) }} alat dipilih
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Items --}}
                @forelse($keranjang as $item)

                    <div class="p-6 border-b transition-all hover:bg-[#FAFCFF]"
                         style="border-color:#F1F5F9;">

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                            {{-- Left --}}
                            <div class="flex items-center gap-4">

                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                                     style="background:#FFF7ED;border:1px solid #FED7AA;">

                                    <i class="fas fa-microchip text-2xl"
                                       style="color:#FB923C;"></i>

                                </div>

                                <div>

                                    <h3 class="font-bold text-base"
                                        style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                        {{ $item->alat->nama }}

                                    </h3>

                                    <div class="flex flex-wrap items-center gap-2 mt-2">

                                        <span class="badge badge-info">
                                            {{ $item->alat->kode }}
                                        </span>

                                        <span class="text-xs"
                                              style="color:#94A3B8;">

                                            Stok tersisa:
                                            <span class="font-semibold text-emerald-600">
                                                {{ $item->alat->stok_tersedia }}
                                            </span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                            {{-- Right --}}
                            <div class="flex items-center gap-6">

                                <div class="text-center">

                                    <p class="text-[11px] uppercase font-semibold tracking-wider mb-1"
                                       style="color:#94A3B8;">

                                        Jumlah

                                    </p>

                                    <div class="flex items-end justify-center gap-1">

                                        <span class="text-2xl font-extrabold"
                                              style="color:#1E2B4A;">

                                            {{ $item->jumlah }}

                                        </span>

                                        <span class="text-xs mb-1"
                                              style="color:#94A3B8;">
                                            unit
                                        </span>

                                    </div>

                                </div>

                                {{-- Delete --}}
                                <form action="{{ route('dosen.keranjang.remove', $item->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="w-11 h-11 rounded-xl flex items-center justify-center transition-all"
                                            style="background:#FEF2F2;color:#EF4444;"
                                            onmouseover="this.style.background='#FEE2E2'"
                                            onmouseout="this.style.background='#FEF2F2'">

                                        <i class="fas fa-trash text-sm"></i>

                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                @empty

                    {{-- Empty --}}
                    <div class="py-20 px-6 text-center">

                        <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-5"
                             style="background:#F8FAFC;">

                            <i class="fas fa-shopping-cart text-4xl"
                               style="color:#CBD5E1;"></i>

                        </div>

                        <h3 class="text-lg font-bold mb-2"
                            style="color:#64748B;font-family:'Plus Jakarta Sans',sans-serif;">

                            Keranjang Masih Kosong

                        </h3>

                        <p class="text-sm mb-6"
                           style="color:#94A3B8;">

                            Belum ada alat yang dipilih untuk dipinjam

                        </p>

                        <a href="{{ route('dosen.alat') }}"
                           class="btn btn-primary">

                            <i class="fas fa-search"></i>
                            Lihat Katalog Alat

                        </a>

                    </div>

                @endforelse

            </div>

        </div>

        {{-- Checkout --}}
        <div class="xl:col-span-1">

            <div class="card p-6 sticky top-24">

                <div class="flex items-center gap-3 mb-6">

                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center"
                         style="background:#ECFDF5;">

                        <i class="fas fa-clipboard-check"
                           style="color:#10B981;"></i>

                    </div>

                    <div>

                        <h3 class="font-bold text-base"
                            style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                            Detail Pengajuan

                        </h3>

                        <p class="text-xs mt-0.5"
                           style="color:#94A3B8;">

                            Semua alat diajukan dalam satu peminjaman

                        </p>

                    </div>

                </div>

                {{-- Summary --}}
                <div class="rounded-2xl p-4 mb-6 flex items-center justify-between"
                     style="background:#FFF7ED;border:1px solid #FED7AA;">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wider"
                           style="color:#FB923C;">

                            Total Jenis Alat

                        </p>

                        <p class="text-sm mt-1"
                           style="color:#94A3B8;">
                            Alat yang dipilih
                        </p>

                    </div>

                    <span class="text-3xl font-extrabold"
                          style="color:#EA580C;">

                        {{ count($keranjang) }}

                    </span>

                </div>

                {{-- Form --}}
                <form action="{{ route('dosen.keranjang.checkout') }}"
                      method="POST">

                    @csrf

                    {{-- Keperluan --}}
                    <div class="mb-5">

                        <label class="form-label">
                            Keperluan Peminjaman
                        </label>

                        <textarea name="keperluan"
                                  rows="4"
                                  class="inp resize-none"
                                  placeholder="Contoh: Penelitian IoT, praktikum mikrokontroler, dsb."
                                  required></textarea>

                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-7">

                        <label class="form-label">
                            Tanggal Kembali
                        </label>

                        <input type="date"
                               name="tanggal_kembali"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                               class="inp"
                               required>

                        <p class="text-xs mt-2 flex items-center gap-1"
                           style="color:#94A3B8;">

                            <i class="fas fa-info-circle"></i>

                            Maksimal 30 hari dari hari ini

                        </p>

                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            {{ count($keranjang) == 0 ? 'disabled' : '' }}
                            class="w-full btn btn-primary justify-center disabled:opacity-50 disabled:cursor-not-allowed">

                        <i class="fas fa-paper-plane"></i>
                        Ajukan Peminjaman

                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection