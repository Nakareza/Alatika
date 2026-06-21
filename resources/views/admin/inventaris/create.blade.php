@extends('layouts.admin')

@section('title', 'Tambah Alat')

@section('content')

<div class="w-full px-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1E2B4A]">
                Tambah Alat
            </h2>

            <p class="text-slate-500 mt-1">
                Tambahkan data alat laboratorium baru
            </p>
        </div>

        
    </div>

    <div class="card overflow-hidden">

        <div class="px-6 py-5 border-b border-slate-200 bg-[#F8FBFF]">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-[#EBF3FD] flex items-center justify-center">
                    <i class="fas fa-box-open text-[#185FA5] text-lg"></i>
                </div>

                <div>
                    <h3 class="font-bold text-lg text-[#1E2B4A]">
                        Informasi Alat
                    </h3>

                    <p class="text-sm text-slate-500">
                        Lengkapi data alat laboratorium
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.alat.store') }}" method="POST">

            @csrf

            <div class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">
                            Nama Alat <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            value="{{ old('nama') }}"
                            placeholder="Contoh: Arduino Uno R3"
                            class="inp w-full">

                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kode --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">
                            Kode Barang <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode"
                            value="{{ old('kode') }}"
                            placeholder="Contoh: ARD-001"
                            class="inp w-full">

                        @error('kode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">
                            Kategori <span class="text-red-500">*</span>
                        </label>

                        <select name="kategori" id="kategoriSelect" class="inp">
                            <option value="">Pilih Kategori</option>

                            @foreach($kategoriOptions as $kategori)
                                <option value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'selected' : '' }}>
                                    {{ $kategori }}
                                </option>
                            @endforeach

                            <option value="__new" {{ old('kategori') == '__new' ? 'selected' : '' }}>+ Tambah kategori baru</option>
                        </select>

                        <input type="text" name="kategori_baru" id="kategoriBaru"
                            class="inp mt-2 hidden"
                            value="{{ old('kategori_baru') }}"
                            placeholder="Masukkan nama kategori baru">

                        @error('kategori')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                        @error('kategori_baru')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">
                            Lokasi Penyimpanan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            name="lokasi"
                            value="{{ old('lokasi') }}"
                            placeholder="Rak A1"
                            class="inp w-full">
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-semibold mb-2 text-slate-700">
                            Stok Total <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="number"
                            min="1"
                            name="stok_total"
                            value="{{ old('stok_total') }}"
                            placeholder="0"
                            class="inp w-full">

                        <p class="text-xs text-slate-400 mt-1">
                            Stok tersedia akan otomatis mengikuti stok total
                        </p>
                    </div>

                </div>

                {{-- Deskripsi --}}
                <div class="mt-6">

                    <label class="block text-sm font-semibold mb-2 text-slate-700">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="5"
                        placeholder="Tambahkan deskripsi alat..."
                        class="inp w-full resize-none">{{ old('deskripsi') }}</textarea>

                </div>

                {{-- Info Box --}}
                <div class="mt-6 p-4 rounded-xl border border-blue-100 bg-blue-50">

                    <div class="flex gap-3">

                        <i class="fas fa-circle-info text-blue-600 mt-1"></i>

                        <div>

                            <p class="font-semibold text-blue-900">
                                Informasi Otomatis
                            </p>

                            <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                <li>• Stok tersedia = stok total</li>
                                <li>• Status alat otomatis "Tersedia"</li>
                                <li>• Alat langsung muncul di daftar alat</li>
                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            <div class="border-t border-slate-200 px-6 py-4 bg-slate-50 flex justify-end gap-3">

                <a href="{{ route('admin.alat') }}"
                class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100 transition">

                    Batal

                </a>

                <button
                    type="submit"
                    class="btn btn-primary flex items-center justify-center gap-2 flex-1 sm:flex-none">

                    <i class="fas fa-save mr-2"></i>
                    Simpan Alat

                </button>

            </div>

        </form>

    </div>

</div>

@endsection

@push('scripts')
<script>
    const kategoriSelect = document.getElementById('kategoriSelect');
    const kategoriBaru = document.getElementById('kategoriBaru');

    function toggleKategori() {
        if (kategoriSelect.value === '__new') {
            kategoriBaru.classList.remove('hidden');
            kategoriBaru.setAttribute('required', 'required');
            kategoriSelect.removeAttribute('required');
        } else {
            kategoriBaru.classList.add('hidden');
            kategoriBaru.removeAttribute('required');
            kategoriBaru.value = '';
            kategoriSelect.setAttribute('required', 'required');
        }
    }

    kategoriSelect.addEventListener('change', toggleKategori);

    // Trigger on page load (e.g. after validation error)
    toggleKategori();
</script>
@endpush