<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen - Alatika</title>
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
                        <h1 class="text-xl font-bold text-gray-900">Data Dosen</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Daftar dosen yang terdaftar sebagai pembimbing dan penanggung jawab</p>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-amber-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chalkboard-teacher text-amber-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">24</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Dosen</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-check text-emerald-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Aktif</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">8</h3>
                    <p class="text-sm text-slate-500 mt-1">Membimbing Mhs Aktif</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-indigo-600 text-lg"></i>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">Persetujuan</span>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">47</h3>
                    <p class="text-sm text-slate-500 mt-1">Total Persetujuan Diberikan</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="Cari nama, NIP, atau email dosen..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Jabatan</option>
                        <option value="lektor">Lektor</option>
                        <option value="asisten_ahli">Asisten Ahli</option>
                        <option value="lektor_kepala">Lektor Kepala</option>
                        <option value="profesor">Profesor</option>
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
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Dosen</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">NIP</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Email</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Jabatan</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Mhs Bimbingan</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Status</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $dosen = [
                                    ['nama' => 'Dr. Ir. Suryono, M.T.', 'nip' => '198501152010011001', 'email' => 'suryono@polines.ac.id', 'jabatan' => 'Lektor Kepala', 'mhs_bimbingan' => 8, 'status' => 'aktif'],
                                    ['nama' => 'Dr. Rina Setiawati, M.Sc.', 'nip' => '198703082011012002', 'email' => 'rina.setiawati@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 5, 'status' => 'aktif'],
                                    ['nama' => 'Ir. Bambang Triatmoko, M.T.', 'nip' => '197806232005011003', 'email' => 'bambang.tri@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 6, 'status' => 'aktif'],
                                    ['nama' => 'Dr. Andi Firmansyah, M.Eng.', 'nip' => '198909142013011004', 'email' => 'andi.firmansyah@polines.ac.id', 'jabatan' => 'Asisten Ahli', 'mhs_bimbingan' => 4, 'status' => 'aktif'],
                                    ['nama' => 'Dra. Sri Wahyuningsih, M.Pd.', 'nip' => '196512041990032005', 'email' => 'sri.wahyu@polines.ac.id', 'jabatan' => 'Lektor Kepala', 'mhs_bimbingan' => 3, 'status' => 'aktif'],
                                    ['nama' => 'Ir. Hadi Pranoto, M.T.', 'nip' => '197703152003011006', 'email' => 'hadi.pranoto@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 7, 'status' => 'aktif'],
                                    ['nama' => 'Dr. Novita Kusuma, M.Kom.', 'nip' => '199005272015012007', 'email' => 'novita.kusuma@polines.ac.id', 'jabatan' => 'Asisten Ahli', 'mhs_bimbingan' => 2, 'status' => 'cuti'],
                                    ['nama' => 'Ir. Agus Widodo, M.Eng.', 'nip' => '198201182008011008', 'email' => 'agus.widodo@polines.ac.id', 'jabatan' => 'Lektor', 'mhs_bimbingan' => 0, 'status' => 'aktif'],
                                ];
                                $jabatanColors = [
                                    'Profesor' => 'bg-purple-100 text-purple-700',
                                    'Lektor Kepala' => 'bg-amber-100 text-amber-700',
                                    'Lektor' => 'bg-blue-100 text-blue-700',
                                    'Asisten Ahli' => 'bg-sky-100 text-sky-700',
                                ];
                            @endphp

                            @foreach($dosen as $index => $d)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($d['nama'], 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-800">{{ $d['nama'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md font-mono font-medium">{{ $d['nip'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $d['email'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $jabatanColors[$d['jabatan']] ?? 'bg-gray-100 text-gray-600' }}">{{ $d['jabatan'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-slate-800">{{ $d['mhs_bimbingan'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($d['status'] === 'aktif')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-500">
                                            <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Cuti
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
                    <p class="text-sm text-slate-500">Menampilkan <span class="font-medium">1-8</span> dari <span class="font-medium">24</span> dosen</p>
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
