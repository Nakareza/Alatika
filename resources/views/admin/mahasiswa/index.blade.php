@extends('layouts.admin')

@section('title', 'Data Mahasiswa')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Data Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-1">
                Data mahasiswa yang tersimpan di sistem dan dapat difilter langsung dari server.
            </p>
        </div>

        <button class="btn btn-secondary" type="button">
            <i class="fas fa-file-export"></i>
            Export
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['total'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Total Mahasiswa</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-link text-emerald-600 text-lg"></i>
                </div>
                <span class="badge badge-success">Telegram</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['linked_telegram'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Sudah Tertaut</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-unlink text-amber-600 text-lg"></i>
                </div>
                <span class="badge badge-warning">Belum</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['unlinked_telegram'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Belum Tertaut</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-sky-600 text-lg"></i>
                </div>
                <span class="badge bg-sky-100 text-sky-700">Bulan Ini</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ number_format($stats['registered_this_month'] ?? 0) }}</h3>
            <p class="text-sm text-slate-500 mt-1">Registrasi Bulan Ini</p>
        </div>
    </div>

    <div class="card p-6">
        <form method="GET" action="{{ route('admin.mahasiswa') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <div class="lg:col-span-6 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIM, atau email mahasiswa..."
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

                <a href="{{ route('admin.mahasiswa') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">#</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Mahasiswa</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">NIM</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Email</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Prodi</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Telegram</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Terdaftar</th>
                        <th class="text-center text-xs font-semibold text-slate-500 uppercase px-6 py-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($mahasiswa as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $mahasiswa->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800">{{ $item->name }}</h4>
                                        <p class="text-xs text-slate-500">Mahasiswa</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs bg-sky-50 text-sky-700 px-3 py-1 rounded-lg font-mono font-medium">
                                    {{ $item->nim ?: '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $item->email }}</td>
                            <td class="px-6 py-4">
                                <span class="badge bg-slate-100 text-slate-500">Belum ada data</span>
                            </td>

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
                            <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                                Tidak ada data mahasiswa yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-semibold">{{ $mahasiswa->firstItem() ?? 0 }}-{{ $mahasiswa->lastItem() ?? 0 }}</span>
                dari
                <span class="font-semibold">{{ number_format($mahasiswa->total()) }}</span>
                mahasiswa
            </p>

            {{ $mahasiswa->links() }}
        </div>
    </div>

</div>

@endsection