<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F5F8FF; color: #1E2B4A; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }

        .card {
            background: white;
            border: 1px solid #EBF3FD;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }
    </style>
</head>
<body class="antialiased" x-data="{}">
    <x-sidebar-mahasiswa />
    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <x-header-dashboard :title="'Riwayat Peminjaman'" :breadcrumbs="[]" />

        <main class="p-8 min-h-screen">

            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium"
                 style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr style="background:#F5F8FF;border-bottom:1px solid #EBF3FD;">
                                <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider whitespace-nowrap"
                                    style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">Tanggal</th>
                                <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider"
                                    style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">Alat</th>
                                <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider whitespace-nowrap"
                                    style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">Kembali</th>
                                <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-center"
                                    style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">Status</th>
                                <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-center"
                                    style="color:#185FA5;font-family:'Plus Jakarta Sans',sans-serif;">Aksi / Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $p)
                            <tr class="transition-colors" style="border-bottom:1px solid #EBF3FD;"
                                onmouseover="this.style.background='#F5F8FF'"
                                onmouseout="this.style.background='white'">
                                <td class="py-4 px-6 text-sm">
                                    <div class="font-bold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                        {{ $p->kode_peminjaman }}
                                    </div>
                                    <div class="text-xs mt-0.5" style="color:#94a3b8;">
                                        {{ $p->updated_at->format('d M Y H:i') }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-sm" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                        {{ $p->alat->nama }}
                                    </div>
                                    <div class="text-xs mt-0.5" style="color:#94a3b8;">{{ $p->jumlah }} unit</div>
                                </td>
                                <td class="py-4 px-6 text-sm">
                                    <div class="font-semibold" style="color:#1E2B4A;">
                                        {{ $p->tanggal_kembali->format('d M Y') }}
                                    </div>
                                    @if($p->tanggal_dikembalikan && $p->status === 'selesai')
                                        <div class="text-xs mt-1" style="color:#10b981;">
                                            Kembali: {{ $p->tanggal_dikembalikan->format('d M Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ str_replace('bg-', 'bg-opacity-20 bg-', $p->status_config['color']) }} border {{ str_replace('bg-', 'border-', str_replace('text-', 'text-', $p->status_config['color'])) }}">
                                        {{ $p->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-center">
                                    @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)
                                        <a href="{{ $p->foto_bukti_url }}" target="_blank"
                                           class="text-xs font-semibold hover:opacity-70 transition-opacity underline"
                                           style="color:#185FA5;">
                                            <i class="fas fa-image mr-1"></i> Lihat Foto Bukti
                                        </a>
                                    @elseif($p->status === 'dipinjam')
                                        <span class="text-xs" style="color:#64748b;">
                                            Gunakan <br>
                                            <code class="px-1.5 py-0.5 rounded text-xs font-mono"
                                                  style="background:#EBF3FD;color:#185FA5;">
                                                /kembali {{ $p->kode_peminjaman }}
                                            </code>
                                            <br>di Telegram
                                        </span>
                                    @else
                                        <span style="color:#cbd5e1;">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-16 px-6 text-center">
                                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
                                         style="background:#EBF3FD;">
                                        <i class="fas fa-inbox text-xl" style="color:#B5D4F4;"></i>
                                    </div>
                                    <p class="text-sm font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                                        Belum ada riwayat peminjaman
                                    </p>
                                    <p class="text-xs mb-4" style="color:#94a3b8;">
                                        Mulai pinjam alat untuk melihat riwayatmu di sini
                                    </p>
                                    <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 text-white text-xs font-semibold rounded-xl transition-all"
                                       style="background:#1E2B4A;box-shadow:0 4px 12px rgba(30,43,74,0.22);">
                                        <i class="fas fa-plus"></i>
                                        Ajukan Peminjaman
                                    </a>
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