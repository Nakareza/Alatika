{{-- Header Dashboard Admin --}}
@props(['title' => 'Dashboard', 'breadcrumbs' => []])

<header class="bg-white/80 backdrop-blur-sm border-b border-slate-200 sticky top-0 z-30 shadow-sm">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            {{-- Left Section: Hamburger + Breadcrumb --}}
            <div class="flex items-center gap-4">
                {{-- Hamburger Menu (Mobile) --}}
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors duration-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1 text-slate-500 hover:text-indigo-600 transition-colors duration-200">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo" class="w-4 h-4 object-contain">
                        <span class="hidden sm:inline">Home</span>
                    </a>
                    @foreach($breadcrumbs as $crumb)
                        <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                        @if($loop->last)
                            <span class="font-semibold text-slate-800">{{ $crumb['name'] }}</span>
                        @else
                            <a href="{{ $crumb['url'] }}" class="text-slate-500 hover:text-indigo-600 transition-colors duration-200">
                                {{ $crumb['name'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            {{-- Right Section: Notifications + Profile --}}
            <div class="flex items-center gap-3">
                {{-- Notifications --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="relative p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors duration-200">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <h4 class="font-semibold text-slate-800">Notifikasi</h4>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <div class="p-4 hover:bg-slate-50 cursor-pointer transition-colors duration-150 border-b border-slate-100">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800">Peminjaman Baru</p>
                                        <p class="text-xs text-slate-500 mt-1">Ahmad Rizki mengajukan peminjaman Arduino Uno</p>
                                        <p class="text-xs text-slate-400 mt-1">5 menit yang lalu</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-slate-50 cursor-pointer transition-colors duration-150 border-b border-slate-100">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        <i class="fas fa-undo-alt"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-800">Pengembalian Alat</p>
                                        <p class="text-xs text-slate-500 mt-1">Siti Nurhaliza mengembalikan Oscilloscope</p>
                                        <p class="text-xs text-slate-400 mt-1">1 jam yang lalu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-3 bg-slate-50 text-center">
                            <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Lihat Semua Notifikasi</a>
                        </div>
                    </div>
                </div>

                {{-- User Profile --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-slate-500"></i>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('admin.profil') }}" 
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors duration-150">
                                <i class="fas fa-user-circle text-slate-500"></i>
                                <span class="text-sm">Profil Saya</span>
                            </a>
                            <a href="#" 
                               class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors duration-150">
                                <i class="fas fa-cog text-slate-500"></i>
                                <span class="text-sm">Pengaturan</span>
                            </a>
                        </div>
                        <div class="p-2 border-t border-slate-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-rose-600 hover:bg-rose-50 transition-colors duration-150">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="text-sm font-medium">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
