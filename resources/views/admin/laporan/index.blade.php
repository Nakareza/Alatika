<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Alatika</title>
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
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false }">

    <x-sidebar-admin />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Laporan & Statistik</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Ringkasan aktivitas peminjaman dan inventaris laboratorium</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="px-4 py-2.5 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2 border border-emerald-200">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>
                        <button class="px-4 py-2.5 bg-blue-50 text-blue-700 text-sm font-semibold rounded-xl hover:bg-blue-100 transition-all flex items-center gap-2 border border-blue-200">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen space-y-6">

            {{-- Overview Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase">Total Peminjaman</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">142</h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1"><i class="fas fa-arrow-up mr-1"></i>+12% dari bulan lalu</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-double text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase">Rate Pengembalian</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">94%</h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1"><i class="fas fa-arrow-up mr-1"></i>Tepat waktu</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase">Peminjam Aktif</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">18</h3>
                    <p class="text-xs text-blue-600 font-medium mt-1"><i class="fas fa-user mr-1"></i>Mahasiswa</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase">Utilisasi Alat</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">72%</h3>
                    <p class="text-xs text-amber-600 font-medium mt-1"><i class="fas fa-chart-line mr-1"></i>Rata-rata</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 uppercase">Terlambat</span>
                    </div>
                    <h3 class="text-3xl font-bold text-slate-800">3</h3>
                    <p class="text-xs text-red-600 font-medium mt-1"><i class="fas fa-arrow-down mr-1"></i>-2 dari bulan lalu</p>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Monthly Borrowing Chart (CSS-based bar chart) --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-blue-500"></i> Peminjaman Per Bulan
                        </h3>
                        <span class="text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg">Tahun 2025</span>
                    </div>
                    @php
                        $months = [
                            ['bulan' => 'Jul', 'jumlah' => 8],
                            ['bulan' => 'Agu', 'jumlah' => 12],
                            ['bulan' => 'Sep', 'jumlah' => 22],
                            ['bulan' => 'Okt', 'jumlah' => 28],
                            ['bulan' => 'Nov', 'jumlah' => 35],
                            ['bulan' => 'Des', 'jumlah' => 37],
                        ];
                        $maxVal = max(array_column($months, 'jumlah'));
                    @endphp
                    <div class="flex items-end gap-4 h-48">
                        @foreach($months as $m)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <span class="text-xs font-bold text-slate-700">{{ $m['jumlah'] }}</span>
                            <div class="w-full bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg transition-all duration-500 hover:from-blue-600 hover:to-blue-500"
                                 style="height: {{ ($m['jumlah'] / $maxVal) * 100 }}%"></div>
                            <span class="text-xs text-slate-500 font-medium">{{ $m['bulan'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Category Distribution --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-chart-pie text-indigo-500"></i> Distribusi Kategori Alat
                        </h3>
                    </div>
                    @php
                        $categories = [
                            ['nama' => 'Microcontroller', 'jumlah' => 43, 'warna' => 'from-blue-500 to-blue-600', 'bg' => 'bg-blue-500', 'persen' => 49],
                            ['nama' => 'Sensor & Aktuator', 'jumlah' => 55, 'warna' => 'from-emerald-500 to-emerald-600', 'bg' => 'bg-emerald-500', 'persen' => 63],
                            ['nama' => 'Lab Equipment', 'jumlah' => 28, 'warna' => 'from-amber-500 to-amber-600', 'bg' => 'bg-amber-500', 'persen' => 32],
                            ['nama' => 'Komponen Elektronik', 'jumlah' => 150, 'warna' => 'from-indigo-500 to-indigo-600', 'bg' => 'bg-indigo-500', 'persen' => 85],
                        ];
                    @endphp
                    <div class="space-y-5">
                        @foreach($categories as $c)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-slate-700">{{ $c['nama'] }}</span>
                                <span class="text-sm font-bold text-slate-800">{{ $c['jumlah'] }} unit</span>
                            </div>
                            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r {{ $c['warna'] }} transition-all duration-500" style="width: {{ $c['persen'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Top Borrowed Items + Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Top Borrowed Items --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-fire text-orange-500"></i> Alat Terpopuler
                    </h3>
                    @php
                        $topItems = [
                            ['nama' => 'Arduino Uno R3', 'kode' => 'ARD-001', 'total' => 42, 'icon' => 'fa-microchip'],
                            ['nama' => 'ESP32 DevKit V1', 'kode' => 'ESP-015', 'total' => 38, 'icon' => 'fa-microchip'],
                            ['nama' => 'Sensor DHT22', 'kode' => 'SNS-022', 'total' => 31, 'icon' => 'fa-satellite-dish'],
                            ['nama' => 'Multimeter Digital', 'kode' => 'MUL-012', 'total' => 28, 'icon' => 'fa-flask'],
                            ['nama' => 'Breadboard Set', 'kode' => 'BRD-010', 'total' => 25, 'icon' => 'fa-puzzle-piece'],
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($topItems as $index => $item)
                        <div class="flex items-center gap-4 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $index === 0 ? 'bg-gradient-to-br from-amber-400 to-amber-500 text-white' :
                                   ($index === 1 ? 'bg-gradient-to-br from-slate-300 to-slate-400 text-white' :
                                   ($index === 2 ? 'bg-gradient-to-br from-amber-600 to-amber-700 text-white' :
                                   'bg-slate-200 text-slate-600')) }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-blue-500 flex-shrink-0">
                                <i class="fas {{ $item['icon'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $item['nama'] }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $item['kode'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-800">{{ $item['total'] }}x</p>
                                <p class="text-[10px] text-slate-400">dipinjam</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Monthly Summary Table --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-table text-blue-500"></i> Ringkasan Bulanan
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left text-xs font-semibold text-slate-500 uppercase py-3 px-2">Bulan</th>
                                    <th class="text-center text-xs font-semibold text-slate-500 uppercase py-3 px-2">Pengajuan</th>
                                    <th class="text-center text-xs font-semibold text-slate-500 uppercase py-3 px-2">Disetujui</th>
                                    <th class="text-center text-xs font-semibold text-slate-500 uppercase py-3 px-2">Ditolak</th>
                                    <th class="text-center text-xs font-semibold text-slate-500 uppercase py-3 px-2">Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php
                                    $ringkasan = [
                                        ['bulan' => 'Desember 2025', 'pengajuan' => 37, 'disetujui' => 32, 'ditolak' => 3, 'selesai' => 28],
                                        ['bulan' => 'November 2025', 'pengajuan' => 35, 'disetujui' => 30, 'ditolak' => 2, 'selesai' => 33],
                                        ['bulan' => 'Oktober 2025', 'pengajuan' => 28, 'disetujui' => 25, 'ditolak' => 1, 'selesai' => 24],
                                        ['bulan' => 'September 2025', 'pengajuan' => 22, 'disetujui' => 20, 'ditolak' => 1, 'selesai' => 19],
                                        ['bulan' => 'Agustus 2025', 'pengajuan' => 12, 'disetujui' => 10, 'ditolak' => 0, 'selesai' => 10],
                                        ['bulan' => 'Juli 2025', 'pengajuan' => 8, 'disetujui' => 7, 'ditolak' => 0, 'selesai' => 7],
                                    ];
                                @endphp
                                @foreach($ringkasan as $r)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-2 text-sm font-medium text-slate-700">{{ $r['bulan'] }}</td>
                                    <td class="py-3 px-2 text-center">
                                        <span class="text-sm font-bold text-slate-800">{{ $r['pengajuan'] }}</span>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-full">{{ $r['disetujui'] }}</span>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <span class="text-xs font-semibold text-red-700 bg-red-50 px-2 py-1 rounded-full">{{ $r['ditolak'] }}</span>
                                    </td>
                                    <td class="py-3 px-2 text-center">
                                        <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-1 rounded-full">{{ $r['selesai'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bottom Info --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm border border-blue-100">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800">Catatan</h3>
                        <p class="text-sm text-blue-700 mt-1">Data laporan diperbarui secara otomatis setiap hari. Gunakan tombol Export untuk mengunduh laporan lengkap dalam format PDF atau Excel.</p>
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
