{{-- Post-Login Reminder Popup --}}
@if(session('show_post_login_reminder'))
<div
    x-data="{ showReminder: true }"
    x-show="showReminder"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    @keydown.escape.window="showReminder = false"
>
    {{-- Backdrop --}}
    <div
        x-show="showReminder"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0"
        style="background:rgba(30,43,74,0.5);backdrop-filter:blur(4px);"
        @click="showReminder = false"
    ></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            x-show="showReminder"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 border"
            style="border-color:#EBF3FD;"
        >

            {{-- Icon --}}
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                     style="background:#FFF7ED;border:1px solid #FED7AA;">
                    <i class="fas fa-shield-halved text-2xl" style="color:#F59E0B;"></i>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center mb-4">
                <h3 class="text-xl font-bold mb-2" style="color:#1E2B4A;font-family:'Plus Jakarta Sans',sans-serif;">
                    Selamat Datang di Alatika!
                </h3>
                <p class="text-sm leading-relaxed" style="color:#64748b;">
                    Demi keamanan akun Anda, mohon perhatikan hal berikut:
                </p>
            </div>

            {{-- Checklist Items --}}
            <div class="space-y-3 mb-5">
                <div class="flex items-start gap-3 p-3 rounded-xl" style="background:#FEF3C7;">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#FDE68A;">
                        <i class="fas fa-exclamation-triangle text-sm" style="color:#D97706;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color:#92400E;">Lengkapi Data Profil</p>
                        <p class="text-xs mt-0.5" style="color:#A16207;">Pastikan email Anda sudah terisi dengan benar di halaman profil.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl" style="background:#DBEAFE;">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#BFDBFE;">
                        <i class="fas fa-key text-sm" style="color:#2563EB;"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color:#1E40AF;">Jangan Lupa Ganti Password</p>
                        <p class="text-xs mt-0.5" style="color:#1D4ED8;">Disarankan untuk segera mengganti password default Anda demi keamanan.</p>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.profil') : (Auth::user()->role === 'kalab' ? route('kalab.profil') : (Auth::user()->role === 'dosen' ? route('dosen.profil') : route('mahasiswa.profil'))) }}"
                   class="flex-1 py-3 rounded-xl text-white font-semibold text-sm transition-all text-center"
                   style="background:linear-gradient(135deg,#185FA5,#378ADD);box-shadow:0 4px 14px rgba(24,95,165,0.3);"
                   onmouseover="this.style.filter='brightness(1.1)'"
                   onmouseout="this.style.filter=''">
                    <i class="fas fa-user-edit mr-1.5"></i> Buka Profil
                </a>
                <button @click="showReminder = false"
                        class="flex-1 py-3 rounded-xl text-sm font-semibold transition-all"
                        style="background:#F5F8FF;color:#64748b;border:1px solid #EBF3FD;"
                        onmouseover="this.style.background='#EBF3FD'"
                        onmouseout="this.style.background='#F5F8FF'">
                    Nanti Saja
                </button>
            </div>

        </div>
    </div>
</div>
@endif
