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
                <td class="px-6 py-5 text-sm font-semibold text-slate-500">
                    {{ $index + 1 }}
                </td>

                {{-- Mahasiswa --}}
                <td class="px-6 py-5">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-[#1E2B4A] text-white flex items-center justify-center font-bold">

                            {{ strtoupper(substr($p->user->name,0,1)) }}

                        </div>

                        <div>

                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $p->user->name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $p->user->nim ?? '-' }}
                            </p>

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

                        {{-- Menunggu Kalab --}}
                        @if($p->status === 'pending' && !$p->kalab_approved_at)

                            <button
                                type="button"
                                disabled
                                title="Menunggu persetujuan Kalab"
                                class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed">

                                <i class="fas fa-hourglass-half"></i>

                            </button>

                        {{-- Menunggu Admin --}}
                        @elseif($p->status === 'pending' && $p->kalab_approved_at && !$p->admin_approved_at)

                            <form
                                action="{{ route('admin.peminjaman.approve', $p->id) }}"
                                method="POST">

                                @csrf

                                <button
                                    type="submit"
                                    title="Setujui"
                                    class="w-10 h-10 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition">

                                    <i class="fas fa-check"></i>

                                </button>

                            </form>

                            <button
                                type="button"
                                title="Tolak"
                                onclick="showRejectModal({{ $p->id }})"
                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition">

                                <i class="fas fa-times"></i>

                            </button>

                        {{-- Disetujui --}}
                        @elseif($p->status === 'pending' && $p->kalab_approved_at && $p->admin_approved_at)

                            <span class="badge badge-success">

                                Disetujui

                            </span>

                        @endif

                        @if($p->status === 'dipinjam')

                            <span class="badge badge-info">

                                Sedang Dipinjam

                            </span>

                        @endif

                        @if($p->status === 'selesai')

                            <span class="badge badge-success">

                                Selesai

                            </span>

                        @endif

                        @if($p->status === 'ditolak')

                            <span class="badge badge-danger">

                                Ditolak

                            </span>

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
            Masukkan alasan penolakan untuk dosen:
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