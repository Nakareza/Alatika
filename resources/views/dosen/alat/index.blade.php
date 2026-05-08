@extends('layouts.dosen')

@section('title', 'Katalog Alat')

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

    {{-- Top Action --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <h2 class="text-2xl font-bold"
                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Katalog Alat
            </h2>

            <p class="text-sm mt-1" style="color:#94A3B8;">
                Pilih alat, tambahkan ke keranjang, lalu ajukan peminjaman
            </p>
        </div>

        <div class="flex items-center gap-3">

            @php
                $cartCount = \App\Models\Keranjang::where('user_id', auth()->id())->count();
            @endphp

            <a href="{{ route('dosen.keranjang') }}"
               class="relative btn btn-secondary">

                <i class="fas fa-shopping-cart"></i>
                Keranjang

                @if($cartCount > 0)
                    <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white"
                          style="background:#EF4444;">
                        {{ $cartCount }}
                    </span>
                @endif

            </a>

            <a href="{{ route('dosen.peminjaman.ajukan') }}"
               class="btn btn-primary">

                <i class="fas fa-plus"></i>
                Pinjam Langsung
            </a>

        </div>

    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        @forelse($alat as $item)

            <div class="card overflow-hidden"
                 x-data="{ open:false, jumlah:1 }">

                {{-- Header Card --}}
                <div class="h-36 flex items-center justify-center border-b"
                     style="border-color:#EBF3FD;
                     background:{{ $item->stok_tersedia > 0 ? '#FFF7ED' : '#F1F5F9' }};">

                    <i class="fas fa-microchip text-5xl"
                       style="color:{{ $item->stok_tersedia > 0 ? '#FB923C' : '#CBD5E1' }};">
                    </i>

                </div>

                {{-- Content --}}
                <div class="p-5">

                    <div class="flex items-start justify-between gap-2 mb-2">

                        <span class="text-[11px] font-bold uppercase tracking-wider"
                              style="color:#F97316;">
                            {{ $item->kategori }}
                        </span>

                        @if($item->stok_tersedia > 0)

                            <span class="badge badge-success">
                                Tersedia
                            </span>

                        @else

                            <span class="badge badge-danger">
                                Habis
                            </span>

                        @endif

                    </div>

                    <h3 class="font-bold text-base leading-snug mb-4"
                        style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                        {{ $item->nama }}

                    </h3>

                    <div class="flex items-center justify-between mb-5">

                        <div>
                            <p class="text-[11px]" style="color:#94A3B8;">
                                Kode
                            </p>

                            <p class="text-sm font-semibold"
                               style="color:#1E2B4A;">
                                {{ $item->kode }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="text-[11px]" style="color:#94A3B8;">
                                Stok
                            </p>

                            <p class="text-sm font-bold"
                               style="color:{{ $item->stok_tersedia > 0 ? '#10B981' : '#EF4444' }};">

                                {{ $item->stok_tersedia }} / {{ $item->stok_total }}

                            </p>
                        </div>

                    </div>

                    {{-- Button --}}
                    @if($item->stok_tersedia > 0)

                        <button @click="open=true"
                                class="w-full btn btn-primary justify-center">

                            <i class="fas fa-cart-plus"></i>
                            Tambah ke Keranjang

                        </button>

                    @else

                        <div class="w-full py-3 rounded-xl text-center text-sm font-semibold"
                             style="background:#F1F5F9;color:#94A3B8;">

                            <i class="fas fa-ban mr-1"></i>
                            Stok Habis

                        </div>

                    @endif

                </div>

                {{-- Modal --}}
                <div x-show="open"
                     x-transition
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     style="background:rgba(30,43,74,0.35);backdrop-filter:blur(4px);"
                     @click.self="open=false">

                    <div class="bg-white rounded-3xl w-full max-w-sm p-6"
                         style="box-shadow:0 20px 60px rgba(30,43,74,0.18);">

                        <div class="flex items-center gap-3 mb-5">

                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                 style="background:#FFF7ED;">

                                <i class="fas fa-microchip text-xl"
                                   style="color:#FB923C;"></i>

                            </div>

                            <div>
                                <h3 class="font-bold text-sm"
                                    style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                    {{ $item->nama }}

                                </h3>

                                <p class="text-xs mt-1" style="color:#94A3B8;">
                                    Stok tersedia:
                                    <span class="font-semibold text-emerald-600">
                                        {{ $item->stok_tersedia }} unit
                                    </span>
                                </p>
                            </div>

                        </div>

                        <label class="form-label">
                            Jumlah Peminjaman
                        </label>

                        <div class="flex items-center gap-3 mb-6">

                            <button type="button"
                                    @click="jumlah = Math.max(1, jumlah - 1)"
                                    class="w-11 h-11 rounded-xl"
                                    style="background:#F5F8FF;">

                                <i class="fas fa-minus text-xs"></i>

                            </button>

                            <input type="number"
                                   x-model="jumlah"
                                   min="1"
                                   max="{{ $item->stok_tersedia }}"
                                   class="inp text-center font-bold">

                            <button type="button"
                                    @click="jumlah = Math.min({{ $item->stok_tersedia }}, jumlah + 1)"
                                    class="w-11 h-11 rounded-xl"
                                    style="background:#F5F8FF;">

                                <i class="fas fa-plus text-xs"></i>

                            </button>

                        </div>

                        <form action="{{ route('dosen.keranjang.add', $item->id) }}"
                              method="POST">

                            @csrf

                            <input type="hidden"
                                   name="jumlah"
                                   :value="jumlah">

                            <div class="flex gap-3">

                                <button type="button"
                                        @click="open=false"
                                        class="flex-1 btn btn-secondary justify-center">

                                    Batal

                                </button>

                                <button type="submit"
                                        class="flex-1 btn btn-primary justify-center">

                                    <i class="fas fa-check"></i>
                                    Tambahkan

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full py-20 text-center">

                <i class="fas fa-box-open text-5xl mb-4"
                   style="color:#CBD5E1;"></i>

                <p class="text-sm" style="color:#94A3B8;">
                    Belum ada data alat tersedia
                </p>

            </div>

        @endforelse

    </div>

@endsection