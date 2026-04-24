@props(['title' => null, 'subtitle' => null, 'breadcrumbs' => [], 'hideSearch' => false])

<div x-data="{}">
<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40 h-16">
    <div class="px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex items-center justify-between h-full">

            <!-- Kiri: Hamburger + Judul -->
            <div class="flex items-center gap-3">
                <button @click="$dispatch('toggle-sidebar')" class="text-gray-600 hover:text-gray-900 p-1.5 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-gray-900">{{ $title }}</h1>
                        <!-- Slot actions: muncul di sebelah judul di mobile -->
                        <div class="flex md:hidden items-center gap-2">
                            {{ $actions ?? '' }}
                        </div>
                    </div>
                    @if($subtitle)
                        <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                    @if(count($breadcrumbs) > 0)
                        <nav class="flex items-center gap-1.5 text-xs mt-1">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="text-gray-500 hover:text-blue-600 flex items-center gap-1.5">
                                <img src="{{ asset('images/logo-polines.png') }}" alt="Logo" class="w-3.5 h-3.5 object-contain">
                                Home
                            </a>
                            @foreach($breadcrumbs as $crumb)
                                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                @if(isset($crumb['url']))
                                    <a href="{{ $crumb['url'] }}" class="text-gray-500 hover:text-blue-600">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="text-gray-700 font-medium">{{ $crumb['label'] }}</span>
                                @endif
                            @endforeach
                        </nav>
                    @endif
                </div>
            </div>

            <!-- Tengah: Search -->
            @if(!$hideSearch)
            <div class="hidden md:flex items-center flex-1 max-w-xs mx-6">
                <div class="relative w-full">
                    <input type="text"
                           placeholder="Cari alat, peminjaman..."
                           class="w-full px-3.5 py-2 pl-9 text-sm bg-gray-50 border border-gray-200 rounded-lg text-gray-700 placeholder-gray-500 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>
            @endif

            <!-- Kanan: Actions slot + Notif + User -->
            <div class="flex items-center gap-2">

                <!-- Action buttons (tombol keranjang, pinjam dll) — hanya tampil di desktop -->
                @isset($actions)
                <div class="hidden md:flex items-center gap-2 mr-2">
                    {{ $actions }}
                </div>
                @endisset

                <!-- Notifikasi -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-1 w-72 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50"
                         style="display:none;">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h4 class="font-semibold text-sm text-gray-900">Notifikasi</h4>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <a href="#" class="block px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-2.5">
                                    <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                                        <i class="fas fa-check text-green-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 truncate">Peminjaman Disetujui</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Arduino Uno telah disetujui</p>
                                        <p class="text-xs text-gray-400 mt-1">2 jam lalu</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-900 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">Mahasiswa</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50"
                         style="display:none;">
                        <a href="{{ route('mahasiswa.profil') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-4"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-4"></i>
                            <span>Pengaturan</span>
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left">
                                <i class="fas fa-sign-out-alt w-4"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</header>
</div>