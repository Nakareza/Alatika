<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat - Alatika</title>
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
            transition: all 0.2s;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(30,43,74,0.10);
            border-color: #B5D4F4;
        }

        .inp {
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

        @keyframes pop { 0%,100%{transform:scale(1)} 50%{transform:scale(1.35)} }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64" x-data="cartModal()">
        <x-header-dashboard title="Daftar Alat" />

        @if(session('success'))
        <div class="mx-8 mt-6 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium"
             style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-8 mt-6 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium"
             style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <main class="p-8 min-h-screen relative z-0">

            <!-- Top bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">

                <!-- Kiri: Search + Filter -->
                <div class="flex items-center gap-3 flex-wrap">

                    <!-- Search -->
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama alat..."
                               class="inp pl-9 pr-4 py-2.5 w-52">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:#A0BBCC;"></i>
                    </div>

                    <!-- Filter Kategori -->
                    <select x-model="filterKategori"
                            class="inp py-2.5 px-4 pr-8 cursor-pointer appearance-none"
                            style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                        <option value="">Semua Kategori</option>
                        @foreach($alat->pluck('kategori')->unique()->filter()->sort()->values() as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Stok -->
                    <select x-model="filterStok"
                            class="inp py-2.5 px-4 pr-8 cursor-pointer appearance-none"
                            style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                        <option value="">Semua Stok</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>

                </div>

                <!-- Kanan: Action buttons -->
                <div class="flex items-center gap-2 flex-shrink-6">
                    <a href="{{ route('mahasiswa.keranjang') }}"
                       class="relative inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all"
                       style="background:#1E2B4A;box-shadow:0 4px 14px rgba(30,43,74,0.22);font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-shopping-cart"></i>
                        Keranjang
                        @if($cartCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-white text-sm font-semibold rounded-xl transition-all"
                       style="background:#378ADD;box-shadow:0 4px 14px rgba(55,138,221,0.25);font-family:'Plus Jakarta Sans',sans-serif;"
                       onmouseover="this.style.background='#185FA5'"
                       onmouseout="this.style.background='#378ADD'">
                        <i class="fas fa-plus"></i>
                        Pinjam Langsung
                    </a>
                </div>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($alat as $item)
                <div class="card overflow-hidden"
                     x-show="
                        (search === '' || '{{ strtolower($item->nama) }}'.includes(search.toLowerCase())) &&
                        (filterKategori === '' || filterKategori === '{{ $item->kategori }}') &&
                        (filterStok === '' || (filterStok === 'tersedia' && {{ $item->stok_tersedia }} > 0) || (filterStok === 'habis' && {{ $item->stok_tersedia }} === 0))
                     "
                     x-cloak>

                    <!-- Image -->
                    <div class="h-36 flex items-center justify-center border-b"
                         style="{{ $item->stok_tersedia > 0 ? 'background:linear-gradient(135deg,#EBF3FD,#dbeafe);border-color:#D4E6F8;' : 'background:#f1f5f9;border-color:#e2e8f0;' }}">
                        <i class="fas fa-microchip text-4xl" style="{{ $item->stok_tersedia > 0 ? 'color:#B5D4F4;' : 'color:#cbd5e1;' }}"></i>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <span class="text-xs font-bold tracking-wider uppercase" style="color:#185FA5;">{{ $item->kategori }}</span>
                            @if($item->stok_tersedia > 0)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:#d1fae5;color:#065f46;">Tersedia</span>
                            @else
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:#fee2e2;color:#991b1b;">Habis</span>
                            @endif
                        </div>
                        <h3 class="text-base font-bold leading-tight mb-3" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $item->nama }}</h3>

                        <div class="flex items-center justify-between text-sm mb-4">
                            <div>
                                <span class="text-xs block mb-0.5" style="color:#A0BBCC;">Kode</span>
                                <span class="font-semibold" style="color:#1E2B4A;">{{ $item->kode }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs block mb-0.5" style="color:#A0BBCC;">Stok</span>
                                <span class="font-bold" style="{{ $item->stok_tersedia > 0 ? 'color:#10b981;' : 'color:#ef4444;' }}">
                                    {{ $item->stok_tersedia }} / {{ $item->stok_total }}
                                </span>
                            </div>
                        </div>

                        @if($item->stok_tersedia > 0)
                            <button @click="openModal({{ $item->id }}, '{{ addslashes($item->nama) }}', {{ $item->stok_tersedia }})"
                                class="w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm active:scale-95 transition-all flex items-center justify-center gap-2"
                                style="background:#1E2B4A;box-shadow:0 4px 14px rgba(30,43,74,0.20);font-family:'Plus Jakarta Sans',sans-serif;"
                                onmouseover="this.style.filter='brightness(1.1)'"
                                onmouseout="this.style.filter=''">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        @else
                            <form action="{{ route('mahasiswa.alat.waitlist', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 border"
                                    style="background:#fffbeb;color:#d97706;border-color:#fcd34d;font-family:'Plus Jakarta Sans',sans-serif;"
                                    onmouseover="this.style.background='#fef3c7'"
                                    onmouseout="this.style.background='#fffbeb'">
                                    <i class="fas fa-bell"></i> Kabari Saya saat Tersedia
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#EBF3FD;">
                        <i class="fas fa-box-open text-xl" style="color:#B5D4F4;"></i>
                    </div>
                    <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Belum ada data alat di database.</p>
                </div>
                @endforelse

                <!-- Empty state saat filter tidak ada hasil -->
                <div class="col-span-full py-16 text-center"
                     x-show="document.querySelectorAll('.card[style*=\"display: none\"]').length === document.querySelectorAll('.card').length"
                     x-cloak>
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#EBF3FD;">
                        <i class="fas fa-search text-xl" style="color:#B5D4F4;"></i>
                    </div>
                    <p class="text-sm font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Tidak ada alat yang cocok dengan filter.</p>
                    <button @click="search=''; filterKategori=''; filterStok=''"
                            class="mt-2 text-sm font-semibold hover:opacity-70 transition-opacity"
                            style="color:#185FA5;">
                        Reset filter
                    </button>
                </div>
            </div>
        </main>

        <x-modal name="keranjang" title="Tambah ke Keranjang" type="default" size="md">
            <p class="text-sm text-center mb-1" style="color:#64748b;">
                Stok tersedia: <span class="font-semibold" style="color:#10b981;" x-text="selectedItem.stok + ' unit'"></span>
            </p>

            <label class="block text-sm font-semibold mb-3 mt-4" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Jumlah yang dipinjam</label>
            <div class="flex items-center gap-4 mb-2">
                <button @click="jumlah = Math.max(1, jumlah - 1)" type="button"
                    class="w-12 h-12 rounded-xl font-bold transition flex items-center justify-center"
                    style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" x-model.number="jumlah" :max="selectedItem.stok"
                    class="flex-1 text-center rounded-xl py-2.5 font-bold text-lg outline-none"
                    style="border:1.5px solid #D4E6F8;background:#F5F8FF;color:#1E2B4A;">
                <button @click="jumlah = Math.min(selectedItem.stok, jumlah + 1)" type="button"
                    class="w-12 h-12 rounded-xl font-bold transition flex items-center justify-center"
                    style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <form id="cartForm" method="POST" style="display:none;">
                @csrf
                <input type="hidden" id="jumlahInput" name="jumlah">
            </form>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal-keranjang')"
                    class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition"
                    style="border:1.5px solid #D4E6F8;color:#1E2B4A;background:#F5F8FF;font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseover="this.style.borderColor='#378ADD'"
                    onmouseout="this.style.borderColor='#D4E6F8'">
                    Batal
                </button>
                <button type="button" @click="submitCart()"
                    class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm transition flex items-center justify-center gap-2"
                    style="background:#1E2B4A;box-shadow:0 4px 14px rgba(30,43,74,0.20);font-family:'Plus Jakarta Sans',sans-serif;"
                    onmouseover="this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.filter=''">
                    <i class="fas fa-check"></i> Tambahkan
                </button>
            </x-slot>
        </x-modal>

    </div>

    <script>
        function cartModal() {
            return {
                selectedItem: { id: null, nama: '', stok: 0 },
                jumlah: 1,
                search: '',
                filterKategori: '',
                filterStok: '',

                openModal(id, nama, stok) {
                    this.selectedItem = { id, nama, stok };
                    this.jumlah = 1;
                    this.$dispatch('open-modal-keranjang');
                },

                closeModal() {
                    this.$dispatch('close-modal-keranjang');
                    this.selectedItem = { id: null, nama: '', stok: 0 };
                    this.jumlah = 1;
                },

                submitCart() {
                    const form = document.getElementById('cartForm');
                    const jumlahInput = document.getElementById('jumlahInput');
                    jumlahInput.value = this.jumlah;
                    form.action = `/mahasiswa/keranjang/${this.selectedItem.id}/add`;
                    form.submit();
                    this.closeModal();
                }
            }
        }

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