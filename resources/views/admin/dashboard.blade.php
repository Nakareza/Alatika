<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Alatika</title>
    
    {{-- TailwindCSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: #f8fafc;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .glassmorphism {
            background: white;
            border: 1px solid #e2e8f0;
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.12);
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-down {
            animation: slideDown 0.6s ease;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        .luxury-shadow {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .gradient-border {
            position: relative;
        }
        
        /* Profile Dropdown */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Modal */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false }">
    
    {{-- Sidebar Admin --}}
    <x-sidebar-admin />
    
    {{-- Main Content --}}
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Admin</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Panel manajemen sistem peminjaman alat elektronik</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                            <i class="fas fa-bell text-lg"></i>
                        </button>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-all">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50"
                                 x-cloak>
                                
                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <!-- Logout -->
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
        <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
            
            {{-- Welcome Section --}}
            <div class="glassmorphism rounded-2xl p-8 md:p-10 luxury-shadow animate-slide-down overflow-hidden relative mb-6">
                <div class="flex items-center gap-6 md:gap-8">
                    <div class="hidden md:flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-50 border border-blue-100">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-16 h-16 object-contain">
                    </div>
                    <div class="flex-1">
                        <div class="mb-2">
                            <span class="inline-block px-4 py-1.5 rounded-full bg-gradient-to-r from-blue-500/10 to-blue-500/10 border border-blue-200/50 text-blue-700 text-sm font-semibold">
                                <i class="fas fa-crown mr-2 text-amber-500"></i>Administrator Panel
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-black mb-2 leading-tight">
                            Selamat Datang, 
                            <span class="bg-gradient-to-r from-blue-600 via-blue-600 to-blue-600 bg-clip-text text-transparent">
                                {{ Auth::user()->name }}! 👋
                            </span>
                        </h1>
                        <p class="text-slate-600 text-base font-medium">Panel manajemen sistem peminjaman alat elektronik Politeknik Negeri Semarang</p>
                    </div>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <x-card-stats-admin 
                    title="Total Peminjaman" 
                    :value="$stats['total']" 
                    icon="fas fa-clipboard-list" 
                    color="blue" />
                
                <x-card-stats-admin 
                    title="Menunggu Persetujuan" 
                    :value="$stats['pending']" 
                    icon="fas fa-clock" 
                    color="yellow" />
                
                <x-card-stats-admin 
                    title="Sedang Dipinjam" 
                    :value="$stats['dipinjam']" 
                    icon="fas fa-hand-holding" 
                    color="indigo" />
                
                <x-card-stats-admin 
                    title="Selesai" 
                    :value="$stats['selesai']" 
                    icon="fas fa-check-double" 
                    color="green" />
                
                <x-card-stats-admin 
                    title="Terlambat" 
                    :value="$stats['overdue']" 
                    icon="fas fa-exclamation-triangle" 
                    color="red" />
            </div>

            {{-- Quick Actions --}}
            <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-blue-500"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <a href="{{ route('admin.alat') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-indigo-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-laptop text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Kelola Alat</h3>
                            <p class="text-xs text-slate-500">Manajemen alat</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.mahasiswa') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-graduate text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Kelola Mahasiswa</h3>
                            <p class="text-xs text-slate-500">Data mahasiswa</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.peminjaman') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-clipboard-list text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Kelola Peminjaman</h3>
                            <p class="text-xs text-slate-500">Persetujuan</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.pengembalian') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-undo-alt text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Kelola Pengembalian</h3>
                            <p class="text-xs text-slate-500">Verifikasi</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.laporan') }}" 
                       class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 hover:shadow-lg transition-all duration-300 hover-lift group">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-bar text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800 text-sm">Lihat Laporan</h3>
                            <p class="text-xs text-slate-500">Statistik sistem</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Recent Borrowing Table --}}
            <div class="glassmorphism rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-list-alt text-blue-500"></i> Peminjaman Terbaru
                    </h2>
                    <a href="{{ route('admin.peminjaman') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Lihat semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Peminjam</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alat</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentPeminjaman as $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($p->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800 text-sm">{{ $p->user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $p->user->nim ?? $p->user->nip ?? $p->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-800">{{ $p->alat->nama }}</p>
                                    <p class="text-xs text-slate-400">{{ $p->kode_peminjaman }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <p>{{ $p->tanggal_pinjam->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-400">Kembali: {{ $p->tanggal_kembali->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badge = match($p->status) {
                                            'pending'   => 'bg-amber-100 text-amber-700',
                                            'disetujui' => 'bg-blue-100 text-blue-700',
                                            'dipinjam'  => 'bg-indigo-100 text-indigo-700',
                                            'menunggu_verifikasi' => 'bg-purple-100 text-purple-700',
                                            'selesai'   => 'bg-emerald-100 text-emerald-700',
                                            'ditolak'   => 'bg-red-100 text-red-700',
                                            default     => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.peminjaman') }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-inbox text-3xl text-slate-200 mb-3 block"></i>
                                    Belum ada data peminjaman
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Additional Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- System Info (Dynamic) --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <i class="fas fa-info-circle text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Ringkasan Inventaris</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Total Stok Alat</span>
                            <span class="text-sm font-bold text-slate-800">{{ $alatStats['total_alat'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Stok Tersedia</span>
                            <span class="text-sm font-bold text-green-600">{{ $alatStats['tersedia'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Sedang Dipinjam</span>
                            <span class="text-sm font-bold text-blue-600">{{ $stats['dipinjam'] }} Peminjaman</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                            <span class="text-sm text-slate-600">Total Mahasiswa</span>
                            <span class="text-sm font-bold text-slate-800">{{ $alatStats['total_mahasiswa'] }} Orang</span>
                        </div>
                        @if($stats['overdue'] > 0)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 border border-red-100">
                            <span class="text-sm text-red-600 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i> Terlambat Kembali</span>
                            <span class="text-sm font-bold text-red-600">{{ $stats['overdue'] }} Peminjaman</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Activity (Dynamic) --}}
                <div class="glassmorphism rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Aktivitas Terbaru</h3>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentActivities as $act)
                        @php
                            $colors = ['from-blue-500 to-blue-600', 'from-emerald-500 to-teal-600', 'from-amber-500 to-orange-500', 'from-purple-500 to-indigo-600', 'from-rose-500 to-pink-500'];
                            $color = $colors[$loop->index % count($colors)];
                            $actionText = match($act->status) {
                                'pending'   => 'mengajukan peminjaman',
                                'disetujui' => 'peminjaman disetujui',
                                'dipinjam'  => 'mengambil alat',
                                'menunggu_verifikasi' => 'mengirim bukti kembali',
                                'selesai'   => 'mengembalikan alat',
                                'ditolak'   => 'pengajuan ditolak',
                                default     => 'melakukan aktivitas',
                            };
                        @endphp
                        <div class="flex gap-3 p-3 rounded-lg bg-slate-50">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $color }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($act->user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $act->user->name }} {{ $actionText }}</p>
                                <p class="text-xs text-slate-400">{{ $act->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada aktivitas terbaru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="showLogoutModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="showLogoutModal = false">
        
        <!-- Backdrop -->
        <div x-show="showLogoutModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 modal-backdrop"
             @click="showLogoutModal = false"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showLogoutModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                
                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-sign-out-alt text-red-600 text-2xl"></i>
                    </div>
                </div>
                
                <!-- Text -->
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin keluar dari akun Anda?</p>
                </div>
                
                <!-- Buttons -->
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false" 
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl transition-all shadow-sm hover:shadow-md">
                            Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
