@extends('layouts.kaprodi')

@section('title', 'Riwayat Peminjaman')

@section('content')

    <div class="card p-6 mb-6">
        <form method="GET">
            <div class="flex flex-col md:flex-row gap-4">
                {{-- Search --}}
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kode, peminjam, atau alat..."
                        class="inp pl-11 w-full">
                </div>

                {{-- Filter Status --}}
                <select name="status" class="inp md:w-52" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="dipinjam" {{ request('status')=='dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>

                {{-- Filter Role --}}
                <select name="role" class="inp md:w-52" onchange="this.form.submit()">
                    <option value="">Semua Peminjam</option>
                    <option value="mahasiswa" {{ request('role')=='mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ request('role')=='dosen' ? 'selected' : '' }}>Dosen</option>
                </select>

                {{-- Reset --}}
                <a href="{{ route('kaprodi.riwayat') }}" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition flex items-center justify-center">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden">
        {{-- Table Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b" style="border-color:#EBF3FD;">
            <div>
                <h2 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">Data Riwayat & Monitoring</h2>
                <p class="text-sm text-slate-500 mt-1">Semua data peminjaman oleh Mahasiswa dan Dosen.</p>
            </div>
            <div class="badge badge-info">
                <i class="fas fa-database mr-2"></i>
                {{ $peminjaman->total() }} Data
            </div>
        </div>

        {{-- Table --}}
        <x-table title="Data Riwayat Peminjaman">
            <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">
                <tr>
                    <th class="py-4 px-4 text-center text-xs font-bold uppercase text-slate-500">No</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Kode</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Peminjam</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Alat</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Jumlah</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Tanggal Pinjam</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Tanggal Kembali</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Status</th>
                    <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">Kondisi Alat</th>
                    <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#EBF3FD]">
                @forelse($peminjaman as $index => $p)
                <tr class="hover:bg-[#F8FBFF] transition">
                    <td class="px-4 py-5 text-center font-semibold text-slate-500">
                        {{ $peminjaman->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-5">
                        <span class="badge badge-info">{{ $p->kode_peminjaman }}</span>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-[#1E2B4A] text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($p->nama_peminjam,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-[#1E2B4A]">{{ $p->nama_peminjam }}</p>
                                <p class="text-xs text-slate-500">{{ $p->peminjam_role }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">{{ $p->item_name }}</td>
                    <td class="px-6 py-5">{{ $p->jumlah }} Unit</td>
                    <td class="px-6 py-5">{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                    <td class="px-6 py-5">{{ $p->tanggal_kembali->format('d M Y') }}</td>
                    <td class="px-6 py-5">
                        <div class="flex flex-col gap-1">
                            <span class="badge {{ $p->status_config['color'] }}">
                                <i class="fas {{ $p->status_config['icon'] }} mr-1"></i>
                                {{ $p->status_label }}
                            </span>
                            @if($p->status === 'ditolak' && $p->rejected_reason)
                                <div class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-lg border border-red-100 max-w-xs">
                                    <span class="font-semibold">Alasan:</span> {{ $p->rejected_reason }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        @if($p->alat->kondisi == 'baik')
                            <span class="badge badge-success">Baik</span>
                        @elseif($p->alat->kondisi == 'perlu_pengecekan')
                            <span class="badge badge-warning">Perlu Pengecekan</span>
                        @elseif($p->alat->kondisi == 'rusak')
                            <span class="badge badge-danger">Rusak</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-center">
                        <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('open-modal-detail-{{ $p->id }}'))"
                                class="w-10 h-10 rounded-xl bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D9EAFE] transition inline-flex items-center justify-center">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-20 h-20 rounded-2xl bg-[#F5F8FF] flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-4xl text-[#B5D4F4]"></i>
                            </div>
                            <h3 class="font-bold text-[#1E2B4A] mb-1">Belum Ada Riwayat</h3>
                            <p class="text-sm text-slate-500">Data peminjaman akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-table>
        <div class="p-6">
            {{ $peminjaman->links() }}
        </div>
    </div>

    @foreach($peminjaman as $p)
    <x-modal name="detail-{{ $p->id }}" title="Detail Riwayat Peminjaman" size="lg">
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-slate-500">Kode Peminjaman</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->kode_peminjaman }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Status</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->status_label }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Nama Peminjam</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->nama_peminjam }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Role</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->peminjam_role }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Nama Alat</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->item_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Jumlah</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->jumlah }} Unit</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Tanggal Pinjam</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->tanggal_pinjam->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Tanggal Kembali</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->tanggal_kembali->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Kondisi Alat</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ ucfirst(str_replace('_', ' ', $p->alat->kondisi)) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Lokasi</p>
                    <p class="font-semibold text-[#1E2B4A]">{{ $p->alat->lokasi ?? '-' }}</p>
                </div>
            </div>

            @if($p->keperluan)
            <div>
                <p class="text-xs text-slate-500 mb-2">Keperluan Peminjaman</p>
                <div class="bg-[#F8FBFF] border border-[#EBF3FD] rounded-xl p-4 text-sm text-[#1E2B4A]">
                    {{ $p->keperluan }}
                </div>
            </div>
            @endif
        </div>
        <x-slot:footer>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal-detail-{{ $p->id }}'))" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium">
                Tutup
            </button>
        </x-slot>
    </x-modal>
    @endforeach

@endsection
