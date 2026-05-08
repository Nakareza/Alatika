@extends('layouts.kalab')

@section('title', 'Dashboard')

@section('content')

    {{-- Welcome Section --}}
    <div class="card p-8 lg:p-10 overflow-hidden relative mb-6 page-transition">
        <div class="flex items-center gap-6 md:gap-8">
            
            <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl"
                 style="background:#F5F8FF;border:1px solid #D4E6F8;">
                <img src="{{ asset('images/logo-polines.png') }}"
                     alt="Logo Polines"
                     class="w-16 h-16 object-contain">
            </div>

            <div class="flex-1">
                
                <div class="mb-3">
                    <span class="badge badge-info">
                        <i class="fas fa-flask mr-2"></i>
                        Kepala Laboratorium
                    </span>
                </div>

                <h1 class="text-3xl lg:text-4xl font-extrabold leading-tight mb-2"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Selamat Datang,
                    <span style="color:#185FA5;">
                        {{ Auth::user()->name }} 👋
                    </span>

                </h1>

                <p class="text-sm md:text-base"
                   style="color:#64748b;">
                    Panel pengawasan laboratorium elektronika Politeknik Negeri Semarang
                </p>

            </div>
        </div>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

        {{-- Pending --}}
        <div class="card p-6 border-l-4 border-amber-400">
            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                     style="background:#FEF3C7;">
                    <i class="fas fa-clock text-xl text-amber-600"></i>
                </div>

                <span class="badge badge-warning">
                    Pending
                </span>

            </div>

            <h3 class="text-3xl font-black"
                style="color:#1E2B4A;">
                {{ $stats['pending_dosen'] }}
            </h3>

            <p class="text-sm mt-1 text-slate-500">
                Menunggu Persetujuan
            </p>
        </div>

        {{-- Disetujui --}}
        <div class="card p-6 border-l-4 border-emerald-400">
            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                     style="background:#d1fae5;">
                    <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                </div>

                <span class="badge badge-success">
                    Bulan Ini
                </span>

            </div>

            <h3 class="text-3xl font-black"
                style="color:#1E2B4A;">
                {{ $stats['disetujui'] }}
            </h3>

            <p class="text-sm mt-1 text-slate-500">
                Disetujui
            </p>
        </div>

        {{-- Dipinjam --}}
        <div class="card p-6 border-l-4 border-blue-400">
            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                     style="background:#DBEAFE;">
                    <i class="fas fa-hand-holding text-xl text-blue-600"></i>
                </div>

                <span class="badge badge-info">
                    Aktif
                </span>

            </div>

            <h3 class="text-3xl font-black"
                style="color:#1E2B4A;">
                {{ $stats['dipinjam'] }}
            </h3>

            <p class="text-sm mt-1 text-slate-500">
                Sedang Dipinjam
            </p>
        </div>

        {{-- Total --}}
        <div class="card p-6 border-l-4 border-indigo-400">
            <div class="flex items-center justify-between mb-4">

                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                     style="background:#E0E7FF;">
                    <i class="fas fa-laptop text-xl text-indigo-600"></i>
                </div>

                <span class="badge"
                      style="background:#EEF2FF;color:#4338CA;">
                    Tersedia: {{ $stats['tersedia'] }}
                </span>

            </div>

            <h3 class="text-3xl font-black"
                style="color:#1E2B4A;">
                {{ $stats['total_alat'] }}
            </h3>

            <p class="text-sm mt-1 text-slate-500">
                Total Alat Lab
            </p>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="card p-6 mb-6">

        <div class="flex items-center gap-2 mb-5">
            <i class="fas fa-bolt text-[#185FA5]"></i>

            <h2 class="text-xl font-bold"
                style="font-family:'Plus Jakarta Sans',sans-serif;">
                Quick Actions
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <a href="{{ route('kalab.persetujuan') }}"
               class="list-item flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-clipboard-check"></i>
                </div>

                <div>
                    <h3 class="font-semibold text-sm">Persetujuan</h3>
                    <p class="text-xs text-slate-500">
                        {{ $stats['pending_dosen'] }} menunggu
                    </p>
                </div>

            </a>

            <a href="{{ route('kalab.alat') }}"
               class="list-item flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-laptop"></i>
                </div>

                <div>
                    <h3 class="font-semibold text-sm">Data Alat</h3>
                    <p class="text-xs text-slate-500">
                        Lihat inventaris
                    </p>
                </div>

            </a>

            <a href="{{ route('kalab.riwayat') }}"
               class="list-item flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-history"></i>
                </div>

                <div>
                    <h3 class="font-semibold text-sm">Riwayat</h3>
                    <p class="text-xs text-slate-500">
                        Semua peminjaman
                    </p>
                </div>

            </a>

            <a href="{{ route('kalab.laporan') }}"
               class="list-item flex items-center gap-4">

                <div class="w-12 h-12 rounded-xl bg-rose-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-chart-bar"></i>
                </div>

                <div>
                    <h3 class="font-semibold text-sm">Laporan</h3>
                    <p class="text-xs text-slate-500">
                        Statistik lab
                    </p>
                </div>

            </a>

        </div>
    </div>

    {{-- Pending Approval --}}
    <div class="card p-6 mb-6">

        <div class="flex items-center justify-between mb-5">

            <div class="flex items-center gap-2">
                <i class="fas fa-clock text-amber-500"></i>

                <h2 class="text-lg font-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Menunggu Persetujuan Anda
                </h2>
            </div>

            <a href="{{ route('kalab.persetujuan') }}"
               class="text-sm font-semibold"
               style="color:#185FA5;">
                Lihat Semua
                <i class="fas fa-arrow-right ml-1"></i>
            </a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="pb-3 px-4">Mahasiswa</th>
                        <th class="pb-3 px-4">Alat</th>
                        <th class="pb-3 px-4">Tanggal Pinjam</th>
                        <th class="pb-3 px-4">Tanggal Kembali</th>
                        <th class="pb-3 px-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($pending_approvals as $peminjaman)

                    <tr class="hover:bg-slate-50 transition-all">

                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">

                                <div class="w-9 h-9 rounded-full bg-[#185FA5] text-white flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($peminjaman->user->name,0,2)) }}
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $peminjaman->user->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $peminjaman->user->role }}
                                    </p>
                                </div>

                            </div>
                        </td>

                        <td class="py-4 px-4">
                            <p class="text-sm font-semibold text-slate-700">
                                {{ $peminjaman->alat->nama }}
                            </p>
                        </td>

                        <td class="py-4 px-4 text-sm text-slate-600">
                            {{ $peminjaman->tanggal_pinjam ? $peminjaman->tanggal_pinjam->format('d M Y') : '-' }}
                        </td>

                        <td class="py-4 px-4 text-sm text-slate-600">
                            {{ $peminjaman->tanggal_kembali ? $peminjaman->tanggal_kembali->format('d M Y') : '-' }}
                        </td>

                        <td class="py-4 px-4">

                            <div class="flex gap-2">

                                <form action="{{ route('kalab.persetujuan.approve', $peminjaman->id) }}"
                                      method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                                            style="background:#d1fae5;color:#065f46;">
                                        <i class="fas fa-check mr-1"></i>
                                        Setujui
                                    </button>
                                </form>

                                <form action="{{ route('kalab.persetujuan.reject', $peminjaman->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Tolak peminjaman ini?');">

                                    @csrf

                                    <input type="hidden"
                                           name="alasan"
                                           value="Ditolak oleh Kepala Lab">

                                    <button type="submit"
                                            class="px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                                            style="background:#fee2e2;color:#991b1b;">
                                        <i class="fas fa-times mr-1"></i>
                                        Tolak
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="5"
                            class="py-10 text-center text-slate-500">

                            <i class="fas fa-inbox text-4xl mb-3 block text-slate-300"></i>

                            Tidak ada permintaan persetujuan pending.

                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

    {{-- Bottom Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Lab Info --}}
        <div class="card p-6">

            <div class="flex items-center gap-3 mb-5">

                <div class="w-10 h-10 rounded-xl bg-[#185FA5] text-white flex items-center justify-center">
                    <i class="fas fa-info-circle"></i>
                </div>

                <h3 class="text-lg font-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Informasi Laboratorium
                </h3>

            </div>

            <div class="space-y-3">

                <div class="list-item flex justify-between items-center">
                    <span class="text-sm text-slate-600">
                        Total Alat
                    </span>

                    <span class="font-bold">
                        {{ $stats['total_alat'] }} Unit
                    </span>
                </div>

                <div class="list-item flex justify-between items-center">
                    <span class="text-sm text-slate-600">
                        Alat Tersedia
                    </span>

                    <span class="font-bold text-green-600">
                        {{ $stats['tersedia'] }} Unit
                    </span>
                </div>

                <div class="list-item flex justify-between items-center">
                    <span class="text-sm text-slate-600">
                        Sedang Dipinjam
                    </span>

                    <span class="font-bold text-blue-600">
                        {{ $stats['dipinjam'] }} Unit
                    </span>
                </div>

                <div class="list-item flex justify-between items-center">
                    <span class="text-sm text-slate-600">
                        Overdue
                    </span>

                    <span class="font-bold text-amber-600">
                        {{ $stats['overdue'] }} Unit
                    </span>
                </div>

            </div>

        </div>

        {{-- Activity --}}
        <div class="card p-6">

            <div class="flex items-center gap-3 mb-5">

                <div class="w-10 h-10 rounded-xl bg-[#1E2B4A] text-white flex items-center justify-center">
                    <i class="fas fa-history"></i>
                </div>

                <h3 class="text-lg font-bold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Aktivitas Terbaru
                </h3>

            </div>

            <div class="space-y-3">

                @forelse($recent_activities as $act)

                <div class="list-item flex gap-3">

                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold
                        {{ $act->status == 'disetujui' || $act->status == 'selesai'
                            ? 'bg-green-500'
                            : ($act->status == 'ditolak'
                                ? 'bg-red-500'
                                : 'bg-blue-500') }}">

                        <i class="fas
                            {{ $act->status == 'disetujui'
                                ? 'fa-check'
                                : ($act->status == 'ditolak'
                                    ? 'fa-times'
                                    : 'fa-bell') }}">
                        </i>

                    </div>

                    <div class="flex-1">

                        <p class="text-sm font-semibold text-slate-800">
                            {{ $act->user->name }} -
                            {{ $act->alat->nama }}
                            ({{ ucfirst($act->status) }})
                        </p>

                        <p class="text-xs text-slate-500 mt-1">
                            {{ $act->updated_at->diffForHumans() }}
                        </p>

                    </div>

                </div>

                @empty

                <p class="text-sm text-slate-500 text-center py-4">
                    Belum ada aktivitas.
                </p>

                @endforelse

            </div>

        </div>

    </div>

@endsection