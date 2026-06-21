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
                Ringkasan aktivitas peminjaman dan data alat laboratorium
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

            <h3 class="text-3xl font-bold text-slate-800">{{ $stats['total_peminjaman'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Total Peminjaman</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-check-double"></i>
                </div>

                <span class="badge badge-success">
                    Selesai
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">{{ $stats['total_selesai'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Peminjaman Selesai</p>
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

            <h3 class="text-3xl font-bold text-slate-800">{{ $mahasiswaAktif }}</h3>
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
                    Dipinjam
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">{{ $stats['total_dipinjam'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Sedang Dipinjam</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-red-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <span class="badge badge-danger">
                    Overdue
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">{{ $stats['overdue'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Keterlambatan</p>
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
                $months = collect($ringkasanBulanan)->map(function($r) {
                    return [
                        'bulan' => \Carbon\Carbon::parse($r['bulan'])->translatedFormat('M'),
                        'jumlah' => $r['pengajuan'],
                    ];
                })->values()->all();

                $maxVal = max(array_column($months, 'jumlah')) ?: 1;
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
                    Berdasarkan jumlah alat
                </p>
            </div>

            @php
                $maxKategori = $kategoriDistribusi->max('jumlah') ?: 1;
            @endphp

            <div class="space-y-5">

                @forelse($kategoriDistribusi as $c)

                <div>

                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700">
                            {{ $c->kategori }}
                        </span>

                        <span class="text-sm font-bold text-slate-800">
                            {{ $c->jumlah }} unit
                        </span>
                    </div>

                    <div class="w-full h-3 bg-[#EBF3FD] rounded-full overflow-hidden">
                        <div class="h-full bg-[#378ADD] rounded-full"
                             style="width: {{ ($c->jumlah / $maxKategori) * 100 }}%">
                        </div>
                    </div>

                </div>

                @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada data kategori alat.</p>
                @endforelse

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

            <div class="space-y-4">

                @forelse($topAlat as $index => $item)

                <div class="list-item flex items-center gap-4">

                    <div class="w-10 h-10 rounded-xl bg-[#378ADD] text-white flex items-center justify-center font-bold">
                        {{ $index + 1 }}
                    </div>

                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-slate-800">
                            {{ $item->alat->nama ?? '—' }}
                        </h4>

                        <p class="text-xs text-slate-400 font-mono">
                            {{ $item->alat->kode ?? '—' }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800">
                            {{ $item->total_pinjam }}x
                        </p>

                        <p class="text-xs text-slate-400">
                            dipinjam
                        </p>
                    </div>

                </div>

                @empty
                <p class="text-sm text-slate-400 text-center py-4">Belum ada data peminjaman.</p>
                @endforelse

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

                        @forelse($ringkasanBulanan as $r)

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

                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-slate-400">
                                Belum ada data peminjaman bulanan.
                            </td>
                        </tr>
                        @endforelse

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