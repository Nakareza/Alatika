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

    @if($errors->any())
    <div class="mb-6 rounded-xl p-4 text-sm space-y-1"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        @foreach($errors->all() as $error)
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ $error }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('mahasiswa.peminjaman.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="peminjamanForm()">

            <div class="lg:col-span-2 space-y-5">

                <div class="card p-6">
                    <h3 class="font-bold text-base mb-3 text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-plus-circle mr-2 text-[#378ADD]"></i>
                        Tambah Barang Ke Daftar
                    </h3>

                    <!-- Toggle Alat vs ToolSet -->
                    <div class="flex border-b border-slate-100 mb-4">
                        <button type="button" @click="activeItemTab = 'alat'" :class="activeItemTab === 'alat' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500'" class="pb-2 px-4 font-semibold text-xs transition">
                            <i class="fas fa-microchip mr-1"></i> Alat Unit
                        </button>
                        <button type="button" @click="activeItemTab = 'toolset'" :class="activeItemTab === 'toolset' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-slate-500'" class="pb-2 px-4 font-semibold text-xs transition">
                            <i class="fas fa-toolbox mr-1"></i> Tool Set Paket
                        </button>
                    </div>

                    <!-- Tab 1: Alat Unit -->
                    <div x-show="activeItemTab === 'alat'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">Pilih Kategori</label>
                            <select x-model="selectedKategori" class="inp" @change="onKategoriChange"
                                    style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                                <option value="">-- Pilih Kategori --</option>
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
                                            :data-stok="getDynamicStok(item)"
                                            :data-kode="item.kode"
                                            :disabled="getDynamicStok(item) < 1"
                                            x-text="`${item.nama} — Stok: ${getDynamicStok(item)}`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Jumlah</label>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="pilihan.jumlah = Math.max(1, pilihan.jumlah - 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition shrink-0">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="pilihan.jumlah" min="1"
                                       :max="pilihan.stok_max"
                                       class="inp text-center" style="padding:0.6rem;">
                                <button type="button"
                                        @click="pilihan.jumlah = Math.min(pilihan.stok_max, pilihan.jumlah + 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition shrink-0">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex items-end">
                            <button type="button" @click="tambahBarang"
                                    class="btn btn-accent w-full"
                                    :disabled="!pilihan.alat_id"
                                    :class="!pilihan.alat_id ? 'opacity-50 cursor-not-allowed' : ''">
                                <i class="fas fa-plus"></i> Tambah Alat ke Daftar
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Tool Set Paket -->
                    <div x-show="activeItemTab === 'toolset'" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="form-label">Pilih Paket Tool Set</label>
                            <select x-model="pilihanToolset.id" class="inp" @change="onToolsetChange"
                                    style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                                <option value="">-- Pilih Paket Tool Set --</option>
                                <template x-for="item in toolSets" :key="item.id">
                                    <option :value="item.id"
                                            :disabled="getDynamicStokToolset(item) < 1"
                                            x-text="`${item.nama_tool_set} — Stok: ${getDynamicStokToolset(item)} set`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Jumlah</label>
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        @click="pilihanToolset.jumlah = Math.max(1, pilihanToolset.jumlah - 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition shrink-0">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="pilihanToolset.jumlah" min="1"
                                       :max="pilihanToolset.stok_max"
                                       class="inp text-center" style="padding:0.6rem;">
                                <button type="button"
                                        @click="pilihanToolset.jumlah = Math.min(pilihanToolset.stok_max, pilihanToolset.jumlah + 1)"
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold bg-[#EBF3FD] text-[#185FA5] hover:bg-[#D4E6F8] transition shrink-0">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="sm:col-span-3 flex justify-between gap-3 mt-2">
                            <button type="button" x-show="pilihanToolset.id" @click="openComponentsModal" class="btn btn-secondary px-4 py-2 text-xs flex items-center gap-1">
                                <i class="fas fa-search-plus"></i> Lihat Daftar Komponen
                            </button>
                            <button type="button" @click="tambahToolset"
                                    class="btn btn-accent flex-1"
                                    :disabled="!pilihanToolset.id"
                                    :class="!pilihanToolset.id ? 'opacity-50 cursor-not-allowed' : ''">
                                <i class="fas fa-plus"></i> Tambah Tool Set ke Daftar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- List of selected items -->
                <div class="card p-6">
                    <h3 class="font-bold text-base mb-4 text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-list mr-2 text-[#185FA5]"></i>
                        Daftar Pengajuan Peminjaman
                        <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-[#EBF3FD] text-[#185FA5]"
                              x-text="barangList.length + ' item'"></span>
                    </h3>

                    <div x-show="barangList.length === 0" class="py-10 text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 bg-[#EBF3FD]">
                            <i class="fas fa-box-open text-[#B5D4F4]"></i>
                        </div>
                        <p class="text-sm text-slate-400">Belum ada barang ditambahkan.</p>
                        <p class="text-xs text-[#B5D4F4] mt-1">Pilih jenis alat/toolset di atas lalu klik "Tambah ke Daftar".</p>
                    </div>

                    <div class="space-y-3" x-show="barangList.length > 0">
                        <template x-for="(item, index) in barangList" :key="index">
                            <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 bg-[#F5F8FF] hover:bg-white hover:border-[#B5D4F4] transition">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                                     :class="item.type === 'toolset' ? 'bg-purple-100 text-purple-700' : 'bg-[#EBF3FD] text-[#185FA5]'">
                                    <i :class="item.type === 'toolset' ? 'fas fa-toolbox' : 'fas fa-microchip'"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="font-semibold text-sm truncate text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;" x-text="item.nama"></p>
                                        <template x-if="item.type === 'toolset'">
                                            <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 font-bold shrink-0">Tool Set</span>
                                        </template>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5" x-text="'Kode: ' + item.kode"></p>
                                    
                                    <!-- Toolset list component link -->
                                    <template x-if="item.type === 'toolset'">
                                        <button type="button" @click="viewListComponents(item)" class="text-xs text-purple-600 hover:underline mt-1 flex items-center gap-1 font-semibold">
                                            <i class="fas fa-search-plus"></i> Detail Komponen (<span x-text="item.components_count"></span>)
                                        </button>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button type="button"
                                            @click="item.jumlah = Math.max(1, item.jumlah - 1)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs bg-white border border-[#D4E6F8] text-slate-500 hover:border-[#378ADD] hover:text-[#185FA5] transition">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="w-8 text-center font-bold text-sm text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;" x-text="item.jumlah"></span>
                                    <button type="button"
                                            @click="item.jumlah = Math.min(item.stok_max, item.jumlah + 1)"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs bg-white border border-[#D4E6F8] text-slate-500 hover:border-[#378ADD] hover:text-[#185FA5] transition">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button type="button" @click="hapusBarang(index)"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:bg-rose-50 hover:text-rose-600 transition shrink-0">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <div class="space-y-5">

                <div class="card p-6">
                    <h3 class="font-bold text-base mb-5 text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        <i class="fas fa-calendar-alt mr-2 text-[#185FA5]"></i>
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
                            <p x-show="isSameDay" x-cloak class="text-xs mt-1.5 flex items-center gap-1 text-amber-800">
                                <i class="fas fa-clock text-[10px]"></i>
                                <span>Keperluan ini wajib dikembalikan dalam 1 hari (hari yang sama).</span>
                            </p>
                        </div>

                        <div>
                            <label class="form-label">Tanggal Pinjam <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggalPinjam" @change="filterWeekend"
                                   min="{{ date('Y-m-d') }}" class="inp" :disabled="isSameDay" required>
                        </div>

                        <div x-show="!isSameDay" x-transition>
                            <label class="form-label">Tanggal Kembali <span class="text-red-500">*</span></label>
                            <input type="date" x-model="tanggalKembali" :min="tanggalPinjam" class="inp" :required="!isSameDay">
                        </div>

                        {{-- Surat Keterangan (Only shown and required for special tools / Alat Khusus) --}}
                        <div x-show="hasKhususItem" x-transition x-cloak>
                            <label class="form-label">Surat Keterangan <span class="text-red-500">*</span></label>
                            <input type="file" name="surat_keterangan" id="suratKeteranganInput" class="inp w-full" :required="hasKhususItem" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <p class="text-xs text-amber-600 mt-1.5 flex items-center gap-1">
                                <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                <span>Pengajuan memuat Alat Khusus. Wajib melampirkan Surat Keterangan (PDF/JPG/PNG).</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-6 bg-[#EBF3FD] border-[#D4E6F8]">
                    <h3 class="font-bold text-sm mb-3 text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Ringkasan</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Jenis barang</span>
                            <span class="font-semibold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;" x-text="barangList.length + ' jenis'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total unit / set</span>
                            <span class="font-semibold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;" x-text="totalUnit + ' item'"></span>
                        </div>
                    </div>
                </div>

                <div x-show="errorMsg" x-cloak
                     class="rounded-xl p-4 text-sm flex items-start gap-2 bg-rose-50 border border-rose-200 text-rose-700">
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

    <!-- Component Detail Modal for ToolSet selection -->
    <x-modal name="toolset-comp-modal" title="Daftar Komponen Tool Set" size="md">
        <div class="space-y-4 text-sm">
            <div>
                <p class="text-xs text-slate-500">Nama Paket</p>
                <p class="font-bold text-slate-700" x-text="modalToolset.nama_tool_set"></p>
            </div>
            <div>
                <h4 class="font-semibold text-[#1E2B4A] mb-2">Komponen pendukung:</h4>
                <div class="overflow-x-auto border border-slate-100 rounded-xl max-h-60 overflow-y-auto">
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-2 text-left font-bold text-slate-500 uppercase">Nama Komponen</th>
                                <th class="px-4 py-2 text-left font-bold text-slate-500 uppercase">Jumlah</th>
                                <th class="px-4 py-2 text-left font-bold text-slate-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="comp in modalToolset.details" :key="comp.id">
                                <tr>
                                    <td class="px-4 py-2 font-medium text-slate-700" x-text="comp.nama_komponen"></td>
                                    <td class="px-4 py-2 text-slate-600" x-text="comp.jumlah"></td>
                                    <td class="px-4 py-2 text-slate-500" x-text="comp.satuan"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal-toolset-comp-modal'))" class="btn btn-secondary px-4 py-2 rounded-xl text-xs">Tutup</button>
        </x-slot>
    </x-modal>

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
        const alatData = @json($alat);
        const toolSetsData = @json($toolSets);
        const alatByKategori = {};
        
        alatData.forEach(item => {
            const kat = item.program_studi !== null ? 'Alat Khusus' : item.kategori;
            if (!alatByKategori[kat]) {
                alatByKategori[kat] = [];
            }
            alatByKategori[kat].push(item);
        });

        return {
            activeItemTab: 'alat',
            alatByKategori: alatByKategori,
            selectedKategori: '',
            alatOptions: [],
            toolSets: toolSetsData,
            
            pilihan: { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 },
            pilihanToolset: { id: '', nama: '', kode: '', jumlah: 1, stok_max: 0 },
            modalToolset: { nama_tool_set: '', details: [] },

            barangList: @json($pengajuan),
            tanggalPinjam: '{{ date('Y-m-d') }}',
            tanggalKembali: '{{ date('Y-m-d') }}',
            keperluan: '',
            errorMsg: '',
            keperluanMap: @json(collect($keperluanOptions)->mapWithKeys(fn($o) => [$o['name'] => $o['same_day']])),

            get isSameDay() {
                return this.keperluanMap[this.keperluan] === true;
            },

            get hasKhususItem() {
                return this.barangList.some(item => {
                    if (item.type !== 'alat') return false;
                    const matchedAlat = alatData.find(a => a.id == item.alat_id);
                    return matchedAlat && matchedAlat.program_studi !== null;
                });
            },

            get totalUnit() {
                return this.barangList.reduce((sum, i) => sum + i.jumlah, 0);
            },

            getDynamicStok(item) {
                const addedQty = this.barangList
                    .filter(i => i.type === 'alat' && i.nama === item.nama)
                    .reduce((sum, i) => sum + i.jumlah, 0);
                return Math.max(0, item.stok_tersedia - addedQty);
            },

            getDynamicStokToolset(item) {
                const addedQty = this.barangList
                    .filter(i => i.type === 'toolset' && i.alat_id == item.id)
                    .reduce((sum, i) => sum + i.jumlah, 0);
                return Math.max(0, item.stok_tersedia - addedQty);
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
                const matchedItem = this.alatOptions.find(i => i.id == opt.value);
                this.pilihan.stok_max = matchedItem ? this.getDynamicStok(matchedItem) : 0;
                this.pilihan.jumlah   = 1;
            },

            onToolsetChange(e) {
                const opt = e.target.selectedOptions[0];
                if (!opt || !opt.value) return;
                const matchedSet = this.toolSets.find(i => i.id == opt.value);
                if (matchedSet) {
                    this.pilihanToolset.id = matchedSet.id;
                    this.pilihanToolset.nama = matchedSet.nama_tool_set;
                    this.pilihanToolset.kode = matchedSet.kode_tool_set;
                    this.pilihanToolset.stok_max = this.getDynamicStokToolset(matchedSet);
                    this.pilihanToolset.jumlah = 1;
                }
            },

            openComponentsModal() {
                if (!this.pilihanToolset.id) return;
                const matchedSet = this.toolSets.find(i => i.id == this.pilihanToolset.id);
                if (matchedSet) {
                    this.modalToolset = matchedSet;
                    window.dispatchEvent(new CustomEvent('open-modal-toolset-comp-modal'));
                }
            },

            viewListComponents(item) {
                const matchedSet = this.toolSets.find(i => i.id == item.alat_id);
                if (matchedSet) {
                    this.modalToolset = matchedSet;
                    window.dispatchEvent(new CustomEvent('open-modal-toolset-comp-modal'));
                }
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
                    this.tanggalPinjam = '{{ date('Y-m-d') }}';
                    this.tanggalKembali = this.tanggalPinjam;

                    const date = new Date(this.tanggalPinjam);
                    const day = date.getUTCDay();
                    if (day === 0 || day === 6) {
                        this.errorMsg = 'Peminjaman tidak tersedia di hari Sabtu dan Minggu.';
                        this.tanggalPinjam = '';
                        this.tanggalKembali = '';
                    }
                }
            },

            tambahBarang() {
                if (!this.pilihan.alat_id) return;
                const existing = this.barangList.find(i => i.type === 'alat' && i.alat_id == this.pilihan.alat_id);
                if (existing) {
                    existing.jumlah = Math.min(existing.stok_max, existing.jumlah + this.pilihan.jumlah);
                } else {
                    this.barangList.push({
                        alat_id:  this.pilihan.alat_id,
                        nama:     this.pilihan.nama,
                        kode:     this.pilihan.kode,
                        jumlah:   this.pilihan.jumlah,
                        stok_max: this.pilihan.stok_max,
                        type:     'alat',
                        components_count: 0
                    });
                }
                this.pilihan = { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 };
            },

            tambahToolset() {
                if (!this.pilihanToolset.id) return;
                const existing = this.barangList.find(i => i.type === 'toolset' && i.alat_id == this.pilihanToolset.id);
                if (existing) {
                    existing.jumlah = Math.min(existing.stok_max, existing.jumlah + this.pilihanToolset.jumlah);
                } else {
                    const matchedSet = this.toolSets.find(i => i.id == this.pilihanToolset.id);
                    this.barangList.push({
                        alat_id:  this.pilihanToolset.id,
                        nama:     this.pilihanToolset.nama,
                        kode:     this.pilihanToolset.kode,
                        jumlah:   this.pilihanToolset.jumlah,
                        stok_max: this.pilihanToolset.stok_max,
                        type:     'toolset',
                        components_count: matchedSet ? matchedSet.details.length : 0
                    });
                }
                this.pilihanToolset = { id: '', nama: '', kode: '', jumlah: 1, stok_max: 0 };
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

                if (this.hasKhususItem) {
                    const fileInput = document.getElementById('suratKeteranganInput');
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        this.errorMsg = 'Wajib mengunggah Surat Keterangan untuk meminjam Alat Khusus.';
                        return;
                    }
                }

                const container = document.getElementById('hiddenInputs');
                container.innerHTML = '';
                this.barangList.forEach((item, i) => {
                    container.innerHTML += `<input type="hidden" name="items[${i}][alat_id]" value="${item.alat_id}">`;
                    container.innerHTML += `<input type="hidden" name="items[${i}][type]" value="${item.type}">`;
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