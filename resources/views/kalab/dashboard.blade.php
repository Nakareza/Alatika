<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard KA Lab - Alatika</title>
    
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
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(16, 185, 129, 0.12); }
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
    
    {{-- Sidebar KA Lab --}}
    <x-sidebar-kalab />
    
    {{-- Main Content --}}
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Kepala Laboratorium</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Panel pengawasan & persetujuan peminjaman alat</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-all">
                                <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
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
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">Kepala Lab</span>
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
                    <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-16 h-16 object-contain">
                    </div>
                    <div class="flex-1">
                        <div class="mb-2">
                            <span class="inline-block px-4 py-1.5 rounded-full bg-gradient-to-r from-emerald-500/10 to-teal-500/10 border border-emerald-200/50 text-emerald-700 text-sm font-semibold">
                                <i class="fas fa-flask mr-2 text-emerald-500"></i>Kepala Laboratorium
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black mb-2 leading-tight">
                            Selamat Datang, 
                            <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}! 👋
                            </span>
                        </h1>
                        <p class="text-slate-600 text-base font-medium">Panel pengawasan laboratorium elektronika Politeknik Negeri Semarang</p>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Menunggu Persetujuan --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-amber-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center">
                            <i class="fas fa-clock text-amber-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ $stats['pending_dosen'] }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Menunggu Persetujuan</p>
                </div>

                {{-- Disetujui --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-emerald-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Bulan ini</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ $stats['disetujui'] }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Disetujui</p>
                </div>

                {{-- Sedang Dipinjam --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-blue-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                            <i class="fas fa-hand-holding text-blue-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ $stats['dipinjam'] }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Sedang Dipinjam</p>
                </div>

                {{-- Total Alat --}}
                <div class="glassmorphism rounded-2xl p-6 luxury-shadow hover-lift border-l-4 border-l-indigo-400">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center">
                            <i class="fas fa-laptop text-indigo-600 text-xl"></i>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">Tersedia: {{ $stats['tersedia'] }}</span>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800">{{ $stats['total_alat'] }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Alat Lab</p>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-emerald-500"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('kalab.persetujuan') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-clipboard-check text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Persetujuan</h3>
                            <p class="text-xs text-slate-500">{{ $stats['pending_dosen'] }} menunggu</p>
                        </div>
                    </a>

                    <a href="{{ route('kalab.alat') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-laptop text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Data Alat</h3>
                            <p class="text-xs text-slate-500">Lihat inventaris</p>
                        </div>
                    </a>

                    <a href="{{ route('kalab.riwayat') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-history text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Riwayat</h3>
                            <p class="text-xs text-slate-500">Semua peminjaman</p>
                        </div>
                    </a>

                    <a href="{{ route('kalab.laporan') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-bar text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Laporan</h3>
                            <p class="text-xs text-slate-500">Statistik lab</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Pending Approvals Table --}}
            <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-clock text-amber-500"></i>
                        Menunggu Persetujuan Anda
                    </h2>
                    <a href="{{ route('kalab.persetujuan') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-semibold">
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
                                <th class="pb-3 px-4">Dosen Pembimbing</th>
                                <th class="pb-3 px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pending_approvals as $peminjaman)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($peminjaman->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $peminjaman->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $peminjaman->user->role }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <p class="text-sm font-medium text-slate-700">{{ $peminjaman->alat->nama }}</p>
                                    <p class="text-xs text-slate-500">{{ $peminjaman->alat->kode_alat ?? 'Q-'.$peminjaman->jumlah }}</p>
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-600">{{ $peminjaman->tanggal_pinjam ? $peminjaman->tanggal_pinjam->format('d M Y') : '-' }}</td>
                                <td class="py-3 px-4 text-sm text-slate-600">{{ $peminjaman->tanggal_kembali ? $peminjaman->tanggal_kembali->format('d M Y') : '-' }}</td>
                                <td class="py-3 px-4 text-sm text-slate-600">-</td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <form action="{{ route('kalab.persetujuan.approve', $peminjaman->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg hover:bg-emerald-200 transition-colors">
                                                <i class="fas fa-check mr-1"></i>Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('kalab.persetujuan.reject', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Tolak peminjaman ini?');">
                                            @csrf
                                            <input type="hidden" name="alasan" value="Ditolak oleh Kepala Lab">
                                            <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-200 transition-colors">
                                                <i class="fas fa-times mr-1"></i>Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-500 text-sm">
                                    <i class="fas fa-inbox text-slate-300 text-3xl mb-2 block"></i>
                                    Tidak ada permintaan persetujuan pending.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Lab Info --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Informasi Laboratorium</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Total Alat Terdaftar</span>
                            <span class="text-sm font-bold text-slate-800">{{ $stats['total_alat'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Alat Tersedia</span>
                            <span class="text-sm font-bold text-green-600">{{ $stats['tersedia'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Alat Sedang Dipinjam</span>
                            <span class="text-sm font-bold text-blue-600">{{ $stats['dipinjam'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Peminjaman Melewati Batas</span>
                            <span class="text-sm font-bold text-amber-600">{{ $stats['overdue'] }} Unit</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Aktivitas Terbaru</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse($recent_activities as $act)
                        <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $act->status == 'disetujui' || $act->status == 'selesai' ? 'from-emerald-500 to-teal-600' : ($act->status == 'ditolak' ? 'from-red-500 to-red-600' : 'from-blue-500 to-blue-600') }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                <i class="fas {{ $act->status == 'disetujui' ? 'fa-check' : ($act->status == 'ditolak' ? 'fa-times' : 'fa-bell') }} text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $act->user->name }} - {{ $act->alat->nama }} ({{ ucfirst($act->status) }})</p>
                                <p class="text-xs text-slate-500">{{ $act->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 p-3 text-center">Belum ada aktivitas.</p>
                        @endforelse
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
