<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; }
        .form-input { w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 }
        .btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 12px; padding: 0.75rem 1.5rem; font-weight: 500;}
        .btn-primary:hover { filter: brightness(1.05); }
        .btn-secondary { background: #f1f5f9; color: #475569; border-radius: 12px; padding: 0.75rem 1.5rem; font-weight: 500;}
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <x-sidebar-dosen />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-8 py-4 flex justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Ajukan Peminjaman</h1>
                    <p class="text-sm text-gray-500">Form pengajuan peminjaman alat lab</p>
                </div>
            </div>
        </header>

        <main class="p-8 min-h-screen">
            @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="max-w-2xl mx-auto card p-6 md:p-8">
                <form action="{{ route('dosen.peminjaman.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Alat</label>
                        <select name="alat_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            <option value="">-- Pilih Alat --</option>
                            @foreach($alat as $item)
                                <option value="{{ $item->id }}">{{ $item->nama }} (Stok: {{ $item->stok_tersedia }}) - Kode: {{ $item->kode }}</option>
                            @endforeach
                        </select>
                        @error('alat_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                            <input type="number" name="jumlah" value="1" min="1" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" required>
                            @error('jumlah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" required>
                            @error('tanggal_pinjam') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Kembali (Deadline)</label>
                            <input type="date" name="tanggal_kembali" value="{{ date('Y-m-d', strtotime('+7 days')) }}" min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" required>
                            @error('tanggal_kembali') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Keperluan</label>
                        <textarea name="keperluan" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="Jelaskan alasan peminjaman dan penggunaannya..." required></textarea>
                        @error('keperluan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                        <button type="submit" class="btn-primary w-full md:w-auto">
                            <i class="fas fa-paper-plane mr-2"></i> Ajukan Peminjaman
                        </button>
                        <a href="{{ route('dosen.dashboard') }}" class="btn-secondary w-full md:w-auto text-center border border-gray-300">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

