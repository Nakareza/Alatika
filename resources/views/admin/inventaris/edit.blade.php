@extends('layouts.admin')

@section('title', 'Edit Inventaris')

@section('content')

<div class="w-full px-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1E2B4A]">
                Edit Inventaris
            </h2>

            <p class="text-slate-500 mt-1">
                Perbarui data alat laboratorium
            </p>
        </div>

        
    </div>

    @if($errors->any())
    <div class="mb-6 rounded-xl p-4 text-sm flex flex-col gap-2"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        @foreach($errors->all() as $error)
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $error }}</span>
            </div>
        @endforeach
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="card p-6">

        <form action="{{ route('admin.alat.update', $alat->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm mb-2">
                        Nama Alat
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama', $alat->nama) }}"
                        class="inp w-full">
                </div>

                <div>
                    <label class="block text-sm mb-2">
                        Kode Barang
                    </label>

                    <input
                        type="text"
                        name="kode"
                        value="{{ old('kode', $alat->kode) }}"
                        class="inp w-full">
                </div>

                <div>
                    <label class="block text-sm mb-2">
                        Kategori
                    </label>

                    <input
                        type="text"
                        name="kategori"
                        value="{{ old('kategori', $alat->kategori) }}"
                        class="inp w-full">
                </div>

                

                <div>
                    <label class="block text-sm mb-2 font-semibold text-slate-700">
                        Stok Total
                    </label>

                    <input
                        type="number"
                        name="stok_total"
                        value="{{ old('stok_total', $alat->stok_total) }}"
                        class="inp w-full bg-slate-100 cursor-not-allowed"
                        readonly>
                </div>

                <div>
                    <label class="block text-sm mb-2 font-semibold text-slate-700">
                        Jumlah Alat di-Maintenance
                    </label>

                    <input
                        type="number"
                        name="stok_maintenance"
                        value="{{ old('stok_maintenance', $alat->stok_maintenance) }}"
                        min="0"
                        max="{{ $alat->stok_total - $activeBorrowedCount }}"
                        class="inp w-full">
                    <p class="text-xs text-slate-400 mt-1">
                        Maksimal: {{ $alat->stok_total - $activeBorrowedCount }} unit ({{ $activeBorrowedCount }} sedang dipinjam).
                    </p>
                    @error('stok_maintenance')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Program Studi / Kepemilikan --}}
                <div>
                    <label class="block text-sm mb-2">
                        Kepemilikan / Program Studi
                    </label>

                    <select name="program_studi" class="inp w-full">
                        <option value="">Umum (Bisa dipinjam Mahasiswa & Dosen)</option>
                        <option value="D3 TI / D4 TRK" {{ old('program_studi', $alat->program_studi) === 'D3 TI / D4 TRK' ? 'selected' : '' }}>Prodi (D3 TI / D4 TRK - Alat Khusus Mahasiswa)</option>
                    </select>
                </div>

            </div>

            <div class="mt-5">
                <label class="block text-sm mb-2">
                    Deskripsi
                </label>

                <textarea
                    name="deskripsi"
                    rows="4"
                    class="inp w-full">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 mt-6">

                <a href="{{ route('admin.alat') }}"
                   class="px-5 py-3 border rounded-xl">
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-5 py-3 bg-[#185FA5] text-white rounded-xl">
                    Simpan Alat
                </button>

            </div>

        </form>

    </div>

</div>

@endsection