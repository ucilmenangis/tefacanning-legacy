# Phase 7.2 — Role-Based Access Control (RBAC)

File: `classes/AdminService.php`, `includes/auth.php`

## Apa itu RBAC?

**Role-Based Access Control** = kontrol akses berdasarkan peran (role).
Setiap user punya role, dan role menentukan apa yang boleh dan tidak boleh dilakukan.

Proyek ini punya 2 role:

| Role | Akses |
|------|-------|
| `super_admin` | Full access: edit harga, lihat data keuangan, kelola user, lihat audit log |
| `teknisi` | Operational: update batch status, lihat metrik produksi, validasi pickup. Tidak bisa edit harga atau kelola user |

## Kenapa Tidak Ada `role_id` di Tabel `users`?

Database ini **shared dengan Laravel repo** (`../tefa-canning-system/`).
Laravel menggunakan package **Spatie Laravel Permission** yang menyimpan role di tabel pivot `model_has_roles`, bukan di kolom `role_id` pada tabel `users`.

### Analogi: Sekolah

Bayangkan sekolah:

- **`users` table** = Kartu Pelajar
  - Berisi: nama, email, foto
  - **TIDAK ada tulisan "jabatan"** di kartu pelajar

- **`model_has_roles` table** = Clipboard di ruang guru
  - Berisi daftar: "siswa ID 001 = Ketua Kelas"
  - Jabatan disimpan di sini, bukan di kartu

- **`roles` table** = Daftar jabatan yang tersedia
  - `id=1` = Ketua Kelas (super_admin)
  - `id=2` = Sekretaris (teknisi)

```
Kartu Pelajar (users):
┌──────────────────┐
│ ID: 001           │
│ Nama: Ivan        │
│ Email: ivan@school│
│                   │
│ (tidak ada jabatan)│
└──────────────────┘

Clipboard (model_has_roles):
┌───────────┬────────────┬─────────────┐
│ Siapa     │ ID Siswa   │ Jabatan     │
├───────────┼────────────┼─────────────┤
│ Siswa     │ 001        │ Ketua Kelas │
│ Siswa     │ 002        │ Sekretaris  │
└───────────┴────────────┴─────────────┘
```

PHP tidak baca jabatan dari kartu (users table).
PHP baca jabatan dari clipboard (model_has_roles table).

## Struktur Database

```
users                          model_has_roles              roles
┌────┬───────────┐            ┌─────────────────┬──────┐   ┌────┬──────────────┐
│ id │ email     │            │ model_type      │role  │   │ id │ name         │
├────┼───────────┤            ├─────────────────┼──────┤   ├────┼──────────────┤
│  1 │ super@... │──┐         │App\Models\User  │  1   │──→│  1 │ super_admin  │
│  2 │ teknisi@. │──┤         │App\Models\User  │  2   │──→│  2 │ teknisi      │
└────┴───────────┘  │         └─────────────────┴──────┘   └────┴──────────────┘
                   └─── model_id menghubungkan user ke role
```

### Kenapa `model_type` ada?

Karena Spatie dirancang untuk menangani role di **semua jenis model**, bukan hanya User:

```
model_has_roles bisa menyimpan:
│ App\Models\User         │ id=1 │ super_admin │  ← user punya role
│ App\Models\Team         │ id=5 │ teknisi     │  ← team punya role (kalau ada)
│ App\Models\Organization │ id=3 │ super_admin │  ← org punya role (kalau ada)
```

Satu tabel untuk semua model. Proyek ini hanya pakai `User`, jadi terasa berlebihan.
Tapi karena DB shared dengan Laravel, kita ikuti struktur ini.

## Flow: Login → Role Check → Akses

### Step 1: User login

```php
// auth/login-admin.php
$user = db_fetch("SELECT * FROM users WHERE email = ?", [$email]);

if ($user && password_verify($password, $user['password'])) {
    loginAdmin($user['id']);  // kirim user ID, misalnya 1
}
```

### Step 2: `loginAdmin()` simpan role ke session

```php
// includes/auth.php
function loginAdmin(int $userId): void
{
    $_SESSION['admin_id'] = $userId;

    // Tanya DB: "User ini role-nya apa?"
    $adminService = new AdminService();
    $_SESSION['admin_role'] = $adminService->getRole($userId);
    // Hasil: $_SESSION['admin_role'] = 'super_admin'
}
```

### Step 3: `getRole()` query clipboard

```php
// classes/AdminService.php
public function getRole(int $userId): ?string
{
    $row = db_fetch(
        "SELECT r.name
         FROM roles r
         JOIN model_has_roles mhr ON mhr.role_id = r.id
         WHERE mhr.model_type = 'App\\\\Models\\\\User' AND mhr.model_id = ?",
        [$userId]
    );

    return $row ? $row["name"] : null;
}
```

SQL yang dijalankan:
```sql
SELECT r.name
FROM roles r
JOIN model_has_roles mhr ON mhr.role_id = r.id
WHERE mhr.model_type = 'App\Models\User' AND mhr.model_id = 1
-- Result: 'super_admin'
```

### Step 4: Session menyimpan role (sticky note)

Setelah login, session berisi:
```php
$_SESSION['admin_id'] = 1;
$_SESSION['admin_role'] = 'super_admin';  // ← diingat, tidak query DB lagi
```

### Step 5: Setiap halaman cek role dari session

```php
// includes/header-admin.php
$isAdminSuperAdmin = isSuperAdmin();  // baca dari session, bukan DB

// isSuperAdmin() cek $_SESSION['admin_role'] === 'super_admin'
```

```
Flow lengkap:

Login form
  → loginAdmin(1)
    → AdminService::getRole(1)
      → SQL: SELECT r.name FROM roles JOIN model_has_roles WHERE model_id = 1
        → DB jawab: 'super_admin'
    → $_SESSION['admin_role'] = 'super_admin'      ← simpan di session

Setiap page load setelah itu:
  → isSuperAdmin()
    → baca $_SESSION['admin_role']                  ← dari session, tanpa query DB
      → 'super_admin' === 'super_admin' → true      ← sidebar tampil
```

## Fungsi RBAC yang Dipakai

| Fungsi | File | Kegunaan |
|--------|------|----------|
| `getAdminRole()` | `includes/auth.php` | Baca role dari session |
| `isSuperAdmin()` | `includes/auth.php` | Cek apakah role = super_admin |
| `requireSuperAdmin()` | `includes/auth.php` | Redirect kalau bukan super_admin |
| `AdminService::getRole()` | `classes/AdminService.php` | Query role dari DB |
| `AdminService::canEditPrice()` | `classes/AdminService.php` | Hanya super_admin boleh edit harga |

## Proteksi Halaman

### Halaman super_admin only

```php
// admin/activity-log.php, admin/pengaturan.php
requireAdmin();         // harus login dulu
requireSuperAdmin();    // harus super_admin
```

### Harga hanya bisa diedit super_admin

```php
// admin/create-product.php, admin/edit-order.php
$adminService = new AdminService();
$canEditPrice = $adminService->canEditPrice();  // true jika super_admin

// di HTML:
if ($canEditPrice) {
    // tampilkan input harga
} else {
    // tampilkan harga sebagai text (disabled)
}
```

### Sidebar sembunyikan menu

```php
// includes/header-admin.php
<?php if (isSuperAdmin()): ?>
    <!-- Menu Audit & Log + Pengaturan hanya tampil untuk super_admin -->
<?php endif; ?>
```

## Menambah User dengan Role

Via phpMyAdmin / TablePlus, 2 langkah:

### Langkah 1: Buat user

```sql
INSERT INTO users (name, email, password, phone, created_at, updated_at)
VALUES (
    'Nama Admin',
    'email@tefa.polije.ac.id',
    '$2y$10$hashbcryptdisini',  -- gunakan password_hash() untuk generate
    NULL,
    NOW(),
    NOW()
);
```

Generate bcrypt hash:
```bash
php -r "echo password_hash('passwordnya', PASSWORD_BCRYPT);"
```

### Langkah 2: Assign role

Catat ID user yang baru dibuat (misal `id = 3`), lalu:

```sql
-- Untuk super_admin (role_id = 1):
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (1, 'App\Models\User', 3);

-- Untuk teknisi (role_id = 2):
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (2, 'App\Models\User', 3);
```

### Ubah role user

```sql
-- Hapus role lama
DELETE FROM model_has_roles
WHERE model_id = 3 AND model_type = 'App\Models\User';

-- Insert role baru
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (1, 'App\Models\User', 3);  -- 1 = super_admin
```

## Penting: Escaping Backslash

`model_type` berisi `App\Models\User` (dengan backslash).
Di PHP double-quoted string, gunakan **4 backslash** agar MySQL menerima 2 backslash:

```php
// SALAH — PHP ubah \\ jadi \, MySQL baca \M jadi M
"WHERE model_type = 'App\\Models\\User'"

// BENAR — PHP ubah \\\\ jadi \\, MySQL baca \\ jadi literal \
"WHERE model_type = 'App\\\\Models\\\\User'"
```

```
PHP string:  'App\\\\Models\\\\User'
     ↓ PHP parse
SQL receives: 'App\\Models\\User'
     ↓ MySQL parse
DB compares:  App\Models\User  ✓ match!
```

## File yang Terkait

| File | Kegunaan |
|------|----------|
| `classes/AdminService.php` | `getRole()`, `isSuperAdmin()`, `canEditPrice()`, `isCoreProduct()` |
| `includes/auth.php` | `loginAdmin()`, `isSuperAdmin()`, `requireSuperAdmin()`, `getAdminRole()` |
| `includes/header-admin.php` | Sidebar show/hide berdasarkan role |
| `admin/activity-log.php` | Halaman super_admin only |
| `admin/pengaturan.php` | Halaman super_admin only |
| `admin/create-product.php` | Harga disabled untuk teknisi |
| `admin/edit-order.php` | Harga dari DB, teknisi tidak bisa edit |
