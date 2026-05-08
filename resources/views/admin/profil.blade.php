@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Profile Card --}}
    <div class="card overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-[#378ADD] to-[#185FA5] relative">
            <div class="absolute -bottom-10 left-6">
                <div class="w-24 h-24 bg-white rounded-2xl shadow-lg flex items-center justify-center border-4 border-white">
                    <span class="text-4xl font-bold text-[#185FA5]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="pt-16 px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        {{ Auth::user()->name }}
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        {{ Auth::user()->email }}
                    </p>

                    <span class="badge badge-info mt-3">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Admin / Teknisi
                    </span>
                </div>
            </div>

            {{-- Info --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="list-item">
                    <p class="text-xs text-slate-500 font-medium mb-1">NIP</p>
                    <p class="text-sm font-semibold text-[#1E2B4A]">
                        {{ Auth::user()->nip ?? '-' }}
                    </p>
                </div>

                <div class="list-item">
                    <p class="text-xs text-slate-500 font-medium mb-1">Role</p>
                    <p class="text-sm font-semibold text-[#1E2B4A]">
                        {{ ucfirst(Auth::user()->role) }}
                    </p>
                </div>

                <div class="list-item">
                    <p class="text-xs text-slate-500 font-medium mb-1">Terdaftar Sejak</p>
                    <p class="text-sm font-semibold text-[#1E2B4A]">
                        {{ Auth::user()->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Telegram Integration --}}
    @include('components.telegram-connect')

    {{-- Account Actions --}}
    <div class="card p-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-10 h-10 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                <i class="fas fa-cog text-[#185FA5]"></i>
            </div>

            <div>
                <h3 class="text-lg font-bold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Pengaturan Akun
                </h3>
                <p class="text-sm text-slate-500">
                    Kelola akun administrator Anda
                </p>
            </div>
        </div>

        <button
            @click="showLogoutModal = true"
            class="w-full flex items-center gap-4 p-4 rounded-2xl border border-red-100 hover:bg-red-50 transition-all duration-200"
        >
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="fas fa-sign-out-alt text-red-600"></i>
            </div>

            <div class="text-left">
                <p class="text-sm font-semibold text-[#1E2B4A]">
                    Logout
                </p>
                <p class="text-xs text-slate-500">
                    Keluar dari akun administrator
                </p>
            </div>

            <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
        </button>
    </div>

</div>

{{-- Logout Modal --}}
<div
    x-show="showLogoutModal"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    @keydown.escape.window="showLogoutModal = false"
>
    {{-- Backdrop --}}
    <div
        x-show="showLogoutModal"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 modal-backdrop"
        @click="showLogoutModal = false"
    ></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            x-show="showLogoutModal"
            x-transition
            class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border border-[#EBF3FD]"
        >

            <div class="flex justify-center mb-5">
                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-red-600 text-3xl"></i>
                </div>
            </div>

            <div class="text-center mb-6">
                <h3
                    class="text-2xl font-bold text-[#1E2B4A] mb-2"
                    style="font-family:'Plus Jakarta Sans',sans-serif;"
                >
                    Konfirmasi Logout
                </h3>

                <p class="text-sm text-slate-500 leading-relaxed">
                    Apakah Anda yakin ingin keluar dari sistem?
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    @click="showLogoutModal = false"
                    class="flex-1 btn btn-secondary justify-center"
                >
                    Batal
                </button>

                <form action="{{ route('logout') }}" method="POST" class="flex-1">
                    @csrf

                    <button
                        type="submit"
                        class="w-full btn justify-center bg-red-600 hover:bg-red-700 text-white"
                    >
                        Ya, Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection