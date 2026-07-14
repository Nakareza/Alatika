@extends('layouts.dosen')

@section('title', 'Riwayat Peminjaman')

@section('content')

@if(session('success'))
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="space-y-6">

    {{-- Header --}}
    <div class="card p-6">
        <div class="flex items-start justify-between gap-4">

            <div>
                <h2 class="text-xl font-bold mb-1"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                    Riwayat Peminjaman
                </h2>

                <p class="text-sm"
                   style="color:#94a3b8;">
                    Daftar semua pengajuan peminjaman alat laboratorium
                </p>
            </div>

            <div class="hidden md:flex w-14 h-14 rounded-2xl items-center justify-center"
                 style="background:#EBF3FD;">
                <i class="fas fa-clock-rotate-left text-xl"
                   style="color:#185FA5;"></i>
            </div>

        </div>
    </div>

    {{-- Table Card --}}
    <div class="card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                {{-- Table Head --}}
                <thead style="background:#F8FBFF;">

                    <tr class="border-b"
                        style="border-color:#EBF3FD;">

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide whitespace-nowrap"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Kode / Tanggal
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Alat
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wide whitespace-nowrap"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Deadline
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Status
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wide"
                            style="color:#94a3b8;font-family:'Plus Jakarta Sans',sans-serif;">
                            Aksi / Info
                        </th>

                    </tr>

                </thead>

                {{-- Table Body --}}
                <tbody>

                    @forelse($riwayat as $p)

                    <tr class="transition-colors hover:bg-[#F8FBFF]"
                        style="border-bottom:1px solid #F1F5F9;">

                        {{-- Kode --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-bold"
                               style="color:#1E2B4A;">
                                {{ $p->kode_peminjaman }}
                            </p>

                            <p class="text-xs mt-1"
                               style="color:#94a3b8;">
                                {{ $p->updated_at->format('d M Y • H:i') }}
                            </p>

                        </td>

                        {{-- Alat --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-semibold"
                               style="color:#1E2B4A;">
                                {{ $p->item_name }}
                            </p>

                            <p class="text-xs mt-1"
                               style="color:#94a3b8;">
                                {{ $p->jumlah }} unit
                            </p>

                        </td>

                        {{-- Deadline --}}
                        <td class="px-6 py-5">

                            <p class="text-sm font-semibold"
                               style="color:#1E2B4A;">
                                {{ $p->tanggal_kembali->format('d M Y') }}
                            </p>

                            @if($p->tanggal_dikembalikan && $p->status === 'selesai')

                                <p class="text-xs mt-1 text-green-600">
                                    Dikembalikan:
                                    {{ $p->tanggal_dikembalikan->format('d M Y') }}
                                </p>

                            @endif

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-5 text-center">

                            <span class="badge
                                @if($p->status === 'dipinjam') badge-info
                                @elseif($p->status === 'selesai') badge-success
                                @elseif($p->status === 'ditolak') badge-danger
                                @else badge-warning
                                @endif">

                                {{ $p->status_label }}

                            </span>

                        </td>

                        {{-- Action --}}
                        <td class="px-6 py-5">

                            @if($p->status === 'menunggu_verifikasi' && $p->foto_bukti_kembali)

                                <a href="{{ $p->foto_bukti_url }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 text-xs font-semibold transition-colors"
                                   style="color:#185FA5;"
                                   onmouseover="this.style.color='#1E2B4A'"
                                   onmouseout="this.style.color='#185FA5'">

                                    <i class="fas fa-image"></i>
                                    Lihat Bukti

                                </a>

                            @elseif($p->status === 'dipinjam')

                                <div class="flex flex-col items-start gap-2">
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
                                        <div class="inline-flex items-start gap-1.5 px-2 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 max-w-[280px]">
                                            <i class="fas fa-check-circle shrink-0 mt-0.5"></i>
                                            <div class="break-words text-left">
                                                <div class="font-semibold">Disetujui oleh:</div>
                                                <div class="text-[11px]">{{ implode(', ', $approvers) }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal-kembali-{{ $p->id }}')"
                                        class="w-full px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition font-semibold text-xs flex items-center justify-center gap-1.5 shadow-sm border border-emerald-100">
                                        <i class="fas fa-undo"></i>
                                        Kembalikan Alat
                                    </button>

                                    <div class="flex flex-col items-center gap-1 text-[10px] text-slate-400 w-full mt-1">
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
                                            <div class="break-words text-left">
                                                <div class="font-semibold">Menunggu persetujuan dari:</div>
                                                <div class="text-[11px]">{{ $nextApprover }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="inline-flex items-start gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 max-w-[280px]">
                                            <i class="fas fa-hourglass-start shrink-0 mt-0.5"></i>
                                            <span class="break-words text-left">Menunggu persetujuan</span>
                                        </div>
                                    @endif
                                </div>

                            @else

                                <span class="text-xs"
                                      style="color:#CBD5E1;">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="px-6 py-16 text-center">

                            <div class="flex flex-col items-center">

                                <div class="w-20 h-20 rounded-3xl flex items-center justify-center mb-4"
                                     style="background:#F8FBFF;border:1px solid #EBF3FD;">

                                    <i class="fas fa-box-open text-3xl"
                                       style="color:#B5D4F4;"></i>

                                </div>

                                <h3 class="text-base font-bold mb-1"
                                    style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">

                                    Belum Ada Riwayat
                                </h3>

                                <p class="text-sm mb-5"
                                   style="color:#94a3b8;">

                                    Kamu belum pernah mengajukan peminjaman alat
                                </p>

                                <a href="{{ route('dosen.peminjaman.ajukan') }}"
                                   class="btn btn-primary">

                                    <i class="fas fa-plus"></i>
                                    Ajukan Peminjaman

                                </a>

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Modals for returning tools --}}
@foreach($riwayat as $p)
    @if($p->status === 'dipinjam')
    <x-modal name="kembali-{{ $p->id }}" title="Kirim Pengembalian Alat" size="lg">
        <form id="form-kembali-{{ $p->id }}" action="{{ route('dosen.peminjaman.kembalikan', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-4" x-data="cameraHandler('{{ $p->id }}')">
            @csrf
            <div>
                <div class="p-4 rounded-xl text-left text-sm mb-4" style="background:#f5f8ff;border:1px solid #ebf3fd;color:#1e2b4a;">
                    <p class="font-semibold text-xs uppercase tracking-wider text-slate-500 mb-2">Detail Peminjaman</p>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                        <div>
                            <span class="text-xs text-slate-400">Nama Alat:</span>
                            <p class="font-semibold text-xs">{{ $p->item_name }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Jumlah:</span>
                            <p class="font-semibold text-xs">{{ $p->jumlah }} Unit</p>
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

                <!-- Input File -->
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
                        
                        <!-- Canvas tersembunyi for image capture -->
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
@endforeach

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