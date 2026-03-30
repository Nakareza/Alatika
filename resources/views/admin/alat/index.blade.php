<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Alat - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        [x-cloak] { display: none !important; }
        .modal-backdrop { background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, viewMode: 'table' }">

    <x-sidebar-admin />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Data Alat</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Inventaris alat dan komponen laboratorium elektronika</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center bg-slate-100 rounded-lg p-1">
                            <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-3 py-1.5 rounded-md text-sm font-medium transition-all">
                                <i class="fas fa-list"></i>
                            </button>
                            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-500'" class="px-3 py-1.5 rounded-md text-sm font-medium transition-all">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                        <button class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Alat
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen space-y-6">

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">87</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Alat</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Tersedia</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">63</h3>
                    <p class="text-sm text-slate-500 mt-1">Alat Tersedia</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hand-holding text-indigo-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Dipinjam</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">18</h3>
                    <p class="text-sm text-slate-500 mt-1">Sedang Dipinjam</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tools text-amber-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Perbaikan</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">6</h3>
                    <p class="text-sm text-slate-500 mt-1">Dalam Perbaikan</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama alat atau kode..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Kategori</option>
                        <option value="microcontroller">Microcontroller</option>
                        <option value="sensor">Sensor & Aktuator</option>
                        <option value="tools">Lab Equipment</option>
                        <option value="komponen">Komponen Elektronik</option>
                    </select>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                    <button class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>

            @php
                $alat = [
                    ['nama' => 'Arduino Uno R3', 'kode' => 'ARD-001', 'kategori' => 'Microcontroller', 'stok_total' => 15, 'tersedia' => 12, 'dipinjam' => 3, 'status' => 'tersedia', 'lokasi' => 'Rak A1'],
                    ['nama' => 'ESP32 DevKit V1', 'kode' => 'ESP-015', 'kategori' => 'Microcontroller', 'stok_total' => 20, 'tersedia' => 14, 'dipinjam' => 6, 'status' => 'tersedia', 'lokasi' => 'Rak A2'],
                    ['nama' => 'Raspberry Pi 4 Model B', 'kode' => 'RPI-008', 'kategori' => 'Microcontroller', 'stok_total' => 8, 'tersedia' => 5, 'dipinjam' => 3, 'status' => 'tersedia', 'lokasi' => 'Rak A3'],
                    ['nama' => 'Oscilloscope Digital', 'kode' => 'OSC-005', 'kategori' => 'Lab Equipment', 'stok_total' => 5, 'tersedia' => 3, 'dipinjam' => 2, 'status' => 'tersedia', 'lokasi' => 'Rak B1'],
                    ['nama' => 'Multimeter Digital', 'kode' => 'MUL-012', 'kategori' => 'Lab Equipment', 'stok_total' => 10, 'tersedia' => 8, 'dipinjam' => 2, 'status' => 'tersedia', 'lokasi' => 'Rak B2'],
                    ['nama' => 'Soldering Station', 'kode' => 'SOL-003', 'kategori' => 'Lab Equipment', 'stok_total' => 6, 'tersedia' => 4, 'dipinjam' => 1, 'status' => 'tersedia', 'lokasi' => 'Rak B3'],
                    ['nama' => 'Sensor DHT22', 'kode' => 'SNS-022', 'kategori' => 'Sensor & Aktuator', 'stok_total' => 30, 'tersedia' => 25, 'dipinjam' => 5, 'status' => 'tersedia', 'lokasi' => 'Rak C1'],
                    ['nama' => 'Sensor Ultrasonik HC-SR04', 'kode' => 'SNS-004', 'kategori' => 'Sensor & Aktuator', 'stok_total' => 25, 'tersedia' => 20, 'dipinjam' => 5, 'status' => 'tersedia', 'lokasi' => 'Rak C1'],
                    ['nama' => 'Power Supply Digital', 'kode' => 'PWR-007', 'kategori' => 'Lab Equipment', 'stok_total' => 4, 'tersedia' => 0, 'dipinjam' => 2, 'status' => 'maintenance', 'lokasi' => 'Rak B4'],
                    ['nama' => 'Logic Analyzer 8ch', 'kode' => 'LGA-003', 'kategori' => 'Lab Equipment', 'stok_total' => 3, 'tersedia' => 0, 'dipinjam' => 3, 'status' => 'dipinjam', 'lokasi' => 'Rak B5'],
                ];
                $kategoriIcons = [
                    'Microcontroller' => 'fa-microchip',
                    'Lab Equipment' => 'fa-flask',
                    'Sensor & Aktuator' => 'fa-satellite-dish',
                    'Komponen Elektronik' => 'fa-puzzle-piece',
                ];
                $statusConfig = [
                    'tersedia' => ['label' => 'Tersedia', 'color' => 'bg-emerald-100 text-emerald-700'],
                    'dipinjam' => ['label' => 'Semua Dipinjam', 'color' => 'bg-indigo-100 text-indigo-700'],
                    'maintenance' => ['label' => 'Maintenance', 'color' => 'bg-amber-100 text-amber-700'],
                ];
            @endphp

            {{-- Table View --}}
            <div x-show="viewMode === 'table'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">#</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Nama Alat</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Kategori</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Lokasi</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Stok</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($alat as $index => $a)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                                            <i class="fas {{ $kategoriIcons[$a['kategori']] ?? 'fa-cube' }} text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-slate-800 block">{{ $a['nama'] }}</span>
                                            <span class="text-xs text-slate-400 font-mono">{{ $a['kode'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">{{ $a['kategori'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $a['lokasi'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-bold text-slate-800">{{ $a['tersedia'] }}/{{ $a['stok_total'] }}</span>
                                        <div class="w-16 h-1.5 bg-slate-200 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full rounded-full {{ $a['tersedia'] > 0 ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ ($a['tersedia'] / max($a['stok_total'], 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php $sc = $statusConfig[$a['status']]; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['color'] }}">
                                        {{ $sc['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-medium">1-10</span> dari <span class="font-medium">87</span> alat</p>
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">Prev</button>
                        <button class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg">1</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">2</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">Next</button>
                    </div>
                </div>
            </div>

            {{-- Grid View --}}
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($alat as $a)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden group">
                    <div class="h-32 bg-gradient-to-br from-slate-50 to-blue-50 flex items-center justify-center border-b border-slate-100">
                        <i class="fas {{ $kategoriIcons[$a['kategori']] ?? 'fa-cube' }} text-4xl text-blue-300 group-hover:text-blue-500 transition-colors group-hover:scale-110 transform duration-300"></i>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">{{ $a['nama'] }}</h3>
                                <p class="text-xs text-slate-400 font-mono">{{ $a['kode'] }}</p>
                            </div>
                            @php $sc = $statusConfig[$a['status']]; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $sc['color'] }}">{{ $sc['label'] }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div>
                                <span class="text-xs text-slate-500">Tersedia</span>
                                <p class="text-lg font-bold text-slate-800">{{ $a['tersedia'] }}<span class="text-sm text-slate-400 font-normal">/{{ $a['stok_total'] }}</span></p>
                            </div>
                            <div class="flex gap-1">
                                <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i class="fas fa-edit text-xs"></i></button>
                                <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"><i class="fas fa-trash text-xs"></i></button>
                            </div>
                        </div>
                        <div class="w-full h-1.5 bg-slate-200 rounded-full mt-3 overflow-hidden">
                            <div class="h-full rounded-full {{ $a['tersedia'] > 0 ? 'bg-emerald-500' : 'bg-red-500' }}" style="width: {{ ($a['tersedia'] / max($a['stok_total'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </main>
    </div>

    {{-- Logout Modal --}}
    <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showLogoutModal = false">
        <div x-show="showLogoutModal" x-transition class="fixed inset-0 modal-backdrop" @click="showLogoutModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showLogoutModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex justify-center mb-4"><div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-sign-out-alt text-red-600 text-2xl"></i></div></div>
                <div class="text-center mb-6"><h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3><p class="text-sm text-gray-600">Apakah Anda yakin ingin keluar?</p></div>
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">@csrf<button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl transition-all">Ya, Logout</button></form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
