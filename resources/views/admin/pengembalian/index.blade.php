@extends('layouts.admin')

@section('title', 'Kelola Pengembalian')

@section('content')

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        
        <x-card-stats
        title="Menunggu Dikembalikan" 
        :value="$stats['dipinjam']" 
        icon="fas fa-hourglass-half" 
        color="indigo" />

        <x-card-stats
        title="Butuh Verifikasi" 
        :value="$stats['verifikasi']" 
        icon="fas fa-clipboard-list" 
        color="yellow" />

        <x-card-stats
        title="Sudah Dikembalikan" 
        :value="$stats['selesai']" 
        icon="fas fa-check-circle" 
        color="green" />

        <x-card-stats
        title="Total Peminjaman" 
        :value="$stats['total']" 
        icon="fas fa-clipboard-list" 
        color="blue" />

    </div>


    {{-- Filter --}}
    <div class="card p-6">
        <form method="GET" action="{{ route('admin.pengembalian') }}">
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

                <option value="dipinjam"
                    {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                    Menunggu Dikembalikan
                </option>

                <option value="menunggu_verifikasi"
                    {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>
                    Butuh Verifikasi
                </option>

                <option value="selesai"
                    {{ request('status') == 'selesai' ? 'selected' : '' }}>
                    Sudah Dikembalikan
                </option>

            </select>

            <a href="{{ route('admin.pengembalian') }}"
                class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>

        </div>
        </form>
    </div>

    {{-- Table --}}
    <x-table title="Data Pengembalian Alat">

        <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">

            <tr>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                    No
                </th>

                <th class="px-2 py-4 text-left text-xs font-bold uppercase text-slate-500">
                    Mahasiswa
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                    Alat
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                    Deadline
                </th>

                <th class="px-6 py-4 text-left text-xs font-bold uppercase text-slate-500">
                    Status
                </th>

                <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                    Bukti
                </th>

                <th class="px-6 py-4 text-center text-xs font-bold uppercase text-slate-500">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($pengembalian as $index => $p)

            <tr class="hover:bg-[#F8FBFF] transition">

                {{-- No --}}
                <td class="px-6 py-5 text-sm text-slate-500 font-semibold">
                    {{ $index + 1 }}
                </td>

                {{-- Mahasiswa --}}
                <td class="px-2 py-5">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold bg-[#1E2B4A]">

                            {{ strtoupper(substr($p->user->name,0,1)) }}

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

                {{-- Alat --}}
                <td class="px-6 py-5">

                    <p class="text-sm font-semibold text-slate-800">
                        {{ $p->alat->nama }}
                    </p>

                    <p class="text-xs text-slate-400">
                        {{ $p->kode_peminjaman }}
                    </p>

                </td>

                {{-- Deadline --}}
                <td class="px-6 py-5">

                    <div>

                        <p class="text-sm {{ $p->isOverdue() ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                            {{ $p->tanggal_kembali->format('d M Y') }}
                        </p>

                        @if($p->isOverdue() && $p->status !== 'selesai')

                            <span class="text-xs text-red-500">
                                Terlambat
                            </span>

                        @endif

                    </div>

                </td>

                {{-- Status --}}
                <td class="px-6 py-5">

                    <span class="badge {{ $p->status_config['color'] }}">
                        {{ $p->status_label }}
                    </span>

                </td>

                {{-- Bukti --}}
                <td class="px-6 py-5 text-center">

                    @if($p->foto_bukti_kembali)

                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal-bukti-{{ $p->id }}'))"
                            class="w-10 h-10 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D9EAFE] transition">

                            <i class="fas fa-image"></i>

                        </button>

                    @else

                        <span class="text-slate-300">
                            <i class="fas fa-image"></i>
                        </span>

                    @endif

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5 text-center">

                    @if($p->status === 'menunggu_verifikasi')

                        <form
                            action="{{ route('admin.pengembalian.verify', $p->id) }}"
                            method="POST"
                            onsubmit="return confirm('Verifikasi pengembalian alat ini?');">

                            @csrf

                            <input
                                type="hidden"
                                name="kondisi_kembali"
                                value="baik">

                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm hover:bg-emerald-700 transition">

                                <i class="fas fa-check mr-1"></i>
                                Verifikasi

                            </button>

                        </form>

                    @elseif($p->status === 'selesai')

                        <span class="text-emerald-600 font-semibold text-sm">
                            Selesai
                        </span>

                    @else

                        <span class="text-slate-400 text-sm">
                            Menunggu
                        </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7" class="py-14 text-center">

                    <div class="flex flex-col items-center">

                        <i class="fas fa-inbox text-5xl text-slate-300 mb-4"></i>

                        <h3 class="font-bold text-lg text-[#1E2B4A]">
                            Belum Ada Data Pengembalian
                        </h3>

                        <p class="text-slate-500">
                            Data pengembalian alat akan muncul di sini.
                        </p>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>
    <x-pagination :data="$pengembalian" />

</div>
@foreach($pengembalian as $p)

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