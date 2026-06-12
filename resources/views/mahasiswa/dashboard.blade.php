@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="text-sm" style="color:#64748b;">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="btn btn-primary flex items-center justify-center gap-2 flex-1 sm:flex-none">
                    <i class="fas fa-plus-circle text-xs"></i>
                    <span>Ajukan Peminjaman</span>
                </a>
                <a href="{{ route('mahasiswa.alat') }}" class="btn btn-secondary flex items-center justify-center gap-2 flex-1 sm:flex-none">
                    <i class="fas fa-box-open text-xs text-slate-400"></i>
                    <span>Lihat Alat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <x-card-stats
            title="Total Peminjaman"
            :value="$stats['total']"
            icon="fas fa-clipboard-list"
            color="blue" />

        <x-card-stats
            title="Sedang Dipinjam"
            :value="$stats['dipinjam']"
            icon="fas fa-hand-holding"
            color="green" />

        <x-card-stats
            title="Selesai"
            :value="$stats['selesai']"
            icon="fas fa-check-circle"
            color="yellow" />

        <x-card-stats
            title="Ditolak"
            :value="$stats['ditolak']"
            icon="fas fa-times-circle"
            color="red" />

    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Aktivitas Terbaru --}}
        <div class="lg:col-span-2">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-5">
                    {{-- SESUDAH --}}
                    <h2 class="text-lg font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                        Aktivitas Terbaru
                    </h2>
                    <a href="{{ route('mahasiswa.peminjaman.riwayat') }}"
                       class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                        Lihat Semua →
                    </a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recent as $p)
                    <div class="py-3.5 first:pt-0 last:pb-0 transition hover:bg-slate-50/80 rounded-xl px-2 -mx-2">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 {{ strtok($p->status_config['color'], ' ') }} bg-opacity-10 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas {{ $p->status_config['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="font-bold text-slate-800 text-base mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $p->alat->nama }}</p>
                                        <p class="text-sm text-slate-400 font-medium">
                                            Kode: <span class="font-mono text-slate-600 font-semibold">{{ $p->kode_peminjaman }}</span>
                                        </p>
                                        <p class="text-sm text-slate-400 mt-1 flex items-center gap-1">
                                            <i class="far fa-clock"></i> {{ $p->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <span class="badge {{ $p->status_config['color'] }} text-sm px-2.5 py-1 rounded-full font-semibold shrink-0">
                                        {{ $p->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-400 mb-3">Belum ada aktivitas peminjaman.</p>
                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                           class="btn btn-primary text-sm px-4 py-2">
                            <i class="fas fa-plus text-xs"></i> Ajukan Sekarang
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kanan --}}
        <div class="lg:col-span-1 space-y-4">

            

            <div class="card p-6 bg-linear-to-br from-slate-50 to-blue-50/30 border-slate-200">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0 border border-blue-100">
                        <i class="fas fa-info-circle text-xs"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm mb-2 uppercase tracking-wide" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Informasi Penting
                        </p>
                        <ul class="space-y-2 text-sm text-slate-600 font-medium">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>Ajukan peminjaman minimal 1 hari sebelum penggunaan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>Kembalikan alat tepat waktu untuk menghindari denda</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-emerald-500 mt-0.5 shrink-0"></i>
                                <span>Periksa kondisi alat sebelum/sesudah pemakaian</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection
