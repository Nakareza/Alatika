@extends('layouts.admin')

@section('title', 'Data Alat')

@section('content')

<div x-data="{ viewMode: 'table' }" class="space-y-6">

    {{-- Header Action --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h2 class="text-xl font-bold"
                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                Inventaris Alat Laboratorium
            </h2>
            <p class="text-sm mt-1 text-slate-500">
                Kelola seluruh data alat dan komponen laboratorium
            </p>
        </div>

        <div class="flex items-center gap-3">

            {{-- View Mode --}}
            <div class="flex items-center bg-[#F5F8FF] border border-[#D4E6F8] rounded-xl p-1">

                <button
                    @click="viewMode = 'table'"
                    :class="viewMode === 'table'
                        ? 'bg-white text-[#185FA5] shadow-sm'
                        : 'text-slate-500'"
                    class="px-3 py-2 rounded-lg text-sm transition-all">

                    <i class="fas fa-list"></i>
                </button>

                <button
                    @click="viewMode = 'grid'"
                    :class="viewMode === 'grid'
                        ? 'bg-white text-[#185FA5] shadow-sm'
                        : 'text-slate-500'"
                    class="px-3 py-2 rounded-lg text-sm transition-all">

                    <i class="fas fa-th-large"></i>
                </button>

            </div>

            {{-- Button --}}
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Tambah Alat
            </button>

        </div>

    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-[#EBF3FD] flex items-center justify-center">
                    <i class="fas fa-boxes text-[#185FA5] text-lg"></i>
                </div>

                <span class="badge badge-info">
                    Total
                </span>
            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">87</h3>
            <p class="text-sm text-slate-500 mt-1">
                Total Alat
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>

                <span class="badge badge-success">
                    Tersedia
                </span>
            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">63</h3>
            <p class="text-sm text-slate-500 mt-1">
                Alat Tersedia
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding text-indigo-600 text-lg"></i>
                </div>

                <span class="badge"
                      style="background:#EEF2FF;color:#4338CA;">
                    Dipinjam
                </span>
            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">18</h3>
            <p class="text-sm text-slate-500 mt-1">
                Sedang Dipinjam
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-tools text-amber-600 text-lg"></i>
                </div>

                <span class="badge badge-warning">
                    Perbaikan
                </span>
            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">6</h3>
            <p class="text-sm text-slate-500 mt-1">
                Maintenance
            </p>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card p-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input type="text"
                       placeholder="Cari nama alat atau kode..."
                       class="inp pl-11">
            </div>

            <select class="inp">
                <option>Semua Kategori</option>
                <option>Microcontroller</option>
                <option>Sensor & Aktuator</option>
                <option>Lab Equipment</option>
            </select>

            <select class="inp">
                <option>Semua Status</option>
                <option>Tersedia</option>
                <option>Dipinjam</option>
                <option>Maintenance</option>
            </select>

        </div>

    </div>

    @php
        $alat = [
            ['nama' => 'Arduino Uno R3', 'kode' => 'ARD-001', 'kategori' => 'Microcontroller', 'stok_total' => 15, 'tersedia' => 12, 'status' => 'tersedia', 'lokasi' => 'Rak A1'],
            ['nama' => 'ESP32 DevKit V1', 'kode' => 'ESP-015', 'kategori' => 'Microcontroller', 'stok_total' => 20, 'tersedia' => 14, 'status' => 'tersedia', 'lokasi' => 'Rak A2'],
            ['nama' => 'Oscilloscope Digital', 'kode' => 'OSC-005', 'kategori' => 'Lab Equipment', 'stok_total' => 5, 'tersedia' => 3, 'status' => 'dipinjam', 'lokasi' => 'Rak B1'],
            ['nama' => 'Power Supply Digital', 'kode' => 'PWR-007', 'kategori' => 'Lab Equipment', 'stok_total' => 4, 'tersedia' => 0, 'status' => 'maintenance', 'lokasi' => 'Rak B4'],
        ];

        $kategoriIcons = [
            'Microcontroller' => 'fa-microchip',
            'Lab Equipment' => 'fa-flask',
            'Sensor & Aktuator' => 'fa-satellite-dish',
        ];

        $statusConfig = [
            'tersedia' => ['label' => 'Tersedia', 'color' => 'bg-emerald-100 text-emerald-700'],
            'dipinjam' => ['label' => 'Dipinjam', 'color' => 'bg-indigo-100 text-indigo-700'],
            'maintenance' => ['label' => 'Maintenance', 'color' => 'bg-amber-100 text-amber-700'],
        ];
    @endphp

    {{-- TABLE VIEW --}}
    <div x-show="viewMode === 'table'"
         x-transition
         class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-[#F5F8FF] border-b border-[#EBF3FD]">

                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                            Nama Alat
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                            Kategori
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                            Lokasi
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">
                            Stok
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">
                            Aksi
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-[#EBF3FD]">

                    @foreach($alat as $a)

                    <tr class="hover:bg-[#F8FBFF] transition-all">

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-11 h-11 rounded-2xl bg-[#F5F8FF] border border-[#D4E6F8] flex items-center justify-center text-[#185FA5]">
                                    <i class="fas {{ $kategoriIcons[$a['kategori']] }}"></i>
                                </div>

                                <div>
                                    <p class="font-semibold text-[#1E2B4A]">
                                        {{ $a['nama'] }}
                                    </p>

                                    <p class="text-xs text-slate-400 font-mono">
                                        {{ $a['kode'] }}
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-4">
                            <span class="badge badge-info">
                                {{ $a['kategori'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $a['lokasi'] }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex flex-col items-center">

                                <span class="font-bold text-[#1E2B4A]">
                                    {{ $a['tersedia'] }}/{{ $a['stok_total'] }}
                                </span>

                                <div class="w-20 h-2 bg-slate-200 rounded-full mt-2 overflow-hidden">

                                    <div class="h-full rounded-full bg-[#378ADD]"
                                         style="width: {{ ($a['tersedia'] / max($a['stok_total'],1)) * 100 }}%">
                                    </div>

                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-4">

                            @php $sc = $statusConfig[$a['status']]; @endphp

                            <span class="badge {{ $sc['color'] }}">
                                {{ $sc['label'] }}
                            </span>

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center justify-center gap-2">

                                <button class="w-9 h-9 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition-all">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>

                                <button class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    {{-- GRID VIEW --}}
    <div x-show="viewMode === 'grid'"
         x-transition
         class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        @foreach($alat as $a)

        <div class="card overflow-hidden group">

            <div class="h-32 bg-gradient-to-br from-[#F5F8FF] to-[#EBF3FD] flex items-center justify-center border-b border-[#EBF3FD]">

                <i class="fas {{ $kategoriIcons[$a['kategori']] }} text-5xl text-[#378ADD] group-hover:scale-110 transition-all duration-300"></i>

            </div>

            <div class="p-5">

                <div class="flex items-start justify-between">

                    <div>
                        <h3 class="font-bold text-[#1E2B4A]">
                            {{ $a['nama'] }}
                        </h3>

                        <p class="text-xs text-slate-400 font-mono mt-1">
                            {{ $a['kode'] }}
                        </p>
                    </div>

                    @php $sc = $statusConfig[$a['status']]; @endphp

                    <span class="badge {{ $sc['color'] }}">
                        {{ $sc['label'] }}
                    </span>

                </div>

                <div class="mt-5">

                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-slate-500">
                            Stok Tersedia
                        </span>

                        <span class="font-bold text-[#1E2B4A]">
                            {{ $a['tersedia'] }}/{{ $a['stok_total'] }}
                        </span>
                    </div>

                    <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">

                        <div class="h-full bg-[#378ADD] rounded-full"
                             style="width: {{ ($a['tersedia'] / max($a['stok_total'],1)) * 100 }}%">
                        </div>

                    </div>

                </div>

                <div class="flex items-center justify-end gap-2 mt-5">

                    <button class="w-9 h-9 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition-all">
                        <i class="fas fa-edit text-sm"></i>
                    </button>

                    <button class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all">
                        <i class="fas fa-trash text-sm"></i>
                    </button>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection