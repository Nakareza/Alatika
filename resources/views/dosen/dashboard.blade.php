@extends('layouts.dosen')

@section('title', 'Dashboard')

@section('content')

    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#1E2B4A] mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>

                <p class="text-sm text-slate-500" style="font-family:'Inter',sans-serif;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('dosen.peminjaman.ajukan') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajukan Peminjaman</span>
                </a>

                <a href="{{ route('dosen.alat') }}" class="btn btn-secondary">
                    <i class="fas fa-box-open"></i>
                    <span>Lihat Katalog</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        <!-- Total -->
        <div class="card p-5 border-l-4 border-[#378ADD]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:#EBF3FD;">
                    <i class="fas fa-clipboard-list text-[#185FA5] text-lg"></i>
                </div>

                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                      style="background:#DCFCE7;color:#166534;">
                    +15%
                </span>
            </div>

            <p class="text-sm text-slate-500 mb-1">Total Peminjaman</p>

            <p class="text-3xl font-bold text-[#1E2B4A]"
               style="font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $stats['total'] }}
            </p>
        </div>

        <!-- Dipinjam -->
        <div class="card p-5 border-l-4 border-[#F59E0B]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:#FEF3C7;">
                    <i class="fas fa-hand-holding text-[#D97706] text-lg"></i>
                </div>
            </div>

            <p class="text-sm text-slate-500 mb-1">Sedang Dipinjam</p>

            <p class="text-3xl font-bold text-[#1E2B4A]"
               style="font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $stats['dipinjam'] }}
            </p>
        </div>

        <!-- Selesai -->
        <div class="card p-5 border-l-4 border-[#10B981]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:#D1FAE5;">
                    <i class="fas fa-check-circle text-[#059669] text-lg"></i>
                </div>

                <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                      style="background:#DCFCE7;color:#166534;">
                    +25%
                </span>
            </div>

            <p class="text-sm text-slate-500 mb-1">Selesai</p>

            <p class="text-3xl font-bold text-[#1E2B4A]"
               style="font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $stats['selesai'] }}
            </p>
        </div>

        <!-- Ditolak -->
        <div class="card p-5 border-l-4 border-[#EF4444]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:#FEE2E2;">
                    <i class="fas fa-times-circle text-[#DC2626] text-lg"></i>
                </div>
            </div>

            <p class="text-sm text-slate-500 mb-1">Ditolak</p>

            <p class="text-3xl font-bold text-[#1E2B4A]"
               style="font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $stats['ditolak'] }}
            </p>
        </div>

    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Aktivitas -->
        <div class="lg:col-span-2">
            <div class="card p-6">

                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-[#1E2B4A]"
                        style="font-family:'Plus Jakarta Sans',sans-serif;">
                        Aktivitas Terbaru
                    </h2>

                    <a href="{{ route('dosen.riwayat') }}"
                       class="text-sm font-semibold transition-colors"
                       style="color:#185FA5;"
                       onmouseover="this.style.color='#1E2B4A'"
                       onmouseout="this.style.color='#185FA5'">
                        Lihat Semua →
                    </a>
                </div>

                <div class="space-y-3">

                    @forelse($recent as $p)
                        <div class="list-item">

                            <div class="flex items-start gap-3">

                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:#F5F8FF;">
                                    <i class="fas {{ $p->status_config['icon'] }}"
                                       style="color:#185FA5;"></i>
                                </div>

                                <div class="flex-1 min-w-0">

                                    <div class="flex items-start justify-between gap-2">

                                        <div>
                                            <p class="font-semibold text-sm text-[#1E2B4A] mb-1"
                                               style="font-family:'Plus Jakarta Sans',sans-serif;">
                                                {{ $p->alat->nama }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                Kode: {{ $p->kode_peminjaman }}
                                            </p>

                                            <p class="text-xs text-slate-400 mt-1">
                                                {{ $p->created_at->diffForHumans() }}
                                            </p>
                                        </div>

                                        <span class="badge {{ $p->status_config['color'] }}">
                                            {{ $p->status_label }}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @empty

                        <div class="text-center py-8">
                            <p class="text-sm text-slate-500">
                                Belum ada aktivitas peminjaman
                            </p>
                        </div>

                    @endforelse

                </div>

            </div>
        </div>

        <!-- Sidebar Right -->
        <div class="space-y-6">

            <!-- Menu Cepat -->
            <div class="card p-6">

                <h3 class="text-base font-bold text-[#1E2B4A] mb-4"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Menu Cepat
                </h3>

                <div class="space-y-2">

                    <a href="{{ route('dosen.riwayat') }}"
                       class="flex items-center gap-3 p-3 rounded-xl transition-all border border-transparent hover:border-[#D4E6F8]"
                       onmouseover="this.style.background='#F5F8FF'"
                       onmouseout="this.style.background=''">

                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             style="background:#EBF3FD;">
                            <i class="fas fa-history text-[#185FA5] text-sm"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-[#1E2B4A]">
                                Riwayat
                            </p>

                            <p class="text-xs text-slate-500">
                                Lihat semua peminjaman
                            </p>
                        </div>

                    </a>

                    <a href="{{ route('dosen.profil') }}"
                       class="flex items-center gap-3 p-3 rounded-xl transition-all border border-transparent hover:border-[#D4E6F8]"
                       onmouseover="this.style.background='#F5F8FF'"
                       onmouseout="this.style.background=''">

                        <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                             style="background:#EBF3FD;">
                            <i class="fas fa-user text-[#185FA5] text-sm"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-[#1E2B4A]">
                                Profil
                            </p>

                            <p class="text-xs text-slate-500">
                                Kelola akun Anda
                            </p>
                        </div>

                    </a>

                </div>

            </div>

            <!-- Informasi -->
            <div class="card p-6"
                 style="background:linear-gradient(135deg,#F5F8FF 0%, #EBF3FD 100%);border-color:#D4E6F8;">

                <div class="flex items-start gap-3">

                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:#D4E6F8;">
                        <i class="fas fa-info-circle text-[#185FA5] text-sm"></i>
                    </div>

                    <div>

                        <p class="font-bold text-sm text-[#1E2B4A] mb-2"
                           style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Informasi Penting
                        </p>

                        <ul class="space-y-2 text-xs text-slate-600">

                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-[#185FA5] mt-0.5"></i>
                                <span>Ajukan peminjaman sebelum penggunaan</span>
                            </li>

                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-[#185FA5] mt-0.5"></i>
                                <span>Kembalikan alat tepat waktu</span>
                            </li>

                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-[#185FA5] mt-0.5"></i>
                                <span>Periksa kondisi alat sebelum dipakai</span>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

            <!-- Ringkasan -->
            <div class="card p-6">

                <h3 class="text-base font-bold text-[#1E2B4A] mb-4"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Ringkasan Status
                </h3>

                <div class="space-y-3">

                    <div class="flex items-center justify-between p-3 rounded-xl"
                         style="background:#F5F8FF;">
                        <span class="text-sm text-slate-600">
                            Menunggu Persetujuan
                        </span>

                        <span class="font-bold text-[#1E2B4A]">
                            {{ $statusSummary['pending'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl"
                         style="background:#F5F8FF;">
                        <span class="text-sm text-slate-600">
                            Dalam Penggunaan
                        </span>

                        <span class="font-bold text-[#1E2B4A]">
                            {{ $statusSummary['dipinjam'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl"
                         style="background:#F5F8FF;">
                        <span class="text-sm text-slate-600">
                            Total Selesai
                        </span>

                        <span class="font-bold text-[#1E2B4A]">
                            {{ $statusSummary['selesai'] }}
                        </span>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection