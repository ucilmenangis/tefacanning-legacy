# Phase 1.5 — Flash Message

File: `includes/auth.php` (fungsi baru), `includes/header-customer.php`, `includes/header-admin.php`

## Apa itu Flash Message?

Pesan notifikasi yang muncul **sekali** setelah aksi (submit form, hapus data, dll), lalu hilang saat halaman di-refresh.

```
User submit form → redirect ke halaman lain
                → muncul banner hijau "Berhasil!"
                → refresh → banner hilang
```

Kenapa pakai session? Setelah redirect, PHP fresh — tidak ingat apa yang barusan terjadi.
Simpan di session → baca di halaman baru → hapus setelah ditampilkan.

## Fungsi yang Dibuat

### `setFlash($type, $message): void`
Simpan pesan di session. Tipe: `success`, `error`, `warning`, `info`.

```php
setFlash('success', 'Produk berhasil ditambahkan!');
header('Location: /admin/products.php');
exit;
```

### `getFlash(): ?array`
Ambil pesan dari session dan hapus. Return array atau null.

```php
$flash = getFlash();
// ['type' => 'success', 'message' => 'Produk berhasil ditambahkan!']
// Atau: null
```

### `renderFlash(): string`
Output HTML banner otomatis. **Sudah dipasang di header layout** — tidak perlu panggil manual.

Banner otomatis muncul kalau ada flash message, hilang setelah ditampilkan.

## Tipe dan Warna

| Tipe | Warna | Icon |
|------|-------|------|
| `success` | Hijau | ph-check-circle |
| `error` | Merah | ph-x-circle |
| `warning` | Kuning | ph-warning |
| `info` | Biru | ph-info |

## Contoh Pakai

```php
// Di handler POST (backend)
if ($success) {
    setFlash('success', 'Pesanan berhasil dibuat!');
    header('Location: /customer/orders.php');
    exit;
} else {
    setFlash('error', 'Gagal membuat pesanan. Coba lagi.');
    header('Location: /customer/preorder.php');
    exit;
}
```

```php
// Di halaman — TIDAK PERLU kode apa pun
// renderFlash() otomatis dipanggil di header layout
// Banner akan muncul kalau ada flash message di session
```

## Flow

```
1. User submit form di halaman A
2. Backend proses → setFlash('success', 'Berhasil!')
3. Backend redirect ke halaman B
4. Halaman B load → renderFlash() baca session → tampilkan banner hijau
5. renderFlash() hapus pesan dari session
6. User refresh → banner hilang (session sudah kosong)
```
