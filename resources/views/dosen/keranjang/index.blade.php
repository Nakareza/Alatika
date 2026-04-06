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
    <x-sidebar-dosen />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-8 py-4 flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Keranjang Peminjaman</h1>
                    <p class="text-sm text-gray-500">Review alat yang akan Anda pinjam, lalu ajukan sekaligus</p>
                </div>
                <a href="{{ route('dosen.alat') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                    <i class="fas fa-arrow-left"></i> Tambah Alat Lain
                </a>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 mb-6">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-box-open text-amber-500"></i> Item Peminjaman
                            </h2>
                            <span class="text-sm text-gray-500 font-medium">{{ count($keranjang) }} alat dipilih</span>
                        </div>

                        @forelse($keranjang as $item)
                        <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4 hover:bg-amber-50/30 transition">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-16 h-16 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl border border-amber-100 flex items-center justify-center text-amber-500 flex-shrink-0">
                                    <i class="fas fa-microchip text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $item->alat->nama }}</h3>
                                    <p class="text-sm text-gray-500 mt-0.5">
                                        <span class="inline-block bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-medium mr-2">{{ $item->alat->kode }}</span>
                                        Stok tersisa: <span class="font-semibold text-emerald-600">{{ $item->alat->stok_tersedia }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Jumlah</p>
                                    <span class="text-xl font-black text-gray-800">{{ $item->jumlah }}</span>
                                    <span class="text-xs text-gray-500 ml-1">unit</span>
                                </div>

                                <form action="{{ route('dosen.keranjang.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-10 h-10 rounded-full bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition"
                                        title="Hapus dari keranjang">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="p-16 text-center">
                            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shopping-cart text-4xl text-gray-200"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600 mb-2">Keranjang Kosong</h3>
                            <p class="text-gray-400 mb-6 text-sm">Anda belum memilih alat apapun untuk dipinjam.</p>
                            <a href="{{ route('dosen.alat') }}"
                                class="inline-flex items-center gap-2 bg-amber-500 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-amber-600 shadow-sm transition">
                                <i class="fas fa-search"></i> Lihat Katalog Alat
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Checkout Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
                            <i class="fas fa-clipboard-check text-emerald-500"></i> Detail Pengajuan
                        </h2>
                        <p class="text-xs text-gray-400 mb-6">Semua alat akan diajukan dalam satu kode peminjaman</p>

                        <div class="flex justify-between items-center p-4 bg-amber-50 rounded-xl mb-6 border border-amber-100">
                            <span class="text-gray-600 font-medium text-sm">Total Jenis Alat</span>
                            <span class="text-2xl font-black text-amber-600">{{ count($keranjang) }}</span>
                        </div>

                        <form action="{{ route('dosen.keranjang.checkout') }}" method="POST">
                            @csrf

                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Keperluan / Tujuan Peminjaman <span class="text-red-500">*</span>
                                </label>
                                <textarea name="keperluan" rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-sm bg-gray-50 outline-none resize-none"
                                    placeholder="Misal: Penelitian Sensor IoT, Praktikum Pengukuran..." required></textarea>
                            </div>

                            <div class="mb-7">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Rencana Tanggal Kembali <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_kembali"
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition text-sm bg-gray-50 outline-none" required>
                                <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i> Maksimal 30 hari dari hari ini
                                </p>
                            </div>

                            <button type="submit"
                                {{ count($keranjang) == 0 ? 'disabled' : '' }}
                                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3.5 rounded-xl font-bold shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
