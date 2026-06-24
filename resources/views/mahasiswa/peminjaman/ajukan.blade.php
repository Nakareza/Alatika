@extends('layouts.app')

@section('title', 'Pengajuan Peminjaman')

@section('content')

    @if(session('error'))
    <div class="mb-6 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('mahasiswa.peminjaman.store') }}" method="POST" @submit.prevent="submitForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="peminjamanForm()">

            <div class="lg:col-span-2 space-y-5">

                <div class="card p-6">
                    <h3 class="font-bold text-base mb-5"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                        <i class="fas fa-plus-circle mr-2" style="color:#378ADD;"></i>
                        Tambah Barang
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Pilih Jenis</label>
                            <select x-model="selectedKategori" class="inp" @change="onKategoriChange"
                                    style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($kategori as $kat)
                                    <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Pilih Alat</label>
                            <select x-model="pilihan.alat_id" class="inp" @change="onAlatChange"
                                    :disabled="!selectedKategori"
                                    style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                                <option value="">-- Pilih Alat --</option>
                                <template x-for="item in alatOptions" :key="item.id">
                                    <option :value="item.id"
                                            :data-nama="item.nama"
                                            :data-stok="item.stok_tersedia"
                                            :data-kode="item.kode"
                                            :disabled="item.stok_tersedia < 1"
                                            x-text="`${item.nama} — Stok: ${item.stok_tersedia}`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Jumlah</label>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="pilihan.jumlah = Math.max(1, pilihan.jumlah - 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition shrink-0"
                                        style="background:#EBF3FD;color:#185FA5;"
                                        onmouseover="this.style.background='#D4E6F8'"
                                        onmouseout="this.style.background='#EBF3FD'">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="pilihan.jumlah" min="1"
                                       :max="pilihan.stok_max"
                                       class="inp text-center" style="padding:0.6rem;">
                                <button type="button"
                                        @click="pilihan.jumlah = Math.min(pilihan.stok_max, pilihan.jumlah + 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition shrink-0"
                                        style="background:#EBF3FD;color:#185FA5;"
                                        onmouseover="this.style.background='#D4E6F8'"
                                        onmouseout="this.style.background='#EBF3FD'">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" @click="tambahBarang"
                                class="btn btn-accent"
                                :disabled="!pilihan.alat_id"
                                :class="!pilihan.alat_id ? 'opacity-50 cursor-not-allowed' : ''">
                            <i class="fas fa-plus"></i> Tambah ke Daftar
                        </button>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="font-bold text-base mb-4"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                        <i class="fas fa-list mr-2" style="color:#185FA5;"></i>
                        Daftar Pengajuan
                        <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full"
                              style="background:#EBF3FD;color:#185FA5;"
                              x-text="barangList.length + ' item'"></span>
                    </h3>

                    <div x-show="barangList.length === 0" class="py-10 text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"
                             style="background:#EBF3FD;">
                            <i class="fas fa-box-open" style="color:#B5D4F4;"></i>
                        </div>
                        <p class="text-sm" style="color:#94a3b8;">Belum ada barang ditambahkan.</p>
                        <p class="text-xs mt-1" style="color:#B5D4F4;">Pilih alat di atas lalu klik "Tambah ke Daftar".</p>
                    </div>

                    <div class="space-y-3" x-show="barangList.length > 0">
                        <template x-for="(item, index) in barangList" :key="index">
                            <div class="flex items-center gap-4 p-4 rounded-xl transition"
                                 style="border:1px solid #EBF3FD;background:#F5F8FF;"
                                 onmouseover="this.style.borderColor='#B5D4F4';this.style.background='white';"
                                 onmouseout="this.style.borderColor='#EBF3FD';this.style.background='#F5F8FF';">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                     style="background:#EBF3FD;color:#185FA5;">
                                    <i class="fas fa-microchip text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm truncate"
                                       style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;"
                                       x-text="item.nama"></p>
                                    <p class="text-xs mt-0.5" style="color:#94a3b8;"
                                       x-text="'Kode: ' + item.kode"></p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            @click="item.jumlah = Math.max(1, item.jumlah - 1)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition"
                                            style="background:white;border:1px solid #D4E6F8;color:#64748b;"
                                            onmouseover="this.style.borderColor='#378ADD';this.style.color='#185FA5';"
                                            onmouseout="this.style.borderColor='#D4E6F8';this.style.color='#64748b';">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="w-8 text-center font-bold text-sm"
                                          style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;"
                                          x-text="item.jumlah"></span>
                                    <button type="button"
                                            @click="item.jumlah = Math.min(item.stok_max, item.jumlah + 1)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs transition"
                                            style="background:white;border:1px solid #D4E6F8;color:#64748b;"
                                            onmouseover="this.style.borderColor='#378ADD';this.style.color='#185FA5';"
                                            onmouseout="this.style.borderColor='#D4E6F8';this.style.color='#64748b';">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button type="button" @click="hapusBarang(index)"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center transition shrink-0"
                                        style="color:#cbd5e1;"
                                        onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444';"
                                        onmouseout="this.style.background='';this.style.color='#cbd5e1';">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <div class="space-y-5">

                <div class="card p-6">
                    <h3 class="font-bold text-base mb-5"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                        <i class="fas fa-calendar-alt mr-2" style="color:#185FA5;"></i>
                        Detail Peminjaman
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Keperluan <span class="text-red-500">*</span></label>
                            <select x-model="keperluan" @change="onKeperluanChange" class="inp" required
                                    style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                                <option value="">-- Pilih Keperluan --</option>
                                @foreach($keperluanOptions as $option)
                                    <option value="{{ $option['name'] }}" data-same-day="{{ $option['same_day'] ? '1' : '0' }}">{{ $option['name'] }}</option>
                                @endforeach
                            </select>
                            <p x-show="isSameDay" x-cloak class="text-xs mt-1.5 flex items-center gap-1" style="color:#92400E;">
                                <i class="fas fa-clock text-[10px]"></i>
                                <span>Keperluan ini wajib dikembalikan dalam 1 hari (hari yang sama).</span>
                            </p>
                        </div>

                        <div>
                            <label class="form-label">Tanggal Pinjam <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggalPinjam" @change="filterWeekend"
                                   min="{{ date('Y-m-d') }}" class="inp" required>
                        </div>

                        <div x-show="!isSameDay" x-transition>
                            <label class="form-label">Tanggal Kembali <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggalKembali" :min="tanggalPinjam" class="inp" :required="!isSameDay">
                        </div>
                    </div>
                </div>

                <div class="card p-6" style="background:#EBF3FD;border-color:#D4E6F8;">
                    <h3 class="font-bold text-sm mb-3"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">Ringkasan</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span style="color:#64748b;">Jenis barang</span>
                            <span class="font-semibold"
                                  style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;"
                                  x-text="barangList.length + ' jenis'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span style="color:#64748b;">Total unit</span>
                            <span class="font-semibold"
                                  style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;"
                                  x-text="totalUnit + ' unit'"></span>
                        </div>
                    </div>
                </div>

                <div x-show="errorMsg" x-cloak
                     class="rounded-xl p-4 text-sm flex items-start gap-2"
                     style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
                    <i class="fas fa-exclamation-circle mt-0.5 shrink-0"></i>
                    <span x-text="errorMsg"></span>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="button" @click="submitForm"
                            class="btn btn-primary w-full"
                            :disabled="barangList.length === 0"
                            :class="barangList.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                        <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                    </button>
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary w-full">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>

            </div>
        </div>

        <div id="hiddenInputs"></div>
    </form>

@endsection

@push('styles')
<style>
    .btn-accent {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 0.6rem 1.25rem;
        background: #185FA5; color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 600;
        font-size: 0.875rem; border-radius: 12px; border: none; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-accent:hover { background: #1E2B4A; }
    .btn-accent:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
@endpush

@push('scripts')
<script>
    function peminjamanForm() {
        // Kelompokkan alat by kategori
        const alatData = @json($alat);
        const alatByKategori = {};
        alatData.forEach(item => {
            if (!alatByKategori[item.kategori]) {
                alatByKategori[item.kategori] = [];
            }
            alatByKategori[item.kategori].push(item);
        });

        return {
            alatByKategori: alatByKategori,
            selectedKategori: '',
            alatOptions: [],
            pilihan: { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 },
            barangList: @json($pengajuan),
            tanggalPinjam: '{{ date('Y-m-d') }}',
            tanggalKembali: '{{ date('Y-m-d') }}',
            keperluan: '',
            errorMsg: '',
            keperluanMap: @json(collect($keperluanOptions)->mapWithKeys(fn($o) => [$o['name'] => $o['same_day']])),

            get isSameDay() {
                return this.keperluanMap[this.keperluan] === true;
            },

            get totalUnit() {
                return this.barangList.reduce((sum, i) => sum + i.jumlah, 0);
            },

            onKategoriChange() {
                this.alatOptions = this.alatByKategori[this.selectedKategori] || [];
                this.pilihan = { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 };
            },

            onAlatChange(e) {
                const opt = e.target.selectedOptions[0];
                if (!opt || !opt.value) return;
                this.pilihan.nama     = opt.dataset.nama;
                this.pilihan.kode     = opt.dataset.kode;
                this.pilihan.stok_max = parseInt(opt.dataset.stok) || 1;
                this.pilihan.jumlah   = 1;
            },

            filterWeekend(e) {
                const date = new Date(e.target.value);
                const day = date.getUTCDay();
                if (day === 0 || day === 6) {
                    this.errorMsg = 'Peminjaman tidak tersedia di hari Sabtu dan Minggu.';
                    this.tanggalPinjam = '';
                    this.tanggalKembali = '';
                    e.target.value = '';
                } else {
                    this.errorMsg = '';
                    if (this.isSameDay) {
                        this.tanggalKembali = this.tanggalPinjam;
                    }
                }
            },

            onKeperluanChange() {
                this.errorMsg = '';
                if (this.isSameDay) {
                    this.tanggalKembali = this.tanggalPinjam;
                }
            },

            tambahBarang() {
                if (!this.pilihan.alat_id) return;
                const existing = this.barangList.find(i => i.alat_id === this.pilihan.alat_id);
                if (existing) {
                    existing.jumlah = Math.min(existing.stok_max, existing.jumlah + this.pilihan.jumlah);
                } else {
                    this.barangList.push({
                        alat_id:  this.pilihan.alat_id,
                        nama:     this.pilihan.nama,
                        kode:     this.pilihan.kode,
                        jumlah:   this.pilihan.jumlah,
                        stok_max: this.pilihan.stok_max,
                    });
                }
                this.pilihan = { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 };
            },

            hapusBarang(index) {
                this.barangList.splice(index, 1);
            },

            submitForm() {
                this.errorMsg = '';
                if (this.barangList.length === 0) { this.errorMsg = 'Tambahkan minimal satu barang terlebih dahulu.'; return; }
                if (!this.tanggalPinjam) { this.errorMsg = 'Tanggal pinjam harus diisi.'; return; }
                if (!this.keperluan) { this.errorMsg = 'Keperluan harus diisi.'; return; }

                if (this.isSameDay) {
                    this.tanggalKembali = this.tanggalPinjam;
                } else {
                    if (!this.tanggalKembali) { this.errorMsg = 'Tanggal kembali harus diisi.'; return; }
                    if (new Date(this.tanggalKembali) < new Date(this.tanggalPinjam)) {
                        this.errorMsg = 'Tanggal kembali tidak boleh kurang dari tanggal pinjam.';
                        return;
                    }
                }

                const container = document.getElementById('hiddenInputs');
                container.innerHTML = '';
                this.barangList.forEach((item, i) => {
                    container.innerHTML += `<input type="hidden" name="items[${i}][alat_id]" value="${item.alat_id}">`;
                    container.innerHTML += `<input type="hidden" name="items[${i}][jumlah]" value="${item.jumlah}">`;
                });
                container.innerHTML += `<input type="hidden" name="tanggal_pinjam" value="${this.tanggalPinjam}">`;
                container.innerHTML += `<input type="hidden" name="tanggal_kembali" value="${this.tanggalKembali}">`;
                container.innerHTML += `<input type="hidden" name="keperluan" value="${this.keperluan}">`;
                this.$el.closest('form').submit();
            }
        }
    }
</script>
@endpush