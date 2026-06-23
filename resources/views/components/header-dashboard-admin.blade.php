{{-- Header Dashboard Admin --}}
@props([
    'title' => 'Dashboard',
    'subtitle' => null,
    'breadcrumbs' => []
])

<header class="sticky top-0 z-30 h-16"
        style="background:#1E2B4A;border-bottom:1px solid rgba(255,255,255,0.08);">

    <div class="px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center justify-between h-full">

            {{-- LEFT --}}
            <div class="flex items-center gap-3">

                {{-- Hamburger --}}
                <button @click="$dispatch('toggle-sidebar')"
                        class="p-1.5 rounded-lg transition-colors"
                        style="color:rgba(255,255,255,0.6);"
                        onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'"
                        onmouseout="this.style.background='';this.style.color='rgba(255,255,255,0.6)'">

                    <i class="fas fa-bars text-lg"></i>
                </button>

                {{-- Title --}}
                <div>
                    <div class="flex items-center gap-3">

                        <h1 class="text-xl font-bold"
                            style="color:#fff;font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.01em;">
                            {{ $title }}
                        </h1>

                    </div>

                    {{-- Subtitle --}}
                    @if($subtitle)
                        <p class="text-xs mt-0.5"
                           style="color:rgba(255,255,255,0.5);font-family:'Inter',sans-serif;">
                            {{ $subtitle }}
                        </p>
                    @endif

                    {{-- Breadcrumb --}}
                    @if(count($breadcrumbs) > 0)
                        <nav class="flex items-center gap-1.5 text-xs mt-0.5">

                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-1.5 transition-colors"
                               style="color:rgba(255,255,255,0.5);"
                               onmouseover="this.style.color='#B5D4F4'"
                               onmouseout="this.style.color='rgba(255,255,255,0.5)'">

                                <img src="{{ asset('images/logo-polines.png') }}"
                                     alt="Logo"
                                     class="w-3.5 h-3.5 object-contain opacity-70">

                                Dashboard
                            </a>

                            @foreach($breadcrumbs as $crumb)

                                <i class="fas fa-chevron-right text-[10px]"
                                   style="color:rgba(255,255,255,0.2);"></i>

                                @if($loop->last)
                                    <span style="color:rgba(255,255,255,0.85);font-weight:600;">
                                        {{ $crumb['name'] }}
                                    </span>
                                @else
                                    <a href="{{ $crumb['url'] }}"
                                       class="transition-colors"
                                       style="color:rgba(255,255,255,0.5);"
                                       onmouseover="this.style.color='#B5D4F4'"
                                       onmouseout="this.style.color='rgba(255,255,255,0.5)'">

                                        {{ $crumb['name'] }}
                                    </a>
                                @endif

                            @endforeach
                        </nav>
                    @endif
                </div>
            </div>

            {{-- RIGHT --}}
            <div class="flex items-center gap-2">


                {{-- Notifications --}}
                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open"
                            class="relative p-1.5 rounded-lg transition-colors"
                            style="color:rgba(255,255,255,0.6);"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'"
                            onmouseout="this.style.background='';this.style.color='rgba(255,255,255,0.6)'">

                        <i class="fas fa-bell text-lg"></i>

                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl overflow-hidden z-50"
                         style="border:1px solid #EBF3FD;
                                box-shadow:0 8px 32px rgba(30,43,74,0.20);
                                display:none;">

                        <div class="px-4 py-3 border-b border-slate-100">
                            <h4 class="font-bold text-sm text-slate-800">
                                Notifikasi
                            </h4>
                        </div>

                        <div class="max-h-96 overflow-y-auto">

                            <div class="p-4 hover:bg-slate-50 transition-colors border-b border-slate-100 cursor-pointer">
                                <div class="flex gap-3">

                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0">
                                        <i class="fas fa-clipboard-list text-sm"></i>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800">
                                            Peminjaman Baru
                                        </p>

                                        <p class="text-xs text-slate-500 mt-1">
                                            Ahmad Rizki mengajukan peminjaman Arduino Uno
                                        </p>

                                        <p class="text-xs text-slate-400 mt-1">
                                            5 menit lalu
                                        </p>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="px-4 py-3 border-t border-slate-100">
                            <a href="#"
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                Lihat Semua →
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Profile --}}
                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-xl transition-colors"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.background=''">

                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm"
                             style="background:rgba(255,255,255,0.15);
                                    border:1px solid rgba(255,255,255,0.2);">

                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="hidden md:block text-left">
                            <p class="text-xs font-bold leading-tight text-white"
                               style="font-family:'Plus Jakarta Sans',sans-serif;">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs"
                               style="color:rgba(255,255,255,0.5);">
                                Administrator
                            </p>
                        </div>

                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                           style="color:rgba(255,255,255,0.4);"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl overflow-hidden z-50"
                         style="border:1px solid #EBF3FD;
                                box-shadow:0 8px 32px rgba(30,43,74,0.20);
                                display:none;">

                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        <div class="p-2">

                            <a href="{{ route('admin.profil') }}"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm text-slate-700 hover:bg-slate-50 transition-colors">

                                <i class="fas fa-user-circle text-slate-400"></i>

                                Profil Saya
                            </a>


                        </div>

                        <div class="p-2 border-t border-slate-100">

    <button
        @click="open = false; $dispatch('open-modal-logout')"
        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-colors"
        style="color:#EF4444;font-family:'Inter',sans-serif;"
        onmouseover="this.style.background='#FEF2F2'"
        onmouseout="this.style.background=''">

        <i class="fas fa-sign-out-alt"></i>

        Logout
    </button>

</div>

                    </div>
                    
                </div>

            </div>
        </div>
    </div>
</header>


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
            style="background:#F5F8FF;
                   color:#475569;
                   border:1px solid #EBF3FD;
                   font-family:'Plus Jakarta Sans',sans-serif;"
            onmouseover="this.style.background='#EBF3FD'"
            onmouseout="this.style.background='#F5F8FF'">

            Batal
        </button>

        <form action="{{ route('logout') }}" method="POST" class="flex-1">
            @csrf

            <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-semibold text-white transition-colors"
                    style="background:#1E2B4A;
                           font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseover="this.style.background='#185FA5'"
                    onmouseout="this.style.background='#1E2B4A'">

                Ya, Logout
            </button>
        </form>

    </x-slot>

</x-modal>