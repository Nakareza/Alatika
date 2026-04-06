{{-- Sidebar Dosen untuk Sistem Peminjaman Alat Elektronik --}}
<aside x-data="sidebarHandler()" 
       class="fixed inset-y-0 left-0 z-40 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out shadow-sm"
       :class="collapsed ? 'w-20' : 'w-64'"
       x-init="updateMainContent()">
    
    <!-- Header Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-orange-50"
         x-bind:class="collapsed ? 'justify-center' : ''">
        <div class="flex items-center gap-3 overflow-hidden"
             x-bind:class="collapsed ? 'justify-center w-full' : ''">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-amber-100 flex-shrink-0">
                <img src="{{ asset('images/logo-polines.png') }}" alt="Logo" class="w-7 h-7 object-contain">
            </div>
            <div x-show="!collapsed" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="overflow-hidden">
                <h2 class="text-gray-800 font-bold text-base leading-tight bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">Alatika</h2>
                <p class="text-gray-500 text-xs">Panel Dosen</p>
            </div>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="toggle()"
            class="absolute -right-3 top-20 w-7 h-7 bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105 z-50">
        <i class="fas text-xs transition-transform duration-300"
           :class="collapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
    </button>

    <!-- User Profile Card -->
    <div class="p-4 border-b border-gray-100"
         x-bind:class="collapsed ? 'px-2' : 'px-4'">
        <div class="flex items-center gap-3"
             x-bind:class="collapsed ? 'justify-center' : ''">
            <div class="relative flex-shrink-0">
                <div class="w-11 h-11 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            <div x-show="!collapsed"
                 x-transition:enter="transition ease-out duration-200 delay-75"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="flex-1 min-w-0 overflow-hidden">
                <h3 class="text-gray-800 font-semibold text-sm truncate">{{ Auth::user()->name }}</h3>
                <p class="text-gray-500 text-xs truncate">Dosen</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-3 space-y-1.5 overflow-y-auto h-[calc(100vh-180px)]">
        
        <!-- Dashboard -->
        <a href="{{ route('dosen.dashboard') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.dashboard') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-home w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Ajukan Peminjaman -->
        <a href="{{ route('dosen.peminjaman.ajukan') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.peminjaman*') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Ajukan Peminjaman</span>
        </a>

        <!-- Daftar Alat -->
        <a href="{{ route('dosen.alat') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.alat') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-laptop w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Daftar Alat</span>
        </a>

        <!-- Keranjang -->
        @php $cartCount = \App\Models\Keranjang::where('user_id', auth()->id())->count(); @endphp
        <a href="{{ route('dosen.keranjang') }}" 
           class="group relative flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.keranjang*') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-shopping-cart w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap flex-1">Keranjang</span>
            @if($cartCount > 0)
            <span x-show="!collapsed" class="bg-red-500 text-white text-xs font-black px-1.5 py-0.5 rounded-full min-w-[20px] text-center">{{ $cartCount }}</span>
            @endif
        </a>

        <!-- Riwayat -->
        <a href="{{ route('dosen.riwayat') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.riwayat*') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-history w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Riwayat</span>
        </a>

        <div x-show="!collapsed" class="border-t border-gray-200 my-2"></div>

        <!-- Profil -->
        <a href="{{ route('dosen.profil') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dosen.profil') ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-sm shadow-amber-200' : 'text-gray-700 hover:bg-amber-50 hover:text-amber-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-user w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Profil</span>
        </a>
    </nav>
</aside>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sidebarHandler', () => ({
            collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
            
            toggle() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('sidebarCollapsed', this.collapsed);
                this.updateMainContent();
            },
            
            updateMainContent() {
                const mainContent = document.getElementById('mainContent');
                if (mainContent) {
                    mainContent.className = this.collapsed 
                        ? 'transition-all duration-300 ease-in-out ml-20'
                        : 'transition-all duration-300 ease-in-out ml-64';
                }
            }
        }));
    });
</script>
