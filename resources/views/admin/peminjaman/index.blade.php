@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')

@section('content')

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <x-card-stats
        title="Total Pengajuan" 
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
        :value="$stats['aktif']" 
        icon="fas fa-hand-holding" 
        color="indigo" />
        
        <x-card-stats
        title="Ditolak" 
        :value="$stats['ditolak']" 
        icon="fas fa-times-circle" 
        color="red" />

    </div>

    {{-- Filter --}}
    <div class="card p-6">

        <form method="GET" action="{{ route('admin.peminjaman') }}">
            <div class="flex flex-col md:flex-row gap-4">

                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa atau nama alat..."
                        class="inp pl-10 w-full">
                </div>

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="inp md:w-52">

                    <option value="">Semua Status</option>

                    <option value="pending"
                        {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Menunggu Persetujuan
                    </option>

                    <option value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                        Sedang Dipinjam
                    </option>

                    <option value="selesai"
                        {{ request('status') == 'selesai' ? 'selected' : '' }}>
                        Selesai
                    </option>

                    <option value="ditolak"
                        {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>

                </select>
                <a href="{{ route('admin.peminjaman') }}"
                class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>

            </div>
        </form>

    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead style="background:#F5F8FF;">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Alat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Tanggal Pinjam</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Deadline</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($peminjaman as $index => $p)

                    <tr class="border-t border-slate-100 hover:bg-slate-50 transition">

                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold"
                                     style="background:#1E2B4A;">
                                    {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $p->user->name }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        {{ $p->user->nim ?? '-' }}
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $p->alat->nama }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ $p->kode_peminjaman }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $p->tanggal_pinjam->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $p->tanggal_kembali->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">

                            <span class="badge {{ $p->status_config['color'] }}">
                                {{ $p->status_label }}
                            </span>

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex justify-center gap-2">

                            {{-- Menunggu Persetujuan --}}
                            @if($p->status === 'pending')

                                <form action="{{ route('admin.peminjaman.approve', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="w-9 h-9 rounded-lg text-green-600 hover:bg-green-50 transition">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <input
                                        type="hidden"
                                        name="alasan"
                                        value="Ditolak admin">

                                    <button
                                        type="submit"
                                        class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>

                            @endif

                            {{-- Sedang Dipinjam --}}
                            @if($p->status === 'dipinjam')

                                <span class="text-indigo-600 text-sm font-medium">
                                    Sedang Dipinjam
                                </span>

                            @endif

                            {{-- Selesai --}}
                            @if($p->status === 'selesai')

                                <span class="text-emerald-600 text-sm font-medium">
                                    Selesai
                                </span>

                            @endif

                            {{-- Ditolak --}}
                            @if($p->status === 'ditolak')

                                <span class="text-red-600 text-sm font-medium">
                                    Ditolak
                                </span>

                            @endif

                        </div>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7"
                            class="px-6 py-10 text-center text-slate-400">
                            Belum ada data peminjaman.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



@endsection