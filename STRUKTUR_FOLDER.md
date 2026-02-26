# 📁 Struktur Folder Admin & Mahasiswa

## 🎯 Struktur yang Telah Dibuat

### 📂 Controllers
```
app/Http/Controllers/
├── AuthController.php                    # Handle login, register, logout
├── Admin/
│   └── DashboardController.php          # Admin dashboard
└── Mahasiswa/
    └── DashboardController.php          # Mahasiswa dashboard
```

### 📂 Views
```
resources/views/
├── landingpage.blade.php                # Landing page utama (public)
├── login-register.blade.php             # Halaman login/register
├── admin/
│   └── dashboard.blade.php              # Dashboard untuk admin
└── mahasiswa/
    └── dashboard.blade.php              # Dashboard untuk mahasiswa
```

### 📂 Middleware
```
app/Http/Middleware/
└── CheckRole.php                        # Middleware untuk cek role user
```

---

## 🔐 Role-Based Access Control

### Roles yang Tersedia:
1. **admin** - Akses penuh ke sistem
2. **mahasiswa** - Akses terbatas untuk mahasiswa

### Default Role:
- Saat registrasi, user otomatis mendapat role **mahasiswa**

---

## 🛣️ Route Structure

### Public Routes:
- `GET /` - Landing page
- `GET /login-new` - Halaman login/register
- `POST /register` - Proses registrasi
- `POST /login` - Proses login
- `POST /logout` - Logout

### Admin Routes (require auth + role:admin):
- `GET /admin/dashboard` - Dashboard admin

### Mahasiswa Routes (require auth + role:mahasiswa):
- `GET /mahasiswa/dashboard` - Dashboard mahasiswa

---

## 🔄 Flow Sistem

1. **User mengakses `/`** → Landing page
2. **Klik "Login/Register"** → Diarahkan ke `/login-new`
3. **Setelah login/register:**
   - Jika role = **admin** → redirect ke `/admin/dashboard`
   - Jika role = **mahasiswa** → redirect ke `/mahasiswa/dashboard`
4. **Logout** → Kembali ke `/login-new`

---

## 🗄️ Database Schema

### Tabel: users
```
- id
- name
- email
- password
- role (default: 'mahasiswa')
- email_verified_at
- remember_token
- timestamps
```

---

## 📝 Cara Membuat Admin

### Opsi 1: Manual via Database
```sql
UPDATE users SET role = 'admin' WHERE email = 'admin@example.com';
```

### Opsi 2: Via Tinker
```bash
php artisan tinker
```
```php
$user = User::where('email', 'admin@example.com')->first();
$user->role = 'admin';
$user->save();
```

### Opsi 3: Buat User Admin Langsung
```php
User::create([
    'name' => 'Administrator',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin'
]);
```

---

### Pengembangan Selanjutnya (Versi Web Peminjaman Alat Elektronik + n8n)

# Untuk Admin:

- Kelola data alat elektronik
(Tambah, edit, hapus, kategori, stok tersedia)

- Kelola peminjaman & pengembalian
(Persetujuan admin, validasi kondisi alat, status peminjaman real-time)

- Kelola data mahasiswa peminjam
(Integrasi form pendaftaran, lookup otomatis melalui database)

- Kelola notifikasi otomatis via n8n
(WA/Email pengingat pengembalian, pemberitahuan peminjaman disetujui/ditolak)

- Kelola log aktivitas & audit trail
(Setiap proses tercatat dan bisa dipantau)

- Dashboard monitoring
(Statistik alat paling sering dipinjam, riwayat peminjaman, grafik kebutuhan alat)

# Untuk Mahasiswa:

- Melihat daftar alat elektronik
(Filter berdasarkan kategori, ketersediaan, kondisi)

- Melakukan peminjaman alat
(Form peminjaman tersambung otomatis ke workflow n8n)

- Melihat status peminjaman
(Pending, disetujui, ditolak, belum dikembalikan)

- Riwayat peminjaman pribadi
(Detail tanggal ambil, pengembalian, catatan admin)

- Menerima notifikasi otomatis
(Peminjaman disetujui, pengingat pengembalian, denda jika terlambat)

# Fitur Tambahan Berbasis n8n (Opsional)

- Workflow verifikasi stok otomatis
Ketika mahasiswa mengajukan peminjaman → n8n cek stok → hasil dikirim ke admin.

- Integrasi Google Sheet / Firebase / Supabase
untuk backup dan pelacakan riwayat.

- Denda otomatis jika terlambat mengembalikan
(Mengirim notifikasi dan catat ke database)

- QR Code untuk proses peminjaman & pengembalian
Scan QR → trigger ke n8n workflow → update status.

---

## Tips

1. **Tambah fitur baru untuk admin:**
   - Buat controller di `app/Http/Controllers/Admin/`
   - Buat view di `resources/views/admin/`
   - Tambah route di grup `admin` di `routes/web.php`

2. **Tambah fitur baru untuk mahasiswa:**
   - Buat controller di `app/Http/Controllers/Mahasiswa/`
   - Buat view di `resources/views/mahasiswa/`
   - Tambah route di grup `mahasiswa` di `routes/web.php`

3. **Middleware otomatis protect:**
   - Semua route di grup `admin` hanya bisa diakses oleh user dengan role `admin`
   - Semua route di grup `mahasiswa` hanya bisa diakses oleh user dengan role `mahasiswa`
