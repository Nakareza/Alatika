@extends('layouts.kalab')

@section('title', 'Dashboard')

@section('content')

    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="text-sm" style="color:#64748b;">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <x-card-stats
            title="Menunggu Persetujuan" 
            :value="$stats['pending_dosen']" 
            icon="fas fa-clock" 
            color="yellow" />

        <x-card-stats 
            title="Disetujui" 
            :value="$stats['disetujui']" 
            icon="fas fa-check-circle" 
            color="green" />

        <x-card-stats 
            title="Sedang Dipinjam" 
            :value="$stats['dipinjam']" 
            icon="fas fa-hand-holding" 
            color="blue" />

        <x-card-stats 
            title="Total Alat Lab" 
            :value="$stats['total_alat']" 
            icon="fas fa-laptop" 
            color="indigo" />

    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Pending Approval --}}
        <div class="lg:col-span-2">
            <div class="card p-6">

                <div class="flex items-center justify-between mb-5">

                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-[#185FA5]"></i>

                        <h2 class="text-lg font-bold"
                            style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                            Menunggu Persetujuan Anda
                        </h2>
                    </div>

                    <a href="{{ route('kalab.persetujuan') }}"
                       class="text-sm font-semibold transition-colors"
                       style="color:#185FA5;"
                       onmouseover="this.style.color='#1E2B4A'"
                       onmouseout="this.style.color='#185FA5'">
                        Lihat Semua →
                    </a>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Peminjam</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Alat</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Jumlah</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Diajukan</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse($pending_approvals as $peminjaman)

                            <tr class="hover:bg-slate-50 transition-all">

                                {{-- Peminjam --}}
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-full bg-[#185FA5] text-white flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($peminjaman->user->name, 0, 2)) }}
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $peminjaman->user->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ ucfirst($peminjaman->user->role) }}
                                            </p>
                                        </div>

                                    </div>
                                </td>

                                {{-- Nama Alat --}}
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $peminjaman->alat->nama }}
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            {{ $peminjaman->alat->kode }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Jumlah --}}
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                                        style="background:#EBF3FD;color:#185FA5;">
                                        {{ $peminjaman->jumlah }} Unit
                                    </span>
                                </td>

                                {{-- Diajukan --}}
                                <td class="py-4 px-4">
                                    <div>
                                        <p class="text-sm text-slate-700">
                                            {{ $peminjaman->created_at->format('d M Y') }}
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            {{ $peminjaman->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4">
                                    <a href="{{ route('kalab.persetujuan') }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                                    style="background:#EBF3FD;color:#185FA5;">

                                        <i class="fas fa-eye"></i>
                                        Detail

                                    </a>
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="5"
                                    class="py-12 text-center text-slate-500">

                                    <i class="fas fa-inbox text-4xl mb-3 block text-slate-300"></i>

                                    <p class="font-medium">
                                        Tidak ada permintaan persetujuan
                                    </p>

                                    <p class="text-xs mt-1">
                                        Semua pengajuan sudah diproses
                                    </p>

                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

        {{-- Sidebar Right --}}
        <div class="space-y-6">

            {{-- Menu Cepat --}}
            <div class="card p-6">

                <h3 class="text-lg font-bold mb-4" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                    Menu Cepat
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('kalab.persetujuan') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                            <i class="fas fa-clipboard-check text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Persetujuan</p>
                            <p class="text-sm text-slate-400 font-medium">{{ $stats['pending_dosen'] }} menunggu</p>
                        </div>
                    </a>
                    <a href="{{ route('kalab.alat') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                            <i class="fas fa-laptop text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Data Alat</p>
                            <p class="text-sm text-slate-400 font-medium">Lihat inventaris</p>
                        </div>
                    </a>
                    <a href="{{ route('kalab.riwayat') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                            <i class="fas fa-history text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Riwayat</p>
                            <p class="text-sm text-slate-400 font-medium">Semua peminjaman</p>
                        </div>
                    </a>
                    <a href="{{ route('kalab.laporan') }}"
                       class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                            <i class="fas fa-chart-bar text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Laporan</p>
                            <p class="text-sm text-slate-400 font-medium">Statistik lab</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>    

        </div>


@endsection