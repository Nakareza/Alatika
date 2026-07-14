@extends('layouts.kalab')

@section('title', 'Persetujuan Peminjaman')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Persetujuan Peminjaman</h2>
            <p class="text-sm text-slate-500">Kelola dan monitor pengajuan peminjaman alat</p>
        </div>
        <div>
            <a href="{{ route('kalab.peminjaman.create') }}" class="px-4 py-2.5 bg-[#185FA5] hover:bg-[#1e2b4a] text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-plus"></i> Input Peminjaman Manual
            </a>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="mb-6 card p-4 flex items-center gap-3 border-l-4 border-green-500">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>

            <div>
                <h4 class="font-semibold text-green-700 text-sm">
                    Berhasil
                </h4>
                <p class="text-sm text-green-600">
                    {{ session('success') }}
                </p>
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
            color="Purple" />

    </div>
    
    {{-- Filter & Search --}}
    
    <div class="card p-6 mb-6">

        @php
            $currentGroup = $group ?? request('group', 'all');
            $queryParams = request()->query();
            $groupBase = function($value) use ($queryParams) {
                $params = $queryParams;
                $params['group'] = $value;
                return route('kalab.persetujuan', $params);
            };
        @endphp

        <div class="flex flex-wrap gap-2 mb-5">
            <a href="{{ $groupBase('all') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $currentGroup === 'all' ? 'bg-[#185FA5] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fas fa-list-ul mr-1.5"></i> Semua
            </a>
            <a href="{{ $groupBase('dosen') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $currentGroup === 'dosen' ? 'bg-[#185FA5] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fas fa-user-tie mr-1.5"></i> Dosen
            </a>
            <a href="{{ $groupBase('mahasiswa') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $currentGroup === 'mahasiswa' ? 'bg-[#185FA5] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fas fa-user-graduate mr-1.5"></i> Mahasiswa
            </a>
            <a href="{{ $groupBase('organisasi') }}"
               class="px-4 py-2 rounded-full text-sm font-semibold transition {{ $currentGroup === 'organisasi' ? 'bg-[#185FA5] text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                <i class="fas fa-building mr-1.5"></i> UKM / Organisasi
            </a>
        </div>

        <form method="GET" action="{{ route('kalab.persetujuan') }}">

            <div class="flex flex-col lg:flex-row gap-4">

                {{-- Search --}}
                <div class="flex-1 relative">

                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari dosen, alat, atau kode peminjaman..."
                        class="inp pl-11 w-full">

                </div>

                {{-- Status --}}
                <select
                    name="status"
                    class="inp lg:w-56"
                    onchange="this.form.submit()">

                    <option value="">Semua Status</option>

                    <option value="pending"
                        {{ request('status') == 'pending' ? 'selected' : '' }}>
                        Menunggu Persetujuan
                    </option>

                    <option value="dipinjam"
                        {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                        Dipinjam
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

                {{-- Periode --}}
                <select
                    name="periode"
                    class="inp lg:w-48"
                    onchange="this.form.submit()">

                    <option value="">Semua Periode</option>

                    <option value="hari_ini"
                        {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>
                        Hari Ini
                    </option>

                    <option value="minggu_ini"
                        {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>
                        Minggu Ini
                    </option>

                    <option value="bulan_ini"
                        {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>
                        Bulan Ini
                    </option>

                </select>

                {{-- Reset --}}
                <a href="{{ route('kalab.persetujuan') }}"
                class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">

                    <i class="fas fa-rotate-left"></i>

                </a>

            </div>

        </form>

    </div>


    {{-- Table Card --}}
    <div class="card overflow-hidden"
         x-data="{ selectAll: false }">

        {{-- Action Bar --}}
        <form action="{{ route('kalab.persetujuan.bulk-approve') }}"
            method="POST"
            x-data="{ selectAll: false }">

            @csrf

            <x-table title="Monitoring Peminjaman">

                <thead class="bg-[#F5F8FF]">

                    <tr>

                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase text-slate-500">No</th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Peminjam
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Alat
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Jumlah
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Tanggal Pinjam
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($peminjaman as $p)

                    <tr class="hover:bg-[#F8FBFF] transition">

                        {{-- no --}}
                        <td class="px-4 py-4 text-center font-medium text-slate-600">
                            {{ $loop->iteration }}
                        </td>

                        {{-- Dosen --}}
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="w-10 h-10 rounded-xl bg-[#185FA5] text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($p->nama_peminjam, 0, 2)) }}
                                </div>

                                <div>

                                    <p class="font-semibold text-[#1E2B4A]">
                                        {{ $p->nama_peminjam }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $p->peminjam_role }}
                                    </p>

                                </div>

                            </div>

                        </td>

                        {{-- Alat --}}
                        <td class="px-6 py-4">

                            <p class="font-semibold text-[#1E2B4A]">
                                {{ $p->item_name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $p->alat->kode }}
                            </p>

                            @if($p->surat_keterangan)
                                <div class="mt-1.5">
                                    <a href="{{ asset('storage/' . $p->surat_keterangan) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 font-semibold hover:underline bg-blue-50 px-2 py-1 rounded-lg">
                                        <i class="fas fa-file-alt"></i> Surat Keterangan
                                    </a>
                                </div>
                            @endif

                        </td>

                        {{-- Jumlah --}}
                        <td class="px-6 py-4">

                            <span class="badge badge-info">
                                {{ $p->jumlah }} Unit
                            </span>

                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-4">

                            <p class="text-sm text-slate-700">
                                {{ $p->tanggal_pinjam->format('d M Y') }}
                            </p>

                            <p class="text-xs text-slate-500">
                                s/d {{ $p->tanggal_kembali->format('d M Y') }}
                            </p>

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">

                            @if($p->status == 'pending')

                                @if($p->kalab_approved_by !== null)
                                    @php
                                        $pendingRoles = [];
                                        if (is_array($p->required_approvals)) {
                                            if (in_array('admin', $p->required_approvals) && $p->admin_approved_by === null) {
                                                $pendingRoles[] = 'Admin';
                                            }
                                            if (in_array('kaprodi', $p->required_approvals) && $p->kaprodi_approved_by === null) {
                                                $pendingRoles[] = 'Kaprodi';
                                            }
                                        } else {
                                            if ($p->peminjam_role === 'dosen') {
                                                if ($p->kaprodi_approved_by === null) $pendingRoles[] = 'Kaprodi';
                                            } else {
                                                if ($p->admin_approved_by === null) $pendingRoles[] = 'Admin';
                                            }
                                        }
                                        $pendingStr = implode(' & ', $pendingRoles);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Disetujui Ka Lab{{ !empty($pendingStr) ? ', Menunggu ' . $pendingStr : '' }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        Menunggu Persetujuan
                                    </span>
                                @endif

                            @elseif($p->status == 'menunggu_verifikasi')

                                <span class="badge bg-purple-100 text-purple-700">
                                    Menunggu Verifikasi
                                </span>

                            @elseif($p->status == 'dipinjam')

                                <span class="badge badge-info">
                                    Sedang Dipinjam
                                </span>

                            @elseif($p->status == 'selesai')

                                <span class="badge badge-success">
                                    Selesai
                                </span>

                            @elseif($p->status == 'ditolak')

                                <div class="flex flex-col gap-1">
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                    @if($p->rejected_reason)
                                        <div class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-lg border border-red-100 max-w-xs">
                                            <span class="font-semibold">Alasan:</span> {{ $p->rejected_reason }}
                                        </div>
                                    @endif
                                </div>

                            @endif

                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button
                                    type="button"
                                    onclick="showDetail(
                                        '{{ $p->kode_peminjaman }}',
                                        '{{ addslashes($p->nama_peminjam) }}',
                                        '{{ $p->item_name }}',
                                        '{{ $p->jumlah }}',
                                        '{{ $p->tanggal_pinjam->format('d M Y') }}',
                                        '{{ $p->tanggal_kembali->format('d M Y') }}',
                                        '{{ $p->status }}',
                                        '{{ addslashes($p->keperluan) }}',
                                        '{{ $p->surat_keterangan ? asset('storage/' . $p->surat_keterangan) : '' }}'
                                    )"
                                    class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition flex items-center justify-center"
                                    title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if($p->status == 'pending' && $p->kalab_approved_by === null)
                                    <button
                                        type="button"
                                        onclick="showApproveModal(
                                            {{ $p->id }},
                                            '{{ addslashes($p->keperluan ?? '') }}',
                                            '{{ addslashes($p->nama_peminjam) }}',
                                            '{{ addslashes($p->item_name) }}',
                                            {{ $p->jumlah }},
                                            '{{ $p->surat_keterangan ? asset('storage/' . $p->surat_keterangan) : '' }}'
                                        )"
                                        class="w-9 h-9 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition flex items-center justify-center"
                                        title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <button
                                        type="button"
                                        onclick="if(confirm('Apakah Anda yakin ingin menolak peminjaman ini?')) document.getElementById('reject-form-{{ $p->id }}').submit()"
                                        class="w-9 h-9 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition flex items-center justify-center"
                                        title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif

                                @if($p->status == 'menunggu_verifikasi')
                                    <button
                                        type="button"
                                        onclick="showCompleteReturnModal(
                                            {{ $p->id }},
                                            '{{ addslashes($p->nama_peminjam) }}',
                                            '{{ addslashes($p->item_name) }}',
                                            {{ $p->jumlah }}
                                        )"
                                        class="w-9 h-9 rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 transition flex items-center justify-center"
                                        title="Verifikasi Pengembalian">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="7" class="py-12 text-center">

                            <div class="flex flex-col items-center">

                                <i class="fas fa-check-circle text-4xl text-slate-300 mb-3"></i>

                                <p class="font-semibold text-[#1E2B4A]">
                                    Tidak Ada Pengajuan
                                </p>

                                <p class="text-sm text-slate-500">
                                    Semua pengajuan telah diproses.
                                </p>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </x-table>

        </form>

    </div>

    {{-- Hidden Reject Forms --}}
    @foreach($peminjaman as $p)

        <form id="reject-form-{{ $p->id }}"
              action="{{ route('kalab.persetujuan.reject', $p->id) }}"
              method="POST"
              class="hidden">

            @csrf

            <input type="hidden"
                   name="alasan"
                   value="Ditolak secara individual oleh Kepala Laboratorium">

        </form>

    @endforeach

    {{-- Hidden Approve Forms --}}
    @foreach($peminjaman as $p)
        <form id="approve-form-{{ $p->id }}"
              action="{{ route('kalab.persetujuan.approve', $p->id) }}"
              method="POST"
              class="hidden">
            @csrf
            <input type="hidden" name="keperluan" id="approve-keperluan-{{ $p->id }}" value="">
        </form>

        <form id="complete-return-form-{{ $p->id }}"
              action="{{ route('kalab.peminjaman.complete-return', $p->id) }}"
              method="POST"
              class="hidden">
            @csrf
            <input type="hidden" name="kondisi_kembali" id="complete-return-kondisi-{{ $p->id }}" value="baik">
            <input type="hidden" name="catatan_kondisi" id="complete-return-catatan-{{ $p->id }}" value="">
        </form>
    @endforeach

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
            <p id="detail_keperluan"></p>
        </div>

        <div class="col-span-2" id="detail_surat_container">
            <p class="text-slate-500">Surat Keterangan</p>
            <a id="detail_surat_link" href="#" target="_blank" class="text-blue-600 font-semibold hover:underline flex items-center gap-1.5 mt-0.5">
                <i class="fas fa-file-download"></i> Lihat Lampiran Surat Keterangan
            </a>
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

<script>

function showDetail(
    kode,
    user,
    alat,
    jumlah,
    pinjam,
    kembali,
    status,
    keperluan,
    suratKeterangan
){

    document.getElementById('detail_kode').innerText = kode;
    document.getElementById('detail_user').innerText = user;
    document.getElementById('detail_alat').innerText = alat;
    document.getElementById('detail_jumlah').innerText = jumlah + ' Unit';
    document.getElementById('detail_pinjam').innerText = pinjam;
    document.getElementById('detail_kembali').innerText = kembali;
    document.getElementById('detail_status').innerText = status;
    document.getElementById('detail_keperluan').innerText = keperluan;

    const docContainer = document.getElementById('detail_surat_container');
    const docLink = document.getElementById('detail_surat_link');
    if (suratKeterangan) {
        docContainer.classList.remove('hidden');
        docLink.href = suratKeterangan;
    } else {
        docContainer.classList.add('hidden');
    }

    window.dispatchEvent(
        new CustomEvent('open-modal-detail-peminjaman')
    );

}

</script>

{{-- Approve Modal --}}
<div id="approve-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Setujui Peminjaman</h3>
        </div>

        <div class="mb-4 p-3 rounded-xl" style="background:#F5F8FF;border:1px solid #EBF3FD;">
            <p class="text-xs text-slate-500 mb-1">Peminjam</p>
            <p id="approve-user" class="text-sm font-semibold text-[#1E2B4A]"></p>
            <p class="text-xs text-slate-500 mt-2 mb-1">Alat & Jumlah</p>
            <p id="approve-alat" class="text-sm font-semibold text-[#1E2B4A]"></p>
        </div>

        <div class="mb-4 hidden" id="approve-surat-container">
            <p class="text-xs text-slate-500 mb-1">Surat Keterangan</p>
            <a id="approve-surat-link" href="#" target="_blank" class="text-blue-600 font-semibold text-xs hover:underline flex items-center gap-1">
                <i class="fas fa-file-download"></i> Lihat Lampiran Surat Keterangan
            </a>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Keperluan <span class="text-slate-400">(bisa diubah)</span></label>
            <input type="text" id="approve-keperluan-input"
                   class="inp w-full"
                   placeholder="Keperluan peminjaman...">
            <p class="text-xs mt-1 text-slate-400">Kosongkan untuk menggunakan keperluan asli dari dosen.</p>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
                    onclick="closeApproveModal()">
                Batal
            </button>
            <button type="button"
                    class="flex-1 px-4 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition font-semibold"
                    onclick="submitApprove()">
                <i class="fas fa-check mr-1"></i> Setujui
            </button>
        </div>
    </div>
</div>

<div id="complete-return-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Tandai Pengembalian Selesai</h3>
        </div>

        <div class="mb-4 p-3 rounded-xl" style="background:#F5F8FF;border:1px solid #EBF3FD;">
            <p class="text-xs text-slate-500 mb-1">Peminjam</p>
            <p id="complete-return-user" class="text-sm font-semibold text-[#1E2B4A]"></p>
            <p class="text-xs text-slate-500 mt-2 mb-1">Alat & Jumlah</p>
            <p id="complete-return-alat" class="text-sm font-semibold text-[#1E2B4A]"></p>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kondisi saat kembali</label>
            <select id="complete-return-kondisi-input" class="inp w-full">
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Catatan</label>
            <textarea id="complete-return-catatan-input" rows="3" class="inp w-full" placeholder="Catatan pengembalian..."></textarea>
        </div>

        <div class="flex gap-3">
            <button type="button"
                    class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition"
                    onclick="closeCompleteReturnModal()">
                Batal
            </button>
            <button type="button"
                    class="flex-1 px-4 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition font-semibold"
                    onclick="submitCompleteReturn()">
                <i class="fas fa-check mr-1"></i> Selesai
            </button>
        </div>
    </div>
</div>

<script>
let approvePeminjamanId = null;
let completeReturnPeminjamanId = null;

function showApproveModal(id, keperluan, user, alat, jumlah, suratKeterangan) {
    approvePeminjamanId = id;
    document.getElementById('approve-user').innerText = user;
    document.getElementById('approve-alat').innerText = alat + ' — ' + jumlah + ' Unit';
    document.getElementById('approve-keperluan-input').value = keperluan;

    const approveSuratContainer = document.getElementById('approve-surat-container');
    const approveSuratLink = document.getElementById('approve-surat-link');
    if (suratKeterangan) {
        approveSuratContainer.classList.remove('hidden');
        approveSuratLink.href = suratKeterangan;
    } else {
        approveSuratContainer.classList.add('hidden');
    }

    document.getElementById('approve-modal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
    approvePeminjamanId = null;
}

function submitApprove() {
    if (!approvePeminjamanId) return;

    var keperluanInput = document.getElementById('approve-keperluan-input');
    var keperluanField = document.getElementById('approve-keperluan-' + approvePeminjamanId);
    keperluanField.value = keperluanInput.value;
    document.getElementById('approve-form-' + approvePeminjamanId).submit();
}

function showCompleteReturnModal(id, user, alat, jumlah) {
    completeReturnPeminjamanId = id;
    document.getElementById('complete-return-user').innerText = user;
    document.getElementById('complete-return-alat').innerText = alat + ' — ' + jumlah + ' Unit';
    document.getElementById('complete-return-kondisi-input').value = 'baik';
    document.getElementById('complete-return-catatan-input').value = '';
    document.getElementById('complete-return-modal').classList.remove('hidden');
}

function closeCompleteReturnModal() {
    document.getElementById('complete-return-modal').classList.add('hidden');
    completeReturnPeminjamanId = null;
}

function submitCompleteReturn() {
    if (!completeReturnPeminjamanId) return;

    const kondisi = document.getElementById('complete-return-kondisi-input').value;
    const catatan = document.getElementById('complete-return-catatan-input').value;
    document.getElementById('complete-return-kondisi-' + completeReturnPeminjamanId).value = kondisi;
    document.getElementById('complete-return-catatan-' + completeReturnPeminjamanId).value = catatan;
    document.getElementById('complete-return-form-' + completeReturnPeminjamanId).submit();
}

// Close modal on outside click
document.getElementById('approve-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeCompleteReturnModal();
    }
});

// Close complete return modal on outside click
document.getElementById('complete-return-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCompleteReturnModal();
});
</script>

@endsection