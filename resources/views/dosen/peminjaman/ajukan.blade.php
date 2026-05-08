@extends('layouts.dosen')

@section('title', 'Ajukan Peminjaman')

@section('content')

@if(session('error'))
<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    {{ session('error') }}
</div>
@endif

<div class="max-w-3xl mx-auto">

    {{-- Header Card --}}
    <div class="card p-6 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                    Form Pengajuan Peminjaman
                </h2>

                <p class="text-sm" style="color:#94a3b8;">
                    Isi data peminjaman alat laboratorium dengan lengkap
                </p>
            </div>

            <div class="hidden md:flex w-14 h-14 rounded-2xl items-center justify-center"
                 style="background:#EBF3FD;">
                <i class="fas fa-paper-plane text-xl" style="color:#185FA5;"></i>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="card p-6 md:p-8">

        <form action="{{ route('dosen.peminjaman.store') }}" method="POST">
            @csrf

            {{-- Pilih Alat --}}
            <div class="mb-6">
                <label class="form-label">
                    Pilih Alat
                </label>

                <select name="alat_id"
                        class="inp"
                        required>

                    <option value="">-- Pilih Alat --</option>

                    @foreach($alat as $item)
                        <option value="{{ $item->id }}">
                            {{ $item->nama }}
                            (Stok: {{ $item->stok_tersedia }})
                            - Kode: {{ $item->kode }}
                        </option>
                    @endforeach

                </select>

                @error('alat_id')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Jumlah --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div>
                    <label class="form-label">
                        Jumlah
                    </label>

                    <input type="number"
                           name="jumlah"
                           value="1"
                           min="1"
                           class="inp"
                           required>

                    @error('jumlah')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <div>
                    <label class="form-label">
                        Tanggal Pinjam
                    </label>

                    <input type="date"
                           name="tanggal_pinjam"
                           value="{{ date('Y-m-d') }}"
                           min="{{ date('Y-m-d') }}"
                           class="inp"
                           required>

                    @error('tanggal_pinjam')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">
                        Deadline Pengembalian
                    </label>

                    <input type="date"
                           name="tanggal_kembali"
                           value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                           min="{{ date('Y-m-d') }}"
                           class="inp"
                           required>

                    @error('tanggal_kembali')
                        <p class="text-xs text-red-500 mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Keperluan --}}
            <div class="mb-8">
                <label class="form-label">
                    Keperluan
                </label>

                <textarea name="keperluan"
                          rows="4"
                          class="inp resize-none"
                          placeholder="Jelaskan tujuan penggunaan alat..."
                          required></textarea>

                @error('keperluan')
                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Action --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t"
                 style="border-color:#EBF3FD;">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fas fa-paper-plane"></i>
                    Ajukan Peminjaman
                </button>

                <a href="{{ route('dosen.dashboard') }}"
                   class="btn btn-secondary justify-center">

                    Batal
                </a>

            </div>

        </form>

    </div>

</div>

@endsection