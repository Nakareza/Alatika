<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Peminjaman - Alatika</title>
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

        .card {
            background: white;
            border: 1px solid #EBF3FD;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }

        .inp {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #F5F8FF;
            border: 1.5px solid #D4E6F8;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            color: #1E2B4A;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .inp:focus {
            border-color: #378ADD;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(55,138,221,0.10);
        }
        .inp::placeholder { color: #A0BBCC; }
    </style>
</head>
<body class="antialiased">
    <x-sidebar-mahasiswa />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        <x-header-dashboard title="Keranjang"/>
        <div class="container mx-auto p-8">

            @if(session('success'))
                <div class="px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium mb-6"
                     style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium mb-6"
                     style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Cart Items List -->
                <div class="lg:col-span-2">
                    <div class="card overflow-hidden">
                        <div class="p-6" style="border-bottom:1px solid #EBF3FD;">
                            <h2 class="text-lg font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                <i class="fas fa-box-open mr-2" style="color:#185FA5;"></i> Item Peminjaman
                            </h2>
                        </div>

                        @forelse($keranjang as $item)
                        <div class="p-6 flex flex-col sm:flex-row items-center justify-between gap-4 transition"
                             style="border-bottom:1px solid #F5F8FF;"
                             onmouseover="this.style.background='#F5F8FF'"
                             onmouseout="this.style.background='white'">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background:#EBF3FD;color:#185FA5;">
                                    <i class="fas fa-microchip text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $item->alat->nama }}</h3>
                                    <p class="text-sm mt-0.5" style="color:#94a3b8;">
                                        Kode: {{ $item->alat->kode ?? $item->alat->kode_alat }}
                                        &middot;
                                        Stok Tersedia: {{ $item->alat->stok_tersedia }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#A0BBCC;">Jumlah</span>
                                    <span class="font-bold text-lg" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $item->jumlah }}</span>
                                </div>

                                <form action="{{ route('mahasiswa.keranjang.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center transition"
                                        style="background:#fee2e2;color:#ef4444;"
                                        onmouseover="this.style.background='#fecaca'"
                                        onmouseout="this.style.background='#fee2e2'"
                                        title="Hapus dari Keranjang">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                                 style="background:#EBF3FD;">
                                <i class="fas fa-shopping-cart text-3xl" style="color:#B5D4F4;"></i>
                            </div>
                            <h3 class="text-lg font-bold mb-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Keranjang Kosong</h3>
                            <p class="text-sm mb-6" style="color:#94a3b8;">Anda belum menambahkan alat apapun ke keranjang peminjaman.</p>
                            <a href="{{ route('mahasiswa.alat') }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 text-white text-sm font-semibold rounded-xl transition"
                               style="background:#1E2B4A;box-shadow:0 4px 14px rgba(30,43,74,0.22);font-family:'Plus Jakarta Sans',sans-serif;"
                               onmouseover="this.style.filter='brightness(1.1)'"
                               onmouseout="this.style.filter=''">
                                <i class="fas fa-box-open"></i>
                                Lihat Katalog Alat
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="lg:col-span-1">
                    <div class="card p-6 sticky top-24">
                        <h2 class="text-lg font-bold mb-6" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                            <i class="fas fa-clipboard-check mr-2" style="color:#10b981;"></i> Detail Pengajuan
                        </h2>

                        <div class="flex justify-between items-center mb-6 pb-6" style="border-bottom:1px solid #EBF3FD;">
                            <span class="font-medium text-sm" style="color:#64748b;">Total Item</span>
                            <span class="text-xl font-black" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ count($keranjang) }} Alat</span>
                        </div>

                        <form action="{{ route('mahasiswa.keranjang.checkout') }}" method="POST">
                            @csrf

                            <div class="mb-5">
                                <label class="block text-sm font-semibold mb-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Keperluan Peminjaman <span class="text-red-500">*</span>
                                </label>
                                <textarea name="keperluan" rows="3" class="inp" style="resize:vertical;"
                                          placeholder="Misal: Praktikum Mikrokontroler, Proyek Akhir..." required></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-semibold mb-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Rencana Tanggal Kembali <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_kembali"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                       class="inp" required>
                                <p class="text-xs mt-2" style="color:#94a3b8;">
                                    <i class="fas fa-info-circle mr-1" style="color:#B5D4F4;"></i>
                                    Peminjaman maksimal 14 hari dari hari ini.
                                </p>
                            </div>

                            <button type="submit"
                                class="w-full py-3.5 rounded-xl text-white font-bold text-sm flex items-center justify-center gap-2 transition-all"
                                style="background:#1E2B4A;box-shadow:0 6px 20px rgba(30,43,74,0.22);font-family:'Plus Jakarta Sans',sans-serif;"
                                onmouseover="this.style.filter='brightness(1.1)';this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.filter='';this.style.transform=''"
                                {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane"></i> Ajukan Semua Peminjaman
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
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