<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan - Alatika</title>
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
                <h1 class="text-xl font-bold text-gray-900">Persetujuan Peminjaman Dosen</h1>
                <p class="text-sm text-gray-500">Daftar pengajuan alat dari dosen yang menunggu persetujuan.</p>
            </div>
        </header>

        <main class="p-8 min-h-screen">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white border border-gray-200 rounded-16px shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-4 px-6 text-sm">Dosen</th>
                            <th class="py-4 px-6 text-sm">Alat / Jumlah</th>
                            <th class="py-4 px-6 text-sm">Tanggal</th>
                            <th class="py-4 px-6 text-sm">Keperluan</th>
                            <th class="py-4 px-6 text-sm text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($peminjaman as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6 text-sm font-semibold">{{ $p->user->name }}</td>
                            <td class="py-4 px-6 text-sm">{{ $p->alat->nama }} ({{ $p->jumlah }} unit)</td>
                            <td class="py-4 px-6 text-sm">{{ $p->tanggal_pinjam->format('d M y') }} - {{ $p->tanggal_kembali->format('d M y') }}</td>
                            <td class="py-4 px-6 text-sm">{{ $p->keperluan }}</td>
                            <td class="py-4 px-6 text-sm text-center">
                                <div class="flex items-center gap-2 justify-center">
                                    <form action="{{ route('kalab.persetujuan.approve', $p->id) }}" method="POST">
                                        @csrf
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('kalab.persetujuan.reject', $p->id) }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="text" name="alasan" placeholder="Alasan tolak" class="border px-2 py-1 text-xs w-24 rounded" required>
                                        <button class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">Tidak ada pengajuan pending.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
