@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')

<div 
    x-data="{ 
        showLogoutModal: false, 
        showDeleteModal: false, 
        deleteUserId: null, 
        deleteUserName: '', 
        showRoleModal: false, 
        roleUserId: null, 
        roleUserName: '', 
        roleValue: '' 
    }"
    class="space-y-6"
>

    {{-- Header Action --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Kelola User
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Manajemen akun pengguna sistem laboratorium
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Tambah User
        </a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            class="card border border-emerald-200 bg-emerald-50 p-4 flex items-center gap-3"
        >
            <i class="fas fa-check-circle text-emerald-600"></i>
            <p class="text-sm text-emerald-700 font-medium">
                {{ session('success') }}
            </p>

            <button @click="show = false" class="ml-auto text-emerald-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            class="card border border-red-200 bg-red-50 p-4 flex items-center gap-3"
        >
            <i class="fas fa-exclamation-circle text-red-600"></i>

            <p class="text-sm text-red-700 font-medium">
                {{ session('error') }}
            </p>

            <button @click="show = false" class="ml-auto text-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card p-6">
        <form 
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex flex-col lg:flex-row gap-4"
        >

            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, email, NIM, atau NIP..."
                    class="inp pl-11"
                >
            </div>

            <select 
                name="role"
                onchange="this.form.submit()"
                class="inp lg:w-60"
            >
                <option value="">Semua Role</option>

                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="kalab" {{ request('role') === 'kalab' ? 'selected' : '' }}>
                    KA Lab
                </option>

                <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>
                    Dosen
                </option>

                <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>
                    Mahasiswa
                </option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i>
                Filter
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px]">

                <thead class="bg-[#F5F8FF] border-b border-[#EBF3FD]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">NIM / NIP</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">Terdaftar</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#EBF3FD]">

                    @forelse($users as $index => $user)

                        @php
                            $roleColors = [
                                'admin' => 'badge-info',
                                'kalab' => 'badge-success',
                                'dosen' => 'badge-warning',
                                'mahasiswa' => 'bg-sky-100 text-sky-700',
                            ];

                            $roleLabels = [
                                'admin' => 'Admin',
                                'kalab' => 'KA Lab',
                                'dosen' => 'Dosen',
                                'mahasiswa' => 'Mahasiswa',
                            ];
                        @endphp

                        <tr class="hover:bg-[#F8FBFF] transition">

                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $users->firstItem() + $index }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold
                                        @if($user->role === 'admin')
                                            bg-gradient-to-br from-blue-500 to-blue-700
                                        @elseif($user->role === 'kalab')
                                            bg-gradient-to-br from-emerald-500 to-teal-600
                                        @elseif($user->role === 'dosen')
                                            bg-gradient-to-br from-amber-500 to-orange-600
                                        @else
                                            bg-gradient-to-br from-sky-400 to-blue-500
                                        @endif
                                    ">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $user->name }}
                                        </p>
                                    </div>

                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4">

                                @if($user->nim)
                                    <span class="badge bg-sky-100 text-sky-700 font-mono">
                                        {{ $user->nim }}
                                    </span>

                                @elseif($user->nip)
                                    <span class="badge bg-indigo-100 text-indigo-700 font-mono">
                                        {{ $user->nip }}
                                    </span>

                                @else
                                    <span class="text-slate-400 text-sm">—</span>
                                @endif

                            </td>

                            <td class="px-6 py-4">
                                <span class="badge {{ $roleColors[$user->role] ?? 'badge-info' }}">
                                    {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- Edit Role --}}
                                    <button
                                        @click="
                                            showRoleModal = true;
                                            roleUserId = {{ $user->id }};
                                            roleUserName = '{{ $user->name }}';
                                            roleValue = '{{ $user->role }}';
                                        "
                                        class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition"
                                    >
                                        <i class="fas fa-user-shield text-sm"></i>
                                    </button>

                                    {{-- Delete --}}
                                    @if($user->id !== auth()->id())

                                    <button
                                        @click="
                                            showDeleteModal = true;
                                            deleteUserId = {{ $user->id }};
                                            deleteUserName = '{{ $user->name }}';
                                        "
                                        class="w-9 h-9 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition"
                                    >
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="py-16 text-center">

                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                        <i class="fas fa-users-slash text-2xl text-slate-400"></i>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-700">
                                            Tidak ada user ditemukan
                                        </p>

                                        <p class="text-sm text-slate-400 mt-1">
                                            Coba gunakan keyword lain
                                        </p>
                                    </div>
                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-[#EBF3FD]">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif

    </div>

</div>

@endsection