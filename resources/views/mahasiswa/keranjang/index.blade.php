<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Peminjaman - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; background: #f8fafc; }</style>
</head>
<body class="bg-gray-50 antialiased">
    <x-sidebar-mahasiswa />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        <div class="container mx-auto p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Keranjang Peminjaman</h1>
                    <p class="text-gray-500 mt-1">Daftar alat yang akan Anda pinjam secara bersamaan.</p>
                </div>
                <a href="{{ route('mahasiswa.alat') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Tambah Alat Lain
                </a>
            </div>

            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-box-open mr-2 text-blue-500"></i> Item Peminjaman</h2>
                        </div>
                        
                        @forelse($keranjang as $item)
                        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-16 h-16 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                                    <i class="fas fa-microchip text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $item->alat->nama }}</h3>
                                    <p class="text-sm text-gray-500">Kode: {{ $item->alat->kode ?? $item->alat->kode_alat }} &middot; Stok Tersedia: {{ $item->alat->stok_tersedia }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs text-gray-500 font-semibold uppercase mb-1">Jumlah</span>
                                    <span class="font-bold text-lg text-gray-800">{{ $item->jumlah }}</span>
                                </div>
                                
                                <form action="{{ route('mahasiswa.keranjang.remove', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-10 h-10 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition tooltip" title="Hapus dari Keranjang">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-cart text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-700 mb-2">Keranjang Kosong</h3>
                            <p class="text-gray-500 mb-6">Anda belum menambahkan alat apapun ke keranjang peminjaman.</p>
                            <a href="{{ route('mahasiswa.alat') }}" class="inline-block bg-blue-600 text-white px-6 py-2.5 rounded-xl font-medium hover:bg-blue-700 shadow-sm transition">
                                Lihat Katalog Alat
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Checkout Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-800 mb-6"><i class="fas fa-clipboard-check mr-2 text-emerald-500"></i> Detail Pengajuan</h2>
                        
                        <div class="flex justify-between items-center mb-6 pb-6 border-b border-gray-100">
                            <span class="text-gray-500 font-medium">Total Item</span>
                            <span class="text-xl font-black text-gray-900">{{ count($keranjang) }} Alat</span>
                        </div>

                        <form action="{{ route('mahasiswa.keranjang.checkout') }}" method="POST">
                            @csrf
                            
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Keperluan Peminjaman <span class="text-red-500">*</span></label>
                                <textarea name="keperluan" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50" placeholder="Misal: Praktikum Mikrokontroler, Proyek Akhir..." required></textarea>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tanggal Kembali <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_kembali" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm bg-gray-50" required>
                                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Peminjaman maksimal 14 hari dari hari ini.</p>
                            </div>
                            
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3.5 rounded-xl font-bold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-center flex items-center justify-center gap-2" {{ count($keranjang) == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane"></i> Ajukan Semua Peminjaman
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
