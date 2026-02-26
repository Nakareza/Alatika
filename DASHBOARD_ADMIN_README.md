# Dashboard Admin - Sistem Peminjaman Alat Elektronik

## 📋 Overview
Dashboard admin telah berhasil diubah dari sistem akademik menjadi sistem manajemen peminjaman alat elektronik dengan desain modern menggunakan glassmorphism, soft colors, dan Logo Polines.

## 🎨 Design System
- **Framework**: TailwindCSS 3.x (CDN)
- **JavaScript**: Alpine.js 3.x untuk interaktivitas
- **Icons**: Font Awesome 6.4.2
- **Typography**: Google Fonts - Inter (300-800)
- **Color Palette**: 
  - Soft slate (50/100/200/500/600/700/800)
  - Indigo (400/500/600)
  - Purple (400/500/600)
  - Emerald, Rose, Amber, Orange
- **Effects**: Glassmorphism, soft shadows, smooth transitions, hover-lift

## 📁 File Structure

### Components (resources/views/components/)

#### 1. sidebar-admin.blade.php
**Purpose**: Main navigation sidebar for admin role

**Features**:
- Logo Polines integration (40x40px in white rounded box)
- 7 Navigation menu items with active state
- User profile section with avatar
- Logout button
- Responsive: hidden on mobile, toggle with hamburger

**Menu Items**:
- Dashboard
- Kelola Peminjaman
- Kelola Pengembalian
- Data Alat
- Data Mahasiswa
- Data Dosen
- Laporan

**Styling**:
- Gradient background: from-slate-50 to-slate-100
- Active menu: gradient from-indigo-500 to-purple-600
- Hover effect: white background with shadow

**Usage**:
```blade
<x-sidebar-admin />
```

#### 2. card-stats-admin.blade.php
**Purpose**: Reusable statistic card component

**Props**:
- `title` (string): Card title
- `value` (string/number): Main statistic value
- `icon` (string): Font Awesome icon class
- `color` (string): Color variant (blue/green/red/purple/yellow/indigo/orange)
- `trend` (number, optional): Percentage change from previous month

**Features**:
- 7 color variants with gradient backgrounds
- Optional trend indicator (up/down arrow)
- Glassmorphism background
- Hover lift effect with icon rotation
- Shadow effects

**Usage**:
```blade
<x-card-stats-admin 
    title="Total Peminjaman" 
    value="142" 
    icon="fas fa-clipboard-list" 
    color="blue"
    :trend="12" />
```

#### 3. table-recent-admin.blade.php
**Purpose**: Display recent borrowing requests with actions

**Props**:
- `peminjaman` (array): Array of borrowing data

**Columns**:
- Mahasiswa (name + NIM with avatar)
- Nama Alat (name + code)
- Tanggal Pinjam (date + time)
- Tanggal Kembali (date)
- Status (badge with 5 variants)
- Aksi (approve/reject/view buttons)

**Status Badges**:
- `pending`: Amber (Menunggu)
- `disetujui`: Blue (Disetujui)
- `ditolak`: Rose (Ditolak)
- `dipinjam`: Indigo (Dipinjam)
- `selesai`: Emerald (Selesai)

**Features**:
- Action buttons for pending requests (approve/reject)
- Detail button for all requests
- Empty state design
- Link to full borrowing list
- Responsive table with hover effects

**Usage**:
```blade
<x-table-recent-admin :peminjaman="$borrowingData" />
```

**Expected Data Format**:
```php
[
    [
        'mahasiswa_nama' => 'Ahmad Rizki',
        'mahasiswa_nim' => '23010001',
        'nama_alat' => 'Arduino Uno',
        'kode_alat' => 'ARD-001',
        'tanggal_pinjam' => '2025-12-08 10:30:00',
        'tanggal_kembali' => '2025-12-15',
        'status' => 'pending'
    ]
]
```

#### 4. header-dashboard-admin.blade.php
**Purpose**: Top navigation bar with breadcrumb, notifications, and user menu

**Props**:
- `title` (string): Page title
- `breadcrumbs` (array): Breadcrumb navigation

**Features**:
- Hamburger menu toggle (mobile)
- Logo Polines in breadcrumb home link (16x16px)
- Notification dropdown with badge dot
- User profile dropdown with avatar
- Settings and logout options
- Alpine.js powered dropdowns

**Notifications**:
- Real-time notification badge
- Dropdown with notification list
- Link to view all notifications

**User Menu**:
- Profile link
- Settings link
- Logout button

**Usage**:
```blade
<x-header-dashboard-admin 
    title="Dashboard Admin" 
    :breadcrumbs="[
        ['name' => 'Peminjaman', 'url' => route('admin.peminjaman')]
    ]" />
```

### Main Dashboard (resources/views/admin/dashboard.blade.php)

**Sections**:

1. **Welcome Section**
   - Logo Polines (80x80px, hidden on mobile)
   - Personalized greeting with admin name
   - System description

2. **Statistics Cards** (5 cards)
   - Total Peminjaman (142, +12%)
   - Menunggu Persetujuan (8)
   - Sedang Dipinjam (24, +5%)
   - Selesai (105, +8%)
   - Ditolak (5, -3%)

3. **Quick Actions** (5 buttons)
   - Kelola Alat (Blue gradient)
   - Kelola Mahasiswa (Purple gradient)
   - Kelola Peminjaman (Emerald gradient)
   - Kelola Pengembalian (Amber gradient)
   - Lihat Laporan (Rose gradient)

4. **Recent Borrowing Table**
   - 5 recent borrowing requests
   - Action buttons (approve/reject/view)
   - Link to full list

5. **Additional Info Cards**
   - **System Info**: Total alat, tersedia, dipinjam, mahasiswa
   - **Recent Activity**: Timeline of recent actions

**Features**:
- Alpine.js for sidebar toggle (x-data="{ sidebarOpen: false }")
- Responsive grid layouts (1/2/5 columns)
- Glassmorphism effects throughout
- Smooth animations (slideDown on welcome section)
- Professional color scheme matching mahasiswa dashboard

## 🛣️ Routes (routes/web.php)

```php
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/peminjaman', ...)->name('peminjaman');
    Route::get('/pengembalian', ...)->name('pengembalian');
    Route::get('/alat', ...)->name('alat');
    Route::get('/mahasiswa', ...)->name('mahasiswa');
    Route::get('/dosen', ...)->name('dosen');
    Route::get('/laporan', ...)->name('laporan');
    Route::get('/profil', ...)->name('profil');
});
```

**Route Names**:
- `admin.dashboard` → `/admin/dashboard`
- `admin.peminjaman` → `/admin/peminjaman`
- `admin.pengembalian` → `/admin/pengembalian`
- `admin.alat` → `/admin/alat`
- `admin.mahasiswa` → `/admin/mahasiswa`
- `admin.dosen` → `/admin/dosen`
- `admin.laporan` → `/admin/laporan`
- `admin.profil` → `/admin/profil`

## 🎯 Controller Integration

### AdminDashboardController
**Location**: `app/Http/Controllers/Admin/DashboardController.php`

**Recommended Implementation**:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalPeminjaman = Peminjaman::count();
        $menungguPersetujuan = Peminjaman::where('status', 'pending')->count();
        $sedangDipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $selesai = Peminjaman::where('status', 'selesai')->count();
        $ditolak = Peminjaman::where('status', 'ditolak')->count();
        
        // Get recent borrowing
        $recentPeminjaman = Peminjaman::with(['user', 'alat'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'mahasiswa_nama' => $item->user->name,
                    'mahasiswa_nim' => $item->user->nim,
                    'nama_alat' => $item->alat->nama,
                    'kode_alat' => $item->alat->kode,
                    'tanggal_pinjam' => $item->tanggal_pinjam,
                    'tanggal_kembali' => $item->tanggal_kembali,
                    'status' => $item->status
                ];
            });
        
        // Get system info
        $totalAlat = Alat::count();
        $alatTersedia = Alat::where('status', 'tersedia')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        
        return view('admin.dashboard', compact(
            'totalPeminjaman',
            'menungguPersetujuan',
            'sedangDipinjam',
            'selesai',
            'ditolak',
            'recentPeminjaman',
            'totalAlat',
            'alatTersedia',
            'totalMahasiswa'
        ));
    }
}
```

## 📊 Database Models

### Peminjaman Model
**Location**: `app/Models/Peminjaman.php`

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
        'tanggal_pengembalian_aktual',
        'status',
        'keterangan',
        'keperluan'
    ];
    
    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_pengembalian_aktual' => 'datetime',
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

### Alat Model
**Location**: `app/Models/Alat.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table = 'alat';
    
    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'deskripsi',
        'kondisi',
        'stok',
        'stok_tersedia',
        'gambar'
    ];
    
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }
}
```

## 🗄️ Migration Suggestions

### Peminjaman Migration
```php
Schema::create('peminjaman', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('alat_id')->constrained('alat')->onDelete('cascade');
    $table->dateTime('tanggal_pinjam');
    $table->dateTime('tanggal_kembali');
    $table->dateTime('tanggal_pengembalian_aktual')->nullable();
    $table->enum('status', ['pending', 'disetujui', 'ditolak', 'dipinjam', 'selesai'])->default('pending');
    $table->text('keterangan')->nullable();
    $table->text('keperluan');
    $table->timestamps();
});
```

### Alat Migration
```php
Schema::create('alat', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->string('kode')->unique();
    $table->string('kategori');
    $table->text('deskripsi')->nullable();
    $table->enum('kondisi', ['baik', 'rusak', 'maintenance'])->default('baik');
    $table->integer('stok')->default(1);
    $table->integer('stok_tersedia')->default(1);
    $table->string('gambar')->nullable();
    $table->timestamps();
});
```

## ✅ Checklist Implementasi

### Phase 1: Dashboard Setup ✅
- [x] Create sidebar-admin component
- [x] Create card-stats-admin component
- [x] Create table-recent-admin component
- [x] Create header-dashboard-admin component
- [x] Update admin dashboard with new design
- [x] Add admin routes

### Phase 2: Pages (Next Steps)
- [ ] Create peminjaman management page (admin/peminjaman/index.blade.php)
- [ ] Create pengembalian management page (admin/pengembalian/index.blade.php)
- [ ] Create alat management page (admin/alat/index.blade.php)
- [ ] Create mahasiswa data page (admin/mahasiswa/index.blade.php)
- [ ] Create dosen data page (admin/dosen/index.blade.php)
- [ ] Create laporan page (admin/laporan/index.blade.php)
- [ ] Create profil admin page (admin/profil.blade.php)

### Phase 3: Backend Integration
- [ ] Create AdminDashboardController with real data
- [ ] Create PeminjamanController for CRUD operations
- [ ] Create PengembalianController for return verification
- [ ] Create AlatController for equipment management
- [ ] Create MahasiswaController for student data
- [ ] Create DosenController for lecturer data
- [ ] Create LaporanController for reports generation

### Phase 4: Database
- [ ] Create Peminjaman model & migration
- [ ] Create Alat model & migration
- [ ] Setup relationships (User, Peminjaman, Alat)
- [ ] Create seeders for dummy data
- [ ] Implement validation rules

### Phase 5: Features
- [ ] Implement approval/rejection functionality
- [ ] Add real-time notifications
- [ ] Create export laporan (PDF/Excel)
- [ ] Add search & filter functionality
- [ ] Implement pagination
- [ ] Add image upload for equipment

## 🎨 Design Consistency

Both mahasiswa and admin dashboards now share:
- Same color palette (soft slate/indigo/purple)
- Logo Polines integration
- Glassmorphism effects
- Smooth animations
- Professional, clean design
- Responsive layouts
- Alpine.js interactivity

## 📱 Responsive Breakpoints

- **Mobile**: < 768px (sidebar hidden, hamburger menu)
- **Tablet**: 768px - 1024px (2-3 column grids)
- **Desktop**: > 1024px (5 column grids, sidebar always visible)

## 🔐 Security Notes

- All admin routes protected by middleware: `['auth', 'role:admin']`
- Sidebar only shows for authenticated admin users
- Logout uses CSRF protection (@csrf)
- Role-based access control required

## 📝 Next Priority

1. **Create Kelola Peminjaman Page** - Most critical for admin workflow
2. **Implement Approval System** - Backend logic for approve/reject
3. **Create Kelola Alat Page** - Equipment CRUD operations
4. **Build Laporan Page** - Statistics and export functionality

## 🎯 Success Metrics

Dashboard is ready when:
- ✅ All components render correctly
- ✅ Sidebar navigation works
- ✅ Statistics display properly
- ✅ Table shows borrowing data
- ✅ Quick actions link correctly
- ✅ Responsive on all devices
- ✅ Logo Polines displays on all sections

---

**Status**: Dashboard UI Complete ✅  
**Next Step**: Create admin management pages & backend integration
