@extends('layouts.admin')

@section('title', 'Kelola Pengembalian')

@section('content')

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                     style="background:#FEF3C7;">
                    <i class="fas fa-hourglass-half text-lg" style="color:#D97706;"></i>
                </div>

                <span class="badge badge-warning">
                    Menunggu
                </span>
            </div>

            <h3 class="text-3xl font-extrabold mb-1" style="color:#1E2B4A;">
                {{ $stats['dipinjam'] }}
            </h3>

            <p class="text-sm" style="color:#64748b;">
                Menunggu Dikembalikan
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                     style="background:#FEE2E2;">
                    <i class="fas fa-exclamation-triangle text-lg" style="color:#DC2626;"></i>
                </div>

                <span class="badge badge-danger">
                    Verifikasi
                </span>
            </div>

            <h3 class="text-3xl font-extrabold mb-1" style="color:#1E2B4A;">
                {{ $stats['verifikasi'] }}
            </h3>

            <p class="text-sm" style="color:#64748b;">
                Butuh Verifikasi
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                     style="background:#D1FAE5;">
                    <i class="fas fa-check-circle text-lg" style="color:#059669;"></i>
                </div>

                <span class="badge badge-success">
                    Selesai
                </span>
            </div>

            <h3 class="text-3xl font-extrabold mb-1" style="color:#1E2B4A;">
                {{ $stats['selesai'] }}
            </h3>

            <p class="text-sm" style="color:#64748b;">
                Sudah Dikembalikan
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                     style="background:#EBF3FD;">
                    <i class="fas fa-undo-alt text-lg" style="color:#185FA5;"></i>
                </div>

                <span class="badge badge-info">
                    Total
                </span>
            </div>

            <h3 class="text-3xl font-extrabold mb-1" style="color:#1E2B4A;">
                {{ $stats['total'] }}
            </h3>

            <p class="text-sm" style="color:#64748b;">
                Total Peminjaman Aktif
            </p>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row gap-4">

            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2"
                   style="color:#94a3b8;"></i>

                <input type="text"
                       placeholder="Cari nama mahasiswa atau kode alat..."
                       class="inp pl-11">
            </div>

            <select class="inp lg:w-64">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu Kembali</option>
                <option value="terlambat">Terlambat</option>
                <option value="dikembalikan">Sudah Dikembalikan</option>
            </select>

            <button class="btn btn-primary">
                <i class="fas fa-filter"></i>
                Filter
            </button>

        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead style="background:#F5F8FF;">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">#</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Mahasiswa</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Alat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Tgl Pinjam</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Deadline</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Kondisi</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($pengembalian as $index => $p)

                    <tr class="border-t hover:bg-[#F8FBFF] transition">

                        <td class="px-6 py-4 text-sm">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold"
                                     style="background:#1E2B4A;">
                                    {{ strtoupper(substr($p->user->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-semibold text-sm">
                                        {{ $p->user->name }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        {{ $p->user->nim ?? '-' }}
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-4">
                            <p class="font-semibold text-sm">
                                {{ $p->alat->nama }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ $p->kode_peminjaman }}
                            </p>
                        </td>

                        <td class="px-6 py-4 text-sm">
                            {{ $p->tanggal_pinjam->format('d M Y') }}
                        </td>

                        <td class="px-6 py-4">

                            <span class="text-sm {{ $p->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                                {{ $p->tanggal_kembali->format('d M Y') }}
                            </span>

                            @if($p->isOverdue())
                                <p class="text-xs text-red-500 mt-1">
                                    Terlambat
                                </p>
                            @endif

                        </td>

                        <td class="px-6 py-4">

                            @if($p->kondisi_kembali)

                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ ucfirst($p->kondisi_kembali) }}
                                </span>

                            @else

                                <span class="text-xs text-slate-400">
                                    Belum dicek
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4">

                            <span class="badge {{ $p->status_config['color'] }}">
                                <i class="fas {{ $p->status_config['icon'] }} mr-1"></i>
                                {{ $p->status_label }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-center">

                            @if($p->status !== 'selesai')

                                <form action="{{ route('admin.pengembalian.verify', $p->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Verifikasi pengembalian alat ini?');">

                                    @csrf

                                    <input type="hidden"
                                           name="kondisi_kembali"
                                           value="baik">

                                    <button type="submit"
                                            class="btn btn-primary text-xs px-4 py-2">
                                        <i class="fas fa-check"></i>
                                        Verifikasi
                                    </button>

                                </form>

                            @else

                                <span class="text-sm font-semibold text-emerald-600">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Selesai
                                </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="py-10 text-center text-slate-500">
                            Belum ada data pengembalian alat.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection