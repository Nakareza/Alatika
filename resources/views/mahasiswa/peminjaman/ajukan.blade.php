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

    <style>
        * { box-sizing: border-box; }
        body { background: #F5F8FF; font-family: 'Inter', sans-serif; color: #1E2B4A; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }

        .card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #EBF3FD;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }

        .inp {
            width: 100%; padding: 0.75rem 1rem;
            background: #F5F8FF; border: 1.5px solid #D4E6F8;
            border-radius: 12px; font-family: 'Inter', sans-serif;
            font-size: 0.875rem; color: #1E2B4A; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .inp:focus {
            border-color: #378ADD;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(55,138,221,0.10);
        }
        .inp::placeholder { color: #A0BBCC; }

        .form-label {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.875rem; font-weight: 600;
            color: #1E2B4A; margin-bottom: 0.5rem;
        }

        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.8rem 1.6rem;
            background: #1E2B4A;
            color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700;
            font-size: 0.875rem; border-radius: 12px; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 4px 14px rgba(30,43,74,0.22);
        }
        .btn-primary:hover {
            background: #185FA5;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30,43,74,0.28);
        }

        .btn-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 0.8rem 1.6rem; background: #fff; color: #1E2B4A;
            font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 0.875rem;
            border-radius: 12px; border: 1.5px solid #D4E6F8; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 2px 10px rgba(30,43,74,0.07); text-decoration: none;
        }
        .btn-secondary:hover {
            border-color: #378ADD;
            background: #F5F8FF;
            transform: translateY(-1px);
        }

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

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased" x-data="{}">

    <x-sidebar-mahasiswa />

    <div id="mainContent" class="transition-all duration-300 ease-in-out ml-64">

        <x-header-dashboard :title="'Ajukan Peminjaman'" />

        <main class="p-8 min-h-screen" x-data="peminjamanForm()">

            @if(session('error'))
            <div class="mb-6 rounded-xl p-4 text-sm flex items-center gap-2"
                 style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
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
                            <h3 class="font-bold text-base mb-5"
                                style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                                <i class="fas fa-plus-circle mr-2" style="color:#378ADD;"></i>
                                Tambah Barang
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Pilih alat -->
                                <div class="sm:col-span-2">
                                    <label class="form-label">Pilih Alat</label>
                                    <select x-model="pilihan.alat_id" class="inp" @change="onAlatChange"
                                            style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
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
                                        <button type="button"
                                                @click="pilihan.jumlah = Math.max(1, pilihan.jumlah - 1)"
                                                class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition flex-shrink-0"
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
                                                class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition flex-shrink-0"
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
                                        class="btn-accent"
                                        :disabled="!pilihan.alat_id"
                                        :class="!pilihan.alat_id ? 'opacity-50 cursor-not-allowed' : ''">
                                    <i class="fas fa-plus"></i> Tambah ke Daftar
                                </button>
                            </div>
                        </div>

                        <!-- Daftar barang yang dipilih -->
                        <div class="card p-6">
                            <h3 class="font-bold text-base mb-4"
                                style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                                <i class="fas fa-list mr-2" style="color:#185FA5;"></i>
                                Daftar Barang
                                <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full"
                                      style="background:#EBF3FD;color:#185FA5;"
                                      x-text="barangList.length + ' item'"></span>
                            </h3>

                            <!-- Empty state -->
                            <div x-show="barangList.length === 0" class="py-10 text-center">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"
                                     style="background:#EBF3FD;">
                                    <i class="fas fa-box-open" style="color:#B5D4F4;"></i>
                                </div>
                                <p class="text-sm" style="color:#94a3b8;">Belum ada barang ditambahkan.</p>
                                <p class="text-xs mt-1" style="color:#B5D4F4;">Pilih alat di atas lalu klik "Tambah ke Daftar".</p>
                            </div>

                            <!-- List -->
                            <div class="space-y-3" x-show="barangList.length > 0">
                                <template x-for="(item, index) in barangList" :key="index">
                                    <div class="flex items-center gap-4 p-4 rounded-xl transition"
                                         style="border:1px solid #EBF3FD;background:#F5F8FF;"
                                         onmouseover="this.style.borderColor='#B5D4F4';this.style.background='white';"
                                         onmouseout="this.style.borderColor='#EBF3FD';this.style.background='#F5F8FF';">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
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
                                        <div class="flex items-center gap-2 flex-shrink-0">
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
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition flex-shrink-0"
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

                    <!-- Kanan: Detail peminjaman -->
                    <div class="space-y-5">

                        <div class="card p-6">
                            <h3 class="font-bold text-base mb-5"
                                style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                                <i class="fas fa-calendar-alt mr-2" style="color:#185FA5;"></i>
                                Detail Peminjaman
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="form-label">Tanggal Pinjam <span class="text-red-500">*</span></label>
                                    <input type="date" x-model="tanggalPinjam" @change="filterWeekend"
                                           min="{{ date('Y-m-d') }}" class="inp" required>
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

                        <!-- Error -->
                        <div x-show="errorMsg" x-cloak
                             class="rounded-xl p-4 text-sm flex items-start gap-2"
                             style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
                            <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                            <span x-text="errorMsg"></span>
                        </div>

                        <!-- Tombol -->
                        <div class="flex flex-col gap-3">
                            <button type="button" @click="submitForm"
                                    class="btn-primary w-full"
                                    :disabled="barangList.length === 0"
                                    :class="barangList.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                                <i class="fas fa-paper-plane"></i> Ajukan Peminjaman
                            </button>
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn-secondary w-full">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>

                    </div>
                </div>

                <div id="hiddenInputs"></div>
            </form>

        </main>
    </div>

    <script>
        function peminjamanForm() {
            return {
                pilihan: { alat_id: '', nama: '', kode: '', jumlah: 1, stok_max: 99 },
                barangList: [],
                tanggalPinjam: '{{ date('Y-m-d') }}',
                tanggalKembali: '{{ date('Y-m-d') }}',
                keperluan: '',
                errorMsg: '',

                get totalUnit() {
                    return this.barangList.reduce((sum, i) => sum + i.jumlah, 0);
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
                    if (!this.keperluan.trim()) { this.errorMsg = 'Keperluan harus diisi.'; return; }
                    this.tanggalKembali = this.tanggalPinjam;

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