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

    {{-- Header Card --}}
    <div class="card p-6 mb-6">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full badge-info mb-3">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Persetujuan Kepala Laboratorium</span>
                </div>

                <h1 class="text-2xl font-bold mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Persetujuan Peminjaman Dosen
                </h1>

                <p class="text-sm text-slate-500">
                    Daftar pengajuan alat dari dosen yang menunggu persetujuan.
                </p>
            </div>

            <div class="flex items-center gap-3">

                <div class="list-item flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                        <i class="fas fa-clock text-[#185FA5]"></i>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Menunggu</p>
                        <p class="font-bold text-sm text-[#1E2B4A]">
                            {{ $peminjaman->count() }} Pengajuan
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden"
         x-data="{ selectAll: false }">

        {{-- Action Bar --}}
        <form action="{{ route('kalab.persetujuan.bulk-approve') }}"
              method="POST"
              id="bulkApproveForm">

            @csrf

            <div class="p-5 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-4"
                 style="border-color:#EBF3FD;background:#F9FBFF;">

                <div class="flex items-center gap-3">

                    <input type="checkbox"
                           id="selectAllCheckbox"
                           x-model="selectAll"
                           class="w-4 h-4 rounded border-gray-300 text-[#185FA5] focus:ring-[#378ADD]">

                    <label for="selectAllCheckbox"
                           class="text-sm font-semibold text-[#1E2B4A]"
                           style="font-family:'Plus Jakarta Sans',sans-serif;">
                        Pilih Semua
                    </label>

                </div>

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-check-double"></i>
                    <span>Setujui Terpilih</span>

                </button>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr style="background:#F5F8FF;">

                            <th class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                #
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Dosen
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Alat / Jumlah
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Tanggal
                            </th>

                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                Keperluan
                            </th>

                            <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y"
                           style="divide-color:#EBF3FD;">

                        @forelse($peminjaman as $p)

                            <tr class="hover:bg-[#F9FBFF] transition-all duration-200">

                                {{-- Checkbox --}}
                                <td class="px-4 py-5 text-center">

                                    <input type="checkbox"
                                           name="peminjaman_ids[]"
                                           value="{{ $p->id }}"
                                           x-bind:checked="selectAll"
                                           class="w-4 h-4 rounded border-gray-300 text-[#185FA5] focus:ring-[#378ADD]">

                                </td>

                                {{-- Dosen --}}
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-xl bg-[#1E2B4A] text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($p->user->name, 0, 2)) }}
                                        </div>

                                        <div>
                                            <p class="font-semibold text-sm text-[#1E2B4A]">
                                                {{ $p->user->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                Dosen
                                            </p>
                                        </div>

                                    </div>

                                </td>

                                {{-- Alat --}}
                                <td class="px-6 py-5">

                                    <div class="space-y-1">

                                        <p class="text-sm font-semibold text-[#1E2B4A]">
                                            {{ $p->alat->nama }}
                                        </p>

                                        <span class="badge badge-info">
                                            {{ $p->jumlah }} Unit
                                        </span>

                                    </div>

                                </td>

                                {{-- Tanggal --}}
                                <td class="px-6 py-5">

                                    <div class="text-sm text-slate-600 space-y-1">

                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-calendar-alt text-[#378ADD] text-xs"></i>

                                            <span>
                                                {{ $p->tanggal_pinjam->format('d M Y') }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-arrow-right text-slate-400 text-xs"></i>

                                            <span>
                                                {{ $p->tanggal_kembali->format('d M Y') }}
                                            </span>
                                        </div>

                                    </div>

                                </td>

                                {{-- Keperluan --}}
                                <td class="px-6 py-5">

                                    <div class="max-w-xs">

                                        <p class="text-sm text-slate-600 leading-relaxed">
                                            {{ $p->keperluan }}
                                        </p>

                                    </div>

                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-5">

                                    <div class="flex flex-col gap-2">

                                        {{-- Approve --}}
                                        <button type="submit"
                                                formaction="{{ route('kalab.persetujuan.approve', $p->id) }}"
                                                class="btn justify-center"
                                                style="background:#dcfce7;color:#166534;padding:0.6rem 1rem;font-size:0.75rem;">

                                            <i class="fas fa-check"></i>
                                            <span>Setujui</span>

                                        </button>

                                        {{-- Reject --}}
                                        <button type="button"
                                                onclick="document.getElementById('reject-form-{{ $p->id }}').submit()"
                                                class="btn justify-center"
                                                style="background:#fee2e2;color:#b91c1c;padding:0.6rem 1rem;font-size:0.75rem;">

                                            <i class="fas fa-times"></i>
                                            <span>Tolak</span>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="py-16 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="w-20 h-20 rounded-2xl bg-[#F5F8FF] flex items-center justify-center mb-4">
                                            <i class="fas fa-check-circle text-4xl text-[#B5D4F4]"></i>
                                        </div>

                                        <h3 class="font-bold text-[#1E2B4A] mb-1"
                                            style="font-family:'Plus Jakarta Sans',sans-serif;">
                                            Tidak Ada Pengajuan
                                        </h3>

                                        <p class="text-sm text-slate-500">
                                            Semua pengajuan telah diproses.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

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

@endsection