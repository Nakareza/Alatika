@extends('layouts.admin')

@section('title', 'Data Inventaris')

@section('content')
@php
    $statusBadge = function ($isBorrowable) {
        return $isBorrowable
            ? ['label' => 'Bisa Dipinjam', 'class' => 'bg-emerald-100 text-emerald-700']
            : ['label' => 'Aset Statis', 'class' => 'bg-slate-100 text-slate-700'];
    };

    $kondisiBadge = function ($kondisi) {
        $kondisi = strtolower((string) $kondisi);

        return match (true) {
            str_contains($kondisi, 'rusak') => ['label' => 'Rusak', 'class' => 'bg-rose-100 text-rose-700'],
            str_contains($kondisi, 'baik') => ['label' => 'Baik', 'class' => 'bg-emerald-100 text-emerald-700'],
            default => ['label' => ucfirst($kondisi ?: 'Unknown'), 'class' => 'bg-amber-100 text-amber-700'],
        };
    };
@endphp

<div x-data="{ viewMode: 'table' }" class="mb-6 space-y-6">

    <div class="flex items-center justify-between flex-wrap gap-4">

        <div>
            <h2 class="text-2xl font-bold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Inventaris Alat Laboratorium
            </h2>
                <p class="text-sm mt-1 text-slate-500">
                Sumber data tunggal untuk alat borrowable dan aset statis
            </p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('admin.alat.create') }}" class="btn btn-primary flex items-center justify-center gap-2 flex-1 sm:flex-none">
                <i class="fas fa-plus-circle text-xs"></i>
                <span>Tambah Alat</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <x-card-stats
            title="Total Inventaris"
            :value="$stats['total_alat']"
            icon="fas fa-boxes"
            color="blue" />

        <x-card-stats
            title="Total Stok"
            :value="$stats['total_stok']"
            icon="fas fa-layer-group"
            color="yellow" />

        <x-card-stats
            title="Sedang Dipinjam"
            :value="$stats['total_dipinjam']"
            icon="fas fa-hand-holding"
            color="indigo" />

        <x-card-stats
            title="Stok Tersedia"
            :value="$stats['total_tersedia']"
            icon="fas fa-check-circle"
            color="green" />

    

    </div>

    <div class="card p-6">

        <form method="GET" action="{{ route('admin.alat') }}">

            <div class="flex flex-col md:flex-row gap-4">

                {{-- Search --}}
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama alat, kode, lokasi..."
                        class="inp pl-11 w-full">
                </div>

                {{-- Kategori --}}
                <select
                    name="kategori"
                    class="inp md:w-56"
                    onchange="this.form.submit()">

                    <option value="">Semua Kategori</option>

                    @foreach($kategoriOptions as $kategori)
                        <option
                            value="{{ $kategori }}"
                            {{ request('kategori') == $kategori ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach

                </select>

                {{-- Stok --}}
                <select
                    name="stok"
                    class="inp md:w-52"
                    onchange="this.form.submit()">

                    <option value="">Semua Stok</option>

                    <option value="tersedia"
                        {{ request('stok') == 'tersedia' ? 'selected' : '' }}>
                        Stok Tersedia
                    </option>

                    <option value="dipinjam"
                        {{ request('stok') == 'dipinjam' ? 'selected' : '' }}>
                        Sedang Dipinjam
                    </option>

                    <option value="habis"
                        {{ request('stok') == 'habis' ? 'selected' : '' }}>
                        Stok Habis
                    </option>

                </select>

                {{-- Reset --}}
                <a
                    href="{{ route('admin.alat') }}"
                    class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">

                    <i class="fas fa-rotate-left"></i>
                </a>

            </div>

        </form>

    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F5F8FF]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">No</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Nama Alat</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kode Barang</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Stok</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($alat as $index => $item)

                <tr class="hover:bg-[#F8FBFF] transition">
                    
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $alat->firstItem() + $index }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-[#1E2B4A]">
                            {{ $item->nama }}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-slate-700">
                        {{ $item->kategori }}
                    </td>

                    <td class="px-6 py-4 text-sm text-slate-700">
                        {{ $item->kode }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-[#1E2B4A]">
                                {{ $item->stok_tersedia }}/{{ $item->stok_total }}
                            </span>

                            <span class="text-xs text-slate-400">
                                tersedia / total
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm text-slate-700">
                        {{ $item->lokasi ?? '-' }}
                    </td>

                    <td class="px-6 py-4">

                        @if($item->status === 'tersedia')

                            <span class="badge badge-success">
                                Tersedia
                            </span>

                        @elseif($item->status === 'maintenance')

                            <span class="badge badge-warning">
                                Maintenance
                            </span>

                        @else

                            <span class="badge badge-info">
                                {{ ucfirst($item->status) }}
                            </span>

                        @endif

                    </td>

                    <td class="px-6 py-4 text-right">

                        <div class="flex justify-end gap-2">

                            <button
                                type="button"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal-alat-{{ $item->id }}'))"
                                class="px-3 py-2 rounded-lg border border-[#D4E6F8] text-sm text-[#185FA5] hover:bg-[#F5F8FF] transition">

                                <i class="fas fa-eye mr-1"></i>
                                Detail
                            </button>

                            <a href="{{ route('admin.alat.edit', $item->id) }}"
                            class="px-3 py-2 rounded-lg bg-amber-500 text-white text-sm hover:bg-amber-600 transition">

                                <i class="fas fa-pen mr-1"></i>
                                Edit
                            </a>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="8" class="text-center py-10 text-slate-500">
                        Tidak ada data alat
                    </td>
                </tr>

                @endforelse

                </tbody>
            </table>
        </div>
    </div>

    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @forelse($alat as $item)
        <div class="card overflow-hidden group">
            <div class="h-32 bg-gradient-to-br from-[#F5F8FF] to-[#EBF3FD] flex items-center justify-center border-b border-[#EBF3FD]">
                <i class="fas fa-boxes text-5xl text-[#378ADD] group-hover:scale-110 transition-all duration-300"></i>
            </div>

            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-[#1E2B4A] leading-tight">
                            {{ $item->nama }}
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ $item->kategori }}
                        </p>
                    </div>

                    @php($status = $statusBadge($item->status))
                    <span class="badge {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>

                <div class="mt-5">
                    <div class="grid grid-cols-2 gap-3 text-sm">

                        <div>
                            <p class="text-slate-500">Kode</p>
                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $item->kode }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-500">Stok</p>
                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $item->stok_tersedia }}/{{ $item->stok_total }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-500">Kategori</p>
                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $item->kategori }}
                            </p>
                        </div>

                        <div>
                            <p class="text-slate-500">Status</p>
                            <p class="font-semibold text-[#1E2B4A]">
                                {{ ucfirst($item->status) }}
                            </p>
                        </div>

                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-5">
                    <button class="px-3 py-2 rounded-lg border border-[#D4E6F8] text-sm text-[#185FA5] font-semibold">
                        Lihat
                    </button>
                </div>
            </div>
        </div>

    @empty
        <div class="card p-6 xl:col-span-4 text-center text-slate-500">
            Tidak ada data alat.
        </div>
    @endforelse
    </div>

    <div class="pt-2">
        {{ $alat->links() }}
    </div>

</div>

@foreach($alat as $item)

<x-modal
    name="alat-{{ $item->id }}"
    title="Detail Inventaris"
    size="lg">

    <div class="space-y-5">

        <div class="grid grid-cols-2 gap-4">

            <div>
                <p class="text-xs text-slate-500">Nama Alat</p>
                <p class="font-semibold">{{ $item->nama }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Kode Barang</p>
                <p class="font-semibold">{{ $item->kode }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Kategori</p>
                <p>{{ $item->kategori }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Lokasi</p>
                <p>{{ $item->lokasi ?? '-' }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Stok Tersedia</p>
                <p>{{ $item->stok_tersedia }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Stok Total</p>
                <p>{{ $item->stok_total }}</p>
            </div>

        </div>

        <div>
            <p class="text-xs text-slate-500 mb-2">Deskripsi</p>

            <div class="bg-slate-50 rounded-xl p-4">
                {{ $item->deskripsi ?: 'Tidak ada deskripsi' }}
            </div>
        </div>

    </div>

    <x-slot name="footer">

        <a href="{{ route('admin.alat.edit', $item->id) }}"
           class="px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition">

            <i class="fas fa-pen mr-1"></i>
            Edit Alat
        </a>

    </x-slot>

</x-modal>

@endforeach

@endsection
