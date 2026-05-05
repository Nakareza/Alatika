@extends('layouts.app')

@section('content')    
            
            <!-- Welcome -->
            <div class="mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">
                            Selamat Datang, {{ Auth::user()->name }}
                        </h2>
                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('mahasiswa.peminjaman.ajukan') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i>
                            <span>Ajukan Peminjaman</span>
                        </a>
                        <a href="{{ route('mahasiswa.alat') }}" class="btn btn-secondary">
                            <i class="fas fa-box-open"></i>
                            <span>Lihat Alat</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-blue-500 to-cyan-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                <p class="text-sm opacity-90">Total Peminjaman</p>
            </div>
            <i class="fas fa-clipboard-list text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Dipinjam -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-green-400 to-emerald-500 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['dipinjam'] }}</p>
                <p class="text-sm opacity-90">Sedang Dipinjam</p>
            </div>
            <i class="fas fa-hand-holding text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Selesai -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-orange-400 to-yellow-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['selesai'] }}</p>
                <p class="text-sm opacity-90">Selesai</p>
            </div>
            <i class="fas fa-check-circle text-3xl opacity-70"></i>
        </div>
    </div>

    <!-- Ditolak -->
    <div class="p-5 rounded-2xl text-white bg-gradient-to-r from-pink-500 to-red-400 shadow-md hover:scale-[1.02] transition">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-3xl font-bold">{{ $stats['ditolak'] }}</p>
                <p class="text-sm opacity-90">Ditolak</p>
            </div>
            <i class="fas fa-times-circle text-3xl opacity-70"></i>
        </div>
    </div>

</div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Aktivitas Terbaru -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <div class="flex items-center justify-between mb-5">
                            <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h2>
                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                Lihat Semua →
                            </a>
                        </div>
                        <div class="space-y-3">
                            @forelse($recent as $p)
                            <div class="list-item">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 {{ strtok($p->status_config['color'], ' ') }} bg-opacity-10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $p->status_config['icon'] }} text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm mb-1">{{ $p->alat->nama }}</p>
                                                <p class="text-xs text-gray-500">Kode: {{ $p->kode_peminjaman }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $p->created_at->diffForHumans() }}</p>
                                            </div>
                                            <span class="badge {{ $p->status_config['color'] }} flex-shrink-0">{{ $p->status_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">Belum ada aktivitas peminjaman</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar kanan -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="card p-6">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Menu Cepat</h3>
                        <div class="space-y-2">
                            <a href="{{ route('mahasiswa.peminjaman.riwayat') }}" 
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                                    <i class="fas fa-history text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Riwayat</p>
                                    <p class="text-xs text-gray-500">Lihat semua peminjaman</p>
                                </div>
                            </a>
                            <a href="{{ route('mahasiswa.profil') }}" 
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-blue-50 border border-transparent hover:border-blue-100 group transition-all">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Profil</p>
                                    <p class="text-xs text-gray-500">Kelola akun Anda</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="card p-6 bg-gradient-to-br from-blue-50 to-sky-50 border-blue-100">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm mb-2">Informasi Penting</p>
                                <ul class="space-y-1.5 text-xs text-gray-700">
                                    
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-600 mt-0.5 flex-shrink-0"></i>
                                        <span>Kembalikan alat tepat waktu untuk menghindari denda</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fas fa-check text-blue-600 mt-0.5 flex-shrink-0"></i>
                                        <span>Periksa kondisi alat sebelum/sesudah pemakaian</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

       

    

@endsection

@push('styles')
<style>
    .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; }
    .btn { padding: 0.75rem 1.5rem; border-radius: 12px; }
    .btn-primary { background: #3b82f6; color: white; }
    .btn-secondary { background: #f8fafc; border: 1px solid #e2e8f0; }
</style>
@endpush
