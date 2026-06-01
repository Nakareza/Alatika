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
                        Pending
                    </option>

                    <option value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                        Dipinjam
                    </option>

                    <option value="menunggu_verifikasi"
                        {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>
                        Menunggu Verifikasi
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
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">#</th>
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

                                @if($p->status === 'pending')

                                <form action="{{ route('admin.peminjaman.approve', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="w-9 h-9 rounded-lg text-green-600 hover:bg-green-50 transition">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.peminjaman.reject', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <input type="hidden"
                                        name="alasan"
                                        value="Ditolak admin">

                                    <button type="submit"
                                            class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>

                                @endif

                                @if($p->status === 'disetujui')

                                <form action="{{ route('admin.peminjaman.dipinjam', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700 transition">
                                        Tandai Dipinjam
                                    </button>
                                </form>

                                @if($p->status === 'menunggu_verifikasi')

                                <form action="{{ route('admin.peminjaman.approveReturn', $p->id) }}"
                                    method="POST">
                                    @csrf

                                    <button type="submit"
                                            class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition">

                                        <i class="fas fa-check mr-1"></i>
                                        Terima
                                    </button>
                                </form>

                                @endif
                                                                @endif
                                @if($p->foto_bukti_kembali)

                                <button
                                    type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal-bukti-{{ $p->id }}'))"
                                    class="px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm hover:bg-slate-200 transition">
                                    
                                    <i class="fas fa-image mr-1"></i>
                                    Lihat Bukti
                                </button>

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

@foreach($peminjaman as $p)

    @if($p->foto_bukti_kembali)

        <x-modal
    name="bukti-{{ $p->id }}"
    title="Bukti Pengembalian"
    size="lg"
    type="default">

    <div class="space-y-5">

        <div x-data="{ preview:false }">

        {{-- Gambar utama --}}
        <img
            src="{{ $p->foto_bukti_url }}"
            alt="Bukti Pengembalian"
            @click="preview = true"
            class="w-full h-[420px] object-cover rounded-2xl border cursor-zoom-in hover:opacity-90 transition"
        >

        {{-- Preview fullscreen --}}
        <div
            x-show="preview"
            x-transition
            class="fixed inset-0 z-[99999] bg-black/90 flex items-center justify-center p-6"
            style="position:fixed;"
            @click="preview = false"
        >

            <img
                src="{{ $p->foto_bukti_url }}"
                class="max-w-6xl max-h-[92vh] rounded-2xl shadow-2xl object-contain"
            >

            <button
                @click="preview = false"
                class="absolute top-5 right-5 w-12 h-12 rounded-full bg-white/90 hover:bg-white text-slate-800 shadow-lg transition">

                <i class="fas fa-times"></i>
            </button>

        </div>

    </div>

            {{-- Info --}}
            <div class="bg-slate-50 rounded-2xl p-4 space-y-2">

                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-500">
                        Peminjam
                    </span>

                    <span class="text-slate-700">
                        {{ $p->user->name }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-500">
                        Alat
                    </span>

                    <span class="text-slate-700">
                        {{ $p->alat->nama }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-500">
                        Kode
                    </span>

                    <span class="text-slate-700 font-medium">
                        {{ $p->kode_peminjaman }}
                    </span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="font-semibold text-slate-500">
                        Status
                    </span>

                    <span class="badge {{ $p->status_config['color'] }}">
                        {{ $p->status_label }}
                    </span>
                </div>

            </div>

        </div>

    <x-slot name="footer">

        {{-- Kalau masih menunggu verifikasi --}}
        @if($p->status === 'menunggu_verifikasi')

            {{-- Tombol Tolak --}}
            <form
                action="{{ route('admin.peminjaman.rejectReturn', $p->id) }}"
                method="POST"
                class="flex-1">

                @csrf

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition font-medium">

                    <i class="fas fa-times mr-1"></i>
                    Tolak
                </button>
            </form>

            {{-- Tombol Terima --}}
            <form
                action="{{ route('admin.peminjaman.approveReturn', $p->id) }}"
                method="POST"
                class="flex-1">

                @csrf

                <button
                    type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition font-medium">

                    <i class="fas fa-check mr-1"></i>
                    Terima Pengembalian
                </button>
            </form>

        @else

            {{-- Kalau sudah selesai --}}
            <button
                type="button"
                @click="open = false"
                class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium">

                Tutup
            </button>

        @endif

    </x-slot>

</x-modal>

    @endif

@endforeach

@endsection