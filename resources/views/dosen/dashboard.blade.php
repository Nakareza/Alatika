<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen - Alatika</title>
    
    {{-- TailwindCSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .glassmorphism { background: white; border: 1px solid #e2e8f0; }
        .hover-lift { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(245, 158, 11, 0.12); }
        .luxury-shadow { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down { animation: slideDown 0.6s ease; }

        [x-cloak] { display: none !important; }
        .modal-backdrop { background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false }">
    
    {{-- Sidebar Dosen --}}
    <x-sidebar-dosen />
    
    {{-- Main Content --}}
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Dosen</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Panel pemantauan peminjaman mahasiswa bimbingan</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">2</span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-all">
                                <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                                 x-cloak>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Dosen</span>
                                </div>
                                <div class="py-1">
                                    <button @click="open = false; showLogoutModal = true" 
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span>Logout</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        {{-- Dashboard Content --}}
        <main class="p-4 sm:p-6 lg:p-8 min-h-screen space-y-6">
            
            {{-- Welcome Section --}}
            <div class="glassmorphism rounded-2xl p-8 md:p-10 luxury-shadow animate-slide-down overflow-hidden relative">
                <div class="flex items-center gap-6 md:gap-8">
                    <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-16 h-16 object-contain">
                    </div>
                    <div class="flex-1">
                        <div class="mb-2">
                            <span class="inline-block px-4 py-1.5 rounded-full bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-200/50 text-amber-700 text-sm font-semibold">
                                <i class="fas fa-chalkboard-teacher mr-2 text-amber-500"></i>Panel Dosen
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black mb-2 leading-tight">
                            Selamat Datang, 
                            <span class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-600 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}! 👋
                            </span>
                        </h1>
                        <p class="text-slate-600 text-base font-medium">Pantau peminjaman alat mahasiswa bimbingan Anda di laboratorium elektronika</p>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Mahasiswa Bimbingan --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-amber-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center">
                            <i class="fas fa-user-graduate text-amber-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">12</h3>
                    <p class="text-sm text-slate-500 mt-1">Mahasiswa Bimbingan</p>
                </div>

                {{-- Peminjaman Aktif --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-blue-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                            <i class="fas fa-hand-holding text-blue-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Berjalan</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">5</h3>
                    <p class="text-sm text-slate-500 mt-1">Peminjaman Aktif</p>
                </div>

                {{-- Menunggu Rekomendasi --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-emerald-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-emerald-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Pending</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">3</h3>
                    <p class="text-sm text-slate-500 mt-1">Menunggu Rekomendasi</p>
                </div>

                {{-- Total Selesai --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-indigo-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center">
                            <i class="fas fa-check-double text-indigo-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">Semester ini</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">28</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Selesai</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('dosen.peminjaman') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-clipboard-list text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Peminjaman Mahasiswa</h3>
                            <p class="text-xs text-slate-500">3 menunggu rekomendasi</p>
                        </div>
                    </a>

                    <a href="{{ route('dosen.alat') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-laptop text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Daftar Alat</h3>
                            <p class="text-xs text-slate-500">Lihat ketersediaan</p>
                        </div>
                    </a>

                    <a href="{{ route('dosen.riwayat') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-history text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Riwayat</h3>
                            <p class="text-xs text-slate-500">Semua peminjaman</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Peminjaman Mahasiswa Bimbingan --}}
            <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-amber-500"></i>
                        Peminjaman Mahasiswa Bimbingan
                    </h2>
                    <a href="{{ route('dosen.peminjaman') }}" class="text-sm text-amber-600 hover:text-amber-700 font-semibold">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3 px-4">Mahasiswa</th>
                                <th class="pb-3 px-4">Alat</th>
                                <th class="pb-3 px-4">Tanggal Pinjam</th>
                                <th class="pb-3 px-4">Tanggal Kembali</th>
                                <th class="pb-3 px-4">Status</th>
                                <th class="pb-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">AR</div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Ahmad Rizki Saputra</p>
                                            <p class="text-xs text-slate-500">23010001</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-700">Arduino Uno R3</p>
                                    <p class="text-xs text-slate-500">ARD-001</p>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">08 Des 2025</td>
                                <td class="py-3 px-4 text-sm text-slate-600">15 Des 2025</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Menunggu Rekomendasi</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <button class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg hover:bg-emerald-200 transition-colors">
                                            <i class="fas fa-thumbs-up mr-1"></i>Rekomendasikan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">SN</div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Siti Nurhaliza</p>
                                            <p class="text-xs text-slate-500">23010002</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-700">Oscilloscope Digital</p>
                                    <p class="text-xs text-slate-500">OSC-005</p>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">07 Des 2025</td>
                                <td class="py-3 px-4 text-sm text-slate-600">10 Des 2025</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Sedang Dipinjam</span>
                                </td>
                                <td class="py-3 px-4">
                                    <button class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold">BS</div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Budi Santoso</p>
                                            <p class="text-xs text-slate-500">23010003</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-700">Multimeter Digital</p>
                                    <p class="text-xs text-slate-500">MUL-012</p>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">06 Des 2025</td>
                                <td class="py-3 px-4 text-sm text-slate-600">13 Des 2025</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Selesai</span>
                                </td>
                                <td class="py-3 px-4">
                                    <button class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Mahasiswa Bimbingan --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Mahasiswa Bimbingan</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">AR</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">Ahmad Rizki Saputra</p>
                                <p class="text-xs text-slate-500">23010001 — 1 alat dipinjam</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">SN</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">Siti Nurhaliza</p>
                                <p class="text-xs text-slate-500">23010002 — 1 alat dipinjam</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">BS</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">Budi Santoso</p>
                                <p class="text-xs text-slate-500">23010003 — tidak ada peminjaman</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aktivitas Terbaru --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Aktivitas Terbaru</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                <i class="fas fa-bell text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">Ahmad Rizki mengajukan peminjaman baru</p>
                                <p class="text-xs text-slate-500">30 menit yang lalu</p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">Peminjaman Budi Santoso telah selesai</p>
                                <p class="text-xs text-slate-500">2 jam yang lalu</p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                <i class="fas fa-thumbs-up text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">Anda merekomendasikan peminjaman Siti Nurhaliza</p>
                                <p class="text-xs text-slate-500">Kemarin, 14:20</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showLogoutModal = false">
        <div x-show="showLogoutModal"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 modal-backdrop" @click="showLogoutModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showLogoutModal"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin keluar dari akun Anda?</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl transition-all shadow-sm hover:shadow-md">Ya, Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
