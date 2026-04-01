<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Alatika</title>
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

    <x-sidebar-dosen />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Profil Saya</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Kelola informasi akun dan integrasi</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 min-h-screen">

            <div class="max-w-4xl mx-auto space-y-6">

                {{-- Profile Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="h-28 bg-gradient-to-r from-blue-500 to-blue-600 relative">
                        <div class="absolute -bottom-10 left-6">
                            <div class="w-20 h-20 bg-white rounded-2xl shadow-lg flex items-center justify-center border-4 border-white">
                                <span class="text-3xl font-bold bg-gradient-to-br from-blue-500 to-blue-600 bg-clip-text text-transparent">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="pt-14 px-6 pb-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-slate-800">{{ Auth::user()->name }}</h2>
                                <p class="text-sm text-slate-500 mt-0.5">{{ Auth::user()->email }}</p>
                                <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-shield-alt"></i> Dosen
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-100">
                            <div class="bg-slate-50 rounded-xl p-4">
                                <p class="text-xs text-slate-500 font-medium">NIP</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">{{ Auth::user()->nip ?? '-' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4">
                                <p class="text-xs text-slate-500 font-medium">Role</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">{{ ucfirst(Auth::user()->role) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4">
                                <p class="text-xs text-slate-500 font-medium">Terdaftar Sejak</p>
                                <p class="text-sm font-semibold text-slate-800 mt-1">{{ Auth::user()->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Telegram Integration --}}
                @include('components.telegram-connect')

                {{-- Account Actions --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cog text-slate-400"></i> Akun
                    </h3>
                    <div class="space-y-3">
                        <button @click="showLogoutModal = true" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-50 transition-colors text-left group">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-sign-out-alt text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-red-700">Logout</p>
                                <p class="text-xs text-slate-500">Keluar dari akun Anda</p>
                            </div>
                            <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
                        </button>
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

