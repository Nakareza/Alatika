# Alatika Color Palette Guide

## 🎨 Skema Warna Utama

Sistem menggunakan **Blue & Light Blue** sebagai warna utama untuk konsistensi visual.

### Primary Blue (Main Color)
```css
--primary-500: #3b82f6  /* Tombol utama, links, active state */
--primary-600: #2563eb  /* Hover state */
```

### Light Blue (Accent)
```css
--accent-500: #0ea5e9  /* Accent elements */
--accent-600: #0284c7  /* Accent hover */
```

### Semantic Colors
```css
--success: #10b981  /* Status berhasil */
--warning: #f59e0b  /* Status pending/warning */
--danger: #ef4444   /* Status error/danger */
--info: #0ea5e9    /* Informasi */
```

## 📋 Penggunaan

### Di Blade Components
```blade
<!-- Primary Button -->
<button class="bg-blue-500 hover:bg-blue-600 text-white">
    Ajukan Peminjaman
</button>

<!-- Secondary Button -->
<button class="bg-gray-100 hover:bg-gray-200 text-gray-700">
    Lihat Katalog
</button>

<!-- Badge Status -->
<span class="bg-blue-100 text-blue-800">Status</span>
<span class="bg-green-100 text-green-800">Selesai</span>
<span class="bg-yellow-100 text-yellow-800">Pending</span>
<span class="bg-red-100 text-red-800">Ditolak</span>
```

### Di Custom CSS
```css
.my-element {
    background: var(--primary-500);
    color: var(--text-white);
    border: 1px solid var(--border-primary);
    border-radius: var(--radius-md);
    transition: var(--transition-base);
}

.my-element:hover {
    background: var(--primary-600);
    box-shadow: var(--shadow-md);
}
```

## 🎯 Best Practices

1. **Konsistensi Warna**
   - Gunakan blue (#3b82f6) untuk semua primary actions
   - Light blue (#0ea5e9) untuk accents dan highlights
   - Gray untuk neutral elements

2. **Hover Effects**
   - Primary: blue-500 → blue-600
   - Secondary: gray-100 → gray-200
   - Tambahkan subtle transform atau shadow

3. **Status Colors**
   - Success: Green (#10b981)
   - Warning: Yellow/Amber (#f59e0b)
   - Danger: Red (#ef4444)
   - Info: Light Blue (#0ea5e9)

4. **Shadows & Borders**
   - Gunakan `var(--shadow-sm)` untuk card hover
   - Border color: `var(--border-primary)` (#e2e8f0)
   - Border hover: `var(--border-secondary)` (#cbd5e1)

## 📁 File Locations

- **Color Variables**: `resources/css/colors.css`
- **Sidebar Component**: `resources/views/components/sidebar-mahasiswa.blade.php`
- **Dashboard**: `resources/views/mahasiswa/dashboard.blade.php`

## 🔧 Customization

Untuk mengubah warna utama, edit file `colors.css`:

```css
:root {
    --primary-500: #3b82f6;  /* Ganti dengan warna pilihan */
    --accent-500: #0ea5e9;   /* Ganti accent color */
}
```

Semua komponen akan otomatis menggunakan warna baru!
