<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen - Alatika</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
        
        /* Card style */
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
        }
        
        .card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        /* Stats card */
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-left: 3px solid;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        
        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        /* Accent colors */
        .accent-blue { border-left-color: #3b82f6; }
        .accent-emerald { border-left-color: #10b981; }
        .accent-amber { border-left-color: #f59e0b; }
        .accent-rose { border-left-color: #ef4444; }
        
        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.875rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        
        /* Button styles */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.25);
        }
        
        .btn-primary:hover {
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
            filter: brightness(1.05);
        }
        
        .btn-secondary {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        
        .btn-secondary:hover {
            background: white;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        /* List item */
        .list-item {
            background: #fafbfc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.2s ease;
        }
        
        .list-item:hover {
            background: white;
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        /* Menu hover - removed transform to prevent jumping */
        .menu-item {
            transition: all 0.2s ease;
        }
        
        .menu-item:hover {
            background: #f8fafc;
        }
        
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Profile Dropdown */
        .dropdown-menu {
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.2s ease;
            pointer-events: none;
        }
        
        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        
        /* Modal */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, showProfileDropdown: false }">

    <!-- Sidebar Component -->
    <x-sidebar-dosen />

    <!-- Main Content Area - Adjusts based on sidebar -->
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
            
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Selamat datang di portal mahasiswa</p>
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
                                    
                                    <!-- Menu Items -->
                                    <div class="py-1">
                                        <a href="{{ route('dosen.profil') }}" 
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-user-circle w-4 text-gray-400"></i>
                                            <span>Profil Saya</span>
                                        </a>
                                        <a href="{{ route('dosen.riwayat') }}" 
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-history w-4 text-gray-400"></i>
                                            <span>Riwayat Peminjaman</span>
                                        </a>
                                    </div>
                                    
                                    <!-- Logout -->
                                    <div class="border-t border-gray-100 py-1">
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

            <!-- Main Content Area -->
            <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
                
                <!-- Welcome Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">
                                Selamat Datang, {{ Auth::user()->name }}
                            </h2>
                            <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('dosen.peminjaman.ajukan') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i>
                                <span>Ajukan Peminjaman</span>
                            </a>
                            <a href="{{ route('dosen.alat') }}" class="btn btn-secondary">
                                <i class="fas fa-box-open"></i>
                                <span>Lihat Katalog</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Card 1 -->
                    <div class="stat-card accent-blue p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">+15%</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Total Peminjaman</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="stat-card accent-amber p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hand-holding text-amber-600 text-lg"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Sedang Dipinjam</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['dipinjam'] }}</p>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="stat-card accent-emerald p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                            </div>
                            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2.5 py-1 rounded-full">+25%</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['selesai'] }}</p>
                    </div>
                    
                    <!-- Card 4 -->
                    <div class="stat-card accent-rose p-5 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-11 h-11 bg-rose-50 rounded-xl flex items-center justify-center">
                                <i class="fas fa-times-circle text-rose-600 text-lg"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">Ditolak</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['ditolak'] }}</p>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Recent Activity - 2 columns -->
                    <div class="lg:col-span-2">
                        <div class="card p-6">
                            <div class="flex items-center justify-between mb-5">
                                <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h2>
                                <a href="{{ route('dosen.riwayat') }}" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Lihat Semua →
                                </a>
                            </div>
                            
                            <div class="space-y-3">
                                @forelse($recent as $p)
                                <div class="list-item">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 {{ strtok($p->status_config['color'], ' ') }} bg-opacity-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $p->status_config['icon'] }} text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <p class="font-semibold text-gray-900 text-sm mb-1">{{ $p->alat->nama }}</p>
                                                    <p class="text-xs text-gray-500">Kode: {{ $p->kode_peminjaman }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $p->created_at->diffForHumans() }}</p>
                                                </div>
                                                <span class="badge {{ $p->status_config['color'] }} flex-shrink-0">{{ $p->status_label }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">Belum ada aktivitas peminjaman</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links & Info - 1 column -->
                    <div class="lg:col-span-1 space-y-6">
                        
                        <!-- Quick Links -->
                        <div class="card p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Menu Cepat</h3>
                            <div class="space-y-2">
                                <a href="{{ route('dosen.riwayat') }}" 
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                                        <i class="fas fa-history text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Riwayat</p>
                                        <p class="text-xs text-gray-500">Lihat semua peminjaman</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('dosen.profil') }}" 
                                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Profil</p>
                                        <p class="text-xs text-gray-500">Kelola akun Anda</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Info Card -->
                        <div class="card p-6 bg-gradient-to-br from-blue-50 to-sky-50 border-blue-100">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm mb-2">Informasi Penting</p>
                                    <ul class="space-y-1.5 text-xs text-gray-700">
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check text-blue-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Ajukan peminjaman minimal 1 hari sebelum penggunaan</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check text-blue-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Kembalikan alat tepat waktu untuk menghindari denda</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check text-blue-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Periksa kondisi alat sebelum/sesudah pemakaian</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Summary -->
                        <div class="card p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Ringkasan Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Menunggu Persetujuan</span>
                                    <span class="font-semibold text-gray-900">{{ $statusSummary['pending'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Dalam Penggunaan</span>
                                    <span class="font-semibold text-gray-900">{{ $statusSummary['dipinjam'] }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-600">Total Selesai</span>
                                    <span class="font-semibold text-gray-900">{{ $statusSummary['selesai'] }}</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-5 px-4 sm:px-6 lg:px-8 mt-8">
                <div class="text-center">
                    <p class="text-xs text-gray-600 font-medium mb-1">&copy; 2026 Alatika - Politeknik Negeri Semarang</p>
                    <p class="text-xs text-gray-500">Sistem Peminjaman Alat Laboratorium Elektronik</p>
                </div>
            </footer>
        </div>
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

    <script>
        // Sidebar collapse handler
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
