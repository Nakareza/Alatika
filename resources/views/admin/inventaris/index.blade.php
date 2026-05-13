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

<div x-data="{ viewMode: 'table' }" class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h2 class="text-xl font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Inventaris Alat Laboratorium
            </h2>
            <p class="text-sm mt-1 text-slate-500">
                Sumber data tunggal untuk alat borrowable dan aset statis
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

            <a href="{{ route('admin.alat') }}" class="btn btn-primary">
                <i class="fas fa-rotate"></i>
                Muat Ulang Data
            </a>

        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-[#EBF3FD] flex items-center justify-center">
                    <i class="fas fa-boxes text-[#185FA5] text-lg"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-[#1E2B4A]">{{ number_format($stats['total_item'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Total Item Inventaris</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding text-green-600 text-lg"></i>
                </div>
                <span class="badge badge-success">Borrowable</span>
            </div>
            <h3 class="text-3xl font-bold text-[#1E2B4A]">{{ number_format($stats['total_borrowable'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Bisa Dipinjam</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-building text-slate-600 text-lg"></i>
                </div>
                <span class="badge" style="background:#EEF2FF;color:#4338CA;">Statis</span>
            </div>
            <h3 class="text-3xl font-bold text-[#1E2B4A]">{{ number_format($stats['total_non_borrowable'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Aset Statis</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-layer-group text-amber-600 text-lg"></i>
                </div>
                <span class="badge badge-warning">Stok</span>
            </div>
            <h3 class="text-3xl font-bold text-[#1E2B4A]">{{ number_format($stats['total_stok'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Total Stok</p>
        </div>
    </div>

    <div class="card p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text"
                       placeholder="Cari nama alat, kode, lokasi, atau kondisi..."
                       class="inp pl-11"
                       value="{{ request('search') }}"
                       onchange="const url = new URL(window.location.href); if (this.value) { url.searchParams.set('search', this.value); } else { url.searchParams.delete('search'); } window.location = url.toString();">
            </div>

            <select class="inp" onchange="if(this.value===''){window.location='{{ route('admin.alat') }}'}else{const url=new URL(window.location.href);url.searchParams.set('kategori',this.value);window.location=url.toString();}">
                <option value="">Semua Kategori</option>
                @foreach(($kategoriOptions ?? []) as $kategori)
                    <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                @endforeach
            </select>

            <select class="inp" onchange="if(this.value===''){window.location='{{ route('admin.alat') }}'}else{const url=new URL(window.location.href);url.searchParams.set('is_borrowable',this.value);window.location=url.toString();}">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_borrowable') === '1' ? 'selected' : '' }}>Bisa Dipinjam</option>
                <option value="0" {{ request('is_borrowable') === '0' ? 'selected' : '' }}>Aset Statis</option>
            </select>
        </div>
    </div>

    <div x-show="viewMode === 'table'" x-transition class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F5F8FF] border-b border-[#EBF3FD]">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Alat</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kode Barang</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Lokasi</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tahun</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kondisi</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EBF3FD]">
                    @forelse($inventaris as $item)
                        <tr class="hover:bg-[#F8FBFF] transition-all">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-[#1E2B4A]">{{ $item->nama_alat }}</div>
                                @if(!empty($item->perlengkapan_detail))
                                    <div class="text-xs text-slate-500 mt-1">{{ is_array($item->perlengkapan_detail) ? implode(', ', $item->perlengkapan_detail) : $item->perlengkapan_detail }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->kategori }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->kode_barang ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->jumlah_stok }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->lokasi_simpan ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-700">{{ $item->tahun_perolehan ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php($kondisi = $kondisiBadge($item->kondisi))
                                <span class="badge {{ $kondisi['class'] }}">{{ $kondisi['label'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php($status = $statusBadge($item->is_borrowable))
                                <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-sm text-[#185FA5] font-semibold">Detail</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-slate-500">Tidak ada data inventaris yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @forelse($inventaris as $item)
            <div class="card overflow-hidden group">
                <div class="h-32 bg-gradient-to-br from-[#F5F8FF] to-[#EBF3FD] flex items-center justify-center border-b border-[#EBF3FD]">
                    <i class="fas fa-boxes text-5xl text-[#378ADD] group-hover:scale-110 transition-all duration-300"></i>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-[#1E2B4A] leading-tight">{{ $item->nama_alat }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ $item->kategori }}</p>
                        </div>

                        @php($status = $statusBadge($item->is_borrowable))
                        <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </div>

                    <div class="mt-5">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-slate-500">Kode</p>
                                <p class="font-semibold text-[#1E2B4A]">{{ $item->kode_barang ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Stok</p>
                                <p class="font-semibold text-[#1E2B4A]">{{ $item->jumlah_stok }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Lokasi</p>
                                <p class="font-semibold text-[#1E2B4A]">{{ $item->lokasi_simpan ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Tahun</p>
                                <p class="font-semibold text-[#1E2B4A]">{{ $item->tahun_perolehan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 mt-5">
                        <button class="px-3 py-2 rounded-lg border border-[#D4E6F8] text-sm text-[#185FA5] font-semibold">Lihat</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-6 xl:col-span-4 text-center text-slate-500">
                Tidak ada data inventaris yang cocok dengan filter.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $inventaris->links() }}
    </div>

</div>

@endsection
