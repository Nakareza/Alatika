{{-- Tabel Peminjaman Terbaru untuk Admin --}}
@props(['peminjaman' => []])

<div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    {{-- Table Header --}}
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-800">Peminjaman Terbaru</h3>
        <a href="{{ route('admin.peminjaman') }}" 
           class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors duration-200 flex items-center gap-1">
            <span>Lihat Semua</span>
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    {{-- Table Content --}}
    @if(count($peminjaman) > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Alat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Tanggal Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($peminjaman as $item)
                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($item['mahasiswa_nama'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $item['mahasiswa_nama'] }}</p>
                                <p class="text-xs text-slate-500">{{ $item['mahasiswa_nim'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-slate-800">{{ $item['nama_alat'] }}</p>
                        <p class="text-xs text-slate-500">{{ $item['kode_alat'] }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($item['tanggal_pinjam'])->format('d M Y') }}</p>
                        <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($item['tanggal_pinjam'])->format('H:i') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($item['tanggal_kembali'])->format('d M Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $statusConfig = [
                            'pending' => ['class' => 'bg-amber-100 text-amber-700 border-amber-200', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                            'disetujui' => ['class' => 'bg-blue-100 text-blue-700 border-blue-200', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                            'ditolak' => ['class' => 'bg-rose-100 text-rose-700 border-rose-200', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                            'dipinjam' => ['class' => 'bg-indigo-100 text-indigo-700 border-indigo-200', 'icon' => 'fa-hand-holding', 'label' => 'Dipinjam'],
                            'selesai' => ['class' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'icon' => 'fa-check-double', 'label' => 'Selesai'],
                        ];
                        $status = $statusConfig[$item['status']] ?? $statusConfig['pending'];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $status['class'] }}">
                            <i class="fas {{ $status['icon'] }}"></i>
                            {{ $status['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($item['status'] === 'pending')
                            <button class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors duration-200" title="Setujui">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors duration-200" title="Tolak">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                            @endif
                            <button class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors duration-200" title="Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    {{-- Empty State --}}
    <div class="px-6 py-12 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
            <i class="fas fa-inbox text-2xl text-slate-400"></i>
        </div>
        <h4 class="text-lg font-semibold text-slate-800 mb-2">Belum Ada Peminjaman</h4>
        <p class="text-sm text-slate-500 mb-4">Data peminjaman akan muncul di sini ketika mahasiswa mengajukan peminjaman.</p>
    </div>
    @endif
</div>
