<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman - Alatika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body:  ['"Inter"', 'sans-serif'],
                    },
                    colors: {
                        navy:    '#1E2B4A',
                        primary: '#185FA5',
                        accent:  '#378ADD',
                        surface: '#EBF3FD',
                    },
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body { background: #F5F8FF; font-family: 'Inter', sans-serif; color: #1E2B4A; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }
        .card { background: #fff; border-radius: 20px; border: 1px solid #EBF3FD; box-shadow: 0 2px 16px rgba(30,43,74,0.06); }
        .inp {
            width: 100%; padding: 0.75rem 1rem;
            background: #F5F8FF; border: 1.5px solid #D4E6F8;
            border-radius: 12px; font-family: 'Inter', sans-serif;
            font-size: 0.875rem; color: #1E2B4A; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .inp:focus { border-color: #378ADD; background: #fff; box-shadow: 0 0 0 3px rgba(55,138,221,0.10); }
        .inp::placeholder { color: #A0BBCC; }
        .form-label { display: block; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.875rem; font-weight: 600; color: #1E2B4A; margin-bottom: 0.5rem; }
        .error-message { display: block; font-size: 0.75rem; color: #DC2626; margin-top: 0.375rem; font-weight: 500; }
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.8rem 1.6rem; background: linear-gradient(135deg, #1E2B4A 0%, #185FA5 100%);
            color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
            font-size: 0.875rem; border-radius: 12px; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 6px 20px rgba(30,43,74,0.22);
        }
        .btn-primary:hover { background: linear-gradient(135deg, #185FA5 0%, #0F4A8A 100%); transform: translateY(-2px); }
        .btn-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.8rem 1.6rem; background: #fff; color: #1E2B4A;
            font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.875rem;
            border-radius: 12px; border: 1.5px solid #D4E6F8; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 2px 10px rgba(30,43,74,0.07); text-decoration: none;
        }
        .btn-secondary:hover { border-color: #378ADD; background: #F5F8FF; transform: translateY(-2px); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <x-header-dashboard :title="'Ajukan Peminjaman'" />

        <main class="p-8 min-h-screen" x-data="peminjamanForm()">

            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-600 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <form action="{{ route('mahasiswa.peminjaman.store') }}" method="POST" @submit.prevent="submitForm">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Kiri: Form tambah barang + list barang -->
                    <div class="lg:col-span-2 space-y-5">

                        <!-- Panel tambah barang -->
                        <div class="card p-6">
                            <h3 class="font-bold text-base text-navy mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                <i class="fas fa-plus-circle text-accent mr-2"></i>
                                Tambah Barang
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Pilih alat -->
                                <div class="sm:col-span-2">
                                    <label class="form-label">Pilih Alat</label>
                                    <select x-model="pilihan.alat_id" class="inp" @change="onAlatChange">
                                        <option value="">-- Pilih Alat --</option>
                                        @foreach($alat as $item)
                                        <option value="{{ $item->id }}"
                                                data-nama="{{ $item->nama }}"
                                                data-stok="{{ $item->stok_tersedia }}"
                                                data-kode="{{ $item->kode }}"
                                                {{ $item->stok_tersedia < 1 ? 'disabled' : '' }}>
                                            {{ $item->nama }} — Stok: {{ $item->stok_tersedia }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah -->
                                <div>
                                    <label class="form-label">Jumlah</label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="pilihan.jumlah = Math.max(1, pilihan.jumlah - 1)"
                                                class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center text-primary font-bold hover:bg-blue-100 transition flex-shrink-0">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" x-model.number="pilihan.jumlah" min="1" :max="pilihan.stok_max"
                                               class="inp text-center" style="padding:0.6rem;">
                                        <button type="button" @click="pilihan.jumlah = Math.min(pilihan.stok_max, pilihan.jumlah + 1)"
                                                class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center text-primary font-bold hover:bg-blue-100 transition flex-shrink-0">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" @click="tambahBarang"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-accent hover:bg-primary text-white text-sm font-semibold rounded-xl transition-all"
                                        :disabled="!pilihan.alat_id"
                                        :class="!pilihan.alat_id ? 'opacity-50 cursor-not-allowed' : ''">
                                    <i class="fas fa-plus"></i> Tambah ke Daftar
                                </button>
                            </div>
                        </div>

                        <!-- Daftar barang yang dipilih -->
                        <div class="card p-6">
                            <h3 class="font-bold text-base text-navy mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                <i class="fas fa-list text-primary mr-2"></i>
                                Daftar Barang
                                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full bg-surface text-primary" x-text="barangList.length + ' item'"></span>
                            </h3>

                            <!-- Empty state -->
                            <div x-show="barangList.length === 0" class="py-10 text-center text-slate-400">
                                <i class="fas fa-box-open text-4xl text-slate-200 block mb-3"></i>
                                <p class="text-sm">Belum ada barang ditambahkan.</p>
                                <p class="text-xs mt-1">Pilih alat di atas lalu klik "Tambah ke Daftar".</p>
                            </div>

                            <!-- List -->
                            <div class="space-y-3" x-show="barangList.length > 0">
                                <template x-for="(item, index) in barangList" :key="index">
                                    <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-100 bg-slate-50 hover:border-blue-200 transition">
                                        <!-- Icon -->
                                        <div class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center text-primary flex-shrink-0">
                                            <i class="fas fa-microchip text-sm"></i>
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-navy truncate" x-text="item.nama"></p>
                                            <p class="text-xs text-slate-400 mt-0.5" x-text="'Kode: ' + item.kode"></p>
                                        </div>

                                        <!-- Qty control -->
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            <button type="button" @click="item.jumlah = Math.max(1, item.jumlah - 1)"
                                                    class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:border-accent hover:text-accent transition text-xs">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="w-8 text-center font-bold text-sm text-navy" x-text="item.jumlah"></span>
                                            <button type="button" @click="item.jumlah = Math.min(item.stok_max, item.jumlah + 1)"
                                                    class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:border-accent hover:text-accent transition text-xs">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Hapus -->
                                        <button type="button" @click="hapusBarang(index)"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:bg-red-50 hover:text-red-500 transition flex-shrink-0">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Kanan: Detail peminjaman -->
                    <div class="space-y-5">

                        <div class="card p-6">
                            <h3 class="font-bold text-base text-navy mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                Detail Peminjaman
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="form-label">Tanggal Pinjam <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="tanggalPinjam"
                                           min="{{ date('Y-m-d') }}" class="inp" required>
                                </div>

                                <div>
                                    <label class="form-label">Tanggal Kembali <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="tanggalKembali"
                                           :min="tanggalPinjam || '{{ date('Y-m-d') }}'" class="inp" required>
                                </div>

                                <div>
                                    <label class="form-label">Keperluan <span class="text-red-500">*</span></label>
                                    <textarea x-model="keperluan" rows="4" class="inp"
                                              placeholder="Jelaskan tujuan peminjaman..." required
                                              style="resize:vertical;"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="card p-6 bg-surface border-blue-100">
                            <h3 class="font-bold text-sm text-navy mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;">Ringkasan</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Jenis barang</span>
                                    <span class="font-semibold text-navy" x-text="barangList.length + ' jenis'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Total unit</span>
                                    <span class="font-semibold text-navy" x-text="totalUnit + ' unit'"></span>
                                </div>
                                <div class="flex justify-between" x-show="tanggalPinjam && tanggalKembali">
                                    <span class="text-slate-500">Durasi</span>
                                    <span class="font-semibold text-navy" x-text="durasi + ' hari'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Error -->
                        <div x-show="errorMsg" x-cloak
                             class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-600 text-sm flex items-start gap-2">
                            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                            <span x-text="errorMsg"></span>
                        </div>

                        <!-- Tombol -->
                        <div class="flex flex-col gap-3">
                            <button type="button" @click="submitForm"
                                    class="btn-primary w-full justify-center"
                                    :disabled="barangList.length === 0"
                                    :class="barangList.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn-secondary w-full justify-center">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Hidden inputs — di-generate saat submit -->
                <div id="hiddenInputs"></div>

            </form>

        </main>
    </div>

    <script>
        function peminjamanForm() {
            return {
                // State pilihan sementara
                pilihan: {
                    alat_id: '',
                    nama: '',
                    kode: '',
                    jumlah: 1,
                    stok_max: 99,
                },

                // Daftar barang yang sudah ditambah
                barangList: [],

                // Detail peminjaman
                tanggalPinjam: '{{ date('Y-m-d') }}',
                tanggalKembali: '{{ date('Y-m-d') }}',
                keperluan: '',
                errorMsg: '',

                // Computed
                get totalUnit() {
                    return this.barangList.reduce((sum, i) => sum + i.jumlah, 0);
                },
                get durasi() {
                    if (!this.tanggalPinjam || !this.tanggalKembali) return 0;
                    const a = new Date(this.tanggalPinjam);
                    const b = new Date(this.tanggalKembali);
                    return Math.max(0, Math.round((b - a) / (1000 * 60 * 60 * 24)));
                },

                // Event handler saat alat dipilih
                onAlatChange(e) {
                    const opt = e.target.selectedOptions[0];
                    if (!opt || !opt.value) return;
                    this.pilihan.nama     = opt.dataset.nama;
                    this.pilihan.kode     = opt.dataset.kode;
                    this.pilihan.stok_max = parseInt(opt.dataset.stok) || 1;
                    this.pilihan.jumlah   = 1;
                },

                // Tambah ke daftar
                tambahBarang() {
                    if (!this.pilihan.alat_id) return;

                    // Cek sudah ada di list
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

                    // Reset pilihan
                    this.pilihan = { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 };
                },

                // Hapus dari daftar
                hapusBarang(index) {
                    this.barangList.splice(index, 1);
                },

                // Submit form
                submitForm() {
                    this.errorMsg = '';

                    if (this.barangList.length === 0) {
                        this.errorMsg = 'Tambahkan minimal satu barang terlebih dahulu.';
                        return;
                    }
                    if (!this.tanggalPinjam) {
                        this.errorMsg = 'Tanggal pinjam harus diisi.';
                        return;
                    }
                    if (!this.tanggalKembali) {
                        this.errorMsg = 'Tanggal kembali harus diisi.';
                        return;
                    }
                    if (new Date(this.tanggalKembali) < new Date(this.tanggalPinjam)) {
                        this.errorMsg = 'Tanggal kembali tidak boleh sebelum tanggal pinjam.';
                        return;
                    }
                    if (!this.keperluan.trim()) {
                        this.errorMsg = 'Keperluan harus diisi.';
                        return;
                    }

                    // Generate hidden inputs
                    const container = document.getElementById('hiddenInputs');
                    container.innerHTML = '';

                    this.barangList.forEach((item, i) => {
                        container.innerHTML += `<input type="hidden" name="items[${i}][alat_id]" value="${item.alat_id}">`;
                        container.innerHTML += `<input type="hidden" name="items[${i}][jumlah]" value="${item.jumlah}">`;
                    });

                    container.innerHTML += `<input type="hidden" name="tanggal_pinjam" value="${this.tanggalPinjam}">`;
                    container.innerHTML += `<input type="hidden" name="tanggal_kembali" value="${this.tanggalKembali}">`;
                    container.innerHTML += `<input type="hidden" name="keperluan" value="${this.keperluan}">`;

                    // Submit
                    this.$el.closest('form').submit();
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarHandler', () => ({
                collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                toggle() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebarCollapsed', this.collapsed);
                    this.updateMainContent();
                },
                updateMainContent() {
                    const mainContent = document.getElementById('mainContent');
                    if (mainContent) {
                        mainContent.className = this.collapsed
                            ? 'transition-all duration-300 ease-in-out ml-20'
                            : 'transition-all duration-300 ease-in-out ml-64';
                    }
                }
            }));
        });
    </script>
</body>
</html>