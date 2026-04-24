<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; }
    </style>
</head>
<body class="bg-gray-50 antialiased" x-data="{}">
    <x-sidebar-mahasiswa />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">
        
        <!-- Navbar Component -->
        <x-header-dashboard :title="'Riwayat Peminjaman'" :breadcrumbs="[]" />

        <main class="p-8 min-h-screen">
            @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-4 px-6 font-semibold text-gray-700 text-sm whitespace-nowrap">Kode/Tanggal</th>
                                <th class="py-4 px-6 font-semibold text-gray-700 text-sm">Alat</th>
                                <th class="py-4 px-6 font-semibold text-gray-700 text-sm whitespace-nowrap">Deadline / Kembali</th>
                                <th class="py-4 px-6 font-semibold text-gray-700 text-sm text-center">Status</th>
                                <th class="py-4 px-6 font-semibold text-gray-700 text-sm text-center">Aksi / Info</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayat as $p)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-6 text-sm">
                                    <div class="font-bold text-gray-900">{{ $p->kode_peminjaman }}</div>
                                    <div class="text-gray-500">{{ $p->updated_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-medium text-gray-900">{{ $p->alat->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ $p->jumlah }} unit</div>
                                </td>
                                <td class="py-4 px-6 text-sm">
                                    <div class="font-medium text-gray-900">{{ $p->tanggal_kembali->format('d M Y') }}</div>
                                    @if($p->tanggal_dikembalikan && $p->status === 'selesai')
                                        <div class="text-green-600 text-xs mt-1">Kembali: {{ $p->tanggal_dikembalikan->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ str_replace('bg-', 'bg-opacity-20 bg-', $p->status_config['color']) }} border {{ str_replace('bg-', 'border-', str_replace('text-', 'text-', $p->status_config['color'])) }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-center">
                                    @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)
                                        <a href="{{ $p->foto_bukti_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-xs">
                                            <i class="fas fa-image mr-1"></i> Lihat Foto Bukti
                                        </a>
                                    @elseif($p->status === 'dipinjam')
                                        <span class="text-xs text-gray-500">
                                            Gunakan <br><code class="bg-gray-200 px-1 rounded text-red-600">/kembali {{ $p->kode_peminjaman }}</code><br> di Telegram
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                    Belum ada riwayat peminjaman.<br>
                                    <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="text-blue-600 hover:underline mt-2 inline-block">Ajukan peminjaman sekarang</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Sidebar collapse handler
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarHandler', () => ({
                collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                
                toggle() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebarCollapsed', this.collapsed);
                    this.updateMainContent();
                },
                
                updateMainContent() {
                    const mainContent = document.getElementById('mainContent');
                    if (mainContent) {
                        mainContent.className = this.collapsed 
                            ? 'transition-all duration-300 ease-in-out ml-20'
                            : 'transition-all duration-300 ease-in-out ml-64';
                    }
                }
            }));
        });
    </script>
</body>
</html>
