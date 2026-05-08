@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')

<div class="space-y-6">

    {{-- Header Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Laporan & Statistik
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Ringkasan aktivitas peminjaman dan inventaris laboratorium
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button class="btn btn-secondary">
                <i class="fas fa-file-pdf"></i>
                Export PDF
            </button>

            <button class="btn btn-primary">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-clipboard-list"></i>
                </div>

                <span class="badge badge-info">
                    Total
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">142</h3>
            <p class="text-sm text-slate-500 mt-1">Total Peminjaman</p>

            <div class="mt-3 text-xs font-semibold text-emerald-600">
                <i class="fas fa-arrow-up mr-1"></i>
                +12% dari bulan lalu
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double"></i>
                </div>

                <span class="badge badge-success">
                    Baik
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">94%</h3>
            <p class="text-sm text-slate-500 mt-1">Rate Pengembalian</p>

            <div class="mt-3 text-xs font-semibold text-emerald-600">
                <i class="fas fa-clock mr-1"></i>
                Tepat waktu
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-users"></i>
                </div>

                <span class="badge badge-info">
                    Aktif
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">18</h3>
            <p class="text-sm text-slate-500 mt-1">Peminjam Aktif</p>

            <div class="mt-3 text-xs font-semibold text-blue-600">
                <i class="fas fa-user-graduate mr-1"></i>
                Mahasiswa
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-boxes"></i>
                </div>

                <span class="badge badge-warning">
                    Rata-rata
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">72%</h3>
            <p class="text-sm text-slate-500 mt-1">Utilisasi Alat</p>

            <div class="mt-3 text-xs font-semibold text-amber-600">
                <i class="fas fa-chart-line mr-1"></i>
                Penggunaan alat
            </div>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <span class="badge badge-danger">
                    Warning
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">3</h3>
            <p class="text-sm text-slate-500 mt-1">Keterlambatan</p>

            <div class="mt-3 text-xs font-semibold text-red-600">
                <i class="fas fa-arrow-down mr-1"></i>
                -2 dari bulan lalu
            </div>
        </div>

    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Chart Peminjaman --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800"
                        style="font-family:'Plus Jakarta Sans',sans-serif;">
                        Peminjaman Per Bulan
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Statistik peminjaman tahun 2025
                    </p>
                </div>

                <div class="badge badge-info">
                    Tahun 2025
                </div>
            </div>

            @php
                $months = [
                    ['bulan' => 'Jul', 'jumlah' => 8],
                    ['bulan' => 'Agu', 'jumlah' => 12],
                    ['bulan' => 'Sep', 'jumlah' => 22],
                    ['bulan' => 'Okt', 'jumlah' => 28],
                    ['bulan' => 'Nov', 'jumlah' => 35],
                    ['bulan' => 'Des', 'jumlah' => 37],
                ];

                $maxVal = max(array_column($months, 'jumlah'));
            @endphp

            <div class="flex items-end gap-4 h-64">

                @foreach($months as $m)

                <div class="flex-1 flex flex-col items-center gap-3">

                    <span class="text-xs font-bold text-slate-700">
                        {{ $m['jumlah'] }}
                    </span>

                    <div class="w-full bg-[#D4E6F8] rounded-full relative overflow-hidden"
                         style="height: 100%;">

                        <div class="absolute bottom-0 w-full rounded-full bg-[#378ADD] transition-all duration-500"
                             style="height: {{ ($m['jumlah'] / $maxVal) * 100 }}%">
                        </div>

                    </div>

                    <span class="text-xs font-semibold text-slate-500">
                        {{ $m['bulan'] }}
                    </span>

                </div>

                @endforeach

            </div>

        </div>

        {{-- Distribusi --}}
        <div class="card p-6">

            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Distribusi Kategori Alat
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Berdasarkan jumlah inventaris
                </p>
            </div>

            @php
                $categories = [
                    ['nama' => 'Microcontroller', 'jumlah' => 43, 'persen' => 49],
                    ['nama' => 'Sensor & Aktuator', 'jumlah' => 55, 'persen' => 63],
                    ['nama' => 'Lab Equipment', 'jumlah' => 28, 'persen' => 32],
                    ['nama' => 'Komponen Elektronik', 'jumlah' => 150, 'persen' => 85],
                ];
            @endphp

            <div class="space-y-5">

                @foreach($categories as $c)

                <div>

                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700">
                            {{ $c['nama'] }}
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ $c['jumlah'] }} unit
                        </span>
                    </div>

                    <div class="w-full h-3 bg-[#EBF3FD] rounded-full overflow-hidden">
                        <div class="h-full bg-[#378ADD] rounded-full"
                             style="width: {{ $c['persen'] }}%">
                        </div>
                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- Bottom Section --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Alat Terpopuler --}}
        <div class="card p-6">

            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Alat Terpopuler
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Berdasarkan total peminjaman
                </p>
            </div>

            @php
                $topItems = [
                    ['nama' => 'Arduino Uno R3', 'kode' => 'ARD-001', 'total' => 42],
                    ['nama' => 'ESP32 DevKit V1', 'kode' => 'ESP-015', 'total' => 38],
                    ['nama' => 'Sensor DHT22', 'kode' => 'SNS-022', 'total' => 31],
                    ['nama' => 'Multimeter Digital', 'kode' => 'MUL-012', 'total' => 28],
                    ['nama' => 'Breadboard Set', 'kode' => 'BRD-010', 'total' => 25],
                ];
            @endphp

            <div class="space-y-4">

                @foreach($topItems as $index => $item)

                <div class="list-item flex items-center gap-4">

                    <div class="w-10 h-10 rounded-xl bg-[#378ADD] text-white flex items-center justify-center font-bold">
                        {{ $index + 1 }}
                    </div>

                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-slate-800">
                            {{ $item['nama'] }}
                        </h4>

                        <p class="text-xs text-slate-400 font-mono">
                            {{ $item['kode'] }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800">
                            {{ $item['total'] }}x
                        </p>

                        <p class="text-xs text-slate-400">
                            dipinjam
                        </p>
                    </div>

                </div>

                @endforeach

            </div>

        </div>

        {{-- Ringkasan --}}
        <div class="card p-6">

            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-800"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Ringkasan Bulanan
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Statistik pengajuan peminjaman
                </p>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="border-b border-[#EBF3FD]">
                            <th class="text-left py-3 text-xs font-bold text-slate-500 uppercase">
                                Bulan
                            </th>

                            <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">
                                Pengajuan
                            </th>

                            <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">
                                Disetujui
                            </th>

                            <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">
                                Ditolak
                            </th>

                            <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">
                                Selesai
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EBF3FD]">

                        @php
                            $ringkasan = [
                                ['bulan' => 'Desember 2025', 'pengajuan' => 37, 'disetujui' => 32, 'ditolak' => 3, 'selesai' => 28],
                                ['bulan' => 'November 2025', 'pengajuan' => 35, 'disetujui' => 30, 'ditolak' => 2, 'selesai' => 33],
                                ['bulan' => 'Oktober 2025', 'pengajuan' => 28, 'disetujui' => 25, 'ditolak' => 1, 'selesai' => 24],
                            ];
                        @endphp

                        @foreach($ringkasan as $r)

                        <tr class="hover:bg-[#F8FBFF] transition">

                            <td class="py-4 text-sm font-medium text-slate-700">
                                {{ $r['bulan'] }}
                            </td>

                            <td class="py-4 text-center text-sm font-bold text-slate-800">
                                {{ $r['pengajuan'] }}
                            </td>

                            <td class="py-4 text-center">
                                <span class="badge badge-success">
                                    {{ $r['disetujui'] }}
                                </span>
                            </td>

                            <td class="py-4 text-center">
                                <span class="badge badge-danger">
                                    {{ $r['ditolak'] }}
                                </span>
                            </td>

                            <td class="py-4 text-center">
                                <span class="badge badge-info">
                                    {{ $r['selesai'] }}
                                </span>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Info --}}
    <div class="card p-6 bg-gradient-to-r from-[#EBF3FD] to-[#F5F8FF]">

        <div class="flex items-start gap-4">

            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-[#378ADD] shadow-sm">
                <i class="fas fa-info-circle text-lg"></i>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">
                    Informasi Laporan
                </h3>

                <p class="text-sm text-slate-600 leading-relaxed">
                    Data laporan diperbarui otomatis setiap hari. 
                    Gunakan fitur export untuk mengunduh laporan lengkap dalam format PDF atau Excel.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection