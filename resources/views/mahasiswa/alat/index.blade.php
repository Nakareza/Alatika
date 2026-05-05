@extends('layouts.app')

@section('title', 'Daftar Alat')

@section('content')

    @if(session('success'))
    <div class="mb-6 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium"
         style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 px-4 py-3 rounded-xl flex items-center gap-2 text-sm font-medium"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <div x-data="cartModal()">

        <!-- Top bar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">

            <!-- Kiri: Search + Filter -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <input type="text"
                           x-model="search"
                           placeholder="Cari nama alat..."
                           class="inp pl-9 pr-4 py-2.5 w-52">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color:#A0BBCC;"></i>
                </div>

                <select x-model="filterKategori"
                        class="inp py-2.5 px-4 pr-8 cursor-pointer appearance-none"
                        style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                    <option value="">Semua Kategori</option>
                    @foreach($alat->pluck('kategori')->unique()->filter()->sort()->values() as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>

                <select x-model="filterStok"
                        class="inp py-2.5 px-4 pr-8 cursor-pointer appearance-none"
                        style="background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 10px center;background-size:14px;">
                    <option value="">Semua Stok</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="habis">Habis</option>
                </select>
            </div>

            <!-- Kanan: Action buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('mahasiswa.keranjang') }}" class="btn btn-primary relative">
                    <i class="fas fa-shopping-cart"></i>
                    Keranjang
                    @if($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="btn btn-secondary">
                    <i class="fas fa-plus"></i>
                    Pinjam Langsung
                </a>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($alat as $item)
            <div class="card overflow-hidden"
                 x-show="
                    (search === '' || '{{ strtolower($item->nama) }}'.includes(search.toLowerCase())) &&
                    (filterKategori === '' || filterKategori === '{{ $item->kategori }}') &&
                    (filterStok === '' || (filterStok === 'tersedia' && {{ $item->stok_tersedia }} > 0) || (filterStok === 'habis' && {{ $item->stok_tersedia }} === 0))
                 "
                 x-cloak>

                <!-- Image -->
                <div class="h-36 flex items-center justify-center border-b"
                     style="{{ $item->stok_tersedia > 0 ? 'background:linear-gradient(135deg,#EBF3FD,#dbeafe);border-color:#D4E6F8;' : 'background:#f1f5f9;border-color:#e2e8f0;' }}">
                    <i class="fas fa-microchip text-4xl" style="{{ $item->stok_tersedia > 0 ? 'color:#B5D4F4;' : 'color:#cbd5e1;' }}"></i>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <span class="text-xs font-bold tracking-wider uppercase" style="color:#185FA5;">{{ $item->kategori }}</span>
                        @if($item->stok_tersedia > 0)
                            <span class="badge badge-success">Tersedia</span>
                        @else
                            <span class="badge badge-danger">Habis</span>
                        @endif
                    </div>
                    <h3 class="text-base font-bold leading-tight mb-3" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">{{ $item->nama }}</h3>

                    <div class="flex items-center justify-between text-sm mb-4">
                        <div>
                            <span class="text-xs block mb-0.5" style="color:#A0BBCC;">Kode</span>
                            <span class="font-semibold" style="color:#1E2B4A;">{{ $item->kode }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs block mb-0.5" style="color:#A0BBCC;">Stok</span>
                            <span class="font-bold" style="{{ $item->stok_tersedia > 0 ? 'color:#10b981;' : 'color:#ef4444;' }}">
                                {{ $item->stok_tersedia }} / {{ $item->stok_total }}
                            </span>
                        </div>
                    </div>

                    @if($item->stok_tersedia > 0)
                        <button @click="openModal({{ $item->id }}, '{{ addslashes($item->nama) }}', {{ $item->stok_tersedia }})"
                            class="btn btn-primary w-full justify-center">
                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    @else
                        <form action="{{ route('mahasiswa.alat.waitlist', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm transition-all flex items-center justify-center gap-2 border"
                                style="background:#fffbeb;color:#d97706;border-color:#fcd34d;font-family:'Plus Jakarta Sans',sans-serif;"
                                onmouseover="this.style.background='#fef3c7'"
                                onmouseout="this.style.background='#fffbeb'">
                                <i class="fas fa-bell"></i> Kabari Saya saat Tersedia
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center">
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#EBF3FD;">
                    <i class="fas fa-box-open text-xl" style="color:#B5D4F4;"></i>
                </div>
                <p class="text-sm font-semibold" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Belum ada data alat di database.</p>
            </div>
            @endforelse

            <!-- Empty state filter -->
            <div class="col-span-full py-16 text-center"
                 x-show="document.querySelectorAll('.card[style*=\'display: none\']').length === document.querySelectorAll('.card').length"
                 x-cloak>
                <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#EBF3FD;">
                    <i class="fas fa-search text-xl" style="color:#B5D4F4;"></i>
                </div>
                <p class="text-sm font-semibold mb-1" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">Tidak ada alat yang cocok dengan filter.</p>
                <button @click="search=''; filterKategori=''; filterStok=''"
                        class="mt-2 text-sm font-semibold hover:opacity-70 transition-opacity"
                        style="color:#185FA5;">
                    Reset filter
                </button>
            </div>
        </div>

        <!-- Modal Keranjang -->
        <x-modal name="keranjang" title="Tambah ke Keranjang" type="default" size="md">
            <p class="text-sm text-center mb-1" style="color:#64748b;">
                Stok tersedia: <span class="font-semibold" style="color:#10b981;" x-text="selectedItem.stok + ' unit'"></span>
            </p>

            <label class="form-label mt-4">Jumlah yang dipinjam</label>
            <div class="flex items-center gap-4 mb-2">
                <button @click="jumlah = Math.max(1, jumlah - 1)" type="button"
                    class="w-12 h-12 rounded-xl font-bold transition flex items-center justify-center"
                    style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" x-model.number="jumlah" :max="selectedItem.stok"
                    class="inp flex-1 text-center py-2.5 font-bold text-lg">
                <button @click="jumlah = Math.min(selectedItem.stok, jumlah + 1)" type="button"
                    class="w-12 h-12 rounded-xl font-bold transition flex items-center justify-center"
                    style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <form id="cartForm" method="POST" style="display:none;">
                @csrf
                <input type="hidden" id="jumlahInput" name="jumlah">
            </form>

            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal-keranjang')"
                    class="btn btn-secondary flex-1 justify-center">
                    Batal
                </button>
                <button type="button" @click="submitCart()"
                    class="btn btn-primary flex-1 justify-center">
                    <i class="fas fa-check"></i> Tambahkan
                </button>
            </x-slot>
        </x-modal>

    </div>

@endsection

@push('scripts')
<script>
    function cartModal() {
        return {
            selectedItem: { id: null, nama: '', stok: 0 },
            jumlah: 1,
            search: '',
            filterKategori: '',
            filterStok: '',

            openModal(id, nama, stok) {
                this.selectedItem = { id, nama, stok };
                this.jumlah = 1;
                this.$dispatch('open-modal-keranjang');
            },

            closeModal() {
                this.$dispatch('close-modal-keranjang');
                this.selectedItem = { id: null, nama: '', stok: 0 };
                this.jumlah = 1;
            },

            submitCart() {
                const form = document.getElementById('cartForm');
                const jumlahInput = document.getElementById('jumlahInput');
                jumlahInput.value = this.jumlah;
                form.action = `/mahasiswa/keranjang/${this.selectedItem.id}/add`;
                form.submit();
                this.closeModal();
            }
        }
    }
</script>
@endpush