@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')

@section('content')

<div class="space-y-6">

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <x-card-stats
        title="Total Pengajuan" 
        :value="$stats['total']" 
        icon="fas fa-clipboard-list" 
        color="blue" />

        <x-card-stats
        title="Menunggu Persetujuan" 
        :value="$stats['pending']" 
        icon="fas fa-clock" 
        color="yellow" />

        <x-card-stats
        title="Sedang Dipinjam" 
        :value="$stats['aktif']" 
        icon="fas fa-hand-holding" 
        color="indigo" />
        
        <x-card-stats
        title="Ditolak" 
        :value="$stats['ditolak']" 
        icon="fas fa-times-circle" 
        color="red" />

    </div>

    {{-- Success / Error Messages --}}
    @if(session('success'))
    <div class="rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Kelola Keperluan --}}
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#EBF3FD;color:#185FA5;">
                <i class="fas fa-tags text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-sm" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">Kelola Keperluan Peminjaman</h3>
                <p class="text-xs text-slate-400">Tambah atau hapus opsi keperluan yang tersedia untuk mahasiswa dan dosen.</p>
            </div>
        </div>

        {{-- Current Options --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @forelse($keperluanOptions as $option)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold"
                 style="background:#EBF3FD;color:#185FA5;">
                <span>{{ $option['name'] }}</span>
                @if($option['same_day'])
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold" style="background:#FEF3C7;color:#92400E;">
                    <i class="fas fa-clock text-[9px]"></i> 1 Hari
                </span>
                @endif
                <form action="{{ route('admin.peminjaman.keperluan.remove') }}" method="POST" class="inline" onsubmit="return confirm('Hapus keperluan \"{{ $option['name'] }}\"?')">
                    @csrf
                    <input type="hidden" name="keperluan" value="{{ $option['name'] }}">
                    <button type="submit" class="text-red-400 hover:text-red-600 transition">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                </form>
            </div>
            @empty
            <p class="text-xs text-slate-400">Belum ada opsi keperluan.</p>
            @endforelse
        </div>

        {{-- Add New Option --}}
        <form action="{{ route('admin.peminjaman.keperluan.add') }}" method="POST" class="flex flex-col gap-2">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="keperluan" placeholder="Tambah keperluan baru..."
                       class="inp flex-1" maxlength="100" required>
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition"
                        style="background:#185FA5;"
                        onmouseover="this.style.background='#1E2B4A'"
                        onmouseout="this.style.background='#185FA5'">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </button>
            </div>
            <label class="flex items-center gap-2 text-xs cursor-pointer" style="color:#64748b;">
                <input type="checkbox" name="same_day" value="1"
                       class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Wajib kembali dalam 1 hari (contoh: Praktikum, Tugas Harian, Perkuliahan)</span>
            </label>
        </form>
    </div>

    {{-- Filter --}}
    <div class="card p-6">

        <form method="GET" action="{{ route('admin.peminjaman') }}">
            <div class="flex flex-col md:flex-row gap-4">

                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa atau nama alat..."
                        class="inp pl-10 w-full">
                </div>

                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="inp md:w-52">

                    <option value="">Semua Status</option>

                    <option value="pending"
                        {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Menunggu Persetujuan
                    </option>

                    <option value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                        Sedang Dipinjam
                    </option>

                    <option value="selesai"
                        {{ request('status') == 'selesai' ? 'selected' : '' }}>
                        Selesai
                    </option>

                    <option value="ditolak"
                        {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                        Ditolak
                    </option>

                </select>
                <a href="{{ route('admin.peminjaman') }}"
                class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>

            </div>
        </form>

    </div>

    
    <x-table title="Daftar Peminjaman">

        <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">

            <tr>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    No
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Mahasiswa
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Alat
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Tanggal Pinjam
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Deadline
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Status
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($peminjaman as $index => $p)

            <tr class="hover:bg-[#F8FBFF] transition">

                {{-- No --}}
                <td class="px-6 py-5">
                    <p class="text-sm font-semibold text-slate-500">{{ $index + 1 }}</p>
                    <p class="text-xs text-slate-400">{{ $p->kode_peminjaman }}</p>
                    @if($p->status === 'pending')
                        <span class="text-xs mt-1 inline-block px-2 py-0.5 rounded-full font-medium {{ $p->alat->stok_tersedia >= $p->jumlah ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                            <i class="fas fa-box text-[9px]"></i>
                            Stok: {{ $p->alat->stok_tersedia }}/{{ $p->alat->stok_total }}
                        </span>
                    @endif
                </td>

                {{-- Mahasiswa --}}
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#1E2B4A] text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($p->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-[#1E2B4A]">{{ $p->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $p->user->nim ?? '-' }}</p>
                        </div>
                    </div>
                </td>

                {{-- Alat --}}
                <td class="px-6 py-5">

                    <p class="font-semibold text-[#1E2B4A]">
                        {{ $p->alat->nama }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $p->kode_peminjaman }}
                    </p>

                </td>

                {{-- Tanggal --}}
                <td class="px-6 py-5 text-sm text-slate-600">

                    {{ $p->tanggal_pinjam->format('d M Y') }}

                </td>

                {{-- Deadline --}}
                <td class="px-6 py-5 text-sm text-slate-600">

                    {{ $p->tanggal_kembali->format('d M Y') }}

                </td>

                {{-- Status --}}
                <td class="px-6 py-5">

                    <span class="badge {{ $p->status_config['color'] }}">

                        {{ $p->status_label }}

                    </span>

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5">
                    <div class="flex justify-center items-center gap-2">

                        {{-- Detail Button --}}
                        <button
                            type="button"
                            class="w-9 h-9 rounded-lg text-blue-600 hover:bg-blue-50 transition"
                            title="Detail"
                            onclick="showDetail(
                                '{{ $p->kode_peminjaman }}',
                                '{{ $p->user->name }}',
                                '{{ $p->user->nim ?? '-' }}',
                                '{{ $p->alat->nama }}',
                                {{ $p->jumlah }},
                                '{{ $p->tanggal_pinjam->format('d M Y') }}',
                                '{{ $p->tanggal_kembali->format('d M Y') }}',
                                '{{ $p->status_label }}',
                                '{{ $p->keperluan ?? '-' }}'
                            )">
                            <i class="fas fa-eye"></i>
                        </button>

                        {{-- Pending: Admin langsung approve/reject --}}
                        @if($p->status === 'pending')
                            <form action="{{ route('admin.peminjaman.approve', $p->id) }}"
                                method="POST" style="display:inline;">
                                @csrf
                                <button type="submit"
                                    class="w-9 h-9 rounded-lg text-green-600 hover:bg-green-50 transition"
                                    title="Setujui">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>

                            <button type="button"
                                class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition"
                                title="Tolak"
                                onclick="showRejectModal({{ $p->id }})">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif

                        @if($p->status === 'dipinjam')
                            <span class="text-indigo-600 text-sm font-medium">Sedang Dipinjam</span>
                        @endif

                        @if($p->status === 'selesai')
                            <span class="text-emerald-600 text-sm font-medium">Selesai</span>
                        @endif

                        @if($p->status === 'ditolak')
                            <span class="text-red-600 text-sm font-medium">Ditolak</span>
                        @endif

                    </div>
                </td>

            </tr>

            @empty

            <tr>

                <td colspan="7" class="py-16 text-center">

                    <div class="flex flex-col items-center">

                        <i class="fas fa-inbox text-5xl text-slate-300 mb-4"></i>

                        <h3 class="font-bold text-lg text-[#1E2B4A]">

                            Belum Ada Data Peminjaman

                        </h3>

                        <p class="text-slate-500">

                            Data peminjaman akan muncul di sini.

                        </p>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>
    <x-pagination :data="$peminjaman" />

{{-- Hidden Reject Forms --}}
@foreach($peminjaman as $p)
    <form id="reject-form-{{ $p->id }}"
          action="{{ route('admin.peminjaman.reject', $p->id) }}"
          method="POST"
          class="hidden">
        @csrf
        <input type="hidden" name="alasan" id="alasan-input-{{ $p->id }}" value="">
    </form>
@endforeach

{{-- Detail Peminjaman Modal --}}
<x-modal
    name="detail-peminjaman"
    title="Detail Peminjaman"
    size="lg"
    type="default">

    <div class="grid grid-cols-2 gap-4 text-sm">

        <div>
            <p class="text-slate-500">Kode Peminjaman</p>
            <p id="detail_kode" class="font-semibold text-[#1E2B4A]"></p>
        </div>

        <div>
            <p class="text-slate-500">Peminjam</p>
            <p id="detail_user" class="font-semibold text-[#1E2B4A]"></p>
            <p id="detail_nim" class="text-xs text-slate-400"></p>
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
            <p id="detail_pinjam"></p>
        </div>

        <div>
            <p class="text-slate-500">Tanggal Kembali</p>
            <p id="detail_kembali"></p>
        </div>

        <div class="col-span-2">
            <p class="text-slate-500">Status</p>
            <p id="detail_status"></p>
        </div>

        <div class="col-span-2">
            <p class="text-slate-500">Keperluan</p>
            <p id="detail_keperluan" class="font-semibold text-[#1E2B4A]"></p>
        </div>

    </div>

    <x-slot:footer>
        <button
            type="button"
            onclick="window.dispatchEvent(
                new CustomEvent('close-modal-detail-peminjaman')
            )"
            class="flex-1 px-4 py-3 rounded-xl border border-slate-200">
            Tutup
        </button>
    </x-slot:footer>

</x-modal>

{{-- Reject Modal --}}
<div id="reject-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold">Tolak Peminjaman</h3>
        </div>

        <p class="text-slate-600 text-sm mb-6">
            Masukkan alasan penolakan untuk mahasiswa:
        </p>

        <textarea
            id="reject-reason"
            placeholder="Contoh: Stok alat sedang tidak tersedia"
            class="w-full inp h-24 resize-none mb-6"
            required></textarea>

        <div class="flex gap-3">
            <button
                type="button"
                class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
                onclick="closeRejectModal()">
                Batal
            </button>
            <button
                type="button"
                class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white hover:bg-red-700 transition"
                onclick="submitReject()">
                Tolak
            </button>
        </div>
    </div>
</div>

<script>
function showDetail(kode, user, nim, alat, jumlah, pinjam, kembali, status, keperluan) {
    document.getElementById('detail_kode').innerText = kode;
    document.getElementById('detail_user').innerText = user;
    document.getElementById('detail_nim').innerText = nim;
    document.getElementById('detail_alat').innerText = alat;
    document.getElementById('detail_jumlah').innerText = jumlah + ' Unit';
    document.getElementById('detail_pinjam').innerText = pinjam;
    document.getElementById('detail_kembali').innerText = kembali;
    document.getElementById('detail_status').innerText = status;
    document.getElementById('detail_keperluan').innerText = keperluan;

    window.dispatchEvent(
        new CustomEvent('open-modal-detail-peminjaman')
    );
}

let rejectPeminjamanId = null;

function showRejectModal(id) {
    rejectPeminjamanId = id;
    document.getElementById('reject-modal').classList.remove('hidden');
    document.getElementById('reject-reason').value = '';
    document.getElementById('reject-reason').focus();
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    rejectPeminjamanId = null;
}

function submitReject() {
    const reason = document.getElementById('reject-reason').value.trim();
    
    if (!reason) {
        alert('Alasan tidak boleh kosong');
        return;
    }
    
    if (rejectPeminjamanId) {
        document.getElementById('alasan-input-' + rejectPeminjamanId).value = reason;
        document.getElementById('reject-form-' + rejectPeminjamanId).submit();
    }
}

// Close modal when clicking outside
document.getElementById('reject-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

// Allow Enter to submit
document.getElementById('reject-reason')?.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        submitReject();
    }
});
</script>

@endsection