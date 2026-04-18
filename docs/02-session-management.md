# Phase 1.2 — Session Management (Dual Guard)

File: `includes/auth.php`

## Apa itu Session?

HTTP itu **stateless** — setiap request baru, PHP tidak ingat siapa user.
Session kasih "memory" ke PHP. Data disimpan di **server**, browser cuma pegang **session ID** (cookie).

```
User login → PHP buat $_SESSION['admin_id'] = 5
User buka halaman lain → PHP cek $_SESSION['admin_id'] → masih ada → boleh masuk
User logout → PHP hapus $_SESSION['admin_id']
User buka halaman protected → tidak ada → tendang ke login
```

## Kenapa Dual Guard?

Proyek ini punya 2 tipe user:
- **Admin** → login pakai tabel `users`
- **Customer** → login pakai tabel `customers`

Kalau cuma pakai `$_SESSION['user_id']`, admin dan customer bisa bentrok.
Dual guard pakai key berbeda:
- `$_SESSION['admin_id']` untuk admin
- `$_SESSION['customer_id']` untuk customer

Keduanya bisa login bersamaan di browser yang sama tanpa konflik.

## Fungsi yang Dibuat

### `startSession(): void`
Start session dengan setting aman. Wajib dipanggil di awal setiap halaman.
Cek `session_status()` supaya tidak start 2x (kalau sudah start, skip).

Setting keamanan:
- `use_strict_mode` — tolak session ID palsu
- `use_only_cookies` — session ID hanya dari cookie, bukan URL
- `cookie_httponly` — JavaScript tidak bisa baca session cookie
- `cookie_samesite=Lax` — proteksi dari CSRF

### Admin Guard

| Fungsi | Fungsi | Return |
|--------|--------|--------|
| `loginAdmin($userId)` | Set session admin + regenerate ID | void |
| `logoutAdmin()` | Hapus session admin | void |
| `isAdminLoggedIn()` | Cek apakah admin sudah login | bool |
| `getAdminId()` | Ambil ID admin yang login | ?int |
| `requireAdmin()` | Redirect ke login kalau belum login | void |

### Customer Guard

| Fungsi | Fungsi | Return |
|--------|--------|--------|
| `loginCustomer($customerId)` | Set session customer + regenerate ID | void |
| `logoutCustomer()` | Hapus session customer | void |
| `isCustomerLoggedIn()` | Cek apakah customer sudah login | bool |
| `getCustomerId()` | Ambil ID customer yang login | ?int |
| `requireCustomer()` | Redirect ke login kalau belum login | void |

## Konsep PHP yang Dipakai

| Konsep | Dipakai di | Penjelasan |
|--------|-----------|------------|
| `$_SESSION` | Semua | Superglobal — array khusus PHP untuk session data |
| `session_start()` | `startSession()` | Mulai session. Harus sebelum output HTML |
| `session_regenerate_id(true)` | `loginAdmin/loginCustomer` | Bikin session ID baru, hapus yang lama. Cegah **session fixation** |
| `session_status()` | `startSession()` | Cek apakah session sudah aktif |
| `ini_set()` | `startSession()` | Ubah konfigurasi PHP runtime |
| Null coalescing `??` | `getAdminId/getCustomerId` | Return default kalau variable tidak ada |

## Apa itu Session Fixation?

Serangan dimana hacker kasih session ID palsu ke victim:
```
Hacker: "Hei, login di link ini: /login?PHPSESSID=hacker123"
User login → pakai session ID hacker123
Hacker: Buka situs pakai PHPSESSID=hacker123 → sekarang dia juga login sebagai user!
```

Solusi: `session_regenerate_id(true)` saat login → buat session ID baru, hapus yang lama.
Hacker tidak tahu ID baru.

## Contoh Pakai di Halaman

```php
// admin/dashboard.php
<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();  // kalau belum login, auto redirect ke login page

$adminId = getAdminId();
// ... tampilin dashboard
```

```php
// auth/login-admin.php (proses login)
<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$user = db_fetch("SELECT * FROM users WHERE email = ?", [$email]);

if ($user && password_verify($password, $user['password'])) {
    loginAdmin($user['id']);
    header('Location: /admin/dashboard.php');
    exit;
}
```
