<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengembalian - Alatika</title>
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
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, showVerifyModal: false, verifyData: {} }">

    <x-sidebar-admin />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Kelola Pengembalian</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Verifikasi dan monitoring pengembalian alat laboratorium</p>
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
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-amber-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Menunggu</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">12</h3>
                    <p class="text-sm text-slate-500 mt-1">Menunggu Dikembalikan</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-red-600 bg-red-50 px-2.5 py-1 rounded-full">Terlambat</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">3</h3>
                    <p class="text-sm text-slate-500 mt-1">Terlambat Dikembalikan</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Hari Ini</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">5</h3>
                    <p class="text-sm text-slate-500 mt-1">Dikembalikan Hari Ini</p>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-undo-alt text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">105</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Pengembalian</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama mahasiswa atau kode alat..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu Kembali</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="dikembalikan">Sudah Dikembalikan</option>
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
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Alat Dipinjam</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Tgl Pinjam</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Deadline</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Kondisi</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $pengembalian = [
                                    ['nama' => 'Siti Nurhaliza', 'nim' => '23010002', 'alat' => 'Oscilloscope Digital', 'kode' => 'OSC-005', 'tgl_pinjam' => '2025-12-07', 'deadline' => '2025-12-10', 'kondisi' => 'Baik', 'status' => 'menunggu'],
                                    ['nama' => 'Budi Santoso', 'nim' => '23010003', 'alat' => 'Multimeter Digital', 'kode' => 'MUL-012', 'tgl_pinjam' => '2025-12-06', 'deadline' => '2025-12-13', 'kondisi' => 'Baik', 'status' => 'menunggu'],
                                    ['nama' => 'Rini Puspitasari', 'nim' => '23010009', 'alat' => 'Logic Analyzer', 'kode' => 'LGA-003', 'tgl_pinjam' => '2025-11-28', 'deadline' => '2025-12-05', 'kondisi' => '-', 'status' => 'terlambat'],
                                    ['nama' => 'Eko Prasetyo', 'nim' => '23010005', 'alat' => 'Soldering Station', 'kode' => 'SOL-003', 'tgl_pinjam' => '2025-12-04', 'deadline' => '2025-12-11', 'kondisi' => 'Baik', 'status' => 'dikembalikan'],
                                    ['nama' => 'Doni Firmansyah', 'nim' => '23010010', 'alat' => 'Function Generator', 'kode' => 'FNG-002', 'tgl_pinjam' => '2025-11-25', 'deadline' => '2025-12-02', 'kondisi' => '-', 'status' => 'terlambat'],
                                    ['nama' => 'Galih Purnama', 'nim' => '23010007', 'alat' => 'Breadboard Set', 'kode' => 'BRD-010', 'tgl_pinjam' => '2025-12-09', 'deadline' => '2025-12-16', 'kondisi' => 'Baik', 'status' => 'dikembalikan'],
                                    ['nama' => 'Hana Maharani', 'nim' => '23010008', 'alat' => 'Power Supply Digital', 'kode' => 'PWR-007', 'tgl_pinjam' => '2025-12-10', 'deadline' => '2025-12-17', 'kondisi' => 'Baik', 'status' => 'menunggu'],
                                    ['nama' => 'Irfan Hakim', 'nim' => '23010011', 'alat' => 'Raspberry Pi 4 + Case', 'kode' => 'RPI-004', 'tgl_pinjam' => '2025-11-30', 'deadline' => '2025-12-07', 'kondisi' => '-', 'status' => 'terlambat'],
                                ];
                                $statusConfig = [
                                    'menunggu' => ['label' => 'Menunggu Kembali', 'color' => 'bg-amber-100 text-amber-700'],
                                    'terlambat' => ['label' => 'Terlambat', 'color' => 'bg-red-100 text-red-700'],
                                    'dikembalikan' => ['label' => 'Dikembalikan', 'color' => 'bg-emerald-100 text-emerald-700'],
                                ];
                            @endphp

                            @foreach($pengembalian as $index => $p)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
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
                                    <span class="text-xs text-slate-400 font-mono">{{ $p['kode'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ \Carbon\Carbon::parse($p['tgl_pinjam'])->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-sm {{ $p['status'] === 'terlambat' ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                        {{ \Carbon\Carbon::parse($p['deadline'])->format('d M Y') }}
                                    </span>
                                    @if($p['status'] === 'terlambat')
                                        <span class="text-xs text-red-500 block">⚠ Lewat batas</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($p['kondisi'] === 'Baik')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                            <i class="fas fa-check-circle text-[10px]"></i> Baik
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">Belum dicek</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php $sc = $statusConfig[$p['status']]; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc['color'] }}">
                                        {{ $sc['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($p['status'] !== 'dikembalikan')
                                        <button class="px-3 py-1.5 text-xs font-semibold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-sm" title="Verifikasi">
                                            <i class="fas fa-check mr-1"></i> Verifikasi
                                        </button>
                                        @else
                                        <span class="text-xs text-emerald-600 font-medium"><i class="fas fa-check-double mr-1"></i> Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-medium">1-8</span> dari <span class="font-medium">20</span> data</p>
                    <div class="flex items-center gap-2">
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">Prev</button>
                        <button class="px-3 py-1.5 text-sm bg-blue-600 text-white rounded-lg">1</button>
                        <button class="px-3 py-1.5 text-sm border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-500">2</button>
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
