@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div x-data="{}">

    @if(session('success'))
    <div class="mb-4 rounded-xl p-4 text-sm flex items-center gap-2"
         style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <x-table title="Riwayat Peminjaman">

        <thead class="sticky top-0 bg-[#F8FBFF] border-b border-[#EBF3FD]">

            <tr>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Kode / Tanggal
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Alat
                </th>

                <th class="py-4 px-6 text-left text-xs font-bold uppercase text-slate-500">
                    Deadline / Kembali
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Status
                </th>

                <th class="py-4 px-6 text-center text-xs font-bold uppercase text-slate-500">
                    Aksi / Info
                </th>

            </tr>

        </thead>

        <tbody class="divide-y divide-[#EBF3FD]">

            @forelse($riwayat as $p)

            <tr class="hover:bg-[#F8FBFF] transition">

                {{-- Kode --}}
                <td class="px-6 py-5">

                    <div class="font-bold text-[#1E2B4A]">
                        {{ $p->kode_peminjaman }}
                    </div>

                    <div class="text-xs text-slate-400 mt-1">
                        {{ $p->updated_at->format('d M Y H:i') }}
                    </div>

                </td>

                {{-- Alat --}}
                <td class="px-6 py-5">

                    <div class="font-semibold text-[#1E2B4A]">
                        {{ $p->item_name }}
                        @if($p->borrowable_type === 'App\Models\ToolSet')
                            <span class="inline-block px-1.5 py-0.5 ml-1 rounded text-[10px] font-bold bg-purple-100 text-purple-700">Tool Set</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500 mt-1">
                        {{ $p->jumlah }} {{ $p->borrowable_type === 'App\Models\ToolSet' ? 'Set' : 'Unit' }}
                    </div>

                    @if($p->borrowable_type === 'App\Models\ToolSet')
                        <button type="button" 
                                @click="$dispatch('open-modal-components-{{ $p->id }}')"
                                class="mt-1.5 inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 font-semibold hover:underline">
                            <i class="fas fa-list text-[10px]"></i> Lihat Komponen
                        </button>
                    @endif

                </td>

                {{-- Deadline --}}
                <td class="px-6 py-5">

                    <div class="font-medium text-[#1E2B4A]">
                        {{ $p->tanggal_kembali->format('d M Y') }}
                    </div>

                    @if($p->tanggal_dikembalikan && $p->status === 'selesai')

                        <div class="text-xs text-emerald-600 mt-1">
                            Kembali:
                            {{ $p->tanggal_dikembalikan->format('d M Y') }}
                        </div>

                    @endif

                </td>

                {{-- Status --}}
                <td class="px-6 py-5 text-center">

                    <span class="badge {{ $p->status_config['color'] }}">
                        {{ $p->status_label }}
                    </span>

                </td>

                {{-- Aksi --}}
                <td class="px-6 py-5">

                    @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)

                        <a href="{{ $p->foto_bukti_url }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 text-[#185FA5] hover:text-[#378ADD] font-semibold text-sm transition">

                            <i class="fas fa-image"></i>

                            Lihat Foto Bukti

                        </a>

                    @elseif($p->status === 'dipinjam')

                        <div class="flex flex-col items-start gap-3">
                            {{-- Approval Status --}}
                            @php
                                $approvalStatus = $p->approval_status;
                                $approvers = [];
                                if ($approvalStatus['admin_approved']) {
                                    $approvers[] = 'Admin (' . $approvalStatus['admin_approved_at']->format('d M') . ')';
                                }
                                if ($approvalStatus['kalab_approved']) {
                                    $approvers[] = 'Kalab (' . $approvalStatus['kalab_approved_at']->format('d M') . ')';
                                }
                                if ($approvalStatus['kaprodi_approved']) {
                                    $approvers[] = 'Kaprodi (' . $approvalStatus['kaprodi_approved_at']->format('d M') . ')';
                                }
                            @endphp
                            @if(!empty($approvers))
                                <div class="inline-flex items-start gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 max-w-[280px]">
                                    <i class="fas fa-check-circle shrink-0 mt-0.5"></i>
                                    <div class="break-words">
                                        <div class="font-semibold">Disetujui oleh:</div>
                                        {{ implode(', ', $approvers) }}
                                    </div>
                                </div>
                            @endif

                            {{-- Return Button --}}
                            <button
                                type="button"
                                @click="$dispatch('open-modal-kembali-{{ $p->id }}')"
                                class="w-full px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition font-semibold text-xs flex items-center justify-center gap-1.5 shadow-sm border border-emerald-100">
                                <i class="fas fa-undo"></i>
                                Kembalikan Alat
                            </button>

                            <div class="flex flex-col items-center gap-1 text-[10px] text-slate-400 w-full">
                                <span>Atau via Telegram:</span>
                                <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[9px]">
                                    /kembali {{ $p->kode_peminjaman }}
                                </code>
                            </div>
                        </div>

                    @elseif($p->status === 'ditolak')

                        <div class="inline-flex items-start gap-1.5 px-3 py-2 rounded-xl text-xs font-medium bg-red-50 text-red-600 border border-red-100 max-w-[280px] text-left">
                            <i class="fas fa-times-circle shrink-0 mt-0.5"></i>
                            <div class="break-words">
                                <div class="font-semibold">Ditolak</div>
                                <div class="text-[11px] mt-1">{{ $p->rejected_reason ?? 'Tidak ada alasan' }}</div>
                            </div>
                        </div>

                    @elseif($p->status === 'pending')

                        <div class="flex flex-col items-start gap-2">
                            @php
                                $nextApprover = $p->next_approver_role;
                            @endphp
                            @if($nextApprover)
                                <div class="inline-flex items-start gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 max-w-[280px]">
                                    <i class="fas fa-hourglass-start shrink-0 mt-0.5"></i>
                                    <div class="break-words">
                                        <div class="font-semibold">Menunggu persetujuan dari:</div>
                                        {{ $nextApprover }}
                                    </div>
                                </div>
                            @else
                                <div class="inline-flex items-start gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 max-w-[280px]">
                                    <i class="fas fa-hourglass-start shrink-0 mt-0.5"></i>
                                    <span class="break-words">Menunggu persetujuan</span>
                                </div>
                            @endif

                            <form action="{{ route('mahasiswa.peminjaman.cancel', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan peminjaman ini?');">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 transition font-semibold text-xs flex items-center justify-center gap-1.5 shadow-sm border border-rose-100">
                                    <i class="fas fa-times-circle"></i>
                                    Batalkan Pengajuan
                                </button>
                            </form>
                        </div>

                    @else

                        <span class="text-slate-300 text-lg">
                            —
                        </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5" class="py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div class="w-16 h-16 rounded-full bg-[#EBF3FD] flex items-center justify-center mb-4">

                            <i class="fas fa-inbox text-2xl text-[#B5D4F4]"></i>

                        </div>

                        <h3 class="font-bold text-lg text-[#1E2B4A]">

                            Belum Ada Riwayat Peminjaman

                        </h3>

                        <p class="text-slate-500 mt-1">

                            Silakan ajukan peminjaman alat terlebih dahulu.

                        </p>

                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}"
                        class="btn btn-primary mt-5">

                            <i class="fas fa-plus"></i>

                            Ajukan Peminjaman

                        </a>

                    </div>

                </td>

            </tr>

            @endforelse

        </tbody>

    </x-table>

    {{-- Modals for returning tools --}}
    @foreach($riwayat as $p)
        @if($p->status === 'dipinjam')
        <x-modal name="kembali-{{ $p->id }}" title="Kirim Pengembalian Alat" size="lg">
            <form id="form-kembali-{{ $p->id }}" action="{{ route('mahasiswa.peminjaman.kembalikan', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4" x-data="cameraHandler('{{ $p->id }}')">
                @csrf
                <div>
                    <div class="p-4 rounded-xl text-left text-sm mb-4" style="background:#f5f8ff;border:1px solid #ebf3fd;color:#1e2b4a;">
                        <p class="font-semibold text-xs uppercase tracking-wider text-slate-500 mb-2">Detail Peminjaman</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                            <div>
                                <span class="text-xs text-slate-400">Nama Alat/Tool Set:</span>
                                <p class="font-semibold text-xs">{{ $p->item_name }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400">Jumlah:</span>
                                <p class="font-semibold text-xs">{{ $p->jumlah }} {{ $p->borrowable_type === 'App\Models\ToolSet' ? 'Set' : 'Unit' }}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-xs text-slate-400">Kode Peminjaman:</span>
                                <p class="font-mono font-bold text-[#185FA5]">{{ $p->kode_peminjaman }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle Upload vs Kamera -->
                    <div class="flex border-b border-slate-100 mb-4">
                        <button type="button" @click="setMode('upload')" 
                                :class="mode === 'upload' ? 'border-b-2 border-emerald-600 text-emerald-600 font-bold' : 'text-slate-500'" 
                                class="pb-2 px-4 font-semibold text-xs transition">
                            <i class="fas fa-file-upload mr-1"></i> Unggah File
                        </button>
                        <button type="button" @click="setMode('camera')" 
                                :class="mode === 'camera' ? 'border-b-2 border-emerald-600 text-emerald-600 font-bold' : 'text-slate-500'" 
                                class="pb-2 px-4 font-semibold text-xs transition">
                            <i class="fas fa-camera mr-1"></i> Gunakan Kamera
                        </button>
                    </div>

                    <!-- Input File (di-share/dipakai bersama) -->
                    <div x-show="mode === 'upload'">
                        <label class="form-label font-semibold text-slate-700">
                            Foto Bukti Pengembalian <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <input type="file" :id="'file-input-' + id" name="foto_bukti_kembali" 
                           :required="mode === 'upload' || (mode === 'camera' && !capturedImage)" 
                           class="inp w-full" accept="image/*" x-show="mode === 'upload'">

                    <!-- Tampilan Kamera -->
                    <div x-show="mode === 'camera'" class="space-y-3">
                        <label class="form-label font-semibold text-slate-700">
                            Ambil Foto Bukti <span class="text-red-500">*</span>
                        </label>

                        <!-- Preview Area -->
                        <div class="relative w-full aspect-video rounded-2xl bg-slate-950 overflow-hidden flex items-center justify-center border border-slate-100 shadow-inner">
                            <!-- Video feed -->
                            <video :id="'video-' + id" x-show="!capturedImage" class="w-full h-full object-cover" autoplay playsinline></video>
                            
                            <!-- Captured image preview -->
                            <img :src="capturedImage" x-show="capturedImage" class="w-full h-full object-cover">
                            
                            <!-- Canvas tersembunyi untuk pengambilan gambar -->
                            <canvas :id="'canvas-' + id" class="hidden"></canvas>

                            <!-- Placeholder/Status kamera -->
                            <div x-show="!streamActive && !capturedImage" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                                <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-slate-300">
                                    <i class="fas fa-video-slash"></i>
                                </div>
                                <span class="text-xs">Kamera belum aktif</span>
                            </div>
                        </div>

                        <!-- Tombol Kontrol Kamera -->
                        <div class="flex gap-2">
                            <button type="button" x-show="!streamActive && !capturedImage" @click="startCamera" 
                                    class="flex-1 py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold flex items-center justify-center gap-1.5 transition">
                                <i class="fas fa-video"></i> Aktifkan Kamera
                            </button>
                            <button type="button" x-show="streamActive && !capturedImage" @click="capture" 
                                    class="flex-1 py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition shadow-md shadow-emerald-100">
                                <i class="fas fa-camera"></i> Ambil Gambar
                            </button>
                            <button type="button" x-show="capturedImage" @click="retake" 
                                    class="flex-1 py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                                <i class="fas fa-redo"></i> Foto Ulang
                            </button>
                            <button type="button" x-show="streamActive" @click="stopCamera" 
                                    class="py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition">
                                Matikan
                            </button>
                        </div>
                    </div>

                    <p class="text-xs text-slate-400 mt-2">
                        Silakan unggah foto kondisi fisik alat yang dikembalikan secara lengkap (kabel, komponen, dll). Maksimal 5MB.
                    </p>
                </div>
                
                <x-slot name="footer">
                    <button type="button" @click="closeModal" class="flex-1 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium text-xs">
                        Batal
                    </button>
                    <button type="submit" form="form-kembali-{{ $p->id }}" :disabled="mode === 'camera' && !capturedImage" class="flex-1 btn btn-primary font-medium text-xs">
                        Kirim Pengembalian
                    </button>
                </x-slot>
            </form>
        </x-modal>
        @endif
        
        {{-- Modal Detail Komponen Tool Set --}}
        @if($p->borrowable_type === 'App\Models\ToolSet' && $p->borrowable)
        <x-modal name="components-{{ $p->id }}" title="Detail Komponen Tool Set" size="md">
            <div class="mt-4 space-y-3">
                <p class="text-sm font-semibold text-[#1E2B4A]">{{ $p->borrowable->nama_tool_set }} ({{ $p->borrowable->kode_tool_set }})</p>
                <div class="border border-slate-100 rounded-xl overflow-hidden text-sm">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2 font-semibold text-slate-500">Nama Komponen</th>
                                <th class="px-4 py-2 font-semibold text-slate-500 text-center" style="width: 80px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($p->borrowable->details as $d)
                            <tr>
                                <td class="px-4 py-2.5 text-slate-700">{{ $d->nama_komponen }}</td>
                                <td class="px-4 py-2.5 text-slate-600 text-center font-semibold">{{ $d->jumlah }} {{ $d->satuan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal-components-{{ $p->id }}')" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 transition font-medium">
                    Tutup
                </button>
            </x-slot>
        </x-modal>
        @endif
    @endforeach
</div>

@push('scripts')
<script>
function cameraHandler(id) {
    return {
        id: id,
        mode: 'upload',
        streamActive: false,
        stream: null,
        capturedImage: null,

        init() {
            this.$watch('open', value => {
                if (!value) {
                    this.stopCamera();
                }
            });
        },

        setMode(newMode) {
            this.mode = newMode;
            if (newMode === 'upload') {
                this.stopCamera();
            }
        },

        async startCamera() {
            try {
                this.capturedImage = null;
                const constraints = {
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };
                
                this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                this.streamActive = true;
                
                this.$nextTick(() => {
                    const video = document.getElementById('video-' + this.id);
                    if (video) {
                        video.srcObject = this.stream;
                    }
                });
            } catch (err) {
                console.error("Gagal mengakses kamera:", err);
                alert("Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.");
                this.mode = 'upload';
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.streamActive = false;
        },

        capture() {
            const video = document.getElementById('video-' + this.id);
            const canvas = document.getElementById('canvas-' + this.id);
            if (video && canvas) {
                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const dataUrl = canvas.toDataURL('image/jpeg');
                this.capturedImage = dataUrl;
                
                this.stopCamera();
                this.syncFile(dataUrl);
            }
        },

        syncFile(dataUrl) {
            try {
                const blob = this.dataURItoBlob(dataUrl);
                const file = new File([blob], 'bukti-kembali-' + this.id + '.jpg', { type: 'image/jpeg' });
                const fileInput = document.getElementById('file-input-' + this.id);
                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            } catch (e) {
                console.error("Gagal menyinkronkan file kamera:", e);
            }
        },

        retake() {
            this.capturedImage = null;
            const fileInput = document.getElementById('file-input-' + this.id);
            if (fileInput) {
                fileInput.value = '';
            }
            this.startCamera();
        },

        closeModal() {
            this.stopCamera();
            window.dispatchEvent(new CustomEvent('close-modal-kembali-' + this.id));
        },

        dataURItoBlob(dataURI) {
            const byteString = atob(dataURI.split(',')[1]);
            const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], {type: mimeString});
        }
    };
}
</script>
@endpush

@endsection