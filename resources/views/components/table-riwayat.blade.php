@props(['peminjaman' => []])

<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 border-b border-blue-500">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fas fa-clock"></i>
            Peminjaman Terbaru
        </h3>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        @if(count($peminjaman) > 0)
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Alat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pinjam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Kembali</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($peminjaman as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-microchip text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $item['nama_alat'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $item['kode_alat'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                {{ \Carbon\Carbon::parse($item['tanggal_pinjam'])->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <i class="fas fa-calendar-check text-gray-400 mr-2"></i>
                                {{ $item['tanggal_kembali'] ? \Carbon\Carbon::parse($item['tanggal_kembali'])->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock', 'label' => 'Pending'],
                                        'disetujui' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                                        'ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'label' => 'Ditolak'],
                                        'dipinjam' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-hand-holding', 'label' => 'Dipinjam'],
                                        'selesai' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-check-double', 'label' => 'Selesai'],
                                    ];
                                    $status = $statusConfig[$item['status']] ?? $statusConfig['pending'];
                                @endphp
                                
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                                    <i class="fas {{ $status['icon'] }}"></i>
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-blue-600 hover:text-blue-800 font-medium text-sm hover:underline">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="py-16 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-inbox text-4xl text-gray-400"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Peminjaman</h4>
                <p class="text-gray-500 mb-6">Anda belum memiliki riwayat peminjaman alat.</p>
                <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fas fa-plus"></i>
                    Ajukan Peminjaman
                </a>
            </div>
        @endif
    </div>

    <!-- Footer dengan pagination atau link ke halaman lengkap -->
    @if(count($peminjaman) > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-2 justify-center">
                Lihat Semua Riwayat
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    @endif
</div>
