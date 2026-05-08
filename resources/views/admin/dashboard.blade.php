@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    .glassmorphism {
        background: white;
        border: 1px solid #e2e8f0;
    }

    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.12);
    }

    .luxury-shadow {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .animate-slide-down {
        animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-backdrop {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
</style>
@endpush

@section('content')

{{-- Welcome Section --}}
<div class="glassmorphism rounded-2xl p-8 md:p-10 luxury-shadow animate-slide-down overflow-hidden relative mb-6">
    <div class="flex items-center gap-6 md:gap-8">
        
        <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-50 border border-blue-100">
            <img src="{{ asset('images/logo-polines.png') }}" 
                 alt="Logo Polines" 
                 class="w-16 h-16 object-contain">
        </div>

        <div class="flex-1">
            <div class="mb-2">
                <span class="inline-block px-4 py-1.5 rounded-full bg-blue-100 border border-blue-200 text-blue-700 text-sm font-semibold">
                    <i class="fas fa-crown mr-2 text-amber-500"></i>
                    Administrator Panel
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-black mb-2 leading-tight">
                Selamat Datang,
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    {{ Auth::user()->name }}! 👋
                </span>
            </h1>

            <p class="text-slate-600 text-base font-medium">
                Panel manajemen sistem peminjaman alat elektronik Politeknik Negeri Semarang
            </p>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">

    <x-card-stats-admin 
        title="Total Peminjaman" 
        :value="$stats['total']" 
        icon="fas fa-clipboard-list" 
        color="blue" />

    <x-card-stats-admin 
        title="Menunggu Persetujuan" 
        :value="$stats['pending']" 
        icon="fas fa-clock" 
        color="yellow" />

    <x-card-stats-admin 
        title="Sedang Dipinjam" 
        :value="$stats['dipinjam']" 
        icon="fas fa-hand-holding" 
        color="indigo" />

    <x-card-stats-admin 
        title="Selesai" 
        :value="$stats['selesai']" 
        icon="fas fa-check-double" 
        color="green" />

    <x-card-stats-admin 
        title="Terlambat" 
        :value="$stats['overdue']" 
        icon="fas fa-exclamation-triangle" 
        color="red" />

</div>

{{-- Quick Actions --}}
<div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">

    <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
        <i class="fas fa-bolt text-blue-500"></i>
        Quick Actions
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

        <a href="{{ route('admin.alat') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100 hover:shadow-lg transition-all hover-lift group">

            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-laptop text-xl text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Kelola Alat</h3>
                <p class="text-xs text-slate-500">Manajemen alat</p>
            </div>
        </a>

        <a href="{{ route('admin.mahasiswa') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-sky-50 border border-sky-100 hover:shadow-lg transition-all hover-lift group">

            <div class="w-12 h-12 rounded-xl bg-sky-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-user-graduate text-xl text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Kelola Mahasiswa</h3>
                <p class="text-xs text-slate-500">Data mahasiswa</p>
            </div>
        </a>

        <a href="{{ route('admin.peminjaman') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100 hover:shadow-lg transition-all hover-lift group">

            <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-clipboard-list text-xl text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Kelola Peminjaman</h3>
                <p class="text-xs text-slate-500">Persetujuan</p>
            </div>
        </a>

        <a href="{{ route('admin.pengembalian') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100 hover:shadow-lg transition-all hover-lift group">

            <div class="w-12 h-12 rounded-xl bg-amber-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-undo-alt text-xl text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Kelola Pengembalian</h3>
                <p class="text-xs text-slate-500">Verifikasi</p>
            </div>
        </a>

        <a href="{{ route('admin.laporan') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-rose-50 border border-rose-100 hover:shadow-lg transition-all hover-lift group">

            <div class="w-12 h-12 rounded-xl bg-rose-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-bar text-xl text-white"></i>
            </div>

            <div>
                <h3 class="font-semibold text-slate-800 text-sm">Lihat Laporan</h3>
                <p class="text-xs text-slate-500">Statistik sistem</p>
            </div>
        </a>

    </div>
</div>

{{-- Recent Borrowing --}}
<div class="glassmorphism rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">

    <div class="p-6 border-b border-slate-100 flex items-center justify-between">

        <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
            <i class="fas fa-list-alt text-blue-500"></i>
            Peminjaman Terbaru
        </h2>

        <a href="{{ route('admin.peminjaman') }}"
           class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
            Lihat semua →
        </a>
    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-left">

            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Peminjam</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Alat</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-50">

                @forelse($recentPeminjaman as $p)

                @php
                    $badge = match($p->status) {
                        'pending'   => 'bg-amber-100 text-amber-700',
                        'disetujui' => 'bg-blue-100 text-blue-700',
                        'dipinjam'  => 'bg-indigo-100 text-indigo-700',
                        'menunggu_verifikasi' => 'bg-purple-100 text-purple-700',
                        'selesai'   => 'bg-emerald-100 text-emerald-700',
                        'ditolak'   => 'bg-red-100 text-red-700',
                        default     => 'bg-gray-100 text-gray-700',
                    };
                @endphp

                <tr class="hover:bg-slate-50 transition-colors">

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">

                            <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($p->user->name, 0, 2)) }}
                            </div>

                            <div>
                                <p class="font-semibold text-slate-800 text-sm">
                                    {{ $p->user->name }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $p->user->nim ?? $p->user->email }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-800">
                            {{ $p->alat->nama }}
                        </p>

                        <p class="text-xs text-slate-400">
                            {{ $p->kode_peminjaman }}
                        </p>
                    </td>

                    <td class="px-6 py-4 text-sm text-slate-600">
                        <p>{{ $p->tanggal_pinjam->format('d M Y') }}</p>

                        <p class="text-xs text-slate-400">
                            Kembali: {{ $p->tanggal_kembali->format('d M Y') }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ $p->status_label }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <a href="{{ route('admin.peminjaman') }}"
                           class="text-blue-600 hover:text-blue-800 text-xs font-semibold">
                            Detail
                        </a>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <i class="fas fa-inbox text-3xl text-slate-200 mb-3 block"></i>
                        Belum ada data peminjaman
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>
</div>

@endsection