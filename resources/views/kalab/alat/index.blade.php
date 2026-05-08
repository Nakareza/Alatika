@extends('layouts.kalab')

@section('title', 'Data Alat')

@section('content')

    {{-- Header --}}
    <div class="card p-6 mb-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="inline-flex items-center gap-2 badge badge-info mb-3">
                    <i class="fas fa-laptop"></i>
                    <span>Inventaris Laboratorium</span>
                </div>

                <h1 class="text-2xl font-bold mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Data Alat Laboratorium
                </h1>

                <p class="text-sm text-slate-500">
                    Daftar seluruh alat laboratorium yang tersedia untuk peminjaman.
                </p>

            </div>

            <div class="flex items-center gap-3">

                <div class="list-item flex items-center gap-3">

                    <div class="w-11 h-11 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                        <i class="fas fa-box text-[#185FA5]"></i>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Total Alat</p>
                        <p class="font-bold text-[#1E2B4A]">
                            {{ $alat->count() }} Unit
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

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

    {{-- Table --}}
    <div class="card overflow-hidden">

        {{-- Search --}}
        <div class="p-5 border-b"
             style="border-color:#EBF3FD;background:#F9FBFF;">

            <form method="GET"
                  class="flex flex-col md:flex-row gap-3">

                <div class="relative flex-1">

                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama alat..."
                           class="inp pl-11">

                </div>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-filter"></i>
                    <span>Filter</span>

                </button>

            </form>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr style="background:#F5F8FF;">

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

                    </tr>

                </thead>

                <tbody class="divide-y"
                       style="divide-color:#EBF3FD;">

                    @forelse($alat as $item)

                        <tr class="hover:bg-[#F9FBFF] transition-all duration-200">

                            {{-- Nama --}}
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
                                            {{ $item->kategori ?? 'Elektronika' }}
                                        </p>

                                    </div>

                                </div>

                            </td>

                            {{-- Kode --}}
                            <td class="px-6 py-5">

                                <span class="badge badge-info">
                                    {{ $item->kode_alat ?? '-' }}
                                </span>

                            </td>

                            {{-- Stok --}}
                            <td class="px-6 py-5">

                                <span class="font-semibold text-[#1E2B4A]">
                                    {{ $item->stok ?? 0 }} Unit
                                </span>

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

                                @if(($item->stok ?? 0) > 0)

                                    <span class="badge badge-success">
                                        Tersedia
                                    </span>

                                @else

                                    <span class="badge badge-danger">
                                        Tidak Tersedia
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="py-16 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="w-20 h-20 rounded-2xl bg-[#F5F8FF] flex items-center justify-center mb-4">
                                        <i class="fas fa-box-open text-4xl text-[#B5D4F4]"></i>
                                    </div>

                                    <h3 class="font-bold text-[#1E2B4A] mb-1"
                                        style="font-family:'Plus Jakarta Sans',sans-serif;">
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

            </table>

        </div>

        {{-- Pagination --}}
        @if(method_exists($alat, 'links'))

            <div class="p-5 border-t"
                 style="border-color:#EBF3FD;">

                {{ $alat->links() }}

            </div>

        @endif

    </div>

@endsection