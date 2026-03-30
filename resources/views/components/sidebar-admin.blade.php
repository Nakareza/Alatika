{{-- Sidebar Admin untuk Sistem Peminjaman Alat Elektronik --}}
<aside x-data="sidebarHandler()" 
       class="fixed inset-y-0 left-0 z-40 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out shadow-sm"
       :class="collapsed ? 'w-20' : 'w-64'"
       x-init="updateMainContent()">
    
    <!-- Header Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50"
         x-bind:class="collapsed ? 'justify-center' : ''">
        <div class="flex items-center gap-3 overflow-hidden"
             x-bind:class="collapsed ? 'justify-center w-full' : ''">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-blue-100 flex-shrink-0">
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
                <h2 class="text-gray-800 font-bold text-base leading-tight bg-gradient-to-r from-blue-600 to-blue-600 bg-clip-text text-transparent">Alatika</h2>
                <p class="text-gray-500 text-xs">Admin Panel</p>
            </div>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="toggle()"
            class="absolute -right-3 top-20 w-7 h-7 bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all duration-200 hover:scale-105 z-50">
        <i class="fas text-xs transition-transform duration-300"
           :class="collapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
    </button>

    <!-- User Profile Card -->
    <div class="p-4 border-b border-gray-100"
         x-bind:class="collapsed ? 'px-2' : 'px-4'">
        <div class="flex items-center gap-3"
             x-bind:class="collapsed ? 'justify-center' : ''">
            <div class="relative flex-shrink-0">
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-semibold text-sm shadow-sm">
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
                <p class="text-gray-500 text-xs truncate">Administrator</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-3 space-y-1.5 overflow-y-auto h-[calc(100vh-180px)]">
        
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-home w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Kelola Peminjaman -->
        <a href="{{ route('admin.peminjaman') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.peminjaman*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Kelola Peminjaman</span>
        </a>

        <!-- Kelola Pengembalian -->
        <a href="{{ route('admin.pengembalian') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.pengembalian*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-undo-alt w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Kelola Pengembalian</span>
        </a>

        <!-- Data Alat -->
        <a href="{{ route('admin.alat') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.alat*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-laptop w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Data Alat</span>
        </a>

        <!-- Data Mahasiswa -->
        <a href="{{ route('admin.mahasiswa') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.mahasiswa*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-user-graduate w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Data Mahasiswa</span>
        </a>

        <!-- Data Dosen -->
        <a href="{{ route('admin.dosen') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dosen*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-chalkboard-teacher w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Data Dosen</span>
        </a>

        <!-- Kelola User -->
        <a href="{{ route('admin.users.index') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-users-cog w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Kelola User</span>
        </a>

        <div x-show="!collapsed" class="border-t border-gray-200 my-2"></div>

        <!-- Laporan -->
        <a href="{{ route('admin.laporan') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.laporan*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Laporan</span>
        </a>

        <!-- Profil -->
        <a href="{{ route('admin.profil') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.profil*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-user-circle w-5 text-center"></i>
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
