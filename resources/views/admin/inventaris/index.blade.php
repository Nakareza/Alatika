@extends('layouts.admin')

@section('title', 'Data Alat & Tool Set')

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

<div x-data="{ viewMode: 'table', activeTab: '{{ request()->has('toolset_page') ? 'toolset' : 'alat' }}' }" class="mb-6 space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Inventaris Alat & Tool Set Laboratorium
            </h2>
            <p class="text-sm mt-1 text-slate-500">
                Kelola data alat satuan dan paket tool set laboratorium
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center bg-[#F5F8FF] border border-[#D4E6F8] rounded-xl p-1">
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-slate-500'" class="px-3 py-2 rounded-lg text-sm transition-all">
                    <i class="fas fa-list"></i>
                </button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-[#185FA5] shadow-sm' : 'text-slate-500'" class="px-3 py-2 rounded-lg text-sm transition-all">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>

            <a href="{{ route('admin.alat.create') }}" class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Inventaris
            </a>

            <a href="{{ route('admin.alat') }}" class="btn btn-secondary flex items-center gap-2">
                <i class="fas fa-rotate"></i>
                Muat Ulang Data
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500">Total Alat</p>
                <h4 class="text-xl font-bold text-[#1E2B4A]">{{ $stats['total_alat'] }} ({{ $stats['total_stok'] }} unit)</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500">Alat Tersedia</p>
                <h4 class="text-xl font-bold text-emerald-600">{{ $stats['total_tersedia'] }} unit</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500">Total Tool Set</p>
                <h4 class="text-xl font-bold text-[#1E2B4A]">{{ $stats['total_tool_sets'] }} ({{ $stats['total_tool_sets_stok'] }} set)</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                <i class="fas fa-toolbox"></i>
            </div>
        </div>
        <div class="card p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500">Tool Set Tersedia</p>
                <h4 class="text-xl font-bold text-violet-600">{{ $stats['total_tool_sets_tersedia'] }} set</h4>
            </div>
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-600">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-6">
        <form method="GET" action="{{ route('admin.alat') }}">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, kode, lokasi..."
                        class="inp pl-11 w-full">
                </div>

                <div class="md:col-span-2">
                    <select
                        name="kategori"
                        class="inp w-full"
                        onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach(($kategoriOptions ?? []) as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>
                                {{ $kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select
                        name="stok"
                        class="inp w-full"
                        onchange="this.form.submit()">
                        <option value="">Semua Stok</option>
                        <option value="tersedia" {{ request('stok') === 'tersedia' ? 'selected' : '' }}>
                            Stok Tersedia
                        </option>
                        <option value="dipinjam" {{ request('stok') === 'dipinjam' ? 'selected' : '' }}>
                            Sedang Dipinjam
                        </option>
                        <option value="habis" {{ request('stok') === 'habis' ? 'selected' : '' }}>
                            Stok Habis
                        </option>
                    </select>
                </div>

                <div>
                    <a
                        href="{{ route('admin.alat') }}"
                        class="h-full w-full inline-flex items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200">
        <button @click="activeTab = 'alat'" :class="activeTab === 'alat' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-3 font-semibold text-sm transition">
            <i class="fas fa-boxes mr-2"></i> Inventaris Alat Unit
        </button>
        <button @click="activeTab = 'toolset'" :class="activeTab === 'toolset' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-3 font-semibold text-sm transition">
            <i class="fas fa-toolbox mr-2"></i> Tool Set Paket
        </button>
    </div>

    <!-- Tab 1: Inventaris Alat Unit -->
    <div x-show="activeTab === 'alat'" class="space-y-6">
        <div x-show="viewMode === 'table'" class="card overflow-hidden">
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
                    <tbody class="divide-y divide-slate-100">
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
                            <div class="flex flex-col text-sm">
                                <span class="font-semibold text-[#1E2B4A]">
                                    {{ $item->stok_tersedia }}/{{ $item->stok_total }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    tersedia / total
                                </span>
                                @if($item->stok_maintenance > 0)
                                <span class="text-xs text-rose-600 font-medium mt-0.5">
                                    {{ $item->stok_maintenance }} maintenance
                                </span>
                                @endif
                                @php
                                    $dipinjamCount = $item->stok_total - $item->stok_tersedia - $item->stok_maintenance;
                                    $borrowers = $activePeminjaman[$item->id] ?? collect();
                                @endphp
                                @if($dipinjamCount > 0)
                                    <span class="text-xs font-semibold mt-1 text-indigo-600">
                                        <i class="fas fa-hand-holding text-[10px]"></i>
                                        {{ $dipinjamCount }} dipinjam
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $item->lokasi ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status === 'maintenance')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                    Maintenance
                                </span>
                            @elseif($item->stok_tersedia === 0 && $item->stok_total > 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    Dipinjam
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    Tersedia
                                </span>
                            @endif

                            @if($borrowers->isNotEmpty())
                                <div class="mt-2">
                                    <button
                                        type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('open-modal-borrowers-{{ $item->id }}'))"
                                        class="text-xs font-semibold px-2 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition">
                                        <i class="fas fa-users text-[10px] mr-1"></i>
                                        {{ $borrowers->count() }} peminjam
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @php
                                    $isBorrowed = $borrowers->isNotEmpty();
                                @endphp
                                <form action="{{ route('admin.alat.status', $item->id) }}" method="POST" id="status-form-{{ $item->id }}" class="hidden">
                                    @csrf
                                    <input type="hidden" name="status" id="status-val-{{ $item->id }}" value="">
                                </form>

                                <button
                                    type="button"
                                    onclick="confirmStatusChange({{ $item->id }}, '{{ $item->status }}', {{ $isBorrowed ? 'true' : 'false' }})"
                                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition flex items-center justify-center"
                                    title="Ubah Status">
                                    <i class="fas fa-wrench"></i>
                                </button>

                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal-alat-{{ $item->id }}'))"
                                    class="w-10 h-10 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#DDEEFF] transition flex items-center justify-center">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <a href="{{ route('admin.alat.edit', $item->id) }}"
                                class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 hover:bg-amber-200 transition flex items-center justify-center">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="{{ route('admin.alat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200 transition flex items-center justify-center">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($alat as $item)
            <div class="card overflow-hidden group">
                <div class="h-32 bg-[#F5F8FF] flex items-center justify-center border-b border-[#EBF3FD]">
                    <i class="fas fa-boxes text-5xl text-[#378ADD] group-hover:scale-110 transition-all duration-300"></i>
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-[#1E2B4A]">{{ $item->nama }}</h3>
                            <p class="text-sm text-slate-500">{{ $item->kategori }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-lg bg-slate-100 text-slate-600">
                            {{ $item->kode }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-slate-500">Stok</span>
                        <span class="font-semibold text-[#1E2B4A]">{{ $item->stok_tersedia }}/{{ $item->stok_total }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        @if($item->status === 'maintenance')
                            <span class="badge badge-warning">Maintenance</span>
                        @elseif($item->stok_tersedia === 0 && $item->stok_total > 0)
                            <span class="badge badge-danger">Dipinjam</span>
                        @else
                            <span class="badge badge-success">Tersedia</span>
                        @endif

                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-alat-{{ $item->id }}'))"
                            class="text-sm font-semibold text-[#185FA5] hover:underline">
                            Detail
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

    <!-- Tab 2: Tool Set Paket -->
    <div x-show="activeTab === 'toolset'" class="space-y-6">
        <div x-show="viewMode === 'table'" class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#F5F8FF]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">No</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Nama Tool Set</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kode Tool Set</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Stok</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Kondisi</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                            <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($toolSets as $index => $item)
                    <tr class="hover:bg-[#F8FBFF] transition">
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $toolSets->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-[#1E2B4A]">
                                {{ $item->nama_tool_set }}
                            </div>
                            <span class="text-xs text-slate-400">
                                {{ $item->details->count() }} komponen
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $item->kode_tool_set }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col text-sm">
                                <span class="font-semibold text-[#1E2B4A]">
                                    {{ $item->stok_tersedia }}/{{ $item->stok }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    tersedia / total
                                </span>
                                @php
                                    $borrowersToolSet = $activePeminjamanToolSet[$item->id] ?? collect();
                                    $dipinjamToolSetCount = $item->stok - $item->stok_tersedia;
                                @endphp
                                @if($dipinjamToolSetCount > 0)
                                    <span class="text-xs font-semibold mt-1 text-violet-600">
                                        <i class="fas fa-hand-holding text-[10px]"></i>
                                        {{ $dipinjamToolSetCount }} dipinjam
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $kondBadge = $kondisiBadge($item->kondisi);
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $kondBadge['class'] }}">
                                {{ $kondBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">
                            {{ $item->lokasi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal-toolset-detail-{{ $item->id }}'))"
                                    class="w-10 h-10 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#DDEEFF] transition flex items-center justify-center"
                                    title="Lihat Komponen">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <a href="{{ route('admin.toolset.edit', $item->id) }}"
                                class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 hover:bg-amber-200 transition flex items-center justify-center"
                                title="Edit Tool Set">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="{{ route('admin.toolset.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket Tool Set ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200 transition flex items-center justify-center" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-500">
                            Tidak ada data Tool Set
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @forelse($toolSets as $item)
            <div class="card overflow-hidden group">
                <div class="h-32 bg-purple-50 flex items-center justify-center border-b border-[#EBF3FD]">
                    <i class="fas fa-toolbox text-5xl text-purple-600 group-hover:scale-110 transition-all duration-300"></i>
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-[#1E2B4A]">{{ $item->nama_tool_set }}</h3>
                            <p class="text-sm text-slate-500">{{ $item->kategori->nama_kategori ?? '-' }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-lg bg-slate-100 text-slate-600">
                            {{ $item->kode_tool_set }}
                        </span>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-sm">
                        <span class="text-slate-500">Stok</span>
                        <span class="font-semibold text-[#1E2B4A]">{{ $item->stok_tersedia }}/{{ $item->stok }} set</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        @php
                            $kondBadge = $kondisiBadge($item->kondisi);
                        @endphp
                        <span class="badge {{ $kondBadge['class'] }}">{{ $kondBadge['label'] }}</span>

                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-toolset-detail-{{ $item->id }}'))"
                            class="text-sm font-semibold text-[#185FA5] hover:underline">
                            Detail Komponen
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="card p-6 xl:col-span-4 text-center text-slate-500">
                Tidak ada data Tool Set.
            </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $toolSets->links() }}
        </div>
    </div>
</div>

<!-- ==========================================
     MODALS SECTION
     ========================================== -->

<!-- Modals for Alat -->
@foreach($alat as $item)
<x-modal
    name="alat-{{ $item->id }}"
    title="Detail Alat Unit"
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
                <p class="text-xs text-slate-500">Stok Maintenance</p>
                <p>{{ $item->stok_maintenance }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Stok Total</p>
                <p>{{ $item->stok_total }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Kondisi Fisik</p>
                <p>{{ ucfirst($item->kondisi ?? 'baik') }}</p>
            </div>
        </div>

        <div>
            <p class="text-xs text-slate-500 mb-2">Deskripsi / Spesifikasi</p>
            <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-700">
                {!! nl2br(e($item->deskripsi ?: 'Tidak ada deskripsi')) !!}
            </div>
        </div>

        @php
            $borrowers = $activePeminjaman[$item->id] ?? collect();
        @endphp
        @if($borrowers->isNotEmpty())
        <div>
            <p class="text-xs text-slate-500 mb-2">Sedang Dipinjam Oleh</p>
            <div class="bg-indigo-50 rounded-xl p-4 space-y-2">
                @foreach($borrowers as $b)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold bg-[#1E2B4A]">
                            {{ strtoupper(substr($b->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-700">{{ $b->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400">{{ $b->user->nim ?? $b->user->nip ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-indigo-600">{{ $b->jumlah }} unit</p>
                        <p class="text-xs text-slate-400">s/d {{ $b->tanggal_kembali->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <x-slot name="footer">
        <a href="{{ route('admin.alat.edit', $item->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-pen"></i>
            Edit Alat
        </a>
    </x-slot>
</x-modal>
@endforeach

<!-- Borrowers Modal for Alat -->
@foreach($alat as $item)
    @php
        $borrowers = $activePeminjaman[$item->id] ?? collect();
    @endphp
    @if($borrowers->isNotEmpty())
    <x-modal
        name="borrowers-{{ $item->id }}"
        title="Peminjam Aktif - {{ $item->nama }}"
        size="lg">
        <div class="space-y-3">
            <div class="flex items-center gap-2 mb-4 p-3 rounded-xl bg-indigo-50">
                <i class="fas fa-info-circle text-indigo-500"></i>
                <p class="text-sm text-indigo-700">
                    <span class="font-semibold">{{ $borrowers->sum('jumlah') }} unit</span> sedang dipinjam dari total {{ $item->stok_total }} unit.
                </p>
            </div>
            @foreach($borrowers as $b)
            <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:bg-slate-50 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold bg-[#1E2B4A]">
                        {{ strtoupper(substr($b->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $b->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $b->user->nim ?? $b->user->nip ?? '' }} &bull; {{ ucfirst($b->user->role) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-indigo-600">{{ $b->jumlah }} unit</p>
                    <p class="text-xs text-slate-400">Kembali: {{ $b->tanggal_kembali->format('d M Y') }}</p>
                    @if($b->isOverdue())
                        <span class="text-xs font-semibold text-red-500">Terlambat</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <x-slot name="footer">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal-borrowers-{{ $item->id }}'))" class="btn btn-secondary px-4 py-2 rounded-xl text-sm">Tutup</button>
        </x-slot>
    </x-modal>
    @endif
@endforeach

<!-- Modals for ToolSet Components Detail -->
@foreach($toolSets as $item)
<x-modal
    name="toolset-detail-{{ $item->id }}"
    title="Detail Tool Set & Komponen"
    size="lg">
    <div class="space-y-5">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-slate-500">Nama Paket Tool Set</p>
                <p class="font-semibold text-[#1E2B4A]">{{ $item->nama_tool_set }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Kode Tool Set</p>
                <p class="font-semibold">{{ $item->kode_tool_set }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Kategori</p>
                <p>{{ $item->kategori->nama_kategori ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Lokasi Lab</p>
                <p>{{ $item->lokasi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Stok (Tersedia / Total)</p>
                <p>{{ $item->stok_tersedia }} / {{ $item->stok }} set</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Kondisi Paket</p>
                <p>{{ ucfirst($item->kondisi ?? 'baik') }}</p>
            </div>
        </div>

        @if(!empty($item->keterangan))
        <div>
            <p class="text-xs text-slate-500 mb-2">Keterangan / Catatan</p>
            <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-700">
                {{ $item->keterangan }}
            </div>
        </div>
        @endif

        <div>
            <h4 class="font-semibold text-sm text-[#1E2B4A] mb-2 flex items-center gap-2">
                <i class="fas fa-toolbox text-purple-600"></i> Komponen di Dalam Set:
            </h4>
            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Nama Komponen</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Merek / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($item->details as $detail)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-semibold text-[#1E2B4A]">{{ $detail->nama_komponen }}</td>
                            <td class="px-6 py-3 text-slate-700">{{ $detail->jumlah }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $detail->satuan }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $detail->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-slate-400">Tidak ada komponen dalam set ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @php
            $borrowersToolSet = $activePeminjamanToolSet[$item->id] ?? collect();
        @endphp
        @if($borrowersToolSet->isNotEmpty())
        <div>
            <p class="text-xs text-slate-500 mb-2 font-semibold">Sedang Dipinjam Oleh</p>
            <div class="bg-purple-50 rounded-xl p-4 space-y-2">
                @foreach($borrowersToolSet as $b)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold bg-[#1E2B4A]">
                            {{ strtoupper(substr($b->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-700">{{ $b->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-slate-400">{{ $b->user->nim ?? $b->user->nip ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-purple-600">{{ $b->jumlah }} set</p>
                        <p class="text-xs text-slate-400">s/d {{ $b->tanggal_kembali->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <x-slot name="footer">
        <a href="{{ route('admin.toolset.edit', $item->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-pen"></i>
            Edit Tool Set
        </a>
    </x-slot>
</x-modal>
@endforeach

@push('scripts')
<script>
function confirmStatusChange(id, currentStatus, isBorrowed) {
    if (currentStatus === 'tersedia') {
        if (isBorrowed) {
            alert('Alat sedang dipinjam sehingga status tidak dapat diubah menjadi Maintenance.');
            return;
        }
        if (confirm('Apakah Anda yakin ingin mengubah status alat ini menjadi Maintenance?')) {
            document.getElementById('status-val-' + id).value = 'maintenance';
            document.getElementById('status-form-' + id).submit();
        }
    } else {
        if (confirm('Apakah Anda yakin ingin mengubah status alat ini menjadi Tersedia?')) {
            document.getElementById('status-val-' + id).value = 'tersedia';
            document.getElementById('status-form-' + id).submit();
        }
    }
}
</script>
@endpush

@endsection
