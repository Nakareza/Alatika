@extends('layouts.kalab')

@section('title', 'Laporan')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Laporan & Statistik
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Ringkasan aktivitas peminjaman dosen dan data alat laboratorium
            </p>
        </div>
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">

        <x-card-stats
            title="Total Pengajuan"
            :value="$stats['total_peminjaman']"
            icon="fas fa-clipboard-list"
            color="blue" />

        <x-card-stats
            title="Sedang Dipinjam"
            :value="$stats['total_dipinjam']"
            icon="fas fa-hand-holding"
            color="indigo" />

        <x-card-stats
            title="Menunggu Persetujuan"
            :value="$stats['total_pending']"
            icon="fas fa-clock"
            color="yellow" />

        <x-card-stats
            title="Selesai"
            :value="$stats['total_selesai']"
            icon="fas fa-check-double"
            color="green" />

        <x-card-stats
            title="Keterlambatan"
            :value="$stats['overdue']"
            icon="fas fa-exclamation-triangle"
            color="red" />

    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="badge badge-info">Dosen</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $dosenAktif }}</h3>
            <p class="text-sm text-slate-500 mt-1">Dosen Aktif</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-boxes"></i>
                </div>
                <span class="badge badge-success">Total</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $alatStats['total_alat'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Total Alat</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="badge badge-success">Tersedia</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $alatStats['total_tersedia'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Stok Tersedia</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fas fa-tools"></i>
                </div>
                <span class="badge badge-warning">Maintenance</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">{{ $alatStats['maintenance'] }}</h3>
            <p class="text-sm text-slate-500 mt-1">Alat Maintenance</p>
        </div>

    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Chart Peminjaman Per Bulan --}}
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        Peminjaman Dosen Per Bulan
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">6 bulan terakhir</p>
                </div>
            </div>

            @php
                $maxVal = max(array_column($ringkasanBulanan, 'pengajuan')) ?: 1;
            @endphp

            <div class="flex items-end gap-3 h-56">
                @foreach($ringkasanBulanan as $m)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <span class="text-xs font-bold text-slate-700">{{ $m['pengajuan'] }}</span>
                    <div class="w-full bg-[#D4E6F8] rounded-full relative overflow-hidden" style="height: 100%;">
                        <div class="absolute bottom-0 w-full rounded-full bg-[#378ADD] transition-all duration-500"
                             style="height: {{ $maxVal > 0 ? ($m['pengajuan'] / $maxVal) * 100 : 0 }}%">
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ \Carbon\Carbon::parse($m['bulan'])->translatedFormat('M') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Alat --}}
        <div class="card p-6">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Alat Terpopuler (Dosen)
                </h3>
                <p class="text-sm text-slate-500 mt-1">Berdasarkan frekuensi peminjaman</p>
            </div>

            <div class="space-y-4">
                @forelse($topAlat as $index => $item)
                <div class="list-item flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#378ADD] text-white flex items-center justify-center font-bold text-sm">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-slate-800">{{ $item->alat->nama ?? 'Unknown' }}</h4>
                        <p class="text-xs text-slate-400">{{ $item->alat->kode ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-800">{{ $item->total_pinjam }}x</p>
                        <p class="text-xs text-slate-400">dipinjam</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                    <p class="text-sm">Belum ada data peminjaman dosen</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Ringkasan Bulanan Table --}}
    <div class="card p-6">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Ringkasan Bulanan
            </h3>
            <p class="text-sm text-slate-500 mt-1">Statistik pengajuan peminjaman dosen</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[#EBF3FD]">
                        <th class="text-left py-3 text-xs font-bold text-slate-500 uppercase">Bulan</th>
                        <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">Pengajuan</th>
                        <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">Disetujui</th>
                        <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">Ditolak</th>
                        <th class="text-center py-3 text-xs font-bold text-slate-500 uppercase">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EBF3FD]">
                    @foreach($ringkasanBulanan as $r)
                    <tr class="hover:bg-[#F8FBFF] transition">
                        <td class="py-4 text-sm font-medium text-slate-700">{{ $r['bulan'] }}</td>
                        <td class="py-4 text-center text-sm font-bold text-slate-800">{{ $r['pengajuan'] }}</td>
                        <td class="py-4 text-center">
                            <span class="badge badge-success">{{ $r['disetujui'] }}</span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="badge badge-danger">{{ $r['ditolak'] }}</span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="badge badge-info">{{ $r['selesai'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info --}}
    <div class="card p-6 bg-gradient-to-r from-[#EBF3FD] to-[#F5F8FF]">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-[#378ADD] shadow-sm">
                <i class="fas fa-info-circle text-lg"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Informasi Laporan</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Data laporan menampilkan statistik peminjaman dosen di laboratorium.
                    Diperbarui secara real-time berdasarkan data yang ada di sistem.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
