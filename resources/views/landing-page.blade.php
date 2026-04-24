<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alatika - Sistem Peminjaman Alat Lab</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">

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
                    animation: { float: 'float 6s ease-in-out infinite' },
                    keyframes: {
                        float: {
                            '0%,100%': { transform: 'translateY(0)' },
                            '50%':     { transform: 'translateY(-14px)' },
                        },
                    },
                }
            }
        }
    </script>

    <style>
        /* ── Base ── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            background: #F5F8FF;
            font-family: 'Inter', sans-serif;
            color: #1E2B4A;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #F5F8FF; }
        ::-webkit-scrollbar-thumb { background: #B5D4F4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #378ADD; }

        /* ── Navbar ── */
        .navbar {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid #EBF3FD;
            box-shadow: 0 1px 16px rgba(30,43,74,0.06);
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(255,255,255,0.98);
            box-shadow: 0 2px 24px rgba(30,43,74,0.10);
        }

        /* ── Cards ── */
        .card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #EBF3FD;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }
        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 36px rgba(30,43,74,0.11);
            border-color: #B5D4F4;
        }

        /* ── Step card ── */
        .step-card {
            background: #fff;
            border: 1px solid #EBF3FD;
            border-radius: 20px;
            padding: 1.75rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            box-shadow: 0 2px 16px rgba(30,43,74,0.06);
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(30,43,74,0.10);
            border-color: #B5D4F4;
        }
        .step-num-bg {
            position: absolute;
            top: -8px; right: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            color: #EBF3FD;
            line-height: 1;
            user-select: none;
            pointer-events: none;
        }

        /* ── Info card ── */
        .info-card {
            background: #fff;
            border: 1px solid #EBF3FD;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            box-shadow: 0 2px 12px rgba(30,43,74,0.05);
            transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .info-card:hover {
            border-color: #B5D4F4;
            transform: translateX(4px);
            box-shadow: 0 6px 20px rgba(30,43,74,0.09);
        }

        /* ── Inputs ── */
        .inp {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #F5F8FF;
            border: 1.5px solid #D4E6F8;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            color: #1E2B4A;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .inp:focus {
            border-color: #378ADD;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(55,138,221,0.10);
        }
        .inp::placeholder { color: #A0BBCC; }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.8rem 1.6rem;
            background: #1E2B4A;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 6px 20px rgba(30,43,74,0.22);
        }
        .btn-primary:hover {
            background: #185FA5;
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(24,95,165,0.28);
        }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.8rem 1.6rem;
            background: #fff;
            color: #1E2B4A;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 12px;
            text-decoration: none;
            border: 1.5px solid #D4E6F8;
            transition: all 0.2s;
            box-shadow: 0 2px 10px rgba(30,43,74,0.07);
        }
        .btn-outline:hover {
            border-color: #378ADD;
            background: #F5F8FF;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30,43,74,0.09);
        }

        /* ── Eyebrow pill ── */
        .eyebrow {
            display: inline-block;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            color: #185FA5;
            background: #EBF3FD;
            padding: 4px 14px;
            border-radius: 99px;
            margin-bottom: 10px;
        }

        /* ── CTA ── */
        .cta-wrap {
            background: linear-gradient(135deg, #1E2B4A 0%, #185FA5 55%, #378ADD 100%);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
        }
        .cta-wrap::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 22px 22px;
            pointer-events: none;
        }
        .cta-wrap::after {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(55,138,221,0.22) 0%, transparent 65%);
            top: -80px; right: -60px;
            pointer-events: none;
        }

        /* ── Footer ── */
        .footer-bg { background: #111E35; }

        /* ── Divider section ── */
        .section-divider {
            width: 36px; height: 3px;
            background: linear-gradient(90deg, #378ADD, #185FA5);
            border-radius: 2px;
            margin: 10px auto 0;
        }
        .section-divider.left { margin: 10px 0 0; }

        /* ── Stat badge ── */
        .stat-badge {
            background: #fff;
            border: 1px solid #EBF3FD;
            border-radius: 14px;
            padding: 12px 16px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(30,43,74,0.06);
        }

        /* ── Scroll reveal ── */
        [data-aos] {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        [data-aos].aos-animate { opacity: 1; transform: translateY(0); }
        [data-aos][data-aos-delay="100"] { transition-delay: 0.10s; }
        [data-aos][data-aos-delay="150"] { transition-delay: 0.15s; }
        [data-aos][data-aos-delay="200"] { transition-delay: 0.20s; }
        [data-aos][data-aos-delay="300"] { transition-delay: 0.30s; }
        [data-aos][data-aos-delay="400"] { transition-delay: 0.40s; }
        [data-aos][data-aos-delay="500"] { transition-delay: 0.50s; }
        [data-aos][data-aos-delay="600"] { transition-delay: 0.60s; }
    </style>
</head>

<body class="antialiased overflow-x-hidden">

<!-- ═══════════════════════════════════
     NAVBAR
═══════════════════════════════════ -->
<nav x-data="{ open: false }"
     x-init="window.addEventListener('scroll', () => $el.classList.toggle('scrolled', window.scrollY > 40))"
     class="navbar fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-[68px]">

            <!-- Logo -->
            <a href="#beranda" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#EBF3FD;">
                    <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-6 h-6 object-contain">
                </div>
                <span class="text-base font-bold tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E2B4A;">
                    Alat<span style="color:#378ADD;">ika</span>
                </span>
            </a>

            <!-- Desktop links -->
            <div class="hidden md:flex items-center gap-0.5">
                <a href="#beranda"  class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-navy rounded-full hover:bg-slate-50 transition-all">Beranda</a>
                <a href="#prosedur" class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-navy rounded-full hover:bg-slate-50 transition-all">Prosedur</a>
                <a href="#kontak"   class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-navy rounded-full hover:bg-slate-50 transition-all">Kontak</a>
                <div class="ml-4 pl-4 border-l border-slate-200">
                    <a href="{{ route('login-new') }}"
                       class="inline-flex items-center gap-2 px-5 py-2 text-sm font-bold text-white rounded-full transition-all"
                       style="background:#1E2B4A;box-shadow:0 3px 12px rgba(30,43,74,0.22);"
                       onmouseover="this.style.background='#185FA5'"
                       onmouseout="this.style.background='#1E2B4A'">
                        Masuk / Daftar
                        <i class="fas fa-arrow-right text-xs opacity-60"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile toggle -->
            <button @click="open = !open"
                    class="md:hidden w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                <i class="fas text-sm" :class="open ? 'fa-times' : 'fa-bars'"></i>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-white border-t border-slate-100" style="display:none;">
        <div class="px-4 py-4 space-y-1">
            <a @click="open=false" href="#beranda"  class="block px-4 py-2.5 text-sm font-medium text-slate-600 rounded-xl hover:bg-slate-50 hover:text-navy transition-all">Beranda</a>
            <a @click="open=false" href="#prosedur" class="block px-4 py-2.5 text-sm font-medium text-slate-600 rounded-xl hover:bg-slate-50 hover:text-navy transition-all">Prosedur</a>
            <a @click="open=false" href="#kontak"   class="block px-4 py-2.5 text-sm font-medium text-slate-600 rounded-xl hover:bg-slate-50 hover:text-navy transition-all">Kontak</a>
            <div class="pt-2 border-t border-slate-100 mt-1">
                <a href="{{ route('login-new') }}" class="block text-center py-2.5 px-4 text-sm font-bold text-white rounded-xl transition-all"
                   style="background:#1E2B4A;" onmouseover="this.style.background='#185FA5'" onmouseout="this.style.background='#1E2B4A'">
                    Masuk / Daftar
                </a>
            </div>
        </div>
    </div>
</nav>


<!-- ═══════════════════════════════════
     HERO
═══════════════════════════════════ -->
<section id="beranda" class="relative pt-[68px] overflow-hidden" style="background:#fff;">

    <!-- Subtle blue tint top right -->
    <div class="absolute pointer-events-none" style="width:600px;height:600px;background:radial-gradient(circle,rgba(55,138,221,0.08) 0%,transparent 65%);top:-150px;right:-100px;"></div>
    <div class="absolute pointer-events-none" style="width:400px;height:400px;background:radial-gradient(circle,rgba(24,95,165,0.06) 0%,transparent 65%);bottom:-80px;left:-80px;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- Teks -->
            <div data-aos data-aos-duration="800">
                <h1 class="font-extrabold leading-[1.05] mb-5"
                    style="font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(2.6rem,5.5vw,4.25rem);color:#1E2B4A;letter-spacing:-0.025em;">
                    Inovasi Dimulai<br>
                    Dari&nbsp;<span style="color:#378ADD;">Alat yang Tepat</span>
                </h1>

                <p class="mb-8 leading-relaxed text-slate-500" style="font-size:0.975rem;max-width:430px;line-height:1.75;">
                    Akses dan pinjam berbagai peralatan laboratorium, perangkat IoT, dan kebutuhan akademik dalam satu platform. Praktis, terorganisir, dan siap mendukung kegiatan belajar Anda.
                </p>

                <div class="flex flex-wrap gap-3 mb-10">
                    @if(auth()->check())
                        {{-- <a href="{{ route('mahasiswa.keranjang') }}" class="relative inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-all">
                            <i class="fas fa-shopping-cart"></i>
                            Keranjang
                            @if($cartCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-black rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all">
                            <i class="fas fa-plus"></i>
                            Pinjam Langsung
                        </a> --}}
                    @else
                        <a href="#prosedur" class="btn-primary">
                            Cara Meminjam <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                        <a href="{{ route('login-new') }}" class="btn-outline">
                            Mulai Sekarang
                        </a>
                    @endif
                </div>

                <!-- Stat badges -->
                <div class="flex gap-3 flex-wrap">
                    <div class="stat-badge" data-aos data-aos-delay="150">
                        <p class="font-extrabold text-xl text-navy" style="font-family:'Plus Jakarta Sans',sans-serif;">500+</p>
                        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Komponen</p>
                    </div>
                    <div class="stat-badge" data-aos data-aos-delay="250">
                        <p class="font-extrabold text-xl text-navy" style="font-family:'Plus Jakarta Sans',sans-serif;">1.2k</p>
                        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Peminjaman</p>
                    </div>
                    <div class="stat-badge" data-aos data-aos-delay="350">
                        <p class="font-extrabold text-xl text-navy" style="font-family:'Plus Jakarta Sans',sans-serif;">24/7</p>
                        <p class="text-xs text-slate-400 mt-0.5 uppercase tracking-wide">Online</p>
                    </div>
                </div>
            </div>

            <!-- Gambar -->
            <div class="relative flex justify-center lg:justify-end" data-aos data-aos-delay="100">
                <!-- Ring dekoratif -->
                <div class="absolute rounded-full pointer-events-none" style="width:360px;height:360px;border:1.5px solid #EBF3FD;top:50%;left:50%;transform:translate(-50%,-50%);"></div>
                <div class="absolute rounded-full pointer-events-none" style="width:460px;height:460px;border:1px solid #F0F6FE;top:50%;left:50%;transform:translate(-50%,-50%);"></div>

                <img src="{{ asset('images/hero.png') }}"
                     alt="Electronics Lab"
                     class="animate-float relative z-10"
                     style="max-width:440px;width:100%;object-fit:contain;filter:drop-shadow(0 16px 40px rgba(30,43,74,0.14));">

                <!-- Badge — approval -->
                <div class="card absolute z-20"
                     style="bottom:16px;left:0;padding:10px 14px;display:flex;align-items:center;gap:10px;border-radius:14px;"
                     data-aos data-aos-delay="500">
                    <div style="width:32px;height:32px;background:#EBF3FD;border-radius:9px;display:flex;align-items:center;justify-content:center;color:#185FA5;font-size:12px;flex-shrink:0;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:11px;font-weight:700;color:#1E2B4A;">Approval kilat</div>
                        <div style="font-size:10px;color:#94a3b8;margin-top:1px;">Tanpa antri, tanpa kertas</div>
                    </div>
                </div>

                <!-- Badge — stok -->
                <div class="card absolute z-20"
                     style="top:12px;right:0;padding:10px 14px;border-radius:14px;"
                     data-aos data-aos-delay="600">
                    <div style="font-size:9px;font-weight:700;color:#378ADD;text-transform:uppercase;letter-spacing:.06em;">Stok Terkini</div>
                    <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.35rem;font-weight:800;color:#1E2B4A;line-height:1.1;">500+</div>
                    <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;letter-spacing:.04em;margin-top:2px;">Komponen tersedia</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave divider ke section berikutnya -->
    <div style="line-height:0;overflow:hidden;">
        <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="display:block;width:100%;height:48px;">
            <path d="M0 0 C360 48 1080 48 1440 0 L1440 48 L0 48 Z" fill="#F5F8FF"/>
        </svg>
    </div>
</section>


<!-- Feature strip -->
<section class="py-12 px-4" style="background:#F5F8FF;">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-5">
            <div class="card card-hover p-6" data-aos data-aos-delay="100">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4" style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="text-sm font-bold text-navy mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Stok Terlengkap</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Dari Arduino hingga Raspberry Pi, resistor hingga osiloskop — semua tersedia dan siap dipinjam.</p>
            </div>
            <div class="card card-hover p-6" data-aos data-aos-delay="200">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4" style="background:#EBF3FD;color:#378ADD;">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="text-sm font-bold text-navy mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Approval Kilat</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Sistem digital tanpa kertas. Ajukan pinjaman, tunggu notifikasi, langsung ambil barang.</p>
            </div>
            <div class="card card-hover p-6" data-aos data-aos-delay="300">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4" style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-sm font-bold text-navy mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Terawat &amp; Kalibrasi</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Tim lab kami melakukan maintenance rutin untuk memastikan akurasi dan kondisi setiap alat.</p>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════
     PROSEDUR
═══════════════════════════════════ -->
<section id="prosedur" class="py-20 px-4" style="background:#F5F8FF;">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-12" data-aos>
            <span class="eyebrow">Cara Meminjam</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Prosedur Peminjaman
            </h2>
            <p class="text-sm text-slate-400 max-w-md mx-auto leading-relaxed">
                Empat langkah mudah untuk meminjam alat lab secara digital — cepat, transparan, dan tanpa antri.
            </p>
            <div class="section-divider"></div>
        </div>

        <!-- Steps -->
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="step-card" data-aos data-aos-delay="100">
                <div class="step-num-bg">01</div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4 relative z-10"
                     style="background:#1E2B4A;color:#fff;box-shadow:0 4px 14px rgba(30,43,74,0.22);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase" style="color:#378ADD;">Langkah 01</span>
                <h3 class="text-sm font-bold text-navy mt-1 mb-1.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Daftar &amp; Masuk</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Buat akun menggunakan NIM dan email kampus Anda.</p>
            </div>
            <div class="step-card" data-aos data-aos-delay="200">
                <div class="step-num-bg">02</div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4 relative z-10"
                     style="background:#185FA5;color:#fff;box-shadow:0 4px 14px rgba(24,95,165,0.25);">
                    <i class="fas fa-search"></i>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase" style="color:#378ADD;">Langkah 02</span>
                <h3 class="text-sm font-bold text-navy mt-1 mb-1.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Pilih Komponen</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Telusuri katalog dan pilih alat yang tersedia sesuai kebutuhan.</p>
            </div>
            <div class="step-card" data-aos data-aos-delay="300">
                <div class="step-num-bg">03</div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4 relative z-10"
                     style="background:#378ADD;color:#fff;box-shadow:0 4px 14px rgba(55,138,221,0.28);">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase" style="color:#378ADD;">Langkah 03</span>
                <h3 class="text-sm font-bold text-navy mt-1 mb-1.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Ajukan Permohonan</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Isi formulir digital beserta tanggal dan keperluan penggunaan.</p>
            </div>
            <div class="step-card" data-aos data-aos-delay="400">
                <div class="step-num-bg">04</div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg mb-4 relative z-10"
                     style="background:#EBF3FD;color:#185FA5;border:2px solid #B5D4F4;">
                    <i class="fas fa-box-open"></i>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase" style="color:#378ADD;">Langkah 04</span>
                <h3 class="text-sm font-bold text-navy mt-1 mb-1.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Ambil &amp; Kembalikan</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Ambil alat setelah disetujui admin dan kembalikan tepat waktu.</p>
            </div>
        </div>

        <!-- Ketentuan -->
        <div class="card p-7 md:p-9" data-aos>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#EBF3FD;color:#185FA5;">
                    <i class="fas fa-info-circle text-sm"></i>
                </div>
                <h3 class="text-sm font-bold text-navy" style="font-family:'Plus Jakarta Sans',sans-serif;">Ketentuan Peminjaman</h3>
            </div>
            <div class="grid sm:grid-cols-2 gap-2.5">
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Peminjam adalah mahasiswa aktif Politeknik Negeri Semarang (Polines).</p>
                </div>
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Maksimal peminjaman 7 hari kerja per pengajuan.</p>
                </div>
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Peminjam bertanggung jawab atas kerusakan atau kehilangan alat.</p>
                </div>
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Alat dikembalikan dalam kondisi bersih dan berfungsi sebagaimana mestinya.</p>
                </div>
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Perpanjangan peminjaman wajib diajukan sebelum tanggal jatuh tempo.</p>
                </div>
                <div class="flex items-start gap-3 px-3 py-2.5 rounded-xl" style="background:#F5F8FF;">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#EBF3FD;">
                        <i class="fas fa-check text-xs" style="color:#378ADD;"></i>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">Pengajuan hanya dapat dilakukan pada hari dan jam kerja laboratorium.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════
     KONTAK
═══════════════════════════════════ -->
<section id="kontak" class="py-20 px-4" style="background:#F5F8FF;">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-12" data-aos>
            <span class="eyebrow">Hubungi Kami</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-navy mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Ada Pertanyaan?
            </h2>
            <p class="text-sm text-slate-400 max-w-sm mx-auto leading-relaxed">
                Tim admin kami siap membantu seputar ketersediaan alat dan prosedur peminjaman.
            </p>
            <div class="section-divider"></div>
        </div>

        <div class="grid lg:grid-cols-2 gap-7">

            <!-- Info + map -->
            <div data-aos>
                <div class="flex flex-col gap-3 mb-5">
                    <div class="info-card">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#EBF3FD;color:#185FA5;">
                            <i class="fas fa-map-marker-alt text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-navy mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Lokasi Lab</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Gedung Sekolah B, Lantai 2, Ruang B204<br>Politeknik Negeri Semarang</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#EBF3FD;color:#185FA5;">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-navy mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Email</h4>
                            <p class="text-xs text-slate-500">lab.elektronika@polines.ac.id</p>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background:#EBF3FD;color:#185FA5;">
                            <i class="fas fa-clock text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-navy mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">Jam Layanan</h4>
                            <p class="text-xs text-slate-500">Senin – Jumat: 08.00 – 16.00 WIB</p>
                        </div>
                    </div>
                </div>

                <div class="card overflow-hidden" style="height:210px;border-radius:16px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d456.89431410338295!2d110.43430939948887!3d-7.054212208057543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708c0251ea97c3%3A0x109b392a56c3d4c9!2sGedung%20Sekolah%20B%20Polines%20(SB)!5e1!3m2!1sid!2sid!4v1772122657790!5m2!1sid!2sid"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <!-- Form -->
            <div class="card p-7 md:p-9" data-aos data-aos-delay="100">
                <h3 class="text-base font-bold text-navy mb-5" style="font-family:'Plus Jakarta Sans',sans-serif;">Kirim Pesan</h3>
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-navy mb-1.5">Nama Lengkap</label>
                            <input type="text" class="inp" placeholder="Nama kamu">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-navy mb-1.5">NIM</label>
                            <input type="text" class="inp" placeholder="NIM mahasiswa">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-navy mb-1.5">Subjek</label>
                        <input type="text" class="inp" placeholder="Topik pertanyaan">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-navy mb-1.5">Pesan</label>
                        <textarea rows="4" class="inp resize-none" placeholder="Tuliskan pertanyaan atau keperluanmu..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center"
                            style="width:100%;justify-content:center;"
                            onmouseover="this.style.background='#185FA5';this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.background='#1E2B4A';this.style.transform=''">
                        Kirim Pesan &nbsp;<i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════
     FOOTER
═══════════════════════════════════ -->
<footer class="footer-bg pt-14 pb-7 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-10 mb-10">

            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center p-1" style="background:rgba(255,255,255,0.07);">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-full h-full object-contain opacity-80">
                    </div>
                    <span class="text-base font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                        Alat<span style="color:#378ADD;">ika</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed max-w-[260px]">
                    Sistem manajemen peminjaman inventaris laboratorium yang modern dan terintegrasi untuk mendukung ekosistem pendidikan teknologi Polines.
                </p>
                
            </div>

            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-widest mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;">Navigasi</h4>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li><a href="#beranda"  class="hover:text-accent transition-colors">Beranda</a></li>
                    <li><a href="#prosedur" class="hover:text-accent transition-colors">Prosedur</a></li>
                    <li><a href="#kontak"   class="hover:text-accent transition-colors">Kontak</a></li>
                    <li><a href="{{ route('login-new') }}" class="hover:text-accent transition-colors">Login Admin</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-widest mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;">Info Lab</h4>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li class="flex items-start gap-2"><i class="fas fa-map-marker-alt text-accent text-xs mt-0.5 w-3"></i>Gedung B, Lt. 2 — B204</li>
                    <li class="flex items-start gap-2"><i class="fas fa-clock text-accent text-xs mt-0.5 w-3"></i>Senin – Jumat, 08–16 WIB</li>
                    <li class="flex items-start gap-2"><i class="fas fa-university text-accent text-xs mt-0.5 w-3"></i>Polines, Semarang</li>
                </ul>
            </div>
        </div>

        <div class="border-t pt-5 flex flex-col md:flex-row justify-between items-center gap-1.5 text-xs text-slate-600"
             style="border-color:rgba(255,255,255,0.05);">
            <p>&copy; {{ date('Y') }} Alatika — Politeknik Negeri Semarang. All rights reserved.</p>
            <p>Tugas Akhir · Sistem Informasi</p>
        </div>
    </div>
</footer>


<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('aos-animate'); });
    }, { threshold: 0.08 });
    document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));
</script>
</body>
</html>