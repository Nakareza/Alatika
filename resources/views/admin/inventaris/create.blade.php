@extends('layouts.admin')

@section('title', 'Tambah Inventaris')

@section('content')

<div class="w-full px-6" x-data="{ type: 'alat', components: [{ nama_komponen: '', jumlah: 1, satuan: 'Buah', keterangan: '' }] }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1E2B4A]">
                Tambah Inventaris Baru
            </h2>
            <p class="text-slate-500 mt-1">
                Tambahkan data alat satuan atau paket tool set laboratorium baru
            </p>
        </div>
    </div>

    <!-- Type Selection -->
    <div class="card p-6 mb-6">
        <label class="block text-sm font-semibold mb-3 text-slate-700">Pilih Jenis Inventaris</label>
        <div class="grid grid-cols-2 gap-4">
            <button type="button" @click="type = 'alat'" :class="type === 'alat' ? 'border-2 border-indigo-600 bg-indigo-50/50 text-indigo-700' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'" class="p-4 rounded-2xl flex flex-col items-center justify-center gap-2 transition">
                <i class="fas fa-boxes text-2xl"></i>
                <span class="font-bold text-sm">Alat Unit</span>
                <span class="text-xs text-slate-500">Alat satuan (misal: Arduino, Multimeter, Monitor)</span>
            </button>
            <button type="button" @click="type = 'toolset'" :class="type === 'toolset' ? 'border-2 border-indigo-600 bg-indigo-50/50 text-indigo-700' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'" class="p-4 rounded-2xl flex flex-col items-center justify-center gap-2 transition">
                <i class="fas fa-toolbox text-2xl"></i>
                <span class="font-bold text-sm">Tool Set Paket</span>
                <span class="text-xs text-slate-500">Paket alat lengkap dengan komponen (misal: Tool kit solder)</span>
            </button>
        </div>
    </div>

    <!-- Form Container -->
    <div class="card overflow-hidden">
        <form action="{{ route('admin.alat.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" :value="type">

            <div class="px-6 py-5 border-b border-slate-200 bg-[#F8FBFF]">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-[#EBF3FD] flex items-center justify-center text-[#185FA5] text-lg">
                        <i :class="type === 'alat' ? 'fas fa-boxes' : 'fas fa-toolbox'"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-[#1E2B4A]">
                            Detail Informasi <span x-text="type === 'alat' ? 'Alat Unit' : 'Tool Set Paket'"></span>
                        </h3>
                        <p class="text-sm text-slate-500">
                            Lengkapi data inventaris di bawah ini
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- ==========================================
                     ALAT UNIT FORM FIELDS
                     ========================================== -->
                <div x-show="type === 'alat'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Nama Alat Unit <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Arduino Uno R3" class="inp w-full" :required="type === 'alat'">
                            @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kode --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kode Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="kode" value="{{ old('kode') }}" placeholder="Contoh: ARD-001" class="inp w-full" :required="type === 'alat'">
                            @error('kode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kategori Alat <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="kategoriSelectAlat" class="inp" :required="type === 'alat'">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                                <option value="__new">+ Tambah kategori baru</option>
                            </select>
                            <input type="text" name="kategori_baru" id="kategoriBaruAlat" class="inp mt-2 hidden" value="{{ old('kategori_baru') }}" placeholder="Masukkan nama kategori baru">
                            @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('kategori_baru') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Stok Total <span class="text-red-500">*</span></label>
                            <input type="number" min="1" name="stok_total" value="{{ old('stok_total') }}" placeholder="0" class="inp w-full" :required="type === 'alat'">
                            <p class="text-xs text-slate-400 mt-1">Stok tersedia akan otomatis mengikuti stok total</p>
                            @error('stok_total') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Lokasi Lab / Tempat Penyimpanan</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', 'Lab TI') }}" placeholder="Contoh: Ruang Lab TI Lemari A" class="inp w-full">
                        </div>

                        {{-- Kondisi --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kondisi Fisik</label>
                            <select name="kondisi" class="inp" :required="type === 'alat'">
                                <option value="baik">Baik</option>
                                <option value="perlu_pengecekan">Perlu Pengecekan</option>
                                <option value="rusak">Rusak</option>
                            </select>
                        </div>

                        {{-- Program Studi / Kepemilikan --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kepemilikan / Program Studi</label>
                            <select name="program_studi" class="inp">
                                <option value="">Umum (Bisa dipinjam Mahasiswa & Dosen)</option>
                                <option value="D3 TI / D4 TRK" {{ old('program_studi') === 'D3 TI / D4 TRK' ? 'selected' : '' }}>Prodi (D3 TI / D4 TRK - Alat Khusus Mahasiswa)</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1.5">Pilih "Prodi" jika alat milik laboratorium/program studi tertentu.</p>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Deskripsi / Spesifikasi Alat</label>
                        <textarea name="deskripsi" rows="4" placeholder="Tambahkan deskripsi alat..." class="inp w-full resize-none">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <!-- ==========================================
                     TOOL SET PAKET FORM FIELDS
                     ========================================== -->
                <div x-show="type === 'toolset'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Nama ToolSet --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Nama Tool Set Paket <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tool_set" value="{{ old('nama_tool_set') }}" placeholder="Contoh: Paket Solder & Toolkit" class="inp w-full" :required="type === 'toolset'">
                            @error('nama_tool_set') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kode ToolSet --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kode Tool Set <span class="text-red-500">*</span></label>
                            <input type="text" name="kode_tool_set" value="{{ old('kode_tool_set') }}" placeholder="Contoh: TLS-001" class="inp w-full" :required="type === 'toolset'">
                            @error('kode_tool_set') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kategori Paket <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="kategoriSelectToolset" class="inp" :required="type === 'toolset'">
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Stok Paket <span class="text-red-500">*</span></label>
                            <input type="number" min="1" name="stok" value="{{ old('stok', 1) }}" placeholder="1" class="inp w-full" :required="type === 'toolset'">
                            <p class="text-xs text-slate-400 mt-1">Jumlah paket toolset yang tersedia di lab</p>
                            @error('stok') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" value="{{ old('lokasi', 'Lab TI') }}" placeholder="Contoh: Lab TI Lemari B" class="inp w-full">
                        </div>

                        {{-- Kondisi --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Kondisi Paket</label>
                            <select name="kondisi" class="inp" :required="type === 'toolset'">
                                <option value="baik">Baik</option>
                                <option value="perlu_pengecekan">Perlu Pengecekan</option>
                                <option value="rusak">Rusak</option>
                            </select>
                        </div>

                        {{-- Tahun --}}
                        <div>
                            <label class="block text-sm font-semibold mb-2 text-slate-700">Tahun Pengadaan</label>
                            <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" class="inp w-full">
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">Keterangan / Catatan Paket</label>
                        <textarea name="keterangan" rows="3" placeholder="Contoh: Box toolkit warna hitam merk Tekiro..." class="inp w-full resize-none">{{ old('keterangan') }}</textarea>
                    </div>

                    <!-- Dynamic Components List -->
                    <div class="border-t border-slate-100 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-[#1E2B4A] text-md">Komponen Pendukung Paket</h4>
                                <p class="text-xs text-slate-500">Daftarkan alat-alat unit yang menjadi bagian dari paket tool set ini</p>
                            </div>
                            <button type="button" @click="components.push({ nama_komponen: '', jumlah: 1, satuan: 'Buah', keterangan: '' })" class="btn btn-secondary px-3 py-1.5 text-xs flex items-center gap-1">
                                <i class="fas fa-plus"></i> Tambah Komponen
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-slate-100 rounded-xl">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase w-5/12">Nama Komponen <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Jumlah <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Satuan <span class="text-red-500">*</span></th>
                                        <th class="px-4 py-2 text-left text-xs font-bold text-slate-500 uppercase w-2/12">Merk / Keterangan</th>
                                        <th class="px-4 py-2 text-center text-xs font-bold text-slate-500 uppercase w-1/12">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(comp, index) in components" :key="index">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="px-3 py-2">
                                                <input type="text" :name="`components[${index}][nama_komponen]`" x-model="comp.nama_komponen" placeholder="Contoh: Solder 60W" class="inp py-1 px-2 w-full text-xs" :required="type === 'toolset'">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" min="1" :name="`components[${index}][jumlah]`" x-model="comp.jumlah" class="inp py-1 px-2 w-full text-xs" :required="type === 'toolset'">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="`components[${index}][satuan]`" x-model="comp.satuan" placeholder="Buah/Pcs" class="inp py-1 px-2 w-full text-xs" :required="type === 'toolset'">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text" :name="`components[${index}][keterangan]`" x-model="comp.keterangan" placeholder="Merk/Tipe" class="inp py-1 px-2 w-full text-xs">
                                            </td>
                                            <td class="px-3 py-2 text-center">
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
                </div>

                {{-- Info Box --}}
                <div class="p-4 rounded-xl border border-blue-100 bg-blue-50">
                    <div class="flex gap-3">
                        <i class="fas fa-circle-info text-blue-600 mt-1"></i>
                        <div>
                            <p class="font-semibold text-blue-900">Informasi Otomatis</p>
                            <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                <li>• Stok tersedia akan diset sama dengan stok total</li>
                                <li>• Kondisi awal diatur sesuai inputan</li>
                                <li>• Paket akan langsung muncul di tab masing-masing</li>
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
                    Simpan Inventaris
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const kategoriSelectAlat = document.getElementById('kategoriSelectAlat');
    const kategoriBaruAlat = document.getElementById('kategoriBaruAlat');

    function toggleKategoriAlat() {
        if (!kategoriSelectAlat) return;
        if (kategoriSelectAlat.value === '__new') {
            kategoriBaruAlat.classList.remove('hidden');
            kategoriBaruAlat.setAttribute('required', 'required');
            kategoriSelectAlat.removeAttribute('required');
        } else {
            kategoriBaruAlat.classList.add('hidden');
            kategoriBaruAlat.removeAttribute('required');
            kategoriBaruAlat.value = '';
            kategoriSelectAlat.setAttribute('required', 'required');
        }
    }

    if (kategoriSelectAlat) {
        kategoriSelectAlat.addEventListener('change', toggleKategoriAlat);
        toggleKategoriAlat();
    }
</script>
@endpush