{{-- Telegram Connection Component --}}
{{-- Usage: @include('components.telegram-connect') --}}

<div x-data="telegramConnect()" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 bg-gradient-to-r from-[#0088cc] to-[#0099e6] flex items-center gap-3">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fab fa-telegram text-white text-xl"></i>
        </div>
        <div>
            <h3 class="text-white font-bold text-sm">Telegram Bot</h3>
            <p class="text-white/80 text-xs">Notifikasi & Perintah via Telegram</p>
        </div>
    </div>

    <div class="p-6">
        {{-- Connected State --}}
        @if(Auth::user()->hasTelegram())
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Telegram Terhubung</p>
                    <p class="text-xs text-slate-500">ID: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">{{ Auth::user()->telegram_chat_id }}</code></p>
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4">
                <p class="text-xs text-emerald-700">
                    <i class="fas fa-bell mr-1"></i> Anda akan menerima notifikasi peminjaman, reminder deadline, dan update status langsung di Telegram.
                </p>
            </div>

            <div class="flex gap-3">
                <button @click="testNotification()" 
                        :disabled="loading"
                        class="flex-1 px-4 py-2.5 bg-[#0088cc] hover:bg-[#006699] text-white text-sm font-semibold rounded-xl transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <i class="fab fa-telegram"></i>
                    <span x-text="loading ? 'Mengirim...' : 'Test Notifikasi'"></span>
                </button>
                <button @click="disconnect()" 
                        :disabled="loading"
                        class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-semibold rounded-xl transition-all border border-red-200 disabled:opacity-50">
                    <i class="fas fa-unlink"></i> Putus
                </button>
            </div>
        </div>
        @else
        {{-- Disconnected State --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-link text-slate-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">Belum Terhubung</p>
                    <p class="text-xs text-slate-500">Hubungkan akun Anda dengan Telegram</p>
                </div>
            </div>

            {{-- Step by step instructions --}}
            <div class="space-y-3 mb-5">
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">1</span>
                    @php $botUsername = config('telegram.bot_username'); @endphp
                    <p class="text-xs text-slate-600">Buka Telegram, cari <a href="https://t.me/{{ $botUsername }}" target="_blank" class="text-blue-600 font-semibold hover:underline">{{ '@' . $botUsername }}</a> lalu klik <b>Start</b></p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">2</span>
                    <p class="text-xs text-slate-600">Klik tombol <b>"Generate Kode"</b> di bawah untuk mendapat kode verifikasi</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">3</span>
                    <p class="text-xs text-slate-600">Kirim ke bot: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-[10px]">/link KODE_ANDA</code></p>
                </div>
            </div>

            {{-- Code Display --}}
            <div x-show="linkCode" x-transition class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <p class="text-xs text-blue-600 font-medium mb-2"><i class="fas fa-key mr-1"></i> Kode Verifikasi (berlaku 10 menit):</p>
                <div class="flex items-center gap-3">
                    <code class="flex-1 text-center text-2xl font-bold text-blue-800 tracking-[0.3em] bg-white py-2 rounded-lg border border-blue-200" x-text="linkCode"></code>
                    <button @click="copyCode()" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Salin">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-[10px] text-blue-500 mt-2 text-center">
                    Kirim ke Telegram: <code class="bg-white px-1 rounded">/link <span x-text="linkCode"></span></code>
                </p>
            </div>

            {{-- Generate Button --}}
            <button @click="generateCode()" 
                    :disabled="loading"
                    class="w-full px-4 py-2.5 bg-gradient-to-r from-[#0088cc] to-[#0099e6] hover:from-[#006699] hover:to-[#0088cc] text-white text-sm font-semibold rounded-xl transition-all flex items-center justify-center gap-2 disabled:opacity-50 shadow-sm hover:shadow-md">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-key'"></i>
                <span x-text="loading ? 'Generating...' : (linkCode ? 'Generate Ulang' : 'Generate Kode Verifikasi')"></span>
            </button>
        </div>
        @endif

        {{-- Status message --}}
        <div x-show="message" x-transition class="mt-4">
            <div :class="success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'"
                 class="border rounded-xl px-4 py-3 text-xs font-medium flex items-center gap-2">
                <i :class="success ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
                <span x-text="message"></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('telegramConnect', () => ({
        linkCode: null,
        loading: false,
        message: '',
        success: false,

        async generateCode() {
            this.loading = true;
            this.message = '';

            try {
                const res = await fetch('{{ route("telegram.generate-code") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                if (data.success) {
                    this.linkCode = data.code;
                    this.message = 'Kode berhasil dibuat! Kirim ke bot Telegram.';
                    this.success = true;
                } else {
                    this.message = data.message || 'Gagal membuat kode.';
                    this.success = false;
                }
            } catch (e) {
                this.message = 'Terjadi kesalahan. Coba lagi.';
                this.success = false;
            }

            this.loading = false;
        },

        copyCode() {
            if (this.linkCode) {
                navigator.clipboard.writeText('/link ' + this.linkCode);
                this.message = 'Perintah "/link ' + this.linkCode + '" disalin ke clipboard!';
                this.success = true;
            }
        },

        async disconnect() {
            if (!confirm('Yakin ingin memutus koneksi Telegram?')) return;

            this.loading = true;
            try {
                const res = await fetch('{{ route("telegram.disconnect") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();

                if (data.success) {
                    window.location.reload();
                }
            } catch (e) {
                this.message = 'Gagal memutus koneksi.';
                this.success = false;
            }
            this.loading = false;
        },

        async testNotification() {
            this.loading = true;
            this.message = '';

            try {
                const res = await fetch('{{ route("telegram.test") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.message = data.message;
                this.success = data.success;
            } catch (e) {
                this.message = 'Gagal mengirim test notifikasi.';
                this.success = false;
            }

            this.loading = false;
        },
    }));
});
</script>
