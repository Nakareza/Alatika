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
        .card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <x-sidebar-mahasiswa />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-8 py-4 flex justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Katalog Alat</h1>
                    <p class="text-sm text-gray-500">Daftar alat-alat elektronik yang tersedia</p>
                </div>
                <div>
                    <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus"></i> Ajukan Pinjam
                    </a>
                </div>
            </div>
        </header>

        <main class="p-8 min-h-screen">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($alat as $item)
                <div class="card overflow-hidden">
                    <div class="h-40 bg-gray-100 flex items-center justify-center border-b border-gray-100">
                        <i class="fas fa-microchip text-4xl text-gray-300"></i>
                    </div>
                    <div class="p-5">
                        <div class="text-xs font-semibold text-blue-600 tracking-wider uppercase mb-1">{{ $item->kategori }}</div>
                        <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2">{{ $item->nama }}</h3>
                        
                        <div class="flex items-center justify-between mt-4">
                            <div class="text-sm">
                                <span class="text-gray-500 block">Kode</span>
                                <span class="font-medium text-gray-900">{{ $item->kode }}</span>
                            </div>
                            <div class="text-sm text-right">
                                <span class="text-gray-500 block">Tersedia</span>
                                <span class="font-bold {{ $item->stok_tersedia > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->stok_tersedia }} / {{ $item->stok_total }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    Belum ada data alat yang tersedia di database.
                </div>
                @endforelse
            </div>
        </main>
    </div>
</body>
</html>
