<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Alatika</title>
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
                        <h1 class="text-xl font-bold text-gray-900">Data Mahasiswa</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Daftar mahasiswa yang terdaftar di sistem peminjaman</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="px-4 py-2.5 bg-emerald-50 text-emerald-700 text-sm font-semibold rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2 border border-emerald-200">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
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
                            <i class="fas fa-user-graduate text-blue-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">256</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Mahasiswa</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-check text-emerald-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">18</h3>
                    <p class="text-sm text-slate-500 mt-1">Sedang Meminjam</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-history text-indigo-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Riwayat</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">142</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Semua Peminjaman</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-plus text-sky-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-full">Baru</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">12</h3>
                    <p class="text-sm text-slate-500 mt-1">Registrasi Bulan Ini</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama, NIM, atau email mahasiswa..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Prodi</option>
                        <option value="te">Teknik Elektronika</option>
                        <option value="ti">Teknik Informatika</option>
                        <option value="tkj">Teknik Komputer Jaringan</option>
                        <option value="tl">Teknik Listrik</option>
                    </select>
                    <button class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">#</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Mahasiswa</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">NIM</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Email</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Prodi</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Peminjaman</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $mahasiswa = [
                                    ['nama' => 'Ahmad Rizki Saputra', 'nim' => '23010001', 'email' => 'ahmad.rizki@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 5, 'aktif' => 1, 'status' => 'aktif'],
                                    ['nama' => 'Siti Nurhaliza', 'nim' => '23010002', 'email' => 'siti.nur@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 3, 'aktif' => 1, 'status' => 'aktif'],
                                    ['nama' => 'Budi Santoso', 'nim' => '23010003', 'email' => 'budi.santoso@student.polines.ac.id', 'prodi' => 'Teknik Informatika', 'total_pinjam' => 8, 'aktif' => 0, 'status' => 'idle'],
                                    ['nama' => 'Dewi Lestari', 'nim' => '23010004', 'email' => 'dewi.lestari@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 2, 'aktif' => 1, 'status' => 'aktif'],
                                    ['nama' => 'Eko Prasetyo', 'nim' => '23010005', 'email' => 'eko.pras@student.polines.ac.id', 'prodi' => 'Teknik Komputer Jaringan', 'total_pinjam' => 12, 'aktif' => 0, 'status' => 'idle'],
                                    ['nama' => 'Fitria Rahmawati', 'nim' => '23010006', 'email' => 'fitria.rahma@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 1, 'aktif' => 1, 'status' => 'aktif'],
                                    ['nama' => 'Galih Purnama', 'nim' => '23010007', 'email' => 'galih.purnama@student.polines.ac.id', 'prodi' => 'Teknik Listrik', 'total_pinjam' => 6, 'aktif' => 0, 'status' => 'idle'],
                                    ['nama' => 'Hana Maharani', 'nim' => '23010008', 'email' => 'hana.maharani@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 4, 'aktif' => 2, 'status' => 'aktif'],
                                    ['nama' => 'Irfan Hakim', 'nim' => '23010011', 'email' => 'irfan.hakim@student.polines.ac.id', 'prodi' => 'Teknik Informatika', 'total_pinjam' => 7, 'aktif' => 0, 'status' => 'terlambat'],
                                    ['nama' => 'Jihan Aulia', 'nim' => '23010012', 'email' => 'jihan.aulia@student.polines.ac.id', 'prodi' => 'Teknik Elektronika', 'total_pinjam' => 0, 'aktif' => 0, 'status' => 'idle'],
                                ];
                            @endphp

                            @foreach($mahasiswa as $index => $m)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($m['nama'], 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-800">{{ $m['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs bg-sky-50 text-sky-700 px-2.5 py-1 rounded-md font-mono font-medium">{{ $m['nim'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $m['email'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full">{{ $m['prodi'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-bold text-slate-800">{{ $m['total_pinjam'] }}</span>
                                        @if($m['aktif'] > 0)
                                            <span class="text-[10px] text-blue-600 font-medium">{{ $m['aktif'] }} aktif</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($m['status'] === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif Meminjam
                                        </span>
                                    @elseif($m['status'] === 'terlambat')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Terlambat
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">
                                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
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
                <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-medium">1-10</span> dari <span class="font-medium">256</span> mahasiswa</p>
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
