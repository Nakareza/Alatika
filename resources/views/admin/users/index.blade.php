<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Alatika</title>
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
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, showDeleteModal: false, deleteUserId: null, deleteUserName: '', showRoleModal: false, roleUserId: null, roleUserName: '', roleValue: '' }">
    
    <x-sidebar-admin />
    
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Kelola User</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manajemen akun pengguna sistem (Teknisi, KA Lab, Dosen, Mahasiswa)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.create') }}"
                           class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            Tambah User
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto"><i class="fas fa-times"></i></button>
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NIM, atau NIP..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <select name="role" onchange="this.form.submit()"
                            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Teknisi)</option>
                        <option value="kalab" {{ request('role') === 'kalab' ? 'selected' : '' }}>Kepala Lab</option>
                        <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-900 transition-colors">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">#</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Nama</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Email</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">NIM / NIP</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Role</th>
                                <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Terdaftar</th>
                                <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($users as $index => $user)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $users->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0
                                            @if($user->role === 'admin') bg-gradient-to-br from-blue-500 to-blue-600
                                            @elseif($user->role === 'kalab') bg-gradient-to-br from-emerald-500 to-teal-600
                                            @elseif($user->role === 'dosen') bg-gradient-to-br from-amber-500 to-orange-600
                                            @else bg-gradient-to-br from-sky-400 to-blue-500
                                            @endif">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-slate-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    @if($user->nim)
                                        <span class="text-xs bg-sky-50 text-sky-700 px-2 py-1 rounded-md font-mono">{{ $user->nim }}</span>
                                    @elseif($user->nip)
                                        <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md font-mono">{{ $user->nip }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $roleColors = [
                                            'admin'     => 'bg-blue-100 text-blue-700',
                                            'kalab'     => 'bg-emerald-100 text-emerald-700',
                                            'dosen'     => 'bg-amber-100 text-amber-700',
                                            'mahasiswa' => 'bg-sky-100 text-sky-700',
                                        ];
                                        $roleLabels = [
                                            'admin'     => 'Admin',
                                            'kalab'     => 'KA Lab',
                                            'dosen'     => 'Dosen',
                                            'mahasiswa' => 'Mahasiswa',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Ubah Role --}}
                                        <button @click="showRoleModal = true; roleUserId = {{ $user->id }}; roleUserName = '{{ $user->name }}'; roleValue = '{{ $user->role }}'"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ubah Role">
                                            <i class="fas fa-user-shield text-sm"></i>
                                        </button>
                                        
                                        {{-- Hapus --}}
                                        @if($user->id !== auth()->id())
                                        <button @click="showDeleteModal = true; deleteUserId = {{ $user->id }}; deleteUserName = '{{ $user->name }}'"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-users-slash text-4xl text-slate-300"></i>
                                        <p class="text-sm text-slate-500">Tidak ada user ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $users->withQueryString()->links() }}
                </div>
                @endif
            </div>

            {{-- Info: Akun Default --}}
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                <h3 class="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Akun Default Sistem
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl p-3 border border-blue-100">
                        <p class="text-xs font-semibold text-blue-600 mb-1">Admin (Teknisi)</p>
                        <p class="text-xs text-slate-600">admin@alatika.com</p>
                        <p class="text-xs text-slate-400">password: password</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 border border-emerald-100">
                        <p class="text-xs font-semibold text-emerald-600 mb-1">Kepala Lab</p>
                        <p class="text-xs text-slate-600">kalab@alatika.com</p>
                        <p class="text-xs text-slate-400">password: password</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 border border-amber-100">
                        <p class="text-xs font-semibold text-amber-600 mb-1">Dosen</p>
                        <p class="text-xs text-slate-600">dosen@alatika.com</p>
                        <p class="text-xs text-slate-400">password: password</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 border border-sky-100">
                        <p class="text-xs font-semibold text-sky-600 mb-1">Mahasiswa</p>
                        <p class="text-xs text-slate-600">mahasiswa@alatika.com</p>
                        <p class="text-xs text-slate-400">password: password</p>
                    </div>
                </div>
            </div>

        </main>
    </div>

    {{-- Modal: Ubah Role --}}
    <div x-show="showRoleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showRoleModal = false">
        <div x-show="showRoleModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 modal-backdrop" @click="showRoleModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showRoleModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-indigo-600 text-2xl"></i>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ubah Role User</h3>
                    <p class="text-sm text-gray-600">Mengubah role untuk <strong x-text="roleUserName"></strong></p>
                </div>
                <form :action="'/admin/users/' + roleUserId + '/role'" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="role" x-model="roleValue"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-4">
                        <option value="admin">Admin (Teknisi)</option>
                        <option value="kalab">Kepala Laboratorium</option>
                        <option value="dosen">Dosen</option>
                        <option value="mahasiswa">Mahasiswa</option>
                    </select>
                    <div class="flex gap-3">
                        <button type="button" @click="showRoleModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-medium rounded-xl transition-all">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Hapus User --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="showDeleteModal = false">
        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 modal-backdrop" @click="showDeleteModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showDeleteModal" x-transition class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trash text-red-600 text-2xl"></i>
                    </div>
                </div>
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus User</h3>
                    <p class="text-sm text-gray-600">Yakin ingin menghapus <strong x-text="deleteUserName"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <form :action="'/admin/users/' + deleteUserId" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-xl transition-all">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Logout --}}
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
