<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Alatika</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; transition: all 0.2s ease; }
        .card:hover { border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-left: 3px solid; border-radius: 12px; transition: all 0.2s ease; }
        .stat-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .accent-blue { border-left-color: #3b82f6; }
        .accent-emerald { border-left-color: #10b981; }
        .accent-amber { border-left-color: #f59e0b; }
        .accent-rose { border-left-color: #ef4444; }
        .badge { display: inline-flex; align-items: center; padding: 0.375rem 0.875rem; font-size: 0.75rem; font-weight: 600; border-radius: 20px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; font-weight: 500; font-size: 0.875rem; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; box-shadow: 0 2px 8px rgba(59,130,246,0.25); }
        .btn-primary:hover { box-shadow: 0 4px 12px rgba(59,130,246,0.35); filter: brightness(1.05); }
        .btn-secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: white; border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .list-item { background: #fafbfc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; transition: all 0.2s ease; }
        .list-item:hover { background: white; border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        [x-cloak] { display: none !important; }
        .modal-backdrop { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{}">

    <!-- Sidebar -->
    <x-sidebar-mahasiswa />

    <!-- Main Content -->
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <!-- Navbar Component -->
        <x-header-dashboard :title="'Dashboard'" :breadcrumbs="[]" />

        <!-- Main -->
        <main class="p-4 sm:p-6 lg:p-6 min-h-screen">
            
            <!-- Welcome -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">
                            Selamat Datang, {{ Auth::user()->name }}
                        </h2>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Ajukan Peminjaman</span>
                        </a>
                        <a href="{{ route('mahasiswa.alat') }}" class="btn btn-secondary">
                            <i class="fas fa-box-open"></i>
                            <span>Lihat Alat</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-blue-500 to-cyan-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-sm opacity-90">Total Peminjaman</p>
            </div>
            <i class="fas fa-clipboard-list text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Dipinjam -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-green-400 to-emerald-500 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['dipinjam'] }}</p>
                <p class="text-sm opacity-90">Sedang Dipinjam</p>
            </div>
            <i class="fas fa-hand-holding text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Selesai -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-orange-400 to-yellow-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['selesai'] }}</p>
                <p class="text-sm opacity-90">Selesai</p>
            </div>
            <i class="fas fa-check-circle text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-pink-500 to-red-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['ditolak'] }}</p>
                <p class="text-sm opacity-90">Ditolak</p>
            </div>
            <i class="fas fa-times-circle text-3xl opacity-70"></i>
        </div>
    </div>

</div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Aktivitas Terbaru -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h2>
                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
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

                <!-- Sidebar kanan -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Menu Cepat</h3>
                        <div class="space-y-2">
                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" 
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                                    <i class="fas fa-history text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Riwayat</p>
                                    <p class="text-xs text-gray-500">Lihat semua peminjaman</p>
                                </div>
                            </a>
                            <a href="{{ route('mahasiswa.profil') }}" 
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