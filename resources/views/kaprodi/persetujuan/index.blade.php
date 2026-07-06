@extends('layouts.kaprodi')

@section('title', 'Persetujuan Peminjaman')

@section('content')

    {{-- Alert Success / Error --}}
    @if(session('success'))
        <div class="mb-6 card p-4 flex items-center gap-3 border-l-4 border-green-500">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <h4 class="font-semibold text-green-700 text-sm">Berhasil</h4>
                <p class="text-sm text-green-600">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 card p-4 flex items-center gap-3 border-l-4 border-red-500">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="fas fa-times text-red-600"></i>
            </div>
            <div>
                <h4 class="font-semibold text-red-700 text-sm">Gagal</h4>
                <p class="text-sm text-red-600">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-card-stats
            title="Menunggu Persetujuan"
            :value="$stats['pending']"
            icon="fas fa-clock"
            color="yellow" />
        <x-card-stats
            title="Dipinjam"
            :value="$stats['dipinjam']"
            icon="fas fa-hand-holding"
            color="blue" />
        <x-card-stats
            title="Selesai"
            :value="$stats['selesai']"
            icon="fas fa-check-double"
            color="green" />
        <x-card-stats
            title="Total Pengajuan"
            :value="$stats['total_pengajuan']"
            icon="fas fa-file-alt"
            color="purple" />
    </div>
    
    {{-- Filter & Search --}}
    <div class="card p-6 mb-6">
        <form method="GET" action="{{ route('kaprodi.persetujuan') }}">
            <div class="flex flex-col lg:flex-row gap-4">
                {{-- Search --}}
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari peminjam, alat, atau kode peminjaman..."
                        class="inp pl-11 w-full">
                </div>

                {{-- Status --}}
                <select name="status" class="inp lg:w-56" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                {{-- Periode --}}
                <select name="periode" class="inp lg:w-48" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    <option value="hari_ini" {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                </select>

                {{-- Reset --}}
                <a href="{{ route('kaprodi.persetujuan') }}" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden" x-data="{ selectAll: false, selectedItems: [] }">
        <form action="{{ route('kaprodi.persetujuan.bulk-approve') }}" method="POST" id="bulk-approve-form">
            @csrf
            
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-white">
                <h3 class="font-bold text-lg text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Daftar Persetujuan</h3>
                
                {{-- Bulk Action Button --}}
                <div x-show="selectedItems.length > 0" class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-slate-500"><span x-text="selectedItems.length"></span> terpilih</span>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-xl text-xs font-bold hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fas fa-check-double"></i> Setujui Masal
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#F5F8FF]">
                        <tr>
                            <th class="px-4 py-4 text-center text-xs font-semibold uppercase text-slate-500 w-12">
                                <input type="checkbox" 
                                       @click="selectAll = !selectAll; if(selectAll) { selectedItems = [@foreach($peminjaman->where('status', 'pending') as $p){{ $p->id }},@endforeach] } else { selectedItems = [] }"
                                       x-bind:checked="selectAll">
                            </th>
                            <th class="px-4 py-4 text-center text-xs font-semibold uppercase text-slate-500">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">Peminjam</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">Alat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">Tanggal Pinjam</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($peminjaman as $p)
                        <tr class="hover:bg-[#F8FBFF] transition">
                            <td class="px-4 py-4 text-center">
                                @if($p->status == 'pending')
                                <input type="checkbox" 
                                       name="peminjaman_ids[]" 
                                       value="{{ $p->id }}" 
                                       x-model="selectedItems"
                                       @change="selectAll = (selectedItems.length === {{ $peminjaman->where('status', 'pending')->count() }})">
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center font-medium text-slate-600">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#185FA5] text-white flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($p->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[#1E2B4A]">{{ $p->user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ ucfirst($p->user->role) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-[#1E2B4A]">{{ $p->alat->nama }}</p>
                                <p class="text-xs text-slate-500">{{ $p->alat->kode }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge badge-info">{{ $p->jumlah }} Unit</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700">{{ $p->tanggal_pinjam->format('d M Y') }}</p>
                                <p class="text-xs text-slate-500">s/d {{ $p->tanggal_kembali->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status == 'pending')
                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                                @elseif($p->status == 'dipinjam')
                                    <span class="badge badge-info">Sedang Dipinjam</span>
                                @elseif($p->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($p->status == 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    @if($p->status == 'pending')
                                        <button type="button"
                                                onclick="submitDirectApprove({{ $p->id }})"
                                                class="w-9 h-9 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition flex items-center justify-center"
                                                title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button"
                                                onclick="showRejectModal({{ $p->id }}, '{{ addslashes($p->user->name) }}', '{{ addslashes($p->alat->nama) }}')"
                                                class="w-9 h-9 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition flex items-center justify-center"
                                                title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button type="button"
                                            onclick="showDetail(
                                                '{{ $p->kode_peminjaman }}',
                                                '{{ $p->user->name }}',
                                                '{{ $p->alat->nama }}',
                                                '{{ $p->jumlah }}',
                                                '{{ $p->tanggal_pinjam->format('d M Y') }}',
                                                '{{ $p->tanggal_kembali->format('d M Y') }}',
                                                '{{ $p->status_label }}',
                                                '{{ addslashes($p->keperluan) }}'
                                            )"
                                            class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition flex items-center justify-center"
                                            title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-check-circle text-4xl text-slate-300 mb-3"></i>
                                    <p class="font-semibold text-[#1E2B4A]">Tidak Ada Pengajuan</p>
                                    <p class="text-sm text-slate-500">Semua pengajuan telah diproses.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $peminjaman->links() }}
    </div>

    {{-- Hidden Approve Forms --}}
    @foreach($peminjaman as $p)
        @if($p->status == 'pending')
        <form id="approve-form-{{ $p->id }}" action="{{ route('kaprodi.persetujuan.approve', $p->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        @endif
    @endforeach

    {{-- Detail Modal --}}
    <x-modal name="detail-peminjaman" title="Detail Peminjaman" size="lg" type="default">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-500">Kode Peminjaman</p>
                <p id="detail_kode" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div>
                <p class="text-slate-500">Peminjam</p>
                <p id="detail_user" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div>
                <p class="text-slate-500">Alat</p>
                <p id="detail_alat" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div>
                <p class="text-slate-500">Jumlah</p>
                <p id="detail_jumlah" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div>
                <p class="text-slate-500">Tanggal Pinjam</p>
                <p id="detail_pinjam" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div>
                <p class="text-slate-500">Tanggal Kembali</p>
                <p id="detail_kembali" class="font-semibold text-[#1E2B4A]"></p>
            </div>
            <div class="col-span-2">
                <p class="text-slate-500">Status</p>
                <p id="detail_status" class="font-semibold"></p>
            </div>
            <div class="col-span-2">
                <p class="text-slate-500">Keperluan</p>
                <p id="detail_keperluan" class="font-semibold text-[#1E2B4A]"></p>
            </div>
        </div>
        <x-slot:footer>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal-detail-peminjaman'))" class="flex-1 px-4 py-3 rounded-xl border border-slate-200">
                Tutup
            </button>
        </x-slot:footer>
    </x-modal>

    {{-- Reject Modal --}}
    <div id="reject-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Tolak Peminjaman</h3>
            </div>
            <form id="reject-form" action="" method="POST">
                @csrf
                <div class="mb-4 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Peminjam</p>
                    <p id="reject-user" class="text-sm font-semibold text-[#1E2B4A]"></p>
                    <p class="text-xs text-slate-500 mt-2 mb-1">Alat</p>
                    <p id="reject-alat" class="text-sm font-semibold text-[#1E2B4A]"></p>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Alasan Penolakan</label>
                    <textarea name="alasan" required class="inp w-full h-24 resize-none" placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition" onclick="closeRejectModal()">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white hover:bg-red-700 transition font-semibold">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function submitDirectApprove(id) {
        if(confirm('Apakah Anda yakin ingin menyetujui peminjaman ini?')) {
            document.getElementById('approve-form-' + id).submit();
        }
    }

    function showRejectModal(id, user, alat) {
        const form = document.getElementById('reject-form');
        form.action = `/kaprodi/persetujuan/${id}/reject`;
        document.getElementById('reject-user').innerText = user;
        document.getElementById('reject-alat').innerText = alat;
        document.getElementById('reject-modal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
    }

    function showDetail(kode, user, alat, jumlah, pinjam, kembali, status, keperluan) {
        document.getElementById('detail_kode').innerText = kode;
        document.getElementById('detail_user').innerText = user;
        document.getElementById('detail_alat').innerText = alat;
        document.getElementById('detail_jumlah').innerText = jumlah + ' Unit';
        document.getElementById('detail_pinjam').innerText = pinjam;
        document.getElementById('detail_kembali').innerText = kembali;
        document.getElementById('detail_status').innerText = status;
        document.getElementById('detail_keperluan').innerText = keperluan;

        // Apply status class dynamically
        const statusEl = document.getElementById('detail_status');
        statusEl.className = 'font-semibold';
        if (status.toLowerCase().includes('setuju') || status.toLowerCase().includes('pinjam')) {
            statusEl.classList.add('text-indigo-600');
        } else if (status.toLowerCase().includes('selesai')) {
            statusEl.classList.add('text-green-600');
        } else if (status.toLowerCase().includes('tolak')) {
            statusEl.classList.add('text-red-600');
        } else {
            statusEl.classList.add('text-amber-600');
        }

        window.dispatchEvent(new CustomEvent('open-modal-detail-peminjaman'));
    }

    // Close modal on outside click or escape
    document.getElementById('reject-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRejectModal();
    });
    </script>

@endsection
