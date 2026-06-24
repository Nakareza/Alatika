@extends('layouts.admin')

@section('title', 'Edit Alat')

@section('content')

<div class="w-full px-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#1E2B4A]">
                Edit Alat
            </h2>

            <p class="text-slate-500 mt-1">
                Tambahkan data alat laboratorium baru
            </p>
        </div>

        
    </div>

    <div class="card p-6">

        <form action="{{ route('admin.alat.update', $alat->id) }}"
              method="POST">

            @csrf

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
                    <label class="block text-sm mb-2">
                        Lokasi
                    </label>

                    <input
                        type="text"
                        name="lokasi"
                        value="{{ old('lokasi', $alat->lokasi) }}"
                        class="inp w-full">
                </div>

                <div>
                    <label class="block text-sm mb-2">
                        Stok Total
                    </label>

                    <input
                        type="number"
                        name="stok_total"
                        value="{{ old('stok_total', $alat->stok_total) }}"
                        class="inp w-full">
                </div>

                <div>
                    <label class="block text-sm mb-2">
                        Status
                    </label>

                    <select name="status" class="inp w-full">
                        <option value="tersedia" {{ old('status', $alat->status) == 'tersedia' ? 'selected' : '' }}>
                            Tersedia
                        </option>
                        <option value="maintenance" {{ old('status', $alat->status) == 'maintenance' ? 'selected' : '' }}>
                            Maintenance
                        </option>
                    </select>

                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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