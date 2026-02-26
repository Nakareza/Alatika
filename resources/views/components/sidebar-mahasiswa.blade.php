<!-- Sidebar Mahasiswa dengan Toggle Animation -->
<aside x-data="sidebarHandler()" 
       class="fixed inset-y-0 left-0 z-40 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out shadow-sm"
       :class="collapsed ? 'w-20' : 'w-64'"
       x-init="updateMainContent()">
    
    <!-- Header Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50">
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
                <h2 class="text-gray-800 font-bold text-base leading-tight">Alatika</h2>
                <p class="text-gray-500 text-xs">Portal Mahasiswa</p>
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
                <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-sky-500 rounded-xl flex items-center justify-center text-white font-semibold text-sm shadow-sm">
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
                <p class="text-gray-500 text-xs truncate">Mahasiswa</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="p-3 space-y-1.5 overflow-y-auto h-[calc(100vh-180px)]">
        
        <!-- Dashboard -->
        <a href="{{ route('mahasiswa.dashboard') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-home w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Dashboard</span>
        </a>

        <!-- Ajukan Peminjaman -->
        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mahasiswa.peminjaman.ajukan') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-plus-circle w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Ajukan Peminjaman</span>
        </a>

        <!-- Riwayat Peminjaman -->
        <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mahasiswa.peminjaman.riwayat') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-history w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Riwayat Peminjaman</span>
        </a>

        <!-- Daftar Alat -->
        <a href="{{ route('mahasiswa.alat') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mahasiswa.alat') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
           x-bind:class="collapsed ? 'justify-center' : ''">
            <i class="fas fa-box-open w-5 text-center"></i>
            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="font-medium text-sm whitespace-nowrap">Daftar Alat</span>
        </a>

        <div x-show="!collapsed" class="border-t border-gray-200 my-2"></div>

        <!-- Profil -->
        <a href="{{ route('mahasiswa.profil') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mahasiswa.profil') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm shadow-blue-200' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
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
