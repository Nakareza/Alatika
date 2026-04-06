<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alatika - Sistem Peminjaman Alat Lab</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#4F46E5',
                        dark: '#0F172A',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'blob': 'blob 7s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Modern Background Pattern */
        .bg-grid-pattern {
            background-image: linear-gradient(to right, #e5e7eb 1px, transparent 1px),
            linear-gradient(to bottom, #e5e7eb 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        /* Glassmorphism Effect */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
            border-color: #3b82f6;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-slate-50 selection:bg-blue-200 selection:text-blue-900 overflow-x-hidden">

    <div class="fixed inset-0 z-[-1] bg-grid-pattern opacity-[0.4] pointer-events-none"></div>
    <div class="fixed top-0 left-0 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob pointer-events-none"></div>
    <div class="fixed top-0 right-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000 pointer-events-none"></div>

    <nav x-data="{ mobileMenuOpen: false }" class="fixed w-full z-50 transition-all duration-300 glass shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-md p-1.5">
                        <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-full h-full object-contain">
                    </div>
                    <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-indigo-700">Alatika</span>
                </div>
                
                <div class="hidden md:flex items-center space-x-1">
                    <a href="#beranda" class="px-4 py-2 text-slate-600 font-medium hover:text-blue-600 rounded-full hover:bg-blue-50 transition-all">Beranda</a>
                    <a href="#katalog" class="px-4 py-2 text-slate-600 font-medium hover:text-blue-600 rounded-full hover:bg-blue-50 transition-all">Katalog</a>
                    <a href="#prosedur" class="px-4 py-2 text-slate-600 font-medium hover:text-blue-600 rounded-full hover:bg-blue-50 transition-all">Prosedur</a>
                    <a href="#kontak" class="px-4 py-2 text-slate-600 font-medium hover:text-blue-600 rounded-full hover:bg-blue-50 transition-all">Kontak</a>
                    
                    <div class="ml-4 pl-4 border-l border-slate-200">
                        <a href="{{ route('login-new') }}" class="group relative px-6 py-2.5 font-semibold text-white rounded-full bg-slate-900 overflow-hidden shadow-lg hover:shadow-blue-500/30 transition-all duration-300 inline-block">
                            <span class="relative z-10 group-hover:text-white transition-colors">Masuk / Daftar</span>
                            <div class="absolute inset-0 h-full w-full scale-0 rounded-full transition-all duration-300 group-hover:scale-100 group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-indigo-600"></div>
                        </a>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-600 hover:text-blue-600 focus:outline-none p-2 rounded-md transition-colors">
                        <i class="fas fa-bars text-2xl" x-show="!mobileMenuOpen"></i>
                        <i class="fas fa-times text-2xl" x-show="mobileMenuOpen" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden glass border-t border-slate-200" style="display: none;">
            <div class="px-4 pt-2 pb-6 space-y-2 flex flex-col bg-white">
                <a @click="mobileMenuOpen = false" href="#beranda" class="block px-4 py-3 text-base font-medium text-slate-800 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">Beranda</a>
                <a @click="mobileMenuOpen = false" href="#katalog" class="block px-4 py-3 text-base font-medium text-slate-800 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">Katalog</a>
                <a @click="mobileMenuOpen = false" href="#prosedur" class="block px-4 py-3 text-base font-medium text-slate-800 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">Prosedur</a>
                <a @click="mobileMenuOpen = false" href="#kontak" class="block px-4 py-3 text-base font-medium text-slate-800 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">Kontak</a>
                <div class="pt-2 mt-2 border-t border-slate-100">
                    <a href="{{ route('login-new') }}" class="block text-center px-4 py-3 text-base font-extrabold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-all shadow-md">
                        Masuk / Daftar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section id="beranda" class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 px-4 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <div class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wide text-blue-600 uppercase bg-blue-100 rounded-full">
                        Laboratorium Elektronika & IoT
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
                        Inovasi Dimulai Dari <br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                            Alat Yang Tepat
                        </span>
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-lg">
                        Akses ribuan komponen mikrokontroler, sensor, dan peralatan soldering untuk tugas akhir dan riset Anda. Gratis dan mudah.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#katalog" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xl shadow-blue-500/30 transition-all transform hover:-translate-y-1">
                            Cari Komponen <i class="fas fa-search ml-2"></i>
                        </a>
                        <a href="#prosedur" class="px-8 py-4 bg-white text-slate-700 border border-slate-200 font-bold rounded-xl hover:bg-slate-50 transition-all shadow-sm">
                            Cara Pinjam
                        </a>
                    </div>
                    
                    <div class="mt-12 grid grid-cols-3 gap-6 border-t border-slate-200 pt-8">
                        <div>
                            <p class="text-3xl font-bold text-slate-900">500+</p>
                            <p class="text-sm text-slate-500">Komponen</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-slate-900">1.2k</p>
                            <p class="text-sm text-slate-500">Peminjaman</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-slate-900">24/7</p>
                            <p class="text-sm text-slate-500">Sistem Online</p>
                        </div>
                    </div>
                </div>
                
                <div class="relative" data-aos="fade-left" data-aos-duration="1200">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl opacity-20 blur-2xl animate-pulse"></div>
                    <img src="https://images.unsplash.com/photo-1555664424-778a1e5e1b48?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80" 
                         alt="Electronics Lab" 
                         class="relative rounded-2xl shadow-2xl w-full object-cover border-4 border-white">
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Stok Terlengkap</h3>
                    <p class="text-slate-600 leading-relaxed">Dari Arduino hingga Raspberry Pi, resistor hingga osiloskop. Semua tersedia.</p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Approval Kilat</h3>
                    <p class="text-slate-600 leading-relaxed">Sistem digital tanpa kertas. Ajukan pinjaman, tunggu notifikasi, ambil barang.</p>
                </div>
                
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Terawat & Kalibrasi</h3>
                    <p class="text-slate-600 leading-relaxed">Tim lab kami melakukan maintenance rutin untuk memastikan akurasi alat.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="py-20 px-4 relative">
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-blue-600 font-bold tracking-wider uppercase text-sm">Katalog Kami</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2 mb-4">Eksplorasi Kategori</h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600 mx-auto rounded-full"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass-card rounded-2xl overflow-hidden group" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative h-56 overflow-hidden">
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/0 transition-colors z-10"></div>
                        <img src="https://images.unsplash.com/photo-1608564697071-ddf911d81370?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Microcontroller" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">Microcontroller</h3>
                        <p class="text-slate-600 mb-6 text-sm">Arduino, ESP32, STM32, dan development board untuk otak projek Anda.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition-colors">
                            Lihat 45 Item <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden group" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative h-56 overflow-hidden">
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/0 transition-colors z-10"></div>
                        <img src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Sensor" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">Sensor & Aktuator</h3>
                        <p class="text-slate-600 mb-6 text-sm">Ultrasonik, DHT, Relay, Servo, dan berbagai modul input/output.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition-colors">
                            Lihat 120 Item <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                
                <div class="glass-card rounded-2xl overflow-hidden group" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative h-56 overflow-hidden">
                        <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-slate-900/0 transition-colors z-10"></div>
                        <img src="https://images.unsplash.com/photo-1593344484962-796055d4a3a4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                             alt="Tools" 
                             class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">Lab Equipment</h3>
                        <p class="text-slate-600 mb-6 text-sm">Solder station, Multimeter, Power Supply, dan Oscilloscope.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition-colors">
                            Lihat 30 Item <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 px-4">
        <div class="max-w-7xl mx-auto" data-aos="zoom-in">
            <div class="relative rounded-3xl overflow-hidden h-80 md:h-96 group">
                <img src="https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1769&q=80" 
                     alt="Workbench" 
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105">
                
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-blue-900/80 to-purple-900/80 flex flex-col items-center justify-center text-center px-4">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 tracking-tight">
                        Wujudkan Ide <br> <span class="text-blue-400">Internet of Things</span> Anda
                    </h2>
                    <p class="text-slate-300 text-lg mb-8 max-w-2xl">
                        Jangan biarkan keterbatasan alat menghambat inovasi. Kami di sini untuk mendukung riset mahasiswa.
                    </p>
                    <a href="{{ route('login-new') }}" class="px-8 py-3 bg-white text-blue-900 font-bold rounded-full hover:bg-blue-50 transition shadow-lg transform hover:scale-105">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="kontak" class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-16">
                <div data-aos="fade-right">
                    <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Hubungi Kami</h2>
                    <p class="text-slate-600 mb-10 text-lg">
                        Punya pertanyaan seputar ketersediaan alat atau prosedur peminjaman? Tim admin kami siap membantu.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Lokasi Lab</h4>
                                <p class="text-slate-600">Gedung B, Lantai 2, Ruang B204<br>Kampus Terpadu</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">Email</h4>
                                <p class="text-slate-600">lab.iot@kampus.ac.id</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 rounded-2xl overflow-hidden shadow-lg border border-slate-200 h-64 relative bg-slate-200">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d456.89431410338295!2d110.43430939948887!3d-7.054212208057543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708c0251ea97c3%3A0x109b392a56c3d4c9!2sGedung%20Sekolah%20B%20Polines%20(SB)!5e1!3m2!1sid!2sid!4v1772122657790!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></>
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl border border-slate-100" data-aos="fade-left">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Kirim Pesan</h3>
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Nama</label>
                                <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">NIM</label>
                                <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Subjek</label>
                            <input type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pesan</label>
                            <textarea rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-white pt-20 pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1.5">
                            <img src="{{ asset('images/logo-polines.png') }}" alt="Logo Polines" class="w-full h-full object-contain">
                        </div>
                        <span class="text-2xl font-bold">Alatika</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed max-w-sm">
                        Sistem manajemen inventaris laboratorium yang modern, cepat, dan terintegrasi untuk mendukung ekosistem pendidikan teknologi.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-6">Navigasi</h4>
                    <ul class="space-y-4 text-slate-400">
                        <li><a href="#beranda" class="hover:text-blue-400 transition">Beranda</a></li>
                        <li><a href="#katalog" class="hover:text-blue-400 transition">Katalog</a></li>
                        <li><a href="#prosedur" class="hover:text-blue-400 transition">Prosedur</a></li>
                        <li><a href="{{ route('login-new') }}" class="hover:text-blue-400 transition">Login Admin</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-6">Social</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 transition-colors">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-slate-500 text-sm">
                <p>&copy; 2024 Alatika Lab System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize Animate On Scroll
        AOS.init({
            once: true, // Animation happens only once - while scrolling down
            offset: 100, // Offset (in px) from the original trigger point
            duration: 800, // Duration of animation
            easing: 'ease-out-cubic',
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-md');
                nav.classList.replace('py-0', 'py-0'); // maintain standard height
            } else {
                nav.classList.remove('shadow-md');
            }
        });
    </script>
</body>
</html>