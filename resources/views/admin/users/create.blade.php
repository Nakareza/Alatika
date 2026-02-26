<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }
        [x-cloak] { display: none !important; }
        .modal-backdrop { background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ showLogoutModal: false, selectedRole: '{{ old('role', 'dosen') }}' }">
    
    <x-sidebar-admin />
    
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Tambah User Baru</h1>
                            <p class="text-sm text-gray-500 mt-0.5">Buat akun untuk Dosen, KA Lab, Admin, atau Mahasiswa</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen">
            <div class="max-w-2xl mx-auto">

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="text-sm font-semibold">Terdapat kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    @csrf
                    
                    {{-- Role Selection --}}
                    <div class="p-6 border-b border-slate-100">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Pilih Role User</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="admin" x-model="selectedRole" class="hidden peer">
                                <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all text-center">
                                    <div class="w-10 h-10 mx-auto mb-2 bg-blue-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-user-cog text-blue-600"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Admin</p>
                                    <p class="text-xs text-slate-500">Teknisi</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="kalab" x-model="selectedRole" class="hidden peer">
                                <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all text-center">
                                    <div class="w-10 h-10 mx-auto mb-2 bg-emerald-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-flask text-emerald-600"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">KA Lab</p>
                                    <p class="text-xs text-slate-500">Kepala Lab</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="dosen" x-model="selectedRole" class="hidden peer">
                                <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 transition-all text-center">
                                    <div class="w-10 h-10 mx-auto mb-2 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-chalkboard-teacher text-amber-600"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Dosen</p>
                                    <p class="text-xs text-slate-500">Pengajar</p>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="mahasiswa" x-model="selectedRole" class="hidden peer">
                                <div class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 transition-all text-center">
                                    <div class="w-10 h-10 mx-auto mb-2 bg-sky-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-user-graduate text-sky-600"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Mahasiswa</p>
                                    <p class="text-xs text-slate-500">Pelajar</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="p-6 space-y-5">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Masukkan nama lengkap">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="contoh@email.com">
                        </div>

                        {{-- NIM (shown for mahasiswa) --}}
                        <div x-show="selectedRole === 'mahasiswa'" x-transition>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIM <span class="text-red-500">*</span></label>
                            <input type="text" name="nim" value="{{ old('nim') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor Induk Mahasiswa">
                        </div>

                        {{-- NIP (shown for admin/kalab/dosen) --}}
                        <div x-show="selectedRole !== 'mahasiswa'" x-transition>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip') }}"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nomor Induk Pegawai (opsional)">
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Minimal 8 karakter">
                            <p class="text-xs text-slate-400 mt-1.5">Pastikan password minimal 8 karakter.</p>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan User
                        </button>
                    </div>
                </form>

            </div>
        </main>
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
