@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')

    @if(session('success'))
    <div class="mb-4 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <x-table title="Riwayat Peminjaman">

        <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">

            <tr>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Kode / Tanggal
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Alat
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Deadline / Kembali
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Status
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Aksi / Info
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($riwayat as $p)

            <tr class="hover:bg-[#F8FBFF] transition">

                {{-- Kode --}}
                <td class="px-6 py-5">

                    <div class="font-bold text-[#1E2B4A]">
                        {{ $p->kode_peminjaman }}
                    </div>

                    <div class="text-xs text-slate-400 mt-1">
                        {{ $p->updated_at->format('d M Y H:i') }}
                    </div>

                </td>

                {{-- Alat --}}
                <td class="px-6 py-5">

                    <div class="font-semibold text-[#1E2B4A]">
                        {{ $p->borrowable_type === 'App\Models\ToolSet' ? ($p->borrowable->nama_tool_set ?? '-') : ($p->alat->nama ?? '-') }}
                        @if($p->borrowable_type === 'App\Models\ToolSet')
                            <span class="inline-block px-1.5 py-0.5 ml-1 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Tool Set</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500 mt-1">
                        {{ $p->jumlah }} {{ $p->borrowable_type === 'App\Models\ToolSet' ? 'Set' : 'Unit' }}
                    </div>

                    @if($p->borrowable_type === 'App\Models\ToolSet')
                        <button type="button" 
                                @click="$dispatch('open-modal-components-{{ $p->id }}')"
                                class="mt-1.5 inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                            <i class="fas fa-list text-[10px]"></i> Lihat Komponen
                        </button>
                    @endif

                </td>

                {{-- Deadline --}}
                <td class="px-6 py-5">

                    <div class="font-medium text-[#1E2B4A]">
                        {{ $p->tanggal_kembali->format('d M Y') }}
                    </div>

                    @if($p->tanggal_dikembalikan && $p->status === 'selesai')

                        <div class="text-xs text-emerald-600 mt-1">
                            Kembali:
                            {{ $p->tanggal_dikembalikan->format('d M Y') }}
                        </div>

                    @endif

                </td>

                {{-- Status --}}
                <td class="px-6 py-5 text-center">

                    <span class="badge {{ $p->status_config['color'] }}">
                        {{ $p->status_label }}
                    </span>

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5 text-center">

                    @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)

                        <a href="{{ $p->foto_bukti_url }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 text-[#185FA5] hover:text-[#378ADD] font-semibold text-sm transition">

                            <i class="fas fa-image"></i>

                            Lihat Foto Bukti

                        </a>

                    @elseif($p->status === 'dipinjam')

                        <div class="flex flex-col items-center gap-2">
                            <button
                                type="button"
                                @click="$dispatch('open-modal-kembali-{{ $p->id }}')"
                                class="w-full px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition font-semibold text-xs flex items-center justify-center gap-1.5 shadow-sm border border-emerald-100">
                                <i class="fas fa-undo"></i>
                                Kembalikan Alat
                            </button>

                            <div class="flex flex-col items-center gap-1 text-[10px] text-slate-400">
                                <span>Atau via Telegram:</span>
                                <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[9px]">
                                    /kembali {{ $p->kode_peminjaman }}
                                </code>
                            </div>
                        </div>

                    @elseif($p->status === 'ditolak')

                        <div class="inline-flex items-start gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-red-50 text-red-600 border border-red-100 max-w-[200px] text-left mx-auto">
                            <i class="fas fa-info-circle shrink-0 mt-0.5"></i>
                            <span class="break-words">Alasan: {{ $p->rejected_reason ?? 'Tidak ada alasan' }}</span>
                        </div>

                    @else

                        <span class="text-slate-300 text-lg">
                            —
                        </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5" class="py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div class="w-16 h-16 rounded-full bg-[#EBF3FD] flex items-center justify-center mb-4">

                            <i class="fas fa-inbox text-2xl text-[#B5D4F4]"></i>

                        </div>

                        <h3 class="font-bold text-lg text-[#1E2B4A]">

                            Belum Ada Riwayat Peminjaman

                        </h3>

                        <p class="text-slate-500 mt-1">

                            Silakan ajukan peminjaman alat terlebih dahulu.

                        </p>

                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                        class="btn btn-primary mt-5">

                            <i class="fas fa-plus"></i>

                            Ajukan Peminjaman

                        </a>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>

    {{-- Modals for returning tools --}}
    @foreach($riwayat as $p)
        @if($p->status === 'dipinjam')
        <x-modal name="kembali-{{ $p->id }}" title="Kirim Pengembalian Alat" size="lg">
            <form action="{{ route('mahasiswa.peminjaman.kembalikan', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4">
                @csrf
                <div>
                    <div class="p-4 rounded-xl text-left text-sm mb-4" style="background:#f5f8ff;border:1px solid #ebf3fd;color:#1e2b4a;">
                        <p class="font-semibold text-xs uppercase tracking-wider text-slate-500 mb-2">Detail Peminjaman</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <div>
                                <span class="text-xs text-slate-400">Nama Alat/Tool Set:</span>
                                <p class="font-semibold text-xs">{{ $p->borrowable_type === 'App\Models\ToolSet' ? ($p->borrowable->nama_tool_set ?? '-') : ($p->alat->nama ?? '-') }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400">Jumlah:</span>
                                <p class="font-semibold text-xs">{{ $p->jumlah }} {{ $p->borrowable_type === 'App\Models\ToolSet' ? 'Set' : 'Unit' }}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-slate-400">Kode Peminjaman:</span>
                                <p class="font-mono font-bold text-[#185FA5]">{{ $p->kode_peminjaman }}</p>
                            </div>
                        </div>
                    </div>

                    <label class="form-label font-semibold text-slate-700">
                        Foto Bukti Pengembalian <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="foto_bukti_kembali" required class="inp w-full" accept="image/*">
                    <p class="text-xs text-slate-400 mt-1.5">
                        Silakan unggah foto kondisi fisik alat yang dikembalikan secara lengkap (kabel, komponen, dll). Maksimal 5MB.
                    </p>
                </div>
                
                <x-slot name="footer">
                    <button type="button" @click="$dispatch('close-modal-kembali-{{ $p->id }}')" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 btn btn-primary font-medium">
                        Kirim Pengembalian
                    </button>
                </x-slot>
            </form>
        </x-modal>
        @endif
        
        {{-- Modal Detail Komponen Tool Set --}}
        @if($p->borrowable_type === 'App\Models\ToolSet' && $p->borrowable)
        <x-modal name="components-{{ $p->id }}" title="Detail Komponen Tool Set" size="md">
            <div class="mt-4 space-y-3">
                <p class="text-sm font-semibold text-[#1E2B4A]">{{ $p->borrowable->nama_tool_set }} ({{ $p->borrowable->kode_tool_set }})</p>
                <div class="border border-slate-100 rounded-xl overflow-hidden text-sm">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 font-semibold text-slate-500">Nama Komponen</th>
                                <th class="px-4 py-2 font-semibold text-slate-500 text-center" style="width: 80px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($p->borrowable->details as $d)
                            <tr>
                                <td class="px-4 py-2.5 text-slate-700">{{ $d->nama_komponen }}</td>
                                <td class="px-4 py-2.5 text-slate-600 text-center font-semibold">{{ $d->jumlah }} {{ $d->satuan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal-components-{{ $p->id }}')" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium">
                    Tutup
                </button>
            </x-slot>
        </x-modal>
        @endif
    @endforeach

@endsection