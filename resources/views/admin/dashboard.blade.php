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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">

    <x-card-stats
        title="Total Peminjaman" 
        :value="$stats['total']" 
        icon="fas fa-clipboard-list" 
        color="blue" />

    <x-card-stats
        title="Menunggu Persetujuan" 
        :value="$stats['pending']" 
        icon="fas fa-clock" 
        color="yellow" />

    <x-card-stats 
        title="Sedang Dipinjam" 
        :value="$stats['dipinjam']" 
        icon="fas fa-hand-holding" 
        color="indigo" />

    <x-card-stats 
        title="Selesai" 
        :value="$stats['selesai']" 
        icon="fas fa-check-double" 
        color="green" />

    <x-card-stats 
        title="Terlambat" 
        :value="$stats['overdue']" 
        icon="fas fa-exclamation-triangle" 
        color="r    " />

</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">

    <h2 class="text-lg font-bold text-[#1E2B4A] mb-4 flex items-center gap-2">
        <i class="fas fa-bolt text-[#185FA5]"></i>
        Quick Actions
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

        <a href="{{ route('admin.alat') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-[#B5D4F4] hover:bg-[#F8FBFF] transition-all hover-lift group">

            <div class="w-11 h-11 rounded-xl bg-[#F5F8FF] border border-[#D4E6F8] text-[#185FA5] flex items-center justify-center group-hover:bg-[#185FA5] group-hover:text-white transition-all duration-300">
                <i class="fas fa-laptop text-base"></i>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 text-sm group-hover:text-[#185FA5] transition-colors">Kelola Alat</h3>
                <p class="text-xs text-slate-400">Manajemen alat</p>
            </div>
        </a>

        <a href="{{ route('admin.mahasiswa') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-[#B5D4F4] hover:bg-[#F8FBFF] transition-all hover-lift group">

            <div class="w-11 h-11 rounded-xl bg-[#F5F8FF] border border-[#D4E6F8] text-[#185FA5] flex items-center justify-center group-hover:bg-[#185FA5] group-hover:text-white transition-all duration-300">
                <i class="fas fa-user-graduate text-base"></i>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 text-sm group-hover:text-[#185FA5] transition-colors">Kelola Mahasiswa</h3>
                <p class="text-xs text-slate-400">Data mahasiswa</p>
            </div>
        </a>

        <a href="{{ route('admin.peminjaman') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-[#B5D4F4] hover:bg-[#F8FBFF] transition-all hover-lift group">

            <div class="w-11 h-11 rounded-xl bg-[#F5F8FF] border border-[#D4E6F8] text-[#185FA5] flex items-center justify-center group-hover:bg-[#185FA5] group-hover:text-white transition-all duration-300">
                <i class="fas fa-clipboard-list text-base"></i>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 text-sm group-hover:text-[#185FA5] transition-colors">Kelola Peminjaman</h3>
                <p class="text-xs text-slate-400">Persetujuan</p>
            </div>
        </a>

        <a href="{{ route('admin.pengembalian') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-[#B5D4F4] hover:bg-[#F8FBFF] transition-all hover-lift group">

            <div class="w-11 h-11 rounded-xl bg-[#F5F8FF] border border-[#D4E6F8] text-[#185FA5] flex items-center justify-center group-hover:bg-[#185FA5] group-hover:text-white transition-all duration-300">
                <i class="fas fa-undo-alt text-base"></i>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 text-sm group-hover:text-[#185FA5] transition-colors">Kelola Pengembalian</h3>
                <p class="text-xs text-slate-400">Verifikasi</p>
            </div>
        </a>

        <a href="{{ route('admin.laporan') }}"
           class="flex items-center gap-3 p-4 rounded-xl bg-white border border-slate-100 hover:border-[#B5D4F4] hover:bg-[#F8FBFF] transition-all hover-lift group">

            <div class="w-11 h-11 rounded-xl bg-[#F5F8FF] border border-[#D4E6F8] text-[#185FA5] flex items-center justify-center group-hover:bg-[#185FA5] group-hover:text-white transition-all duration-300">
                <i class="fas fa-chart-bar text-base"></i>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 text-sm group-hover:text-[#185FA5] transition-colors">Lihat Laporan</h3>
                <p class="text-xs text-slate-400">Statistik sistem</p>
            </div>
        </a>

    </div>
</div>

{{-- Recent Borrowing --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">

    <div class="p-6 border-b border-slate-100 flex items-center justify-between">

        <h2 class="text-lg font-bold text-[#1E2B4A] flex items-center gap-2">
            <i class="fas fa-list-alt text-[#185FA5]"></i>
            Peminjaman Terbaru
        </h2>

        <a href="{{ route('admin.peminjaman') }}"
           class="text-xs font-semibold text-[#185FA5] hover:text-[#1E2B4A] transition-colors">
            Lihat semua &rarr;
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
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition-all"
                            style="background:#EBF3FD;color:#185FA5;">

                                <i class="fas fa-eye"></i>
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