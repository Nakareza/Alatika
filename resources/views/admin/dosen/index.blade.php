@extends('layouts.admin')

@section('title', 'Data Dosen')

@section('content')

<div class="space-y-6">

    {{-- HEADER ACTION --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif; color:#1E2B4A;">
                Data Dosen
            </h1>

            <p class="text-sm mt-1 text-slate-500">
                Kelola data dosen pembimbing dan penanggung jawab laboratorium
            </p>
        </div>

        <div class="flex items-center gap-3">

            <button class="btn btn-secondary">
                <i class="fas fa-download text-sm"></i>
                Export
            </button>

            <button class="btn btn-primary">
                <i class="fas fa-plus text-sm"></i>
                Tambah Dosen
            </button>

        </div>

    </div>

    {{-- STATISTIC --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-amber-100">
                    <i class="fas fa-chalkboard-teacher text-amber-600"></i>
                </div>

                <span class="badge badge-warning">
                    Total
                </span>
            </div>

            <h2 class="mt-5 text-3xl font-bold text-slate-800">24</h2>

            <p class="mt-1 text-sm text-slate-500">
                Total Dosen
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-emerald-100">
                    <i class="fas fa-user-check text-emerald-600"></i>
                </div>

                <span class="badge badge-success">
                    Aktif
                </span>
            </div>

            <h2 class="mt-5 text-3xl font-bold text-slate-800">18</h2>

            <p class="mt-1 text-sm text-slate-500">
                Dosen Aktif
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-blue-100">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                </div>

                <span class="badge badge-info">
                    Bimbingan
                </span>
            </div>

            <h2 class="mt-5 text-3xl font-bold text-slate-800">47</h2>

            <p class="mt-1 text-sm text-slate-500">
                Mahasiswa Dibimbing
            </p>
        </div>

        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-100">
                    <i class="fas fa-clipboard-check text-indigo-600"></i>
                </div>

                <span class="badge badge-info">
                    Approval
                </span>
            </div>

            <h2 class="mt-5 text-3xl font-bold text-slate-800">132</h2>

            <p class="mt-1 text-sm text-slate-500">
                Persetujuan Peminjaman
            </p>
        </div>

    </div>

    {{-- FILTER --}}
    <div class="card p-6">

        <div class="flex flex-col lg:flex-row gap-4">

            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    placeholder="Cari nama dosen, NIP, atau email..."
                    class="inp pl-11"
                >
            </div>

            <select class="inp lg:w-60">
                <option>Semua Jabatan</option>
                <option>Lektor</option>
                <option>Lektor Kepala</option>
                <option>Asisten Ahli</option>
                <option>Profesor</option>
            </select>

            <button class="btn btn-primary">
                <i class="fas fa-filter"></i>
                Filter
            </button>

        </div>

    </div>

    @php
        $dosen = [
            ['nama' => 'Dr. Ir. Suryono, M.T.', 'nip' => '198501152010011001', 'email' => 'suryono@polines.ac.id', 'jabatan' => 'Lektor Kepala', 'mhs_bimbingan' => 8, 'status' => 'aktif'],
            ['nama' => 'Dr. Rina Setiawati, M.Sc.', 'nip' => '198703082011012002', 'email' => 'rina.setiawati@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 5, 'status' => 'aktif'],
            ['nama' => 'Ir. Bambang Triatmoko, M.T.', 'nip' => '197806232005011003', 'email' => 'bambang.tri@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 6, 'status' => 'aktif'],
            ['nama' => 'Dr. Andi Firmansyah, M.Eng.', 'nip' => '198909142013011004', 'email' => 'andi.firmansyah@polines.ac.id', 'jabatan' => 'Asisten Ahli', 'mhs_bimbingan' => 4, 'status' => 'aktif'],
            ['nama' => 'Dra. Sri Wahyuningsih, M.Pd.', 'nip' => '196512041990032005', 'email' => 'sri.wahyu@polines.ac.id', 'jabatan' => 'Lektor Kepala', 'mhs_bimbingan' => 3, 'status' => 'aktif'],
            ['nama' => 'Ir. Hadi Pranoto, M.T.', 'nip' => '197703152003011006', 'email' => 'hadi.pranoto@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 7, 'status' => 'aktif'],
            ['nama' => 'Dr. Novita Kusuma, M.Kom.', 'nip' => '199005272015012007', 'email' => 'novita.kusuma@polines.ac.id', 'jabatan' => 'Asisten Ahli', 'mhs_bimbingan' => 2, 'status' => 'cuti'],
            ['nama' => 'Ir. Agus Widodo, M.Eng.', 'nip' => '198201182008011008', 'email' => 'agus.widodo@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 0, 'status' => 'aktif'],
        ];

        $jabatanColors = [
            'Profesor' => 'bg-purple-100 text-purple-700',
            'Lektor Kepala' => 'bg-amber-100 text-amber-700',
            'Lektor' => 'bg-blue-100 text-blue-700',
            'Asisten Ahli' => 'bg-sky-100 text-sky-700',
        ];
    @endphp

    {{-- TABLE --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-[#F8FBFF] border-b border-[#EBF3FD]">

                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Dosen
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            NIP
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Email
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Jabatan
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Bimbingan
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Aksi
                        </th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-[#EBF3FD] bg-white">

                    @foreach($dosen as $index => $d)

                    <tr class="hover:bg-[#F8FBFF] transition-all">

                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $index + 1 }}
                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($d['nama'], 0, 1)) }}
                                </div>

                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800">
                                        {{ $d['nama'] }}
                                    </h3>

                                    <p class="text-xs text-slate-500">
                                        Dosen Pembimbing
                                    </p>
                                </div>

                            </div>

                        </td>

                        <td class="px-6 py-4">

                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium font-mono">
                                {{ $d['nip'] }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-sm text-slate-600">
                            {{ $d['email'] }}
                        </td>

                        <td class="px-6 py-4">

                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $jabatanColors[$d['jabatan']] }}">
                                {{ $d['jabatan'] }}
                            </span>

                        </td>

                        <td class="px-6 py-4 text-center">

                            <span class="text-sm font-bold text-slate-800">
                                {{ $d['mhs_bimbingan'] }}
                            </span>

                        </td>

                        <td class="px-6 py-4">

                            @if($d['status'] === 'aktif')

                                <span class="badge badge-success">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                                    Aktif
                                </span>

                            @else

                                <span class="badge bg-slate-100 text-slate-500">
                                    <span class="w-2 h-2 rounded-full bg-slate-400 mr-2"></span>
                                    Cuti
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4">

                            <div class="flex items-center justify-center gap-2">

                                <button class="w-9 h-9 rounded-xl text-blue-600 hover:bg-blue-50 transition-all">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>

                                <button class="w-9 h-9 rounded-xl text-amber-600 hover:bg-amber-50 transition-all">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>

                                <button class="w-9 h-9 rounded-xl text-red-600 hover:bg-red-50 transition-all">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        <div class="px-6 py-4 border-t border-[#EBF3FD] flex flex-col md:flex-row items-center justify-between gap-4">

            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-semibold text-slate-700">1-8</span>
                dari
                <span class="font-semibold text-slate-700">24</span>
                dosen
            </p>

            <div class="flex items-center gap-2">

                <button class="h-9 px-4 rounded-xl border border-[#D4E6F8] hover:bg-[#F5F8FF] text-sm text-slate-600 transition-all">
                    Prev
                </button>

                <button class="w-9 h-9 rounded-xl bg-[#1E2B4A] text-white text-sm font-semibold">
                    1
                </button>

                <button class="w-9 h-9 rounded-xl border border-[#D4E6F8] hover:bg-[#F5F8FF] text-sm text-slate-600 transition-all">
                    2
                </button>

                <button class="w-9 h-9 rounded-xl border border-[#D4E6F8] hover:bg-[#F5F8FF] text-sm text-slate-600 transition-all">
                    3
                </button>

                <button class="h-9 px-4 rounded-xl border border-[#D4E6F8] hover:bg-[#F5F8FF] text-sm text-slate-600 transition-all">
                    Next
                </button>

            </div>

        </div>

    </div>

</div>

@endsection