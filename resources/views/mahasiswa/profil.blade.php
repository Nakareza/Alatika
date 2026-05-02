<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F5F8FF; color: #1E2B4A; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        <x-header-dashboard title="Profil Saya" :hideSearch="true" />

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
            <div class="max-w-4xl mx-auto space-y-6">

                {{-- Profile Card --}}
                <div class="bg-white rounded-2xl overflow-hidden" style="border:1px solid #EBF3FD;box-shadow:0 2px 16px rgba(30,43,74,0.06);">
                    {{-- Banner --}}
                    <div class="h-28 relative" style="background:linear-gradient(135deg,#1E2B4A 0%,#185FA5 60%,#378ADD 100%);">
                        <div class="absolute -bottom-10 left-6">
                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center"
                                 style="background:white;border:4px solid white;box-shadow:0 4px 16px rgba(30,43,74,0.15);">
                                <span class="text-3xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#185FA5;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-14 px-6 pb-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    {{ Auth::user()->name }}
                                </h2>
                                <p class="text-sm mt-0.5" style="color:#94a3b8;">{{ Auth::user()->email }}</p>
                                <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 text-xs font-bold rounded-full"
                                      style="background:#EBF3FD;color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">
                                    <i class="fas fa-shield-alt"></i> Mahasiswa
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6" style="border-top:1px solid #EBF3FD;">
                            <div class="rounded-xl p-4" style="background:#F5F8FF;border:1px solid #EBF3FD;">
                                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#A0BBCC;">NIM</p>
                                <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    {{ Auth::user()->nim ?? '-' }}
                                </p>
                            </div>
                            <div class="rounded-xl p-4" style="background:#F5F8FF;border:1px solid #EBF3FD;">
                                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#A0BBCC;">Role</p>
                                <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    {{ ucfirst(Auth::user()->role) }}
                                </p>
                            </div>
                            <div class="rounded-xl p-4" style="background:#F5F8FF;border:1px solid #EBF3FD;">
                                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#A0BBCC;">Terdaftar Sejak</p>
                                <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    {{ Auth::user()->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Telegram Integration --}}
                @include('components.telegram-connect')

                {{-- Account Actions --}}
                <div class="bg-white rounded-2xl p-6" style="border:1px solid #EBF3FD;box-shadow:0 2px 16px rgba(30,43,74,0.06);">
                    <h3 class="text-base font-bold mb-4 flex items-center gap-2"
                        style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-cog" style="color:#A0BBCC;"></i> Akun
                    </h3>
                    <div class="space-y-3">
                        <button @click="$dispatch('open-modal-logout')"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-left group"
                                style="background:#F5F8FF;"
                                onmouseover="this.style.background='#fee2e2'"
                                onmouseout="this.style.background='#F5F8FF'">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                 style="background:#fee2e2;">
                                <i class="fas fa-sign-out-alt" style="color:#ef4444;"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Logout</p>
                                <p class="text-xs" style="color:#94a3b8;">Keluar dari akun Anda</p>
                            </div>
                            <i class="fas fa-chevron-right ml-auto text-xs" style="color:#B5D4F4;"></i>
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- Logout Modal — pakai x-modal component --}}
    <x-modal name="logout" title="Konfirmasi Logout" type="danger">
        <p class="text-sm text-center mb-1" style="color:#64748b;">
            Apakah Anda yakin ingin keluar dari akun ini?
        </p>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal-logout')"
                class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition"
                style="border:1.5px solid #D4E6F8;color:#1E2B4A;background:#F5F8FF;font-family:'Plus Jakarta Sans',sans-serif;"
                onmouseover="this.style.borderColor='#378ADD'"
                onmouseout="this.style.borderColor='#D4E6F8'">
                Batal
            </button>
            <form action="{{ route('logout') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit"
                    class="w-full py-2.5 rounded-xl text-white font-semibold text-sm transition"
                    style="background:linear-gradient(135deg,#dc2626,#b91c1c);box-shadow:0 4px 12px rgba(220,38,38,0.25);font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseover="this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.filter=''">
                    Ya, Logout
                </button>
            </form>
        </x-slot>
    </x-modal>

    {{-- Trigger logout modal dari tombol di halaman --}}
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



</body>
</html>