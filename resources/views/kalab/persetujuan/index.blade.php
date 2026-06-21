@extends('layouts.kalab')

@section('title', 'Persetujuan Peminjaman')

@section('content')

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="mb-6 card p-4 flex items-center gap-3 border-l-4 border-green-500">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>

            <div>
                <h4 class="font-semibold text-green-700 text-sm">
                    Berhasil
                </h4>
                <p class="text-sm text-green-600">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">

        <x-card-stats
            title="Menunggu Persetujuan"
            :value="$stats['pending']"
            icon="fas fa-clock"
            color="yellow" />

        <x-card-stats
            title="Dipinjam"
            :value="$stats['dipinjam']"
            icon="fas fa-hand-holding"
            color="blue" />

        <x-card-stats
            title="Selesai"
            :value="$stats['selesai']"
            icon="fas fa-check-double"
            color="green" />

        <x-card-stats
            title="Total Pengajuan"
            :value="$stats['total_pengajuan']"
            icon="fas fa-file-alt"
            color="Purple" />

    </div>
    
    {{-- Filter & Search --}}
    
    <div class="card p-6 mb-6">

        <form method="GET" action="{{ route('kalab.persetujuan') }}">

            <div class="flex flex-col lg:flex-row gap-4">

                {{-- Search --}}
                <div class="flex-1 relative">

                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari dosen, alat, atau kode peminjaman..."
                        class="inp pl-11 w-full">

                </div>

                {{-- Status --}}
                <select
                    name="status"
                    class="inp lg:w-56"
                    onchange="this.form.submit()">

                    <option value="">Semua Status</option>

                    <option value="pending"
                        {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Menunggu Persetujuan
                    </option>

                    <option value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                        Dipinjam
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

                {{-- Periode --}}
                <select
                    name="periode"
                    class="inp lg:w-48"
                    onchange="this.form.submit()">

                    <option value="">Semua Periode</option>

                    <option value="hari_ini"
                        {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>
                        Hari Ini
                    </option>

                    <option value="minggu_ini"
                        {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>
                        Minggu Ini
                    </option>

                    <option value="bulan_ini"
                        {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>
                        Bulan Ini
                    </option>

                </select>

                {{-- Reset --}}
                <a href="{{ route('kalab.persetujuan') }}"
                class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">

                    <i class="fas fa-rotate-left"></i>

                </a>

            </div>

        </form>

    </div>


    {{-- Table Card --}}
    <div class="card overflow-hidden"
         x-data="{ selectAll: false }">

        {{-- Action Bar --}}
        <form action="{{ route('kalab.persetujuan.bulk-approve') }}"
            method="POST"
            x-data="{ selectAll: false }">

            @csrf

            <x-table title="Monitoring Peminjaman">

                <thead class="bg-[#F5F8FF]">

                    <tr>

                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase text-slate-500">No</th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Peminjam
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Alat
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Jumlah
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Tanggal Pinjam
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($peminjaman as $p)

                    <tr class="hover:bg-[#F8FBFF] transition">

                        {{-- no --}}
                        <td class="px-4 py-4 text-center font-medium text-slate-600">
                            {{ $loop->iteration }}
                        </td>

                        {{-- Dosen --}}
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-xl bg-[#185FA5] text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($p->user->name, 0, 2)) }}
                                </div>

                                <div>

                                    <p class="font-semibold text-[#1E2B4A]">
                                        {{ $p->user->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Dosen
                                    </p>

                                </div>

                            </div>

                        </td>

                        {{-- Alat --}}
                        <td class="px-6 py-4">

                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $p->alat->nama }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $p->alat->kode }}
                            </p>

                        </td>

                        {{-- Jumlah --}}
                        <td class="px-6 py-4">

                            <span class="badge badge-info">
                                {{ $p->jumlah }} Unit
                            </span>

                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4">

                            <p class="text-sm text-slate-700">
                                {{ $p->tanggal_pinjam->format('d M Y') }}
                            </p>

                            <p class="text-xs text-slate-500">
                                s/d {{ $p->tanggal_kembali->format('d M Y') }}
                            </p>

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">

                            @if($p->status == 'pending')

                                <span class="badge badge-warning">
                                    Menunggu Persetujuan
                                </span>

                            @elseif($p->status == 'dipinjam')

                                <span class="badge badge-info">
                                    Sedang Dipinjam
                                </span>

                            @elseif($p->status == 'selesai')

                                <span class="badge badge-success">
                                    Selesai
                                </span>

                            @elseif($p->status == 'ditolak')

                                <span class="badge badge-danger">
                                    Ditolak
                                </span>

                            @endif

                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">

                            @if($p->status == 'pending')

                                <div class="flex justify-center gap-2">

                                    <button
                                        type="submit"
                                        formaction="{{ route('kalab.persetujuan.approve',$p->id) }}"
                                        class="w-9 h-9 rounded-lg bg-green-100 text-green-700">

                                        <i class="fas fa-check"></i>

                                    </button>

                                    <button
                                        type="button"
                                        onclick="document.getElementById('reject-form-{{ $p->id }}').submit()"
                                        class="w-9 h-9 rounded-lg bg-red-100 text-red-700">

                                        <i class="fas fa-times"></i>

                                    </button>

                                </div>

                            @elseif($p->status == 'dipinjam')

                                <button
                                    type="button"
                                    onclick="showDetail(
                                        '{{ $p->kode_peminjaman }}',
                                        '{{ $p->user->name }}',
                                        '{{ $p->alat->nama }}',
                                        '{{ $p->jumlah }}',
                                        '{{ $p->tanggal_pinjam->format('d M Y') }}',
                                        '{{ $p->tanggal_kembali->format('d M Y') }}',
                                        '{{ $p->status }}',
                                        '{{ addslashes($p->keperluan) }}'
                                    )"
                                    class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">

                                    <i class="fas fa-eye"></i>

                                </button>

                            @elseif($p->status == 'selesai')

                                <button
                                    type="button"
                                    class="w-9 h-9 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">

                                    <i class="fas fa-eye"></i>

                                </button>

                            @elseif($p->status == 'ditolak')

                                <button
                                    type="button"
                                    class="w-9 h-9 rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200">

                                    <i class="fas fa-circle-info"></i>

                                </button>

                            @endif
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="py-12 text-center">

                            <div class="flex flex-col items-center">

                                <i class="fas fa-check-circle text-4xl text-slate-300 mb-3"></i>

                                <p class="font-semibold text-[#1E2B4A]">
                                    Tidak Ada Pengajuan
                                </p>

                                <p class="text-sm text-slate-500">
                                    Semua pengajuan telah diproses.
                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </x-table>

        </form>

    </div>

    {{-- Hidden Reject Forms --}}
    @foreach($peminjaman as $p)

        <form id="reject-form-{{ $p->id }}"
              action="{{ route('kalab.persetujuan.reject', $p->id) }}"
              method="POST"
              class="hidden">

            @csrf

            <input type="hidden"
                   name="alasan"
                   value="Ditolak secara individual oleh Kepala Laboratorium">

        </form>

    @endforeach

<x-modal
    name="detail-peminjaman"
    title="Detail Peminjaman"
    size="lg"
    type="default">

    <div class="grid grid-cols-2 gap-4 text-sm">

        <div>
            <p class="text-slate-500">Kode Peminjaman</p>
            <p id="detail_kode" class="font-semibold text-[#1E2B4A]"></p>
        </div>

        <div>
            <p class="text-slate-500">Peminjam</p>
            <p id="detail_user" class="font-semibold text-[#1E2B4A]"></p>
        </div>

        <div>
            <p class="text-slate-500">Alat</p>
            <p id="detail_alat" class="font-semibold text-[#1E2B4A]"></p>
        </div>

        <div>
            <p class="text-slate-500">Jumlah</p>
            <p id="detail_jumlah" class="font-semibold text-[#1E2B4A]"></p>
        </div>

        <div>
            <p class="text-slate-500">Tanggal Pinjam</p>
            <p id="detail_pinjam"></p>
        </div>

        <div>
            <p class="text-slate-500">Tanggal Kembali</p>
            <p id="detail_kembali"></p>
        </div>

        <div class="col-span-2">
            <p class="text-slate-500">Status</p>
            <p id="detail_status"></p>
        </div>

        <div class="col-span-2">
            <p class="text-slate-500">Keperluan</p>
            <p id="detail_keperluan"></p>
        </div>

    </div>

    <x-slot:footer>

        <button
            type="button"
            onclick="window.dispatchEvent(
                new CustomEvent('close-modal-detail-peminjaman')
            )"
            class="flex-1 px-4 py-3 rounded-xl border border-slate-200">

            Tutup

        </button>

    </x-slot:footer>

</x-modal>

<script>

function showDetail(
    kode,
    user,
    alat,
    jumlah,
    pinjam,
    kembali,
    status,
    keperluan
){

    document.getElementById('detail_kode').innerText = kode;
    document.getElementById('detail_user').innerText = user;
    document.getElementById('detail_alat').innerText = alat;
    document.getElementById('detail_jumlah').innerText = jumlah + ' Unit';
    document.getElementById('detail_pinjam').innerText = pinjam;
    document.getElementById('detail_kembali').innerText = kembali;
    document.getElementById('detail_status').innerText = status;
    document.getElementById('detail_keperluan').innerText = keperluan;

    window.dispatchEvent(
        new CustomEvent('open-modal-detail-peminjaman')
    );

}

</script>

@endsection