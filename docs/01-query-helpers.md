# Phase 1.1 — Query Helpers (PDO Wrapper)

File: `includes/functions.php`

## Apa itu PDO?

PDO = **PHP Data Objects**. Library bawaan PHP untuk konek ke database.
Amannya pakai PDO dibanding `mysqli_query()` karena PDO mendukung **prepared statements** — cara aman mengirim data ke SQL tanpa takut SQL injection.

## Prepared Statements — Konsep Inti

```
BAIK:  db_fetch("SELECT * FROM users WHERE id = ?", [5])
BURUK: db_fetch("SELECT * FROM users WHERE id = " . $_GET['id'])
```

Tanda `?` = placeholder. Data dikirim terpisah dari SQL. Database tahu "5" itu data, bukan perintah SQL.
Jadi kalau user input `5 OR 1=1`, itu tidak akan menghapus semua data.

## Fungsi yang Dibuat

### `db(): PDO`
Ambil koneksi database. Pakai `static` variable supaya konek sekali saja.
Disebut **Singleton Pattern** — satu koneksi dipakai berulang.

```php
$conn = db();  // PDO object
```

### `db_fetch($sql, $params): ?array`
Ambil **1 baris** data. Return `null` kalau tidak ketemu.

```php
$user = db_fetch("SELECT * FROM users WHERE id = ?", [1]);
// Hasil: ['id' => 1, 'name' => 'Admin', 'email' => '...']
// Atau: null
```

### `db_fetch_all($sql, $params): array`
Ambil **semua baris**. Return array kosong `[]` kalau tidak ada.

```php
$products = db_fetch_all("SELECT * FROM products WHERE is_active = ?", [1]);
// Hasil: [['id' => 1, ...], ['id' => 2, ...]]
```

### `db_insert($sql, $params): int`
Insert data baru. Return ID baru (auto-increment).

```php
$id = db_insert("INSERT INTO products (name, price) VALUES (?, ?)", ["Sarden", 25000]);
// Hasil: 15 (ID produk baru)
```

### `db_update($sql, $params): int`
Update data. Return jumlah baris yang berubah.

```php
$count = db_update("UPDATE products SET price = ? WHERE id = ?", [30000, 1]);
// Hasil: 1 (satu baris berubah)
```

### `db_delete($sql, $params): int`
Hapus data. Return jumlah baris yang dihapus.

```php
$count = db_delete("DELETE FROM products WHERE id = ?", [1]);
// Hasil: 1 (satu baris dihapus)
```

## Konsep PHP yang Dipakai

| Konsep | Dipakai di | Penjelasan |
|--------|-----------|------------|
| `static` variable | `db()` | Variable yang tetap hidup antar pemanggilan fungsi |
| Type hints | Semua fungsi | `string`, `array`, `int`, `?array` (nullable) |
| PDO | Semua fungsi | `prepare()`, `execute()`, `fetch()`, `fetchAll()` |
| Return types | Semua fungsi | `: PDO`, `: ?array`, `: array`, `: int` |

## Catatan Penting

- Soft delete: JANGAN pakai `db_delete()`. Pakai `db_update("UPDATE ... SET deleted_at = NOW()")`.
- Selalu pakai `?` placeholder. JANGAN gabung string SQL dengan input user.
- `db_fetch()` return `null` kalau tidak ketemu — selalu cek `if ($result !== null)`.
