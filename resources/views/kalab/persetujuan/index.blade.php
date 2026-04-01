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

            <div class="bg-white border border-gray-200 rounded-16px shadow-sm overflow-hidden" x-data="{ selectAll: false }">
                <form action="{{ route('kalab.persetujuan.bulk-approve') }}" method="POST" id="bulkApproveForm">
                    @csrf
                    
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="selectAllCheckbox" x-model="selectAll" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                            <label for="selectAllCheckbox" class="text-sm font-medium text-gray-700">Pilih Semua</label>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-check-double"></i> Setujui Terpilih
                        </button>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-4 px-4 w-12 text-center text-sm">#</th>
                                <th class="py-4 px-6 text-sm">Dosen</th>
                                <th class="py-4 px-6 text-sm">Alat / Jumlah</th>
                                <th class="py-4 px-6 text-sm">Tanggal</th>
                                <th class="py-4 px-6 text-sm">Keperluan</th>
                                <th class="py-4 px-6 text-sm text-center">Aksi (Individual)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($peminjaman as $p)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" name="peminjaman_ids[]" value="{{ $p->id }}" x-bind:checked="selectAll" class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-sky-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($p->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $p->user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-700">{{ $p->alat->nama }} <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs ml-1">{{ $p->jumlah }} unit</span></td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                    {{ $p->tanggal_pinjam->format('d M y') }} <i class="fas fa-arrow-right mx-1 text-gray-400 text-xs"></i> {{ $p->tanggal_kembali->format('d M y') }}
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600">{{ $p->keperluan }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-2">
                                        <!-- Note: using formaction allows sending submit to different endpoint despite being in single form -->
                                        <button type="submit" formaction="{{ route('kalab.persetujuan.approve', $p->id) }}" class="w-full bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-200 transition-colors flex items-center justify-center gap-1">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                        <button type="button" onclick="document.getElementById('reject-form-{{ $p->id }}').submit()" class="w-full bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-200 transition-colors flex items-center justify-center gap-1">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-4xl text-gray-300 mb-3 block"></i>
                                    Semua pengajuan telah diproses.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>

                <!-- Hidden external forms for individual rejections to avoid form nesting within a form -->
                @foreach($peminjaman as $p)
                <form id="reject-form-{{ $p->id }}" action="{{ route('kalab.persetujuan.reject', $p->id) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="alasan" value="Ditolak secara massal/individual oleh Kepala Laboratorium">
                </form>
                @endforeach
            </div>
        </main>
    </div>
</body>
</html>
