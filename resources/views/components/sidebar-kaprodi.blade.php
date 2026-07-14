{{-- Sidebar KA Prodi - Style Mengikuti Sidebar KA Lab --}}
<aside 
       x-data="sidebarHandler"
       @toggle-sidebar.window="toggle()"
       class="fixed top-0 left-0 h-full z-30 transition-all duration-300 ease-in-out"
       :class="collapsed ? 'w-20' : 'w-64'"
       style="background:white; solid #EBF3FD; box-shadow:2px 0 16px rgba(30,43,74,0.06);">

    <!-- Header Section -->
    <div class="h-16 flex items-center justify-between px-4"
         style="border-bottom:1px solid #1E2B4A;background:#1E2B4A;">
        
        <div class="flex items-center gap-3 overflow-hidden"
             x-bind:class="collapsed ? 'justify-center w-full' : ''">
             
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);">
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

                <h2 class="font-bold text-base leading-tight text-white"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Alat<span style="color:#B5D4F4;">ika</span>
                </h2>

                <p class="text-xs" style="color:rgba(255,255,255,0.6);">
                    Panel KA Prodi
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-220px)]">

        <!-- Dashboard -->
        <a href="{{ route('kaprodi.dashboard') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.dashboard') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.dashboard'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-home w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Dashboard
            </span>
        </a>

        <!-- Persetujuan -->
        <a href="{{ route('kaprodi.persetujuan') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.persetujuan*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.persetujuan*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-clipboard-check w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Persetujuan
            </span>
        </a>

        <!-- Riwayat -->
        <a href="{{ route('kaprodi.riwayat') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.riwayat*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.riwayat*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-history w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Riwayat
            </span>
        </a>

        <!-- Data Mahasiswa -->
        <a href="{{ route('kaprodi.mahasiswa') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.mahasiswa*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.mahasiswa*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-user-graduate w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Data Mahasiswa
            </span>
        </a>

        <!-- Data Dosen -->
        <a href="{{ route('kaprodi.dosen') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.dosen*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.dosen*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-chalkboard-teacher w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Data Dosen
            </span>
        </a>

        <!-- Kelola User -->
        <a href="{{ route('kaprodi.users.index') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.users*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.users*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-users-cog w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Kelola User
            </span>
        </a>

        <!-- Divider -->
        <div x-show="!collapsed"
             class="my-2"
             style="border-top:1px solid #EBF3FD;"></div>

        <!-- Profil -->
        <a href="{{ route('kaprodi.profil') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('kaprodi.profil') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('kaprodi.profil'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-user w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200 delay-75"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                  Profil
            </span>
        </a>
    </nav>

    <!-- Logout -->
    <div class="absolute bottom-0 left-0 right-0 p-3"
         style="border-top:1px solid #EBF3FD;background:white;">

        <button @click="$dispatch('open-modal-logout')"
                type="button"
                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 text-sm"
                style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;"
                x-bind:class="collapsed ? 'justify-center' : ''"
                onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444';"
                onmouseout="this.style.background='';this.style.color='#94a3b8';">

            <i class="fas fa-sign-out-alt w-4 text-center shrink-0"></i>

            <span x-show="!collapsed"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  class="whitespace-nowrap">
                  Keluar
            </span>
        </button>
    </div>
</aside>
