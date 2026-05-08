@extends('layouts.kalab')

@section('title', 'Profil Saya')

@section('content')

<div x-data="{ showLogoutModal: false }"
     class="max-w-5xl mx-auto space-y-6">

    {{-- Profile Header --}}
    <div class="card overflow-hidden">

        {{-- Banner --}}
        <div class="h-36 bg-gradient-to-r from-[#1E2B4A] via-[#185FA5] to-[#378ADD] relative">

            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-6 right-10 w-32 h-32 bg-white rounded-full"></div>
                <div class="absolute bottom-0 left-20 w-24 h-24 bg-white rounded-full"></div>
            </div>

            {{-- Avatar --}}
            <div class="absolute -bottom-12 left-8">

                <div class="w-24 h-24 rounded-3xl bg-white border-4 border-white shadow-xl flex items-center justify-center">

                    <span class="text-4xl font-extrabold bg-gradient-to-br from-[#185FA5] to-[#378ADD] bg-clip-text text-transparent"
                          style="font-family:'Plus Jakarta Sans',sans-serif;">

                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                    </span>

                </div>

            </div>

        </div>

        {{-- Profile Content --}}
        <div class="pt-16 px-8 pb-8">

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

                <div>

                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#EBF3FD] text-[#185FA5] text-sm font-semibold mb-4">
                        <i class="fas fa-user-shield"></i>
                        Kepala Laboratorium
                    </div>

                    <h1 class="text-3xl font-extrabold text-[#1E2B4A] mb-2"
                        style="font-family:'Plus Jakarta Sans',sans-serif;">

                        {{ Auth::user()->name }}

                    </h1>

                    <p class="text-slate-500 text-sm">
                        {{ Auth::user()->email }}
                    </p>

                </div>

            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">

                {{-- NIP --}}
                <div class="list-item">

                    <div class="flex items-center gap-3 mb-3">

                        <div class="w-11 h-11 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                            <i class="fas fa-id-card text-[#185FA5]"></i>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 font-medium">
                                NIP / NIDN
                            </p>

                            <p class="text-sm font-bold text-[#1E2B4A] mt-1">
                                {{ Auth::user()->nip ?? '-' }}
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Role --}}
                <div class="list-item">

                    <div class="flex items-center gap-3 mb-3">

                        <div class="w-11 h-11 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                            <i class="fas fa-user-tag text-[#185FA5]"></i>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 font-medium">
                                Role
                            </p>

                            <p class="text-sm font-bold text-[#1E2B4A] mt-1 capitalize">
                                {{ Auth::user()->role }}
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Created --}}
                <div class="list-item">

                    <div class="flex items-center gap-3 mb-3">

                        <div class="w-11 h-11 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                            <i class="fas fa-calendar-check text-[#185FA5]"></i>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 font-medium">
                                Terdaftar Sejak
                            </p>

                            <p class="text-sm font-bold text-[#1E2B4A] mt-1">
                                {{ Auth::user()->created_at->format('d M Y') }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Telegram Integration --}}
    <div class="card p-6 lg:p-8">

        <div class="flex items-center gap-3 mb-6">

            <div class="w-12 h-12 rounded-2xl bg-[#EBF3FD] flex items-center justify-center">
                <i class="fab fa-telegram-plane text-2xl text-[#185FA5]"></i>
            </div>

            <div>

                <h2 class="text-xl font-bold text-[#1E2B4A]"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Integrasi Telegram
                </h2>

                <p class="text-sm text-slate-500">
                    Hubungkan akun Telegram untuk menerima notifikasi.
                </p>

            </div>

        </div>

        @include('components.telegram-connect')

    </div>

    {{-- Account Settings --}}
    <div class="card p-6 lg:p-8">

        <div class="flex items-center gap-3 mb-6">

            <div class="w-12 h-12 rounded-2xl bg-[#EBF3FD] flex items-center justify-center">
                <i class="fas fa-cog text-xl text-[#185FA5]"></i>
            </div>

            <div>

                <h2 class="text-xl font-bold text-[#1E2B4A]"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Pengaturan Akun
                </h2>

                <p class="text-sm text-slate-500">
                    Kelola akses dan keamanan akun Anda.
                </p>

            </div>

        </div>

        {{-- Logout --}}
        <button @click="showLogoutModal = true"
                class="w-full flex items-center gap-4 p-5 rounded-2xl border border-red-100 bg-red-50 hover:bg-red-100 transition-all duration-200">

            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="fas fa-sign-out-alt text-red-600"></i>
            </div>

            <div class="text-left flex-1">

                <p class="font-bold text-red-700">
                    Logout
                </p>

                <p class="text-sm text-red-500">
                    Keluar dari akun Kepala Laboratorium
                </p>

            </div>

            <i class="fas fa-chevron-right text-red-400"></i>

        </button>

    </div>

    {{-- Logout Modal --}}
    <div x-show="showLogoutModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
             @click="showLogoutModal = false"></div>

        {{-- Modal --}}
        <div x-show="showLogoutModal"
             x-transition
             class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">

            <div class="flex flex-col items-center text-center">

                <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mb-5">

                    <i class="fas fa-sign-out-alt text-3xl text-red-600"></i>

                </div>

                <h3 class="text-2xl font-bold text-[#1E2B4A] mb-2"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">

                    Konfirmasi Logout

                </h3>

                <p class="text-sm text-slate-500 mb-8">
                    Apakah Anda yakin ingin keluar dari akun ini?
                </p>

                <div class="flex items-center gap-3 w-full">

                    <button @click="showLogoutModal = false"
                            class="btn btn-secondary flex-1 justify-center">

                        Batal

                    </button>

                    <form action="{{ route('logout') }}"
                          method="POST"
                          class="flex-1">

                        @csrf

                        <button type="submit"
                                class="btn flex justify-center w-full bg-red-600 text-white hover:bg-red-700">

                            Logout

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection