{{-- resources/views/components/layouts/header.blade.php --}}

@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [],
    'hideSearch' => false
])

<header x-data="{}"
        class="sticky top-0 z-30 h-16"
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

                        {{-- Mobile Actions --}}
                        <div class="flex md:hidden items-center gap-2">
                            {{ $actions ?? '' }}
                        </div>
                    </div>

                    {{-- Subtitle
                    @if($subtitle)
                        <p class="text-xs mt-0.5"
                           style="color:rgba(255,255,255,0.5);font-family:'Inter',sans-serif;">
                            {{ $subtitle }}
                        </p>
                    @endif --}}

                    {{-- Breadcrumb --}}
                    @if(count($breadcrumbs) > 0)

                        <nav class="flex items-center gap-1.5 text-xs mt-0.5"
                             style="font-family:'Inter',sans-serif;">

                            <a href="{{ route(auth()->user()->role . '.dashboard') }}"
                               class="flex items-center gap-1.5 transition-colors"
                               style="color:rgba(255,255,255,0.5);"
                               onmouseover="this.style.color='#B5D4F4'"
                               onmouseout="this.style.color='rgba(255,255,255,0.5)'">

                                <img src="{{ asset('images/logo-polines.png') }}"
                                     alt="Logo"
                                     class="w-3.5 h-3.5 object-contain opacity-70">

                                Home
                            </a>

                            @foreach($breadcrumbs as $crumb)

                                <i class="fas fa-chevron-right text-xs"
                                   style="color:rgba(255,255,255,0.2);"></i>

                                @if(isset($crumb['url']))

                                    <a href="{{ $crumb['url'] }}"
                                       class="transition-colors"
                                       style="color:rgba(255,255,255,0.5);"
                                       onmouseover="this.style.color='#B5D4F4'"
                                       onmouseout="this.style.color='rgba(255,255,255,0.5)'">

                                        {{ $crumb['label'] }}
                                    </a>

                                @else

                                    <span style="color:rgba(255,255,255,0.85);font-weight:600;">
                                        {{ $crumb['label'] }}
                                    </span>

                                @endif

                            @endforeach

                        </nav>

                    @endif

                </div>
            </div>

            {{-- SEARCH --}}
            @if(!$hideSearch)

            <div class="hidden md:flex items-center flex-1 max-w-xs mx-6">

                <div class="relative w-full">

                    <input type="text"
                           placeholder="Cari data..."
                           class="w-full pl-9 pr-4 py-2 text-sm rounded-xl outline-none transition-all"
                           style="background:rgba(255,255,255,0.1);
                                  border:1.5px solid rgba(255,255,255,0.15);
                                  color:#fff;
                                  font-family:'Inter',sans-serif;"

                           onfocus="this.style.borderColor='#378ADD';
                                    this.style.background='rgba(255,255,255,0.15)';
                                    this.style.boxShadow='0 0 0 3px rgba(55,138,221,0.25)'"

                           onblur="this.style.borderColor='rgba(255,255,255,0.15)';
                                   this.style.background='rgba(255,255,255,0.1)';
                                   this.style.boxShadow=''">

                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm"
                       style="color:rgba(255,255,255,0.35);"></i>

                </div>

            </div>

            @endif

            {{-- RIGHT --}}
            <div class="flex items-center gap-2">

                {{-- Desktop Actions --}}
                @isset($actions)

                <div class="hidden md:flex items-center gap-2 mr-2">
                    {{ $actions }}
                </div>

                @endisset

                {{-- Notification --}}
                <div class="relative" x-data="{ open: false }">

                    <button @click="open = !open"
                            class="relative p-1.5 rounded-lg transition-colors"
                            style="color:rgba(255,255,255,0.6);"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#fff'"
                            onmouseout="this.style.background='';this.style.color='rgba(255,255,255,0.6)'">

                        <i class="fas fa-bell text-lg"></i>

                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full"
                              style="background:#EF4444;"></span>

                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-2xl py-2 z-50"
                         style="border:1px solid #EBF3FD;
                                box-shadow:0 8px 32px rgba(30,43,74,0.20);
                                display:none;">

                        <div class="px-4 py-2.5 border-b"
                             style="border-color:#EBF3FD;">

                            <h4 class="font-bold text-sm"
                                style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                Notifikasi
                            </h4>
                        </div>

                        <div class="max-h-72 overflow-y-auto">

                            <a href="#"
                               class="flex gap-3 px-4 py-3 transition-colors border-b"
                               style="border-color:#F5F8FF;"
                               onmouseover="this.style.background='#F5F8FF'"
                               onmouseout="this.style.background=''">

                                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:#EBF3FD;">

                                    <i class="fas fa-check text-xs"
                                       style="color:#185FA5;"></i>
                                </div>

                                <div class="flex-1 min-w-0">

                                    <p class="text-xs font-semibold truncate"
                                       style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                        Notifikasi Baru
                                    </p>

                                    <p class="text-xs mt-0.5"
                                       style="color:#94a3b8;font-family:'Inter',sans-serif;">

                                        Ada aktivitas terbaru
                                    </p>

                                    <p class="text-xs mt-1"
                                       style="color:#B5D4F4;font-family:'Inter',sans-serif;">

                                        Baru saja
                                    </p>

                                </div>
                            </a>

                        </div>

                    </div>
                </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">

                    <button @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-xl transition-colors"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.background=''">

                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                             style="background:rgba(255,255,255,0.15);
                                    border:1px solid rgba(255,255,255,0.2);">

                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        {{-- User Info --}}
                        <div class="hidden md:block text-left">

                            <p class="text-xs font-bold leading-tight"
                               style="color:#fff;font-family:'Plus Jakarta Sans',sans-serif;">

                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-xs"
                               style="color:rgba(255,255,255,0.5);font-family:'Inter',sans-serif;">

                                {{ ucfirst(Auth::user()->role) }}
                            </p>
                        </div>

                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                           style="color:rgba(255,255,255,0.4);"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl py-2 z-50"
                         style="border:1px solid #EBF3FD;
                                box-shadow:0 8px 32px rgba(30,43,74,0.20);
                                display:none;">

                        {{-- Profile --}}
                        <a href="{{ route(auth()->user()->role . '.profil') }}"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
                           style="color:#475569;font-family:'Inter',sans-serif;"
                           onmouseover="this.style.background='#F5F8FF';this.style.color='#185FA5'"
                           onmouseout="this.style.background='';this.style.color='#475569'">

                            <i class="fas fa-user w-4 text-center"
                               style="color:#B5D4F4;font-size:12px;"></i>

                            Profil Saya
                        </a>

                        {{-- Settings --}}
                        <a href="#"
                           class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
                           style="color:#475569;font-family:'Inter',sans-serif;"
                           onmouseover="this.style.background='#F5F8FF';this.style.color='#185FA5'"
                           onmouseout="this.style.background='';this.style.color='#475569'">

                            <i class="fas fa-cog w-4 text-center"
                               style="color:#B5D4F4;font-size:12px;"></i>

                            Pengaturan
                        </a>

                        <div class="my-1.5 mx-3"
                             style="height:1px;background:#EBF3FD;"></div>

                        {{-- Logout --}}
                        <button @click="open = false; $dispatch('open-modal-logout')"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-left transition-colors"
                                style="color:#EF4444;font-family:'Inter',sans-serif;"
                                onmouseover="this.style.background='#FEF2F2'"
                                onmouseout="this.style.background=''">

                            <i class="fas fa-sign-out-alt w-4 text-center"
                               style="font-size:12px;"></i>

                            Logout
                        </button>

                    </div>
                </div>

            </div>

        </div>
    </div>
</header>

{{-- MODAL LOGOUT --}}
<x-modal name="logout" title="Konfirmasi Logout" type="danger">

    <p class="text-sm text-center mb-1"
       style="color:#94a3b8;font-family:'Inter',sans-serif;">

        Kamu yakin mau keluar dari akun ini?
    </p>

    <x-slot name="footer">

        <button @click="$dispatch('close-modal-logout')"
                class="flex-1 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                style="background:#F5F8FF;
                       color:#475569;
                       border:1px solid #EBF3FD;
                       font-family:'Plus Jakarta Sans',sans-serif;"

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
                    style="background:#1E2B4A;
                           font-family:'Plus Jakarta Sans',sans-serif;"

                    onmouseover="this.style.background='#185FA5'"
                    onmouseout="this.style.background='#1E2B4A'">

                Ya, Logout
            </button>

        </form>

    </x-slot>

</x-modal>