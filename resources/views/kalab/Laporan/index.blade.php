
@extends('layouts.kalab')

@section('title', 'Laporan')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>

            <h2 class="text-2xl font-bold"
                style="font-family:'Plus Jakarta Sans',sans-serif;">

                Laporan & Statistik

            </h2>

            <p class="text-sm text-slate-500 mt-1">

                Ringkasan aktivitas peminjaman dan inventaris laboratorium

            </p>

        </div>

        <div class="flex items-center gap-3">

            <a href="#"
               class="btn btn-secondary">

                <i class="fas fa-file-pdf"></i>

                Export PDF

            </a>

            <a href="#"
               class="btn btn-primary">

                <i class="fas fa-file-excel"></i>

                Export Excel

            </a>

        </div>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">

        {{-- Total Peminjaman --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-5">

                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">

                    <i class="fas fa-clipboard-list"></i>

                </div>

                <span class="badge badge-info">

                    Total

                </span>

            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">

                {{ $stats['total'] }}

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Total Peminjaman

            </p>

        </div>

        {{-- Sedang Dipinjam --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-5">

                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600">

                    <i class="fas fa-box-open"></i>

                </div>

                <span class="badge badge-warning">

                    Aktif

                </span>

            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">

                {{ $stats['dipinjam'] }}

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Sedang Dipinjam

            </p>

        </div>

        {{-- Selesai --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-5">

                <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">

                    <i class="fas fa-check-double"></i>

                </div>

                <span class="badge badge-success">

                    Selesai

                </span>

            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">

                {{ $stats['selesai'] }}

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Pengembalian Selesai

            </p>

        </div>

        {{-- Ditolak --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-5">

                <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center text-red-600">

                    <i class="fas fa-times-circle"></i>

                </div>

                <span class="badge badge-danger">

                    Ditolak

                </span>

            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">

                {{ $stats['ditolak'] }}

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Pengajuan Ditolak

            </p>

        </div>

        {{-- Total Inventaris --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-5">

                <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600">

                    <i class="fas fa-microchip"></i>

                </div>

                <span class="badge badge-info">

                    Inventaris

                </span>

            </div>

            <h3 class="text-3xl font-bold text-[#1E2B4A]">

                {{ $stats['alat'] }}

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Total Alat Laboratorium

            </p>

        </div>

    </div>
    
    {{-- Grafik & Distribusi --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Grafik Peminjaman --}}
        <div class="card p-6">

            <div class="flex items-center justify-between mb-6">

                <div>

                    <h3 class="text-lg font-bold text-[#1E2B4A]"
                        style="font-family:'Plus Jakarta Sans',sans-serif;">

                        Grafik Peminjaman

                    </h3>

                    <p class="text-sm text-slate-500 mt-1">

                        Statistik peminjaman setiap bulan

                    </p>

                </div>

                <span class="badge badge-info">

                    {{ date('Y') }}

                </span>

            </div>

            @php
                $max = $grafik->max('total') ?: 1;
            @endphp

            <div class="flex items-end gap-4 h-72">

                @foreach($grafik as $item)

                <div class="flex-1 flex flex-col items-center gap-3">

                    <span class="text-xs font-bold text-[#1E2B4A]">

                        {{ $item->total }}

                    </span>

                    <div
                        class="w-full h-full bg-[#EBF3FD] rounded-full overflow-hidden relative">

                        <div
                            class="absolute bottom-0 left-0 w-full bg-[#378ADD] rounded-full transition-all duration-700"
                            style="height:{{ ($item->total/$max)*100 }}%">

                        </div>

                    </div>

                    <span class="text-xs font-semibold text-slate-500">

                        {{ \Carbon\Carbon::create()->month($item->bulan)->translatedFormat('M') }}

                    </span>

                </div>

                @endforeach

            </div>

        </div>

        {{-- Distribusi Status --}}
        <div class="card p-6">

            <div class="mb-6">

                <h3 class="text-lg font-bold text-[#1E2B4A]"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Distribusi Status

                </h3>

                <p class="text-sm text-slate-500 mt-1">

                    Persentase status peminjaman saat ini

                </p>

            </div>

            @php

                $total =
                    $statusDistribusi->sum('total') ?: 1;

            @endphp

            <div class="space-y-6">

                @foreach($statusDistribusi as $status)

                @php

                    $persen = round(($status->total / $total) * 100);

                    $warna = match($status->status){

                        'pending' => '#F59E0B',

                        'dipinjam' => '#378ADD',

                        'selesai' => '#10B981',

                        'ditolak' => '#EF4444',

                        default => '#94A3B8'

                    };

                @endphp

                <div>

                    <div class="flex justify-between items-center mb-2">

                        <div>

                            <span class="font-semibold text-[#1E2B4A]">

                                {{ ucfirst($status->status) }}

                            </span>

                        </div>

                        <div class="text-sm font-bold text-slate-700">

                            {{ $status->total }}

                            <span class="text-slate-400">

                                ({{ $persen }}%)

                            </span>

                        </div>

                    </div>

                    <div class="w-full h-3 rounded-full bg-[#EBF3FD] overflow-hidden">

                        <div
                            class="h-full rounded-full transition-all duration-700"
                            style="width:{{ $persen }}%;background:{{ $warna }};">

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

            <div class="grid grid-cols-2 gap-4 mt-8">

                <div class="bg-[#F8FBFF] rounded-2xl p-4 text-center">

                    <p class="text-xs text-slate-500">

                        Total Status

                    </p>

                    <h3 class="text-2xl font-bold text-[#1E2B4A] mt-1">

                        {{ $total }}

                    </h3>

                </div>

                <div class="bg-[#F8FBFF] rounded-2xl p-4 text-center">

                    <p class="text-xs text-slate-500">

                        Status Aktif

                    </p>

                    <h3 class="text-2xl font-bold text-[#378ADD] mt-1">

                        {{ $stats['dipinjam'] }}

                    </h3>

                </div>

            </div>

        </div>

    </div>


</div>

@endsection

