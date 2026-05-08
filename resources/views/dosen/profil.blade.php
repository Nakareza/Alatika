@extends('layouts.dosen')

@section('title', 'Profil Saya')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- Profile Card --}}
    <div class="card overflow-hidden">

        {{-- Cover --}}
        <div class="h-32 relative"
             style="background:linear-gradient(135deg,#185FA5 0%,#378ADD 100%);">

            <div class="absolute -bottom-10 left-8">

                <div class="w-24 h-24 rounded-3xl bg-white flex items-center justify-center shadow-xl border-4 border-white">

                    <span class="text-3xl font-extrabold"
                          style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">

                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>

                </div>

            </div>

        </div>

        {{-- Content --}}
        <div class="pt-16 px-8 pb-8">

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">

                <div>

                    <h2 class="text-2xl font-bold"
                        style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                        {{ Auth::user()->name }}
                    </h2>

                    <p class="text-sm mt-1"
                       style="color:#94a3b8;">

                        {{ Auth::user()->email }}
                    </p>

                    <span class="badge badge-info mt-3">
                        <i class="fas fa-user-tie mr-1.5"></i>
                        Dosen
                    </span>

                </div>

            </div>

            {{-- Info --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">

                <div class="list-item">
                    <p class="text-xs mb-1"
                       style="color:#94a3b8;">
                        NIP
                    </p>

                    <p class="font-semibold"
                       style="color:#1E2B4A;">
                        {{ Auth::user()->nip ?? '-' }}
                    </p>
                </div>

                <div class="list-item">
                    <p class="text-xs mb-1"
                       style="color:#94a3b8;">
                        Role
                    </p>

                    <p class="font-semibold capitalize"
                       style="color:#1E2B4A;">
                        {{ Auth::user()->role }}
                    </p>
                </div>

                <div class="list-item">
                    <p class="text-xs mb-1"
                       style="color:#94a3b8;">
                        Terdaftar Sejak
                    </p>

                    <p class="font-semibold"
                       style="color:#1E2B4A;">
                        {{ Auth::user()->created_at->format('d M Y') }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    {{-- Telegram --}}
    @include('components.telegram-connect')

    {{-- Account Actions --}}
    <div class="card p-6">

        <h3 class="text-lg font-bold mb-5 flex items-center gap-2"
            style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

            <i class="fas fa-cog" style="color:#B5D4F4;"></i>
            Pengaturan Akun
        </h3>

        <div class="space-y-3">

            <button
                @click="$dispatch('open-modal-logout')"
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all text-left"
                style="border:1px solid #FECACA;background:#FEF2F2;"
                onmouseover="this.style.background='#FEE2E2'"
                onmouseout="this.style.background='#FEF2F2'">

                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                     style="background:#fff;">

                    <i class="fas fa-sign-out-alt"
                       style="color:#EF4444;"></i>

                </div>

                <div class="flex-1">

                    <p class="font-semibold"
                       style="color:#B91C1C;font-family:'Plus Jakarta Sans',sans-serif;">

                        Logout
                    </p>

                    <p class="text-sm"
                       style="color:#EF4444;">

                        Keluar dari akun Anda
                    </p>

                </div>

                <i class="fas fa-chevron-right"
                   style="color:#F87171;"></i>

            </button>

        </div>

    </div>

</div>

{{-- Modal Logout --}}
<x-modal name="logout" title="Konfirmasi Logout" type="danger">

    <p class="text-sm text-center mb-1"
       style="color:#94a3b8;font-family:'Inter',sans-serif;">

        Kamu yakin mau keluar dari akun ini?
    </p>

    <x-slot name="footer">

        <button
            @click="$dispatch('close-modal-logout')"
            class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-colors"
            style="background:#F5F8FF;color:#475569;border:1px solid #EBF3FD;font-family:'Plus Jakarta Sans',sans-serif;"
            onmouseover="this.style.background='#EBF3FD'"
            onmouseout="this.style.background='#F5F8FF'">

            Batal
        </button>

        <form action="{{ route('logout') }}"
              method="POST"
              class="flex-1">

            @csrf

            <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-colors"
                    style="background:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseover="this.style.background='#185FA5'"
                    onmouseout="this.style.background='#1E2B4A'">

                Ya, Logout
            </button>

        </form>

    </x-slot>

</x-modal>

@endsection