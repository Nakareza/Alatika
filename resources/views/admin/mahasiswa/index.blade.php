@extends('layouts.admin')

@section('title', 'Data Mahasiswa')

@section('content')

<div class="space-y-6">

    {{-- Header Action --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Data Mahasiswa</h2>
            <p class="text-sm text-slate-500 mt-1">
                Daftar mahasiswa yang terdaftar di sistem peminjaman
            </p>
        </div>

        <button class="btn btn-secondary">
            <i class="fas fa-file-export"></i>
            Export
        </button>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                </div>

                <span class="badge badge-info">
                    Total
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">256</h3>
            <p class="text-sm text-slate-500 mt-1">Total Mahasiswa</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-user-check text-emerald-600 text-lg"></i>
                </div>

                <span class="badge badge-success">
                    Aktif
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">18</h3>
            <p class="text-sm text-slate-500 mt-1">Sedang Meminjam</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-history text-indigo-600 text-lg"></i>
                </div>

                <span class="badge bg-indigo-100 text-indigo-700">
                    Riwayat
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">142</h3>
            <p class="text-sm text-slate-500 mt-1">Total Semua Peminjaman</p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center">
                    <i class="fas fa-calendar-plus text-sky-600 text-lg"></i>
                </div>

                <span class="badge bg-sky-100 text-sky-700">
                    Baru
                </span>
            </div>

            <h3 class="text-3xl font-bold text-slate-800">12</h3>
            <p class="text-sm text-slate-500 mt-1">Registrasi Bulan Ini</p>
        </div>

    </div>

    {{-- Filter --}}
    <div class="card p-6">
        <div class="flex flex-col lg:flex-row gap-4">

            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    placeholder="Cari nama, NIM, atau email mahasiswa..."
                    class="inp pl-11"
                >
            </div>

            <select class="inp lg:w-64">
                <option value="">Semua Prodi</option>
                <option value="te">Teknik Elektronika</option>
                <option value="ti">Teknik Informatika</option>
                <option value="tkj">Teknik Komputer Jaringan</option>
                <option value="tl">Teknik Listrik</option>
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

                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">#</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Mahasiswa</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">NIM</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Email</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Prodi</th>
                        <th class="text-center text-xs font-semibold text-slate-500 uppercase px-6 py-4">Peminjaman</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase px-6 py-4">Status</th>
                        <th class="text-center text-xs font-semibold text-slate-500 uppercase px-6 py-4">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @php
                        $mahasiswa = [
                            ['nama' => 'Ahmad Rizki Saputra', 'nim' => '23010001', 'email' => 'ahmad.rizki@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 5, 'aktif' => 1, 'status' => 'aktif'],
                            ['nama' => 'Siti Nurhaliza', 'nim' => '23010002', 'email' => 'siti.nur@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 3, 'aktif' => 1, 'status' => 'aktif'],
                            ['nama' => 'Budi Santoso', 'nim' => '23010003', 'email' => 'budi.santoso@student.polines.ac.id', 'prodi' => 'Teknik Informatika', 'total_pinjam' => 8, 'aktif' => 0, 'status' => 'idle'],
                            ['nama' => 'Dewi Lestari', 'nim' => '23010004', 'email' => 'dewi.lestari@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 2, 'aktif' => 1, 'status' => 'aktif'],
                            ['nama' => 'Eko Prasetyo', 'nim' => '23010005', 'email' => 'eko.pras@student.polines.ac.id', 'prodi' => 'Teknik Komputer Jaringan', 'total_pinjam' => 12, 'aktif' => 0, 'status' => 'idle'],
                        ];
                    @endphp

                    @foreach($mahasiswa as $index => $m)

                    <tr class="hover:bg-slate-50 transition">

                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($m['nama'], 0, 1)) }}
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-slate-800">
                                        {{ $m['nama'] }}
                                    </h4>
                                </div>

                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-xs bg-sky-50 text-sky-700 px-3 py-1 rounded-lg font-mono font-medium">
                                {{ $m['nim'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $m['email'] }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">
                                {{ $m['prodi'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">

                            <div class="flex flex-col items-center">

                                <span class="text-sm font-bold text-slate-800">
                                    {{ $m['total_pinjam'] }}
                                </span>

                                @if($m['aktif'] > 0)
                                    <span class="text-[11px] text-blue-600 font-medium">
                                        {{ $m['aktif'] }} aktif
                                    </span>
                                @endif

                            </div>

                        </td>

                        <td class="px-6 py-4">

                            @if($m['status'] === 'aktif')

                                <span class="badge badge-success">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                    Aktif Meminjam
                                </span>

                            @elseif($m['status'] === 'terlambat')

                                <span class="badge badge-danger">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Terlambat
                                </span>

                            @else

                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                    Tidak Aktif
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center justify-center gap-2">

                                <button
                                    class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition"
                                    title="Detail"
                                >
                                    <i class="fas fa-eye text-sm"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">

            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-semibold">1-10</span>
                dari
                <span class="font-semibold">256</span>
                mahasiswa
            </p>

            <div class="flex items-center gap-2">

                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                    Prev
                </button>

                <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm">
                    1
                </button>

                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                    2
                </button>

                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                    3
                </button>

                <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm hover:bg-slate-50">
                    Next
                </button>

            </div>

        </div>

    </div>

</div>

@endsection