@extends('layouts.kalab')

@section('title', 'Data Alat')

@section('content')

        {{-- Alert --}}
    @if(session('success'))

        <div class="mb-6 card p-4 border-l-4 border-green-500">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>

                <div>
                    <h4 class="font-semibold text-green-700 text-sm">
                        Berhasil
                    </h4>

                    <p class="text-sm text-green-600">
                        {{ session('success') }}
                    </p>
                </div>

            </div>

        </div>

    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

    <x-card-stats
        title="Total Alat"
        :value="$stats['total']"
        icon="fas fa-boxes"
        color="blue" />

    <x-card-stats
        title="Tersedia"
        :value="$stats['tersedia']"
        icon="fas fa-check-circle"
        color="green" />

    <x-card-stats
        title="Dipinjam"
        :value="$stats['dipinjam']"
        icon="fas fa-hand-holding"
        color="yellow" />

    <x-card-stats
        title="Maintenance"
        :value="$stats['maintenance']"
        icon="fas fa-tools"
        color="red" />

    </div>

    {{-- Filter & Search --}}
    
    <div class="card p-6 mb-6">

        <form method="GET" action="{{ route('kalab.alat') }}">

            <div class="flex flex-col md:flex-row gap-4">

                {{-- Search --}}
                <div class="flex-1 relative">

                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama alat atau kode..."
                        class="inp pl-11 w-full">

                </div>

                {{-- Kategori --}}
                <select
                    name="kategori"
                    class="inp md:w-52"
                    onchange="this.form.submit()">

                    <option value="">Semua Kategori</option>

                    @foreach($kategoriOptions as $kategori)
                        <option value="{{ $kategori }}"
                            {{ request('kategori') == $kategori ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach

                </select>

                {{-- Status --}}
                <select
                    name="status"
                    class="inp md:w-52"
                    onchange="this.form.submit()">

                    <option value="">Semua Status</option>

                    <option value="tersedia"
                        {{ request('status') == 'tersedia' ? 'selected' : '' }}>
                        Tersedia
                    </option>

                    <option value="maintenance"
                        {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                        Maintenance
                    </option>

                    <option value="rusak"
                        {{ request('status') == 'rusak' ? 'selected' : '' }}>
                        Rusak
                    </option>

                </select>

                {{-- Reset --}}
                <a
                    href="{{ route('kalab.alat') }}"
                    class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">

                    <i class="fas fa-rotate-left"></i>

                </a>

            </div>

        </form>

    </div>

    {{-- Table --}}
    <x-table title="Data Alat Laboratorium">

        <thead class="bg-[#F5F8FF]">
            <tr>
                <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                    No
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    Alat
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    Kode
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    Stok
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    Kondisi
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    Status
                </th>

                <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                    Aksi
                </th>
            </tr>
        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($alat as $index => $item)

            <tr class="hover:bg-[#F9FBFF] transition-all duration-200">

                {{-- Nomor --}}
                <td class="px-6 py-5 text-center text-sm font-semibold text-slate-500">
                    {{ $alat->firstItem() + $index }}
                </td>

                {{-- Nama Alat --}}
                <td class="px-6 py-5">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-xl bg-[#1E2B4A] text-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-microchip"></i>
                        </div>

                        <div>

                            <h3 class="font-semibold text-sm text-[#1E2B4A]">
                                {{ $item->nama }}
                            </h3>

                            <p class="text-xs text-slate-500">
                                {{ $item->kategori ?? '-' }}
                            </p>

                        </div>

                    </div>

                </td>

                {{-- Kode --}}
                <td class="px-6 py-5">

                    <span class="badge badge-info">
                        {{ $item->kode ?? '-' }}
                    </span>

                </td>

                {{-- Stok --}}
                <td class="px-6 py-5">

                    <div class="flex flex-col">

                        <span class="font-semibold text-[#1E2B4A]">
                            {{ $item->stok_tersedia }}/{{ $item->stok_total }}
                        </span>

                        <span class="text-xs text-slate-400">
                            tersedia / total
                        </span>

                    </div>

                </td>

                {{-- Kondisi --}}
                <td class="px-6 py-5">

                    @if(($item->kondisi ?? '') == 'baik')

                        <span class="badge badge-success">
                            Baik
                        </span>

                    @elseif(($item->kondisi ?? '') == 'rusak')

                        <span class="badge badge-danger">
                            Rusak
                        </span>

                    @else

                        <span class="badge badge-warning">
                            Perlu Pengecekan
                        </span>

                    @endif

                </td>

                {{-- Status --}}
                <td class="px-6 py-5">

                    @if($item->status == 'tersedia')

                        <span class="badge badge-success">
                            Tersedia
                        </span>

                    @elseif($item->status == 'maintenance')

                        <span class="badge badge-warning">
                            Maintenance
                        </span>

                    @else

                        <span class="badge badge-info">
                            {{ ucfirst($item->status) }}
                        </span>

                    @endif

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5">

                    <div class="flex justify-center gap-2">

                        {{-- Detail --}}
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-detail-{{ $item->id }}'))"
                            class="w-10 h-10 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#DDEEFF] transition flex items-center justify-center">

                            <i class="fas fa-eye"></i>

                        </button>

                        {{-- Edit --}}
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-edit-{{ $item->id }}'))"
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 hover:bg-amber-200 transition flex items-center justify-center">

                            <i class="fas fa-pen"></i>

                        </button>

                    </div>

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7" class="py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div class="w-20 h-20 rounded-2xl bg-[#F5F8FF] flex items-center justify-center mb-4">
                            <i class="fas fa-box-open text-4xl text-[#B5D4F4]"></i>
                        </div>

                        <h3 class="font-bold text-[#1E2B4A] mb-1">
                            Data Alat Kosong
                        </h3>

                        <p class="text-sm text-slate-500">
                            Belum ada data alat laboratorium.
                        </p>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>
    <x-pagination :data="$alat" />

@foreach($alat as $item)

@push('modals')
<x-modal
    name="detail-{{ $item->id }}"
    title="Detail Alat"
    size="lg">

    <div class="space-y-5">

        <div class="grid grid-cols-2 gap-5">

            <div>
                <p class="text-xs text-slate-500">Nama Alat</p>
                <p class="font-semibold">{{ $item->nama }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Kode</p>
                <p class="font-semibold">{{ $item->kode }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Kategori</p>
                <p class="font-semibold">{{ $item->kategori }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Stok Total</p>
                <p class="font-semibold">{{ $item->stok_total }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Stok Tersedia</p>
                <p class="font-semibold">{{ $item->stok_tersedia }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Status</p>
                <p class="font-semibold">{{ ucfirst($item->status) }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">Kondisi</p>
                <p class="font-semibold">{{ $item->kondisi ?? '-' }}</p>
            </div>

        </div>

    </div>

    <x-slot:footer>

        <button
            type="button"
            onclick="window.dispatchEvent(new CustomEvent('close-modal-detail-{{ $item->id }}'))"
            class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200">

            Tutup

        </button>

    </x-slot>

</x-modal>

<x-modal
    name="edit-{{ $item->id }}"
    title="Edit Data Alat"
    size="lg">

    <form
        action="{{ route('kalab.alat.update',$item->id) }}"
        method="POST"
        id="form-edit-{{ $item->id }}"
        class="space-y-4">

        @csrf
        @method('PUT')

        <div>

            <label class="text-sm font-medium">
                Nama Alat
            </label>

            <input
                type="text"
                name="nama"
                value="{{ $item->nama }}"
                class="inp w-full">

        </div>

        <div class="grid grid-cols-2 gap-4">

            <div>

                <label class="text-sm font-medium">

                    Kategori

                </label>

                <input
                    type="text"
                    name="kategori"
                    value="{{ $item->kategori }}"
                    class="inp w-full">

            </div>

        </div>

        <div class="grid grid-cols-2 gap-4">

            <div>

                <label class="text-sm font-medium">

                    Stok Total

                </label>

                <input
                    type="number"
                    name="stok_total"
                    value="{{ $item->stok_total }}"
                    class="inp w-full">

            </div>

            <div>

                <label class="text-sm font-medium">

                    Stok Tersedia

                </label>

                <input
                    type="number"
                    name="stok_tersedia"
                    value="{{ $item->stok_tersedia }}"
                    class="inp w-full">

            </div>

        </div>

        <div>
            <label class="text-sm font-medium">
                Kondisi Alat
            </label>

            <select
                name="kondisi"
                class="inp w-full">

                <option value="baik"
                    {{ $item->kondisi == 'baik' ? 'selected' : '' }}>
                    Baik
                </option>

                <option value="rusak"
                    {{ $item->kondisi == 'rusak' ? 'selected' : '' }}>
                    Rusak
                </option>

                <option value="perlu_pengecekan"
                    {{ $item->kondisi == 'perlu_pengecekan' ? 'selected' : '' }}>
                    Perlu Pengecekan
                </option>
            </select>
        </div>

        <div>
            <label class="text-sm font-medium">
                Status Saat Ini
            </label>

            <div class="mt-2 p-3 rounded-xl bg-slate-50 border">

                @if($item->kondisi == 'hilang')

                    <span class="badge badge-danger">
                        Hilang
                    </span>

                @elseif($item->kondisi == 'maintenance')

                    <span class="badge badge-warning">
                        Maintenance
                    </span>

                @elseif($item->stok_tersedia == 0)

                    <span class="badge badge-info">
                        Dipinjam
                    </span>

                @else

                    <span class="badge badge-success">
                        Tersedia
                    </span>

                @endif

            </div>
        </div>

        <x-slot:footer>

            <button
                type="button"
                onclick="window.dispatchEvent(new CustomEvent('close-modal-edit-{{ $item->id }}'))"
                class="flex-1 py-3 rounded-xl bg-slate-100">

                Batal

            </button>

            <button
                type="submit"
                form="form-edit-{{ $item->id }}"
                class="flex-1 btn btn-primary">

                Simpan Perubahan

            </button>

        </x-slot>

    </form>

</x-modal>

@endpush
@endforeach


@endsection