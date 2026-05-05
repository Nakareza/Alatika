<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Alatika</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; background: #F5F8FF; color: #1E2B4A; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }
        .card { background: white; border: 1px solid #EBF3FD; border-radius: 16px; transition: all 0.2s ease; box-shadow: 0 2px 16px rgba(30,43,74,0.06); }
        .card:hover { border-color: #B5D4F4; box-shadow: 0 4px 20px rgba(30,43,74,0.10); }
        .badge { display: inline-flex; align-items: center; padding: 0.375rem 0.875rem; font-size: 0.75rem; font-weight: 600; border-radius: 20px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #EBF3FD; color: #185FA5; }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 600; font-size: 0.875rem; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; font-family: 'Plus Jakarta Sans', sans-serif; text-decoration: none; }
        .btn-primary { background: #1E2B4A; color: white; box-shadow: 0 4px 14px rgba(30,43,74,0.22); }
        .btn-primary:hover { background: #185FA5; box-shadow: 0 8px 24px rgba(24,95,165,0.30); transform: translateY(-1px); }
        .btn-secondary { background: #F5F8FF; color: #1E2B4A; border: 1.5px solid #D4E6F8; }
        .btn-secondary:hover { background: white; border-color: #378ADD; }
        .list-item { background: #F5F8FF; border: 1px solid #EBF3FD; border-radius: 12px; padding: 1rem; transition: all 0.2s ease; }
        .list-item:hover { background: white; border-color: #B5D4F4; box-shadow: 0 4px 12px rgba(30,43,74,0.07); }
        .inp { width: 100%; padding: 0.75rem 1rem; background: #F5F8FF; border: 1.5px solid #D4E6F8; border-radius: 12px; font-family: 'Inter', sans-serif; font-size: 0.875rem; color: #1E2B4A; outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
        .inp:focus { border-color: #378ADD; background: #fff; box-shadow: 0 0 0 3px rgba(55,138,221,0.10); }
        .inp::placeholder { color: #A0BBCC; }
        .form-label { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 600; color: #1E2B4A; margin-bottom: 0.5rem; }
        [x-cloak] { display: none !important; }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .page-transition {
            animation: fadeIn 0.5s ease-in forwards;
        }
    </style>

    @stack('styles')
</head>

{{-- x-data di body cukup kosong, sidebarHandler didaftarkan via Alpine.data --}}
<body class="antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out" :class="collapsed ? 'ml-20' : 'ml-64'" x-data="sidebarHandler" @load="triggerPageAnimation()" x-init="init()">

        <x-header-dashboard title="{{ View::yieldContent('title', 'Dashboard') }}" />

        @yield('banner')

        <main class="p-6 lg:p-8 min-h-screen relative z-0">
            @yield('content')
        </main>

        <footer class="bg-white border-t py-5 px-6 lg:px-8 mt-8" style="border-color:#EBF3FD;">
            <div class="text-center">
                <p class="text-xs font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                    &copy; {{ date('Y') }} Alatika - Politeknik Negeri Semarang
                </p>
                <p class="text-xs" style="color:#94a3b8;">Sistem Peminjaman Alat Laboratorium Elektronik</p>
            </div>
        </footer>

    </div>

    {{-- Alpine harus defer, script sidebarHandler harus SEBELUM Alpine --}}
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
                },
                
                init() {
                    this.triggerPageAnimation();
                },
                
                triggerPageAnimation() {
                    const main = document.querySelector('main');
                    if (main) {
                        main.classList.add('page-transition');
                    }
                }
            }));
        });
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')

</body>
</html>