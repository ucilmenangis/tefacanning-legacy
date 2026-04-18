# Phase 1.3 — Layout System

File: `includes/header-admin.php`, `includes/footer-admin.php`, `includes/header-customer.php`, `includes/footer-customer.php`

## Apa itu Layout System?

Tanpa layout system, setiap halaman harus tulis ulang `<head>`, sidebar, navbar, footer.
Dengan layout system, tulis sekali, dipakai semua halaman.

```
header-admin.php    → <head> + sidebar
  ↓ include
  halaman kamu      → cuma konten halaman
  ↓ include
footer-admin.php    → tutup HTML
```

## Cara Pakai

### Admin Page
```php
<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Dashboard';  // judul halaman (muncul di tab browser + top bar)
include __DIR__ . '/../includes/header-admin.php';
?>

<!-- konten halaman di sini -->
<div class="bg-white rounded-xl p-6 shadow-sm">
    <h2>Selamat datang di Dashboard</h2>
</div>

<?php include __DIR__ . '/../includes/footer-admin.php'; ?>
```

### Customer Page
```php
<?php
// customer/orders.php
require_once __DIR__ . '/../includes/auth.php';
requireCustomer();

$pageTitle = 'Pesanan Saya';
include __DIR__ . '/../includes/header-customer.php';
?>

<!-- konten halaman di sini -->

<?php include __DIR__ . '/../includes/footer-customer.php'; ?>
```

## Yang Ada di Header

Kedua header punya:
- `<head>` lengkap (Tailwind CDN, Google Fonts Inter, Phosphor Icons)
- Tailwind config (warna: primary, accent, dark, navy)

### Admin Header
- Sidebar kiri (navigasi, user info, logout)
- Top bar dengan judul halaman + link "Lihat Situs"
- Menu: Dashboard, Produk, Batch, Pesanan, Pelanggan, User (super_admin), Activity Log

### Customer Header
- Navbar atas (logo, menu links, user avatar, logout)
- Menu: Dashboard, Pre-Order, Pesanan, Profil

## Konsep PHP yang Dipakai

| Konsep | Dipakai di | Penjelasan |
|--------|-----------|------------|
| `include` | Semua halaman | Sisipkan file lain ke file saat ini |
| `$pageTitle` | Setiap halaman | Variable yang dikirim ke header untuk judul |
| `htmlspecialchars()` | Header | Cegah XSS di judul halaman |
| `dirname($_SERVER['SCRIPT_NAME'])` | Link URL | Dapatkan path relatif agar link benar dari folder mana saja |
| `isset()` | Header | Cek apakah `$pageTitle` sudah diset |

## Penting untuk Alif (Frontend)

- Setiap halaman admin WAJIB mulai dengan `requireAdmin()` + `header-admin.php`
- Setiap halaman customer WAJIB mulai dengan `requireCustomer()` + `header-customer.php`
- JANGAN tulis `<html>`, `<head>`, `<body>` di halaman konten — sudah ada di header
- JANGAN tulis `</body>`, `</html>` di halaman konten — sudah ada di footer
- Konten halaman ditulis di antara header dan footer saja
- `$pageTitle` harus diset SEBELUM include header
