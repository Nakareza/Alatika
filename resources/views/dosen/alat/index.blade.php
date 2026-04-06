<!DOCTYPE html>
<html lang="id">
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
        .badge-cart { animation: pop 0.3s ease; }
        @keyframes pop { 0%,100%{transform:scale(1)} 50%{transform:scale(1.35)} }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <x-sidebar-dosen />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-8 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Katalog Alat</h1>
                    <p class="text-sm text-gray-500">Pilih alat, tambah ke keranjang, lalu ajukan sekaligus</p>
                </div>
                <div class="flex items-center gap-3">
                    @php $cartCount = \App\Models\Keranjang::where('user_id', auth()->id())->count(); @endphp
                    <a href="{{ route('dosen.keranjang') }}" class="relative inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white font-medium rounded-xl hover:bg-amber-600 transition shadow-sm">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                        @if($cartCount > 0)
                        <span class="badge-cart absolute -top-2 -right-2 bg-red-500 text-white text-xs font-black w-5 h-5 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('dosen.peminjaman.ajukan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white font-medium rounded-xl hover:bg-orange-700 transition shadow-sm">
                        <i class="fas fa-plus"></i> Pinjam Langsung
                    </a>
                </div>
            </div>
        </header>

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

        <main class="p-8 min-h-screen">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($alat as $item)
                <div class="card overflow-hidden" x-data="{ open: false, jumlah: 1 }">
                    <div class="h-36 flex items-center justify-center border-b border-gray-100
                        {{ $item->stok_tersedia > 0 ? 'bg-gradient-to-br from-amber-50 to-orange-50' : 'bg-gray-100' }}">
                        <i class="fas fa-microchip text-4xl {{ $item->stok_tersedia > 0 ? 'text-amber-300' : 'text-gray-300' }}"></i>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold text-amber-600 tracking-wider uppercase">{{ $item->kategori }}</span>
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
                            <button @click="open = true"
                                class="w-full py-2.5 px-4 rounded-xl bg-amber-500 text-white font-semibold text-sm hover:bg-amber-600 active:scale-95 transition-all flex items-center justify-center gap-2 shadow-sm">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>

                            <!-- Quantity Modal -->
                            <div x-show="open" x-transition class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center" @click.self="open=false">
                                <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 mx-4" @click.stop>
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                                            <i class="fas fa-microchip text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $item->nama }}</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">Stok: <span class="font-semibold text-emerald-600">{{ $item->stok_tersedia }} unit</span></p>
                                        </div>
                                    </div>

                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah yang dipinjam</label>
                                    <div class="flex items-center gap-3 mb-6">
                                        <button @click="jumlah = Math.max(1, jumlah-1)" type="button"
                                            class="w-10 h-10 rounded-xl bg-gray-100 font-bold text-gray-700 hover:bg-gray-200 transition flex items-center justify-center">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" x-model="jumlah" min="1" max="{{ $item->stok_tersedia }}"
                                            class="flex-1 text-center border border-gray-200 rounded-xl py-2 font-bold text-lg focus:ring-2 focus:ring-amber-400 outline-none">
                                        <button @click="jumlah = Math.min({{ $item->stok_tersedia }}, jumlah+1)" type="button"
                                            class="w-10 h-10 rounded-xl bg-gray-100 font-bold text-gray-700 hover:bg-gray-200 transition flex items-center justify-center">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('dosen.keranjang.add', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="jumlah" :value="jumlah">
                                        <div class="flex gap-3">
                                            <button type="button" @click="open=false"
                                                class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="flex-1 py-2.5 rounded-xl bg-amber-500 text-white font-semibold text-sm hover:bg-amber-600 transition shadow-sm">
                                                <i class="fas fa-check mr-1"></i> Tambahkan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="w-full py-2.5 px-4 rounded-xl bg-gray-100 text-gray-400 font-semibold text-sm flex items-center justify-center gap-2 cursor-not-allowed">
                                <i class="fas fa-ban"></i> Stok Habis
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center text-gray-500">
                    <i class="fas fa-box-open text-5xl text-gray-200 mb-4 block"></i>
                    Belum ada data alat di database.
                </div>
                @endforelse
            </div>
        </main>
    </div>
</body>
</html>
