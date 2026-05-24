@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif; color:#1E2B4A;">
                Data Dosen
            </h1>
            <p class="text-sm mt-1 text-slate-500">
                Data dosen yang tersimpan di sistem dan dapat difilter langsung dari server.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button class="btn btn-secondary" type="button">
                <i class="fas fa-download text-sm"></i>
                Export
            </button>

            <button class="btn btn-primary" type="button">
                <i class="fas fa-plus text-sm"></i>
                Tambah Dosen
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-amber-100">
                    <i class="fas fa-chalkboard-teacher text-amber-600"></i>
                </div>
                <span class="badge badge-warning">Total</span>
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800">{{ number_format($stats['total'] ?? 0) }}</h2>
            <p class="mt-1 text-sm text-slate-500">Total Dosen</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-emerald-100">
                    <i class="fas fa-link text-emerald-600"></i>
                </div>
                <span class="badge badge-success">Telegram</span>
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800">{{ number_format($stats['linked_telegram'] ?? 0) }}</h2>
            <p class="mt-1 text-sm text-slate-500">Sudah Tertaut</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-amber-100">
                    <i class="fas fa-unlink text-amber-600"></i>
                </div>
                <span class="badge badge-warning">Belum</span>
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800">{{ number_format($stats['unlinked_telegram'] ?? 0) }}</h2>
            <p class="mt-1 text-sm text-slate-500">Belum Tertaut</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-100">
                    <i class="fas fa-calendar-plus text-indigo-600"></i>
                </div>
                <span class="badge bg-indigo-100 text-indigo-700">Bulan Ini</span>
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800">{{ number_format($stats['registered_this_month'] ?? 0) }}</h2>
            <p class="mt-1 text-sm text-slate-500">Registrasi Bulan Ini</p>
        </div>
    </div>

    <div class="card p-6">
        <form method="GET" action="{{ route('admin.dosen') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-6 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama dosen, NIP, atau email..."
                    class="inp pl-11"
                >
            </div>

            <div class="lg:col-span-3">
                <select name="telegram_status" class="inp w-full">
                    <option value="">Semua Status Telegram</option>
                    <option value="linked" {{ request('telegram_status') === 'linked' ? 'selected' : '' }}>Sudah Tertaut</option>
                    <option value="unlinked" {{ request('telegram_status') === 'unlinked' ? 'selected' : '' }}>Belum Tertaut</option>
                </select>
            </div>

            <div class="lg:col-span-3 flex gap-3">
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>

                <a href="{{ route('admin.dosen') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#F8FBFF] border-b border-[#EBF3FD]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Dosen</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">NIP</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Telegram</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Terdaftar</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#EBF3FD] bg-white">
                    @forelse($dosen as $index => $item)
                        <tr class="hover:bg-[#F8FBFF] transition-all">
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $dosen->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-800">{{ $item->name }}</h3>
                                        <p class="text-xs text-slate-500">Dosen</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium font-mono">
                                    {{ $item->nip ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->email }}</td>
                            <td class="px-6 py-4">
                                @if($item->telegram_chat_id)
                                    <span class="badge badge-success">Tertaut</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-500">Belum Tertaut</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $item->created_at?->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition" title="Detail" type="button">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                                Tidak ada data dosen yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-[#EBF3FD] flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-semibold">{{ $dosen->firstItem() ?? 0 }}-{{ $dosen->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold">{{ number_format($dosen->total()) }}</span>
                dosen
            </p>

            {{ $dosen->links() }}
        </div>
    </div>

</div>

@endsection