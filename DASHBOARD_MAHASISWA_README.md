# Dashboard Mahasiswa - Sistem Peminjaman Alat Elektronik

## 📋 Ringkasan

Dashboard mahasiswa telah berhasil diubah dari sistem informasi akademik (SKS, IPK) menjadi sistem peminjaman alat elektronik laboratorium yang lengkap dengan fitur:

- ✅ Sidebar navigasi khusus mahasiswa
- ✅ Header dashboard dengan notifikasi
- ✅ Statistik peminjaman (4 card stats)
- ✅ Quick actions (3 tombol aksi cepat)
- ✅ Tabel riwayat peminjaman terbaru
- ✅ Desain modern & responsif menggunakan TailwindCSS + Alpine.js

---

## 📁 Struktur File yang Dibuat

### 1. **Components**
```
resources/views/components/
├── sidebar-mahasiswa.blade.php      # Sidebar navigasi mahasiswa
├── card-stats.blade.php             # Komponen card statistik
├── table-riwayat.blade.php          # Komponen tabel riwayat peminjaman
└── header-dashboard.blade.php       # Header dengan notifikasi & user menu
```

### 2. **Dashboard**
```
resources/views/mahasiswa/
├── dashboard.blade.php              # Dashboard utama mahasiswa (NEW)
└── dashboard-old.blade.php          # Backup dashboard lama
```

---

## 🎨 Fitur Dashboard

### 1. **Welcome Section**
- Menampilkan nama mahasiswa yang login
- Tanggal hari ini
- Background gradient blue-indigo

### 2. **Statistics Cards** (4 Card)
- **Total Peminjaman** (Biru) - Total semua peminjaman mahasiswa
- **Sedang Dipinjam** (Kuning) - Peminjaman yang sedang berlangsung
- **Peminjaman Ditolak** (Merah) - Peminjaman yang ditolak admin
- **Peminjaman Selesai** (Hijau) - Peminjaman yang sudah selesai

### 3. **Quick Actions** (3 Tombol)
- **Ajukan Peminjaman** → `/mahasiswa/peminjaman/ajukan`
- **Riwayat Peminjaman** → `/mahasiswa/peminjaman/riwayat`
- **Daftar Alat** → `/mahasiswa/alat`

### 4. **Tabel Peminjaman Terbaru**
Kolom:
- Nama Alat (dengan icon & kode alat)
- Tanggal Pinjam
- Tanggal Kembali
- Status (badge warna: pending, disetujui, ditolak, dipinjam, selesai)
- Aksi (tombol detail)

### 5. **Tips Banner**
- Tips peminjaman untuk mahasiswa
- Design dengan icon lightbulb

---

## 🔗 Routes yang Ditambahkan

```php
// routes/web.php

Route::prefix('mahasiswa')->middleware(['auth', 'role:mahasiswa'])->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    
    // Peminjaman
    Route::get('/peminjaman/ajukan', ...)->name('peminjaman.ajukan');
    Route::get('/peminjaman/riwayat', ...)->name('peminjaman.riwayat');
    
    // Alat
    Route::get('/alat', ...)->name('alat');
    
    // Profil
    Route::get('/profil', ...)->name('profil');
});
```

---

## 🎯 Cara Menggunakan Components

### 1. **Sidebar Mahasiswa**
```blade
<x-sidebar-mahasiswa />
```

### 2. **Header Dashboard**
```blade
<x-header-dashboard 
    title="Dashboard" 
    :breadcrumbs="[
        ['label' => 'Dashboard']
    ]" 
/>
```

### 3. **Card Stats**
```blade
<x-card-stats 
    title="Total Peminjaman"
    value="12"
    icon="fas fa-clipboard-list"
    color="blue"
    :trend="15"
/>
```

**Props:**
- `title`: Judul card
- `value`: Nilai statistik
- `icon`: Font Awesome icon class
- `color`: Warna (blue, green, red, purple, yellow, indigo)
- `trend` (optional): Persentase perubahan

### 4. **Table Riwayat**
```blade
<x-table-riwayat :peminjaman="[
    [
        'nama_alat' => 'Arduino Uno R3',
        'kode_alat' => 'ARD-001',
        'tanggal_pinjam' => '2025-12-01',
        'tanggal_kembali' => '2025-12-08',
        'status' => 'dipinjam' // pending, disetujui, ditolak, dipinjam, selesai
    ]
]" />
```

---

## 🔧 Integrasi dengan Controller

### Contoh: MahasiswaDashboardController

```php
<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Hitung statistik
        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $sedangDipinjam = Peminjaman::where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->count();
        $ditolak = Peminjaman::where('user_id', $userId)
            ->where('status', 'ditolak')
            ->count();
        $selesai = Peminjaman::where('user_id', $userId)
            ->where('status', 'selesai')
            ->count();
        
        // Ambil peminjaman terbaru
        $peminjamanTerbaru = Peminjaman::where('user_id', $userId)
            ->with('alat')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'nama_alat' => $item->alat->nama,
                    'kode_alat' => $item->alat->kode,
                    'tanggal_pinjam' => $item->tanggal_pinjam,
                    'tanggal_kembali' => $item->tanggal_kembali,
                    'status' => $item->status
                ];
            });
        
        return view('mahasiswa.dashboard', compact(
            'totalPeminjaman',
            'sedangDipinjam',
            'ditolak',
            'selesai',
            'peminjamanTerbaru'
        ));
    }
}
```

### Update View dengan Data dari Controller

```blade
<!-- Statistics Cards dengan data dinamis -->
<x-card-stats 
    title="Total Peminjaman"
    value="{{ $totalPeminjaman }}"
    icon="fas fa-clipboard-list"
    color="blue"
/>

<x-card-stats 
    title="Sedang Dipinjam"
    value="{{ $sedangDipinjam }}"
    icon="fas fa-hand-holding"
    color="yellow"
/>

<!-- Table dengan data dinamis -->
<x-table-riwayat :peminjaman="$peminjamanTerbaru" />
```

---

## 🗄️ Model & Migration (Saran)

### Model Peminjaman
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'user_id',
        'alat_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status', // pending, disetujui, ditolak, dipinjam, selesai
        'keterangan'
    ];
    
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }
}
```

### Migration Peminjaman
```php
Schema::create('peminjaman', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('alat_id')->constrained()->onDelete('cascade');
    $table->date('tanggal_pinjam');
    $table->date('tanggal_kembali')->nullable();
    $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dipinjam', 'selesai'])
          ->default('pending');
    $table->text('keterangan')->nullable();
    $table->timestamps();
});
```

---

## 📱 Fitur Responsif

Dashboard sudah **fully responsive** dengan:
- Mobile: Sidebar tersembunyi dengan hamburger menu
- Tablet: Grid 2 kolom untuk card stats
- Desktop: Grid 4 kolom untuk card stats
- Sidebar overlay pada mobile dengan backdrop blur

---

## 🎨 Teknologi yang Digunakan

1. **TailwindCSS** - Framework CSS utility-first
2. **Alpine.js** - Framework JavaScript ringan untuk interaktivitas
3. **Font Awesome** - Icon library
4. **Laravel Blade Components** - Komponen reusable
5. **Google Fonts (Inter)** - Typography modern

---

## 🚀 Langkah Selanjutnya

### 1. Buat halaman-halaman lain:
- `resources/views/mahasiswa/peminjaman/ajukan.blade.php`
- `resources/views/mahasiswa/peminjaman/riwayat.blade.php`
- `resources/views/mahasiswa/alat/index.blade.php`
- `resources/views/mahasiswa/profil.blade.php`

### 2. Buat Model & Migration:
```bash
php artisan make:model Peminjaman -m
php artisan make:model Alat -m
```

### 3. Buat Controller lengkap:
```bash
php artisan make:controller Mahasiswa/PeminjamanController
php artisan make:controller Mahasiswa/AlatController
```

### 4. Implementasi fitur CRUD untuk peminjaman

---

## ✅ Checklist

- [x] Sidebar mahasiswa dengan menu navigasi
- [x] Header dashboard dengan notifikasi
- [x] Card statistik (4 cards)
- [x] Quick actions (3 buttons)
- [x] Tabel riwayat peminjaman
- [x] Responsive design
- [x] Alpine.js untuk interaktivitas
- [x] Routes untuk semua menu
- [ ] Halaman ajukan peminjaman
- [ ] Halaman riwayat lengkap
- [ ] Halaman daftar alat
- [ ] Halaman profil mahasiswa

---

## 📞 Support

Jika ada pertanyaan atau butuh bantuan lebih lanjut, silakan hubungi tim developer.

**Made with ❤️ for Mahasiswa**
