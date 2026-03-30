<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Alatika</title>
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
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, showDetailModal: false, detailData: {} }">

    <x-sidebar-admin />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Kelola Peminjaman</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manajemen persetujuan dan monitoring peminjaman alat</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all">
                            <i class="fas fa-bell text-lg"></i>
                        </button>
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen space-y-6">

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">142</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Pengajuan</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-amber-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Pending</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">8</h3>
                    <p class="text-sm text-slate-500 mt-1">Menunggu Persetujuan</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hand-holding text-emerald-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">24</h3>
                    <p class="text-sm text-slate-500 mt-1">Sedang Dipinjam</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">Ditolak</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">5</h3>
                    <p class="text-sm text-slate-500 mt-1">Ditolak</p>
                </div>
            </div>

            {{-- Filter & Search --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama mahasiswa atau nama alat..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="dipinjam">Sedang Dipinjam</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <button class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">#</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Mahasiswa</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Alat</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Tanggal Pinjam</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Deadline</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $peminjaman = [
                                    ['id' => 1, 'nama' => 'Ahmad Rizki Saputra', 'nim' => '23010001', 'alat' => 'Arduino Uno R3', 'kode' => 'ARD-001', 'tgl_pinjam' => '2025-12-08', 'deadline' => '2025-12-15', 'status' => 'pending', 'jumlah' => 2],
                                    ['id' => 2, 'nama' => 'Siti Nurhaliza', 'nim' => '23010002', 'alat' => 'Oscilloscope Digital', 'kode' => 'OSC-005', 'tgl_pinjam' => '2025-12-07', 'deadline' => '2025-12-10', 'status' => 'dipinjam', 'jumlah' => 1],
                                    ['id' => 3, 'nama' => 'Budi Santoso', 'nim' => '23010003', 'alat' => 'Multimeter Digital', 'kode' => 'MUL-012', 'tgl_pinjam' => '2025-12-06', 'deadline' => '2025-12-13', 'status' => 'disetujui', 'jumlah' => 1],
                                    ['id' => 4, 'nama' => 'Dewi Lestari', 'nim' => '23010004', 'alat' => 'Raspberry Pi 4', 'kode' => 'RPI-008', 'tgl_pinjam' => '2025-12-05', 'deadline' => '2025-12-12', 'status' => 'pending', 'jumlah' => 1],
                                    ['id' => 5, 'nama' => 'Eko Prasetyo', 'nim' => '23010005', 'alat' => 'Soldering Station', 'kode' => 'SOL-003', 'tgl_pinjam' => '2025-12-04', 'deadline' => '2025-12-11', 'status' => 'selesai', 'jumlah' => 1],
                                    ['id' => 6, 'nama' => 'Fitria Rahmawati', 'nim' => '23010006', 'alat' => 'ESP32 DevKit V1', 'kode' => 'ESP-015', 'tgl_pinjam' => '2025-12-09', 'deadline' => '2025-12-16', 'status' => 'pending', 'jumlah' => 3],
                                    ['id' => 7, 'nama' => 'Galih Purnama', 'nim' => '23010007', 'alat' => 'Sensor DHT22', 'kode' => 'SNS-022', 'tgl_pinjam' => '2025-12-03', 'deadline' => '2025-12-10', 'status' => 'ditolak', 'jumlah' => 5],
                                    ['id' => 8, 'nama' => 'Hana Maharani', 'nim' => '23010008', 'alat' => 'Power Supply Digital', 'kode' => 'PWR-007', 'tgl_pinjam' => '2025-12-10', 'deadline' => '2025-12-17', 'status' => 'pending', 'jumlah' => 1],
                                ];
                                $statusConfig = [
                                    'pending' => ['label' => 'Pending', 'color' => 'bg-amber-100 text-amber-700', 'icon' => 'fa-clock'],
                                    'disetujui' => ['label' => 'Disetujui', 'color' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-check'],
                                    'dipinjam' => ['label' => 'Dipinjam', 'color' => 'bg-indigo-100 text-indigo-700', 'icon' => 'fa-hand-holding'],
                                    'selesai' => ['label' => 'Selesai', 'color' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-check-double'],
                                    'ditolak' => ['label' => 'Ditolak', 'color' => 'bg-red-100 text-red-700', 'icon' => 'fa-times'],
                                ];
                            @endphp

                            @foreach($peminjaman as $index => $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($p['nama'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-slate-800 block">{{ $p['nama'] }}</span>
                                            <span class="text-xs text-slate-400">{{ $p['nim'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-slate-800 block">{{ $p['alat'] }}</span>
                                    <span class="text-xs text-slate-400 font-mono">{{ $p['kode'] }} · {{ $p['jumlah'] }} unit</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p['tgl_pinjam'])->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p['deadline'])->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    @php $sc = $statusConfig[$p['status']]; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['color'] }}">
                                        <i class="fas {{ $sc['icon'] }} text-[10px]"></i>
                                        {{ $sc['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($p['status'] === 'pending')
                                        <button class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Setujui">
                                            <i class="fas fa-check text-sm"></i>
                                        </button>
                                        <button class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Tolak">
                                            <i class="fas fa-times text-sm"></i>
                                        </button>
                                        @endif
                                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Info --}}
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-medium">1-8</span> dari <span class="font-medium">142</span> data</p>
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">Prev</button>
                        <button class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg">1</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">2</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">3</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">Next</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    {{-- Logout Modal --}}
    <div x-show="showLogoutModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showLogoutModal = false">
        <div x-show="showLogoutModal" x-transition class="fixed inset-0 modal-backdrop" @click="showLogoutModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showLogoutModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-sign-out-alt text-red-600 text-2xl"></i></div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Logout</h3>
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin keluar?</p>
                </div>
                <div class="flex gap-3">
                    <button @click="showLogoutModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">@csrf<button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-medium rounded-xl transition-all">Ya, Logout</button></form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
