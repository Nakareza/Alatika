@extends('layouts.admin')

@section('title', 'Edit Tool Set')

@section('content')

<div class="w-full px-6" x-data="{ components: {{ json_encode($toolSet->details) }} }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1E2B4A]">
                Edit Paket Tool Set
            </h2>
            <p class="text-slate-500 mt-1">
                Perbarui data paket tool set dan komponen pendukungnya
            </p>
        </div>
    </div>

    <!-- Edit Form Container -->
    <div class="card overflow-hidden">
        <form action="{{ route('admin.toolset.update', $toolSet->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="px-6 py-5 border-b border-slate-200 bg-[#F8FBFF]">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-[#EBF3FD] flex items-center justify-center text-[#185FA5] text-lg">
                        <i class="fas fa-toolbox"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-[#1E2B4A]">
                            Detail Paket Tool Set: {{ $toolSet->nama_tool_set }}
                        </h3>
                        <p class="text-sm text-slate-500">
                            Ubah informasi paket dan sinkronkan daftar komponen di bawah
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Validation Errors / Alerts -->
                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama ToolSet --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Nama Tool Set Paket <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_tool_set" value="{{ old('nama_tool_set', $toolSet->nama_tool_set) }}" placeholder="Contoh: Paket Solder & Toolkit" class="inp w-full" required>
                        @error('nama_tool_set') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kode ToolSet --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Kode Tool Set <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_tool_set" value="{{ old('kode_tool_set', $toolSet->kode_tool_set) }}" placeholder="Contoh: TLS-001" class="inp w-full" required>
                        @error('kode_tool_set') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Kategori Paket <span class="text-red-500">*</span></label>
                        <select name="kategori_id" class="inp" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id', $toolSet->kategori_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Stok Paket <span class="text-red-500">*</span></label>
                        <input type="number" min="{{ $activeBorrowedCount }}" name="stok" value="{{ old('stok', $toolSet->stok) }}" placeholder="1" class="inp w-full" required>
                        <p class="text-xs text-slate-400 mt-1">
                            Jumlah paket toolset. Minimal: {{ $activeBorrowedCount }} (karena {{ $activeBorrowedCount }} sedang dipinjam).
                        </p>
                        @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Lokasi Penyimpanan</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $toolSet->lokasi) }}" placeholder="Contoh: Lab TI Lemari B" class="inp w-full">
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Kondisi Paket</label>
                        <select name="kondisi" class="inp" required>
                            <option value="baik" {{ old('kondisi', $toolSet->kondisi) === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="perlu_pengecekan" {{ old('kondisi', $toolSet->kondisi) === 'perlu_pengecekan' ? 'selected' : '' }}>Perlu Pengecekan</option>
                            <option value="rusak" {{ old('kondisi', $toolSet->kondisi) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Tahun Pengadaan</label>
                        <input type="number" name="tahun" value="{{ old('tahun', $toolSet->tahun) }}" class="inp w-full">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-semibold mb-2 text-slate-700">Keterangan / Catatan Paket</label>
                    <textarea name="keterangan" rows="3" placeholder="Contoh: Box toolkit warna hitam merk Tekiro..." class="inp w-full resize-none">{{ old('keterangan', $toolSet->keterangan) }}</textarea>
                </div>

                <!-- Dynamic Components List -->
                <div class="border-t border-slate-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4 class="font-bold text-[#1E2B4A] text-md">Komponen Pendukung Paket</h4>
                            <p class="text-xs text-slate-500">Daftarkan komponen yang ada di dalam set ini</p>
                        </div>
                        <button type="button" @click="components.push({ nama_komponen: '', jumlah: 1, satuan: 'Buah', keterangan: '' })" class="btn btn-secondary px-3 py-1.5 text-xs flex items-center gap-1">
                            <i class="fas fa-plus"></i> Tambah Komponen
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-slate-100 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-5/12">Nama Komponen <span class="text-red-500">*</span></th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Jumlah <span class="text-red-500">*</span></th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Satuan <span class="text-red-500">*</span></th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Merk / Keterangan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase w-1/12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(comp, index) in components" :key="index">
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-2">
                                            <input type="text" :name="`components[${index}][nama_komponen]`" x-model="comp.nama_komponen" placeholder="Contoh: Solder 60W" class="inp py-1 px-2 w-full text-xs" required>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" min="1" :name="`components[${index}][jumlah]`" x-model="comp.jumlah" class="inp py-1 px-2 w-full text-xs" required>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" :name="`components[${index}][satuan]`" x-model="comp.satuan" placeholder="Buah/Pcs" class="inp py-1 px-2 w-full text-xs" required>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="text" :name="`components[${index}][keterangan]`" x-model="comp.keterangan" placeholder="Merk/Tipe" class="inp py-1 px-2 w-full text-xs">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" @click="if(components.length > 1) { components.splice(index, 1) } else { alert('Minimal harus ada 1 komponen!') }" class="text-rose-600 hover:text-rose-800 transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="p-4 rounded-xl border border-indigo-100 bg-indigo-50">
                    <div class="flex gap-3">
                        <i class="fas fa-circle-info text-indigo-600 mt-1"></i>
                        <div>
                            <p class="font-semibold text-indigo-900 font-sans">Informasi Tambahan</p>
                            <ul class="text-sm text-indigo-700 mt-1 space-y-1">
                                <li>• Mengubah stok total akan menghitung ulang stok tersedia secara otomatis (Stok Tersedia = Stok Total - Stok Sedang Dipinjam)</li>
                                <li>• Semua perubahan komponen akan langsung disinkronkan ke dalam sistem</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-200 px-6 py-4 bg-slate-50 flex justify-end gap-3">
                <a href="{{ route('admin.alat') }}" class="btn btn-secondary px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100 transition text-sm">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
