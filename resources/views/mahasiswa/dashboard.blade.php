<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Alatika</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* font dasar pakai Inter untuk body text, Plus Jakarta Sans untuk heading */
        body { font-family: 'Inter', sans-serif; background: #F5F8FF; color: #1E2B4A; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }

        .card {
            background: white;
            border: 1px solid #EBF3FD;
            border-radius: 16px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }
        .card:hover {
            border-color: #B5D4F4;
            box-shadow: 0 4px 20px rgba(30,43,74,0.10);
        }

        /* stat cards pakai gradient landing page */
        .stat-card {
            background: white;
            border: 1px solid #EBF3FD;
            border-left: 3px solid;
            border-radius: 12px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 12px rgba(30,43,74,0.06);
        }
        .stat-card:hover { box-shadow: 0 4px 16px rgba(30,43,74,0.10); }
        .accent-blue    { border-left-color: #185FA5; }
        .accent-emerald { border-left-color: #10b981; }
        .accent-amber   { border-left-color: #f59e0b; }
        .accent-rose    { border-left-color: #ef4444; }

        .badge { display: inline-flex; align-items: center; padding: 0.375rem 0.875rem; font-size: 0.75rem; font-weight: 600; border-radius: 20px; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #EBF3FD;  color: #185FA5; }

        /* tombol pakai style landing page */
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; font-weight: 600; font-size: 0.875rem;
            border-radius: 12px; cursor: pointer; transition: all 0.2s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-primary {
            background: #1E2B4A;
            color: white;
            box-shadow: 0 4px 14px rgba(30,43,74,0.22);
        }
        .btn-primary:hover {
            box-shadow: 0 8px 24px rgba(24,95,165,0.30);
            transform: translateY(-1px);
            filter: brightness(1.05);
        }
        .btn-secondary {
            background: #F5F8FF;
            color: #1E2B4A;
            border: 1.5px solid #D4E6F8;
        }
        .btn-secondary:hover {
            background: white;
            border-color: #378ADD;
            box-shadow: 0 4px 12px rgba(30,43,74,0.08);
        }

        .list-item {
            background: #F5F8FF;
            border: 1px solid #EBF3FD;
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.2s ease;
        }
        .list-item:hover {
            background: white;
            border-color: #B5D4F4;
            box-shadow: 0 4px 12px rgba(30,43,74,0.07);
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{}">

    <!-- Sidebar -->
    <x-sidebar-mahasiswa />

    <!-- Main Content -->
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        <!-- Navbar Component -->
        <x-header-dashboard :title="'Dashboard'" :breadcrumbs="[]" />

        <div x-data="{ show: true }" x-show="show"
             class="mx-8 mt-4 bg-blue-50 border border-blue-100 text-primary rounded-xl px-5 py-3 flex items-center justify-between"
             style="color:#185FA5;"
             x-transition>
            <div class="flex items-center gap-3">
                <i class="fas fa-info-circle text-lg"></i>
                <p class="text-sm">
                    Ajukan peminjaman minimal <b>1 hari sebelum</b> penggunaan & kembalikan tepat waktu.
                </p>
            </div>
            <button @click="show = false" class="ml-4 hover:opacity-70 transition-opacity" style="color:#185FA5;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Main -->
        <main class="p-4 sm:p-6 lg:p-6 min-h-screen">
            
            <!-- Welcome -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif; color:#1E2B4A;">
                            Selamat Datang, {{ Auth::user()->name }}
                        </h2>
                        <p class="text-sm" style="color:#64748b;">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
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

            <!-- Stats — gradient sama persis seperti landing page hero stat badges -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <div class="p-5 rounded-2xl text-white hover:scale-[1.02] transition"
                     style="background:#1E2B4A;box-shadow:0 6px 20px rgba(30,43,74,0.22);">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-3xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['total'] }}</p>
                            <p class="text-sm opacity-80">Total Peminjaman</p>
                        </div>
                        <i class="fas fa-clipboard-list text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="p-5 rounded-2xl text-white hover:scale-[1.02] transition"
                     style="background:linear-gradient(135deg,#378ADD 0%,#185FA5 100%);box-shadow:0 6px 20px rgba(55,138,221,0.25);">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-3xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['dipinjam'] }}</p>
                            <p class="text-sm opacity-80">Sedang Dipinjam</p>
                        </div>
                        <i class="fas fa-hand-holding text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="p-5 rounded-2xl text-white hover:scale-[1.02] transition"
                     style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);box-shadow:0 6px 20px rgba(16,185,129,0.25);">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-3xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['selesai'] }}</p>
                            <p class="text-sm opacity-80">Selesai</p>
                        </div>
                        <i class="fas fa-check-circle text-3xl opacity-50"></i>
                    </div>
                </div>

                <div class="p-5 rounded-2xl text-white hover:scale-[1.02] transition"
                     style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);box-shadow:0 6px 20px rgba(245,158,11,0.25);">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-3xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['ditolak'] }}</p>
                            <p class="text-sm opacity-80">Ditolak</p>
                        </div>
                        <i class="fas fa-times-circle text-3xl opacity-50"></i>
                    </div>
                </div>

            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Aktivitas Terbaru -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">Aktivitas Terbaru</h2>
                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" class="text-sm font-semibold hover:opacity-70 transition-opacity" style="color:#185FA5;">
                                Lihat Semua →
                            </a>
                        </div>
                        <div class="space-y-3">
                            @forelse($recent as $p)
                            <div class="list-item">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                         style="background:#EBF3FD;color:#185FA5;">
                                        <i class="fas {{ $p->status_config['icon'] }} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <p class="font-semibold text-sm mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $p->alat->nama }}</p>
                                                <p class="text-xs" style="color:#64748b;">Kode: {{ $p->kode_peminjaman }}</p>
                                                <p class="text-xs mt-1" style="color:#94a3b8;">{{ $p->created_at->diffForHumans() }}</p>
                                            </div>
                                            <span class="badge {{ $p->status_config['color'] }} flex-shrink-0">{{ $p->status_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-10 flex flex-col items-center justify-center">
                                <div class="w-14 h-14 rounded-full flex items-center justify-center mb-4" style="background:#EBF3FD;">
                                    <i class="fas fa-inbox text-xl" style="color:#B5D4F4;"></i>
                                </div>
                                <p class="text-sm font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Belum ada aktivitas peminjaman
                                </p>
                                <p class="text-xs mb-4" style="color:#94a3b8;">
                                    Mulai pinjam alat untuk melihat riwayatmu di sini
                                </p>
                                <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 text-white text-xs font-semibold rounded-xl transition-all"
                                   style="background:#1E2B4A;box-shadow:0 4px 12px rgba(30,43,74,0.22);">
                                    <i class="fas fa-plus"></i>
                                    Ajukan Peminjaman
                                </a>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar kanan -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="card p-6">
                        <h3 class="text-base font-semibold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">Menu Cepat</h3>
                        <div class="space-y-2">

                            <a href="{{ route('mahasiswa.keranjang') }}" 
                               class="flex items-center gap-3 p-3 rounded-xl border border-transparent transition-all group"
                               style="background:#F5F8FF;"
                               onmouseover="this.style.background='#EBF3FD';this.style.borderColor='#B5D4F4';"
                               onmouseout="this.style.background='#F5F8FF';this.style.borderColor='transparent';">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-all flex-shrink-0"
                                     style="background:#EBF3FD;color:#185FA5;">
                                    <i class="fas fa-shopping-cart text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Keranjang</p>
                                    <p class="text-xs" style="color:#94a3b8;">Cek alat pilihanmu</p>
                                </div>
                            </a>

                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" 
                               class="flex items-center gap-3 p-3 rounded-xl border border-transparent transition-all"
                               style="background:#F5F8FF;"
                               onmouseover="this.style.background='#EBF3FD';this.style.borderColor='#B5D4F4';"
                               onmouseout="this.style.background='#F5F8FF';this.style.borderColor='transparent';">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:#EBF3FD;color:#185FA5;">
                                    <i class="fas fa-history text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Riwayat</p>
                                    <p class="text-xs" style="color:#94a3b8;">Lihat peminjaman</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t py-5 px-4 sm:px-6 lg:px-8 mt-8" style="border-color:#EBF3FD;">
            <div class="text-center">
                <p class="text-xs font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">&copy; 2026 Alatika - Politeknik Negeri Semarang</p>
                <p class="text-xs" style="color:#94a3b8;">Sistem Peminjaman Alat Laboratorium Elektronik</p>
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