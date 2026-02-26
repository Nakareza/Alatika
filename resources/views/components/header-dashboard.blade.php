@props(['title' => 'Dashboard', 'breadcrumbs' => []])

<header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <!-- Left: Menu Toggle + Breadcrumb -->
            <div class="flex items-center gap-4">
                <!-- Mobile Menu Toggle -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Breadcrumb & Title -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                    @if(count($breadcrumbs) > 0)
                        <nav class="flex items-center gap-2 text-sm mt-1">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="text-gray-500 hover:text-blue-600 flex items-center gap-2">
                                <img src="{{ asset('images/logo-polines.png') }}" alt="Logo" class="w-4 h-4 object-contain">
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

            <!-- Right: Notifications + User -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <h4 class="font-bold text-gray-900">Notifikasi</h4>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <div class="flex gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">Peminjaman Disetujui</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Arduino Uno telah disetujui</p>
                                        <p class="text-xs text-gray-400 mt-1">2 jam lalu</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="px-4 py-2 border-t border-gray-200">
                            <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">Mahasiswa</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50"
                         style="display: none;">
                        <a href="{{ route('mahasiswa.profil') }}" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-5"></i>
                            <span>Profil Saya</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span>Pengaturan</span>
                        </a>
                        <div class="border-t border-gray-200 my-2"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-2 text-red-600 hover:bg-red-50 transition-colors w-full text-left">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
