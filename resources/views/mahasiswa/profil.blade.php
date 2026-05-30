@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

   <div class="max-w-4xl mx-auto space-y-6"
     x-data="{ showLogoutModal: false }"
     @open-modal-logout.window="showLogoutModal = true">

        {{-- Profile Card --}}
        <div class="card overflow-hidden">
            <div class="h-28 relative" style="background:linear-gradient(135deg,#1E2B4A 0%,#185FA5 100%);">
                <div class="absolute -bottom-10 left-6">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center border-4 border-white"
                         style="background:white;box-shadow:0 8px 24px rgba(30,43,74,0.15);">
                        <span class="text-3xl font-bold" style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="pt-14 px-6 pb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ Auth::user()->name }}</h2>
                        <p class="text-sm mt-0.5" style="color:#94a3b8;">{{ Auth::user()->email }}</p>
                        <span class="badge badge-info mt-2">
                            <i class="fas fa-shield-alt mr-1"></i> Mahasiswa
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6" style="border-top:1px solid #EBF3FD;">
                    <div class="rounded-xl p-4" style="background:#F5F8FF;">
                        <p class="text-xs font-semibold mb-1" style="color:#94a3b8;">NIM</p>
                        <p class="text-sm font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ Auth::user()->nim ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:#F5F8FF;">
                        <p class="text-xs font-semibold mb-1" style="color:#94a3b8;">Role</p>
                        <p class="text-sm font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:#F5F8FF;">
                        <p class="text-xs font-semibold mb-1" style="color:#94a3b8;">Terdaftar Sejak</p>
                        <p class="text-sm font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ Auth::user()->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Telegram Integration --}}
        @include('components.telegram-connect')

        {{-- Account Actions --}}
        <div class="card p-6">
            <h3 class="text-base font-bold mb-4 flex items-center gap-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                <i class="fas fa-cog" style="color:#94a3b8;"></i> Akun
            </h3>
            <div class="space-y-2">
                <button @click="$dispatch('open-modal-logout')"
                        class="w-full flex items-center gap-3 p-3 rounded-xl border border-transparent transition-all text-left"
                        style="background:#F5F8FF;"
                        onmouseover="this.style.background='#fee2e2';this.style.borderColor='#fca5a5';"
                        onmouseout="this.style.background='#F5F8FF';this.style.borderColor='transparent';">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:#fee2e2;color:#ef4444;">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Logout</p>
                        <p class="text-xs" style="color:#94a3b8;">Keluar dari akun Anda</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto" style="color:#cbd5e1;"></i>
                </button>
            </div>
        </div>

    </div>

    {{-- Logout Modal --}}
    <div x-show="showLogoutModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showLogoutModal = false">
        <div x-show="showLogoutModal" x-transition
             class="fixed inset-0"
             style="background:rgba(30,43,74,0.5);backdrop-filter:blur(4px);"
             @click="showLogoutModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showLogoutModal" x-transition
                 class="card relative max-w-md w-full p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center"
                         style="background:#fee2e2;">
                        <i class="fas fa-sign-out-alt text-2xl" style="color:#ef4444;"></i>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold mb-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Konfirmasi Logout</h3>
                    <p class="text-sm" style="color:#64748b;">Apakah Anda yakin ingin keluar?</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false" class="btn btn-secondary flex-1 justify-center">
                        Batal
                    </button>
                    <button type="button" 
                            @click="document.getElementById('logoutFormModal').submit()"
                            class="w-full py-3 rounded-xl text-white font-semibold text-sm transition-all flex-1"
                            style="background:linear-gradient(135deg,#ef4444,#dc2626);box-shadow:0 4px 14px rgba(239,68,68,0.3);"
                            onmouseover="this.style.filter='brightness(1.1)'"
                            onmouseout="this.style.filter=''">
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Logout Form --}}
    <form action="{{ route('logout') }}" method="POST" id="logoutFormModal" style="display: none;">
        @csrf
    </form>

@endsection