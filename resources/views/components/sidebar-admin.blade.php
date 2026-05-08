<!-- Sidebar Admin -->
<aside 
       x-data="sidebarHandler"
       @toggle-sidebar.window="toggle()"
       class="fixed top-0 left-0 h-screen transition-all duration-300 ease-in-out"
       :class="collapsed ? 'w-20' : 'w-64'"
       style="background:white; solid #EBF3FD; 2px 16px rgba(30,43,74,0.06);">
    
    <!-- Header -->
    <div class="h-16 flex items-center justify-between px-4"
         style="border-bottom:1px solid #1E2B4A;background:#1E2B4A;">

        <div class="flex items-center gap-3 overflow-hidden"
             x-bind:class="collapsed ? 'justify-center w-full' : ''">

            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);">
                <img src="{{ asset('images/logo-polines.png') }}"
                     alt="Logo"
                     class="w-7 h-7 object-contain">
            </div>

            <div x-show="!collapsed"
                 x-transition
                 class="overflow-hidden">

                <h2 class="font-bold text-base leading-tight text-white"
                    style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Alat<span style="color:#B5D4F4;">ika</span>
                </h2>

                <p class="text-xs"
                   style="color:rgba(255,255,255,0.6);">
                    Admin Panel
                </p>
            </div>
        </div>
    </div>



    <!-- Navigation Menu -->
    <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-180px)]">
        
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.dashboard') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.dashboard'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-home w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Dashboard
            </span>
        </a>

        <!-- Kelola Peminjaman -->
        <a href="{{ route('admin.peminjaman') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.peminjaman*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.peminjaman*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-clipboard-list w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Kelola Peminjaman
            </span>
        </a>
        
        <!-- Kelola Pengembalian -->
        <a href="{{ route('admin.pengembalian') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.pengembalian*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.pengembalian*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-undo-alt w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Kelola Pengembalian
            </span>
        </a>

        <!-- Data Alat -->
        <a href="{{ route('admin.alat') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.alat*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.alat*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-laptop w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Data Alat
            </span>
        </a>

        <!-- Data Mahasiswa -->
        <a href="{{ route('admin.mahasiswa') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.mahasiswa*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.mahasiswa*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-user-graduate w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Data Mahasiswa
            </span>
        </a>

        <!-- Data Dosen -->
        <a href="{{ route('admin.dosen') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.dosen*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.dosen*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-chalkboard-teacher w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Data Dosen
            </span>
        </a>

        <!-- Kelola User -->
        <a href="{{ route('admin.users.index') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.users*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.users*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-users-cog w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Kelola User
            </span>
        </a>

        

        <div x-show="!collapsed" class="border-t border-gray-200 my-2"></div>

        <!-- Laporan -->
        <a href="{{ route('admin.laporan') }}"
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.laporan*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.laporan*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-chart-bar w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Laporan
            </span>
        </a>

        <!-- Profil -->
        <a href="{{ route('admin.profil') }}" 
           class="group flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200"
           style="{{ request()->routeIs('admin.profil*') ? 'background:#1E2B4A;color:white;box-shadow:0 4px 14px rgba(30,43,74,0.22);' : 'color:#64748b;' }}"
           x-bind:class="collapsed ? 'justify-center' : ''"
           @if(!request()->routeIs('admin.profil*'))
           onmouseover="this.style.background='#EBF3FD';this.style.color='#185FA5';"
           onmouseout="this.style.background='';this.style.color='#64748b';"
           @endif>

            <i class="fas fa-user-circle w-5 text-center"></i>

            <span x-show="!collapsed"
                  x-transition
                  class="text-sm whitespace-nowrap"
                  style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">
                Profil
            </span>
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
