<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Inter', sans-serif; background: #f8fafc; }</style>
</head>
<body class="bg-gray-50 antialiased">
    <x-sidebar-kalab />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-8 py-4">
                <h1 class="text-xl font-bold text-gray-900">Riwayat Peminjaman (Global)</h1>
                <p class="text-sm text-gray-500">Daftar semua riwayat peminjaman.</p>
            </div>
        </header>

        <main class="p-8 min-h-screen">
            <div class="bg-white border border-gray-200 rounded-16px shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-4 px-6 text-sm">Peminjam</th>
                            <th class="py-4 px-6 text-sm">Alat / Jumlah</th>
                            <th class="py-4 px-6 text-sm">Status</th>
                            <th class="py-4 px-6 text-sm">Update Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($peminjaman as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 text-sm">
                                <strong>{{ $p->user->name }}</strong><br>
                                <span class="text-xs text-gray-500 capitalize">{{ $p->user->role }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm">{{ $p->alat->nama }} ({{ $p->jumlah }} unit)</td>
                            <td class="py-4 px-6 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ str_replace('bg-', 'bg-opacity-20 bg-', $p->status_config['color']) }} border {{ str_replace('bg-', 'border-', str_replace('text-', 'text-', $p->status_config['color'])) }}">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm">{{ $p->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">Tidak ada riwayat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
