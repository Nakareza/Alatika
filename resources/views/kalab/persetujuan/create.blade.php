@extends('layouts.kalab')

@section('title', 'Input Peminjaman Manual')

@section('content')

    @if(session('error'))
    <div class="mb-6 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="max-w-3xl mx-auto" x-data="{ selectedUser: '{{ old('user_id') }}', selectedCategory: '' }">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('kalab.persetujuan') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-[#1E2B4A] hover:bg-slate-50 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-[#1E2B4A]" style="font-family:'Plus Jakarta Sans',sans-serif;">Input Peminjaman Manual</h2>
                <p class="text-sm text-slate-500">Mendaftarkan peminjaman baru secara langsung ke sistem</p>
            </div>
        </div>

        <form action="{{ route('kalab.peminjaman.store-manual') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card p-6 space-y-6 bg-white rounded-2xl shadow-sm border border-slate-100">
                
                {{-- Peminjam Section --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Pilih Peminjam <span class="text-red-500">*</span></label>
                        <select name="user_id" id="user_select" required class="inp w-full" x-model="selectedUser" style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                            <option value="">-- Pilih User --</option>
                            <option value="non_user">-- Peminjam Luar / Organisasi (Ketik Manual) --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->name }} ({{ ucfirst($u->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="non_user_input_wrapper" x-show="selectedUser === 'non_user'" x-transition class="transition-all">
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Nama Peminjam / Organisasi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_peminjam_non_user" id="nama_peminjam_non_user" class="inp w-full" placeholder="Ketik nama peminjam / organisasi..." value="{{ old('nama_peminjam_non_user') }}" :required="selectedUser === 'non_user'">
                        <p class="text-xs text-slate-500 mt-1">Pilih user terdaftar untuk mengaitkan histori ke akun yang bersangkutan. Pilih opsi luar/user organisasi bila yang dipinjamkan adalah UKM, organisasi, atau peminjam non-akun.</p>
                    </div>
                </div>

                {{-- Kategori & Alat (Cascading) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Kategori Alat <span class="text-red-500">*</span></label>
                        <select id="kategori_select" required class="inp w-full" x-model="selectedCategory" @change="onCategoryChange()" style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Pilih Alat <span class="text-red-500">*</span></label>
                        <select name="alat_id" id="alat_select" required class="inp w-full" disabled style="appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;background-size:14px;">
                            <option value="">-- Pilih Kategori Terlebih Dahulu --</option>
                            @foreach($alat as $a)
                                <option value="{{ $a->id }}" data-kategori-id="{{ $a->kategori_id }}" data-stok="{{ $a->stok_tersedia }}" style="display:none;">
                                    {{ $a->nama }} (Stok Tersedia: {{ $a->stok_tersedia }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jumlah & Keperluan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Jumlah Unit <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlah_input" min="1" required class="inp w-full" value="{{ old('jumlah', 1) }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Keperluan Peminjaman <span class="text-red-500">*</span></label>
                        <input type="text" name="keperluan" required class="inp w-full" placeholder="Contoh: Kegiatan UKM, Rapat Organisasi" value="{{ old('keperluan') }}">
                    </div>
                </div>

                {{-- Tanggal & Lampiran Surat --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Tanggal Pinjam <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_pinjam" required class="inp w-full" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Tanggal Kembali <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kembali" required class="inp w-full" value="{{ old('tanggal_kembali', date('Y-m-d')) }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#1E2B4A] uppercase tracking-wider mb-2">Lampiran Surat <span class="text-slate-400">(Opsional)</span></label>
                        <input type="file" name="surat_keterangan" class="inp w-full pt-1.5" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                        <p class="text-[10px] text-slate-400 mt-1">PDF, DOC, DOCX, PNG, JPG (Maks. 5MB)</p>
                    </div>
                </div>

                {{-- Custom Approvals --}}
                <div class="p-4 rounded-xl border border-slate-100 bg-[#F8FBFF]">
                    <label class="block text-xs font-bold text-[#185FA5] uppercase tracking-wider mb-3">Persyaratan Persetujuan (Approver)</label>
                    <p class="text-xs text-slate-500 mb-4">Centang siapa saja pihak yang wajib menyetujui peminjaman ini agar statusnya dapat disetujui (Dipinjam). Karena Anda adalah Kepala Laboratorium, pilihan persetujuan Kepala Lab dihilangkan secara otomatis. Jika tidak ada yang dicentang, peminjaman akan langsung disetujui sistem.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-6">
                        <label class="inline-flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="approvers[]" value="admin" class="w-4.5 h-4.5 rounded border-slate-300 text-[#185FA5] focus:ring-[#185FA5]" {{ is_array(old('approvers')) && in_array('admin', old('approvers')) ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700">Admin/Teknisi</span>
                        </label>
                        
                        <label class="inline-flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="approvers[]" value="kaprodi" class="w-4.5 h-4.5 rounded border-slate-300 text-[#185FA5] focus:ring-[#185FA5]" {{ is_array(old('approvers')) && in_array('kaprodi', old('approvers')) ? 'checked' : '' }}>
                            <span class="text-sm font-semibold text-slate-700">Kaprodi</span>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="px-6 py-3 bg-[#185FA5] hover:bg-[#1e2b4a] text-white font-semibold rounded-xl transition flex-1 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Daftarkan Peminjaman
                    </button>
                    <a href="{{ route('kalab.persetujuan') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-center flex-1">
                        Batal
                    </a>
                </div>

            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const alatSelect = document.getElementById('alat_select');
        const jumlahInput = document.getElementById('jumlah_input');

        function onCategoryChange() {
            const catSelect = document.getElementById('kategori_select');
            const selectedCatId = catSelect.value;
            
            alatSelect.value = "";
            jumlahInput.value = 1;
            jumlahInput.removeAttribute('max');

            if (!selectedCatId) {
                alatSelect.disabled = true;
                alatSelect.innerHTML = '<option value="">-- Pilih Kategori Terlebih Dahulu --</option>';
                return;
            }

            alatSelect.disabled = false;
            
            // Build tool options
            let optionsHTML = '<option value="">-- Pilih Alat --</option>';
            
            // Find all matching options from the pre-rendered array
            @json($alat).forEach(a => {
                if (parseInt(a.kategori_id) === parseInt(selectedCatId)) {
                    optionsHTML += `<option value="${a.id}" data-stok="${a.stok_tersedia}">${a.nama} (Stok Tersedia: ${a.stok_tersedia})</option>`;
                }
            });
            
            alatSelect.innerHTML = optionsHTML;
        }

        alatSelect.addEventListener('change', function() {
            const selectedOpt = this.selectedOptions[0];
            if (selectedOpt && selectedOpt.value) {
                const maxStok = parseInt(selectedOpt.dataset.stok) || 1;
                jumlahInput.max = maxStok;
                if (parseInt(jumlahInput.value) > maxStok) {
                    jumlahInput.value = maxStok;
                }
            } else {
                jumlahInput.removeAttribute('max');
            }
        });
    </script>
    @endpush

@endsection
