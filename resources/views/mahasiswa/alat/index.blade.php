<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; transition: all 0.2s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.08); }
        @keyframes pop { 0%,100%{transform:scale(1)} 50%{transform:scale(1.35)} }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <x-header-dashboard title="Katalog Alat"  :hideSearch="true" />

        @if(session('success'))
        <div class="mx-8 mt-6 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-8 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <main class="p-8 min-h-screen" x-data="cartModal()">

            <!-- Top bar -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">

                <!-- Kiri: Search + Filter -->
                <div class="flex items-center gap-3 flex-wrap">

                    <!-- Search -->
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama alat..."
                               class="pl-9 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors w-52">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    </div>

                    <!-- Filter Kategori -->
                    <select x-model="filterKategori"
                            class="py-2.5 px-4 pr-8 text-sm bg-white border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer appearance-none"
                            style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                        <option value="">Semua Kategori</option>
                        @foreach($alat->pluck('kategori')->unique()->filter()->sort()->values() as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Stok -->
                    <select x-model="filterStok"
                            class="py-2.5 px-4 pr-8 text-sm bg-white border border-gray-200 rounded-xl text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors cursor-pointer appearance-none"
                            style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                        <option value="">Semua Stok</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>

                </div>

                <!-- Kanan: Action buttons -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('mahasiswa.keranjang') }}"
                       class="relative inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
                        <i class="fas fa-shopping-cart"></i>
                        Keranjang
                        @if($cartCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all shadow-sm">
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
                    <div class="h-36 flex items-center justify-center border-b border-gray-100
                        {{ $item->stok_tersedia > 0 ? 'bg-gradient-to-br from-blue-50 to-indigo-50' : 'bg-gray-100' }}">
                        <i class="fas fa-microchip text-4xl {{ $item->stok_tersedia > 0 ? 'text-indigo-300' : 'text-gray-300' }}"></i>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold text-blue-600 tracking-wider uppercase">{{ $item->kategori }}</span>
                            @if($item->stok_tersedia > 0)
                                <span class="text-xs bg-emerald-100 text-emerald-700 font-bold px-2 py-0.5 rounded-full">Tersedia</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">Habis</span>
                            @endif
                        </div>
                        <h3 class="text-base font-bold text-gray-900 leading-tight mb-3">{{ $item->nama }}</h3>

                        <div class="flex items-center justify-between text-sm mb-4">
                            <div>
                                <span class="text-gray-400 text-xs block">Kode</span>
                                <span class="font-semibold text-gray-700">{{ $item->kode }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-gray-400 text-xs block">Stok</span>
                                <span class="font-bold {{ $item->stok_tersedia > 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $item->stok_tersedia }} / {{ $item->stok_total }}
                                </span>
                            </div>
                        </div>

                        @if($item->stok_tersedia > 0)
                            <button @click="openModal({{ $item->id }}, '{{ addslashes($item->nama) }}', {{ $item->stok_tersedia }})"
                                class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        @else
                            <form action="{{ route('mahasiswa.alat.waitlist', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-xl bg-amber-50 text-amber-600 font-semibold text-sm hover:bg-amber-100 transition-all flex items-center justify-center gap-2 border border-amber-200">
                                    <i class="fas fa-bell"></i> Kabari Saya saat Tersedia
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center text-gray-500">
                    <i class="fas fa-box-open text-5xl text-gray-200 mb-4 block"></i>
                    Belum ada data alat di database.
                </div>
                @endforelse

                <!-- Empty state saat filter tidak ada hasil -->
                <div class="col-span-full py-16 text-center text-gray-500"
                     x-show="document.querySelectorAll('.card[style*=\"display: none\"]').length === document.querySelectorAll('.card').length"
                     x-cloak>
                    <i class="fas fa-search text-5xl text-gray-200 mb-4 block"></i>
                    <p class="font-medium">Tidak ada alat yang cocok dengan filter.</p>
                    <button @click="search=''; filterKategori=''; filterStok=''"
                            class="mt-3 text-sm text-blue-600 hover:underline">
                        Reset filter
                    </button>
                </div>
            </div>

            <!-- Modal Tambah ke Keranjang -->
            <div x-show="showModal"
                 x-transition
                 class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
                 @click.self="closeModal()"
                 x-cloak>
                <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md" @click.stop>
                    <h3 class="font-bold text-gray-900 text-xl mb-1" x-text="selectedItem.nama"></h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Stok tersedia: <span class="font-semibold text-emerald-600" x-text="selectedItem.stok + ' unit'"></span>
                    </p>

                    <label class="block text-sm font-semibold text-gray-700 mb-3">Jumlah yang dipinjam</label>
                    <div class="flex items-center gap-4 mb-8">
                        <button @click="jumlah = Math.max(1, jumlah - 1)" type="button"
                            class="w-12 h-12 rounded-xl bg-gray-100 font-bold text-gray-700 hover:bg-gray-200 transition flex items-center justify-center">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" x-model.number="jumlah" :max="selectedItem.stok"
                            class="flex-1 text-center border border-gray-300 rounded-xl py-2.5 font-bold text-lg focus:ring-2 focus:ring-indigo-400 focus:border-transparent outline-none">
                        <button @click="jumlah = Math.min(selectedItem.stok, jumlah + 1)" type="button"
                            class="w-12 h-12 rounded-xl bg-gray-100 font-bold text-gray-700 hover:bg-gray-200 transition flex items-center justify-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <form id="cartForm" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" id="jumlahInput" name="jumlah">
                    </form>

                    <div class="flex gap-3">
                        <button type="button" @click="closeModal()"
                            class="flex-1 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="button" @click="submitCart()"
                            class="flex-1 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Tambahkan
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script>
        function cartModal() {
            return {
                showModal: false,
                selectedItem: { id: null, nama: '', stok: 0 },
                jumlah: 1,
                search: '',
                filterKategori: '',
                filterStok: '',

                openModal(id, nama, stok) {
                    this.selectedItem = { id, nama, stok };
                    this.jumlah = 1;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
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