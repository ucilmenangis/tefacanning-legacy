# Materi Belajar Backend SQL: DDL, DML, dan DQL

Dokumen ini berisi materi yang perlu dipelajari dari sisi backend proyek **TEFA Canning SIP Legacy**, khususnya penggunaan SQL yang sudah diterapkan di kode saat ini.

Fokus utamanya:

- `DDL` untuk memahami struktur database.
- `DML` untuk memahami proses tambah, ubah, hapus data.
- `DQL` untuk memahami proses membaca data, join, filter, agregasi, dan laporan.

Referensi kode utama:

- `classes/Database.php`
- `classes/BaseService.php`
- `classes/ProductService.php`
- `classes/BatchService.php`
- `classes/CustomerAdminService.php`
- `classes/OrderService.php`
- `classes/OrderAdminService.php`
- `classes/AdminService.php`
- `classes/ActivityLogService.php`
- `customer/preorder.php`
- `customer/edit-order.php`
- `admin/dashboard.php`

---

## 1. Gambaran Backend Proyek

Backend proyek ini memakai PHP native dengan pola service class. Query SQL tidak ditulis di satu tempat saja, tetapi tersebar di file service dan beberapa halaman controller.

Pola umum yang dipakai:

```php
Database::getInstance()->fetch($sql, $params);
Database::getInstance()->fetchAll($sql, $params);
Database::getInstance()->insert($sql, $params);
Database::getInstance()->update($sql, $params);
Database::getInstance()->delete($sql, $params);
```

Hal penting yang harus dipahami:

- Hampir semua query memakai prepared statement dengan placeholder `?` atau named parameter.
- Data bisnis memakai soft delete lewat kolom `deleted_at`.
- Transaksi order memakai database transaction: `beginTransaction()`, `commit()`, dan `rollBack()`.
- Relasi utama ada pada `orders`, `order_product`, `customers`, `products`, dan `batches`.

---

## 2. DDL: Data Definition Language

`DDL` adalah kelompok SQL untuk membuat atau mengubah struktur database. Contohnya `CREATE TABLE`, `ALTER TABLE`, `DROP TABLE`, `PRIMARY KEY`, `FOREIGN KEY`, `UNIQUE`, dan `INDEX`.

Di proyek ini, DDL tidak banyak muncul langsung di kode PHP harian, tetapi struktur tabelnya terlihat dari query yang dipakai.

### Tabel Utama yang Perlu Dipahami

| Tabel | Fungsi |
| --- | --- |
| `users` | Data admin/operator sistem |
| `customers` | Data pelanggan |
| `products` | Data produk sarden |
| `batches` | Data batch produksi |
| `orders` | Header pesanan |
| `order_product` | Detail produk dalam pesanan |
| `roles` | Role admin |
| `model_has_roles` | Relasi user dengan role |
| `activity_log` | Riwayat aktivitas admin |
| `password_resets` | Token reset password customer |

### Konsep DDL yang Dipakai

#### Primary Key

Setiap tabel utama memakai `id` sebagai primary key.

Contoh konsep:

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

Primary key dipakai di kode seperti:

```php
$orderId = (int) ($_GET['id'] ?? 0);
```

#### Foreign Key

Relasi penting:

- `orders.customer_id` mengarah ke `customers.id`
- `orders.batch_id` mengarah ke `batches.id`
- `order_product.order_id` mengarah ke `orders.id`
- `order_product.product_id` mengarah ke `products.id`

Contoh konsep:

```sql
FOREIGN KEY (customer_id) REFERENCES customers(id)
FOREIGN KEY (batch_id) REFERENCES batches(id)
FOREIGN KEY (order_id) REFERENCES orders(id)
FOREIGN KEY (product_id) REFERENCES products(id)
```

Relasi ini terlihat jelas pada query `JOIN`, misalnya di `OrderAdminService`.

#### Unique

Beberapa data harus unik:

- `customers.email`
- `users.email`
- `products.sku`
- `orders.order_number`
- `orders.pickup_code`

Contoh penggunaan di kode:

```php
$orderNumber = 'ORD-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
$pickupCode  = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
```

#### Enum Status

Status batch dan order dibatasi ke nilai tertentu.

Contoh nilai yang muncul di proyek:

```sql
batches.status: open, processing, ready, closed
orders.status: pending, processing, ready, picked_up
```

Status ini dipakai untuk filter:

```sql
WHERE status = 'open'
WHERE status = 'ready'
WHERE status = 'picked_up'
```

#### Soft Delete

Banyak tabel memiliki kolom:

```sql
deleted_at TIMESTAMP NULL
```

Data dianggap aktif jika:

```sql
WHERE deleted_at IS NULL
```

Data dihapus secara logis dengan:

```sql
UPDATE products SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?
```

Yang perlu dipelajari: soft delete membuat data tidak hilang permanen, sehingga riwayat dan relasi bisnis tetap aman.

---

## 3. DML: Data Manipulation Language

`DML` adalah SQL untuk memanipulasi isi data. Contohnya `INSERT`, `UPDATE`, dan `DELETE`.

### INSERT

Dipakai untuk membuat data baru.

#### Membuat Produk

Lokasi: `classes/ProductService.php`

```sql
INSERT INTO products (name, sku, price, stock, is_active, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, NOW(), NOW())
```

Yang dipelajari:

- `NOW()` dipakai untuk timestamp.
- Nilai dikirim lewat parameter, bukan string concat.
- SKU dibuat otomatis dengan pola `TEFA-SKU-XXX`.

#### Membuat Customer

Lokasi: `classes/CustomerAdminService.php`

```sql
INSERT INTO customers (name, email, phone, password, organization, address, created_at, updated_at)
VALUES (:name, :email, :phone, :password, :organization, :address, NOW(), NOW())
```

Yang dipelajari:

- Named parameter seperti `:name`, `:email`.
- Password harus di-hash sebelum disimpan.
- `organization` dan `address` boleh kosong.

#### Membuat Order

Lokasi:

- `customer/preorder.php`
- `classes/OrderAdminService.php`

```sql
INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, notes, created_at, updated_at)
VALUES (?, ?, ?, ?, 'pending', ?, 0, ?, NOW(), NOW())
```

Setelah order dibuat, detail produk disimpan ke `order_product`:

```sql
INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, NOW(), NOW())
```

Yang dipelajari:

- `orders` adalah header.
- `order_product` adalah detail.
- Harga produk diambil dari database, bukan dari input user.
- Proses order memakai transaction supaya data tidak setengah masuk.

### UPDATE

Dipakai untuk mengubah data yang sudah ada.

#### Update Produk

Lokasi: `classes/ProductService.php`

```sql
UPDATE products
SET name = ?, sku = ?, price = ?, stock = ?, is_active = ?, updated_at = NOW()
WHERE id = ? AND deleted_at IS NULL
```

Yang dipelajari:

- Selalu batasi update dengan `WHERE id = ?`.
- Tambahkan `deleted_at IS NULL` agar data terhapus tidak ikut berubah.
- Update timestamp dengan `updated_at = NOW()`.

#### Update Stok Secara Atomic

Lokasi: `classes/ProductService.php`

```sql
UPDATE products
SET stock = stock - ?, updated_at = NOW()
WHERE id = ? AND stock >= ? AND deleted_at IS NULL
```

Ini penting untuk mencegah stok minus.

Yang dipelajari:

- `stock = stock - ?` melakukan operasi langsung di database.
- `stock >= ?` memastikan stok cukup.
- Cocok untuk proses order yang rawan race condition.

#### Update Order Customer

Lokasi: `customer/edit-order.php`

```sql
UPDATE orders SET total_amount = ?, updated_at = NOW() WHERE id = ?
```

Lalu detail order lama dihapus:

```sql
DELETE FROM order_product WHERE order_id = ?
```

Kemudian detail baru dimasukkan ulang dengan `INSERT`.

Yang dipelajari:

- Untuk detail order, pola yang dipakai adalah replace all detail.
- Sebelum diganti, stok lama dikembalikan dulu.
- Setelah itu stok baru dikurangi.

### DELETE

Ada dua jenis penghapusan di proyek ini.

#### Soft Delete

Dipakai untuk data bisnis.

Contoh:

```sql
UPDATE customers SET deleted_at = NOW() WHERE id = ?
UPDATE batches SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?
UPDATE orders SET deleted_at = NOW() WHERE id = ?
```

Yang dipelajari:

- Data masih tersimpan di database.
- Query list harus selalu menambahkan `WHERE deleted_at IS NULL`.
- Cocok untuk audit dan menjaga riwayat transaksi.

#### Hard Delete

Dipakai untuk data relasi teknis tertentu.

Lokasi: `classes/AdminService.php`

```sql
DELETE FROM model_has_roles
WHERE model_id = ? AND model_type = 'App\\Models\\User'
```

Yang dipelajari:

- Hard delete benar-benar menghapus data.
- Di proyek ini lebih sering dipakai untuk tabel pivot/relasi, bukan data bisnis utama.

---

## 4. DQL: Data Query Language

`DQL` adalah SQL untuk membaca data. Perintah utamanya adalah `SELECT`.

Bagian DQL adalah yang paling banyak digunakan di proyek ini.

### SELECT Dasar

Lokasi: `classes/ProductService.php`

```sql
SELECT id, name, sku, price, stock, is_active
FROM products
WHERE deleted_at IS NULL
ORDER BY name ASC
```

Yang dipelajari:

- Pilih kolom yang dibutuhkan, jangan selalu `SELECT *`.
- Gunakan `WHERE deleted_at IS NULL`.
- Gunakan `ORDER BY` agar tampilan konsisten.

### SELECT by ID

Lokasi: `classes/CustomerAdminService.php`

```sql
SELECT id, name, organization, phone, email, address, created_at
FROM customers
WHERE id = ? AND deleted_at IS NULL
```

Yang dipelajari:

- Detail data biasanya pakai primary key.
- Tetap gunakan soft delete filter.

### JOIN

Dipakai untuk mengambil data dari beberapa tabel sekaligus.

Lokasi: `classes/OrderAdminService.php`

```sql
SELECT o.id, o.order_number, o.status, o.pickup_code, o.total_amount,
       c.name AS customer_name, c.phone AS customer_phone,
       b.name AS batch_name
FROM orders o
JOIN customers c ON c.id = o.customer_id
JOIN batches b ON b.id = o.batch_id
WHERE o.deleted_at IS NULL
ORDER BY o.created_at DESC
```

Yang dipelajari:

- `orders` butuh data customer dan batch.
- Alias `o`, `c`, `b` membuat query lebih ringkas.
- Alias kolom seperti `customer_name` mencegah konflik nama.

### LEFT JOIN

Dipakai saat data utama tetap harus tampil meski data relasi tidak ada.

Lokasi: `classes/CustomerAdminService.php`

```sql
SELECT c.id, c.name, c.organization, c.phone, c.email, c.address, c.created_at,
       COUNT(DISTINCT o.id) AS order_count,
       COALESCE(SUM(o.total_amount), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON o.customer_id = c.id AND o.deleted_at IS NULL
WHERE c.deleted_at IS NULL
GROUP BY c.id, c.name, c.organization, c.phone, c.email, c.address, c.created_at
ORDER BY c.name ASC
```

Yang dipelajari:

- Customer tanpa order tetap muncul.
- `COUNT(DISTINCT o.id)` menghitung jumlah pesanan.
- `SUM(o.total_amount)` menghitung total transaksi.
- `COALESCE(..., 0)` mengganti nilai `NULL` menjadi 0.

### Subquery

Lokasi: `classes/OrderService.php`

```sql
SELECT o.id, o.order_number, o.status, o.total_amount,
       (SELECT COUNT(*) FROM order_product WHERE order_id = o.id) as item_count
FROM orders o
WHERE o.customer_id = ? AND o.deleted_at IS NULL
ORDER BY o.created_at DESC
```

Yang dipelajari:

- Subquery dipakai untuk menghitung jumlah item per order.
- Cocok jika hanya butuh angka ringkas.

### Aggregate Function

Dipakai untuk dashboard dan statistik.

#### COUNT

```sql
SELECT COUNT(*) AS total
FROM customers
WHERE deleted_at IS NULL
```

#### SUM

```sql
SELECT COALESCE(SUM(total_amount), 0) AS total
FROM orders
WHERE deleted_at IS NULL
```

#### COUNT DISTINCT

```sql
SELECT COUNT(DISTINCT customer_id) AS total
FROM orders
WHERE batch_id = ? AND deleted_at IS NULL
```

Yang dipelajari:

- `COUNT(*)` menghitung jumlah baris.
- `SUM()` menjumlahkan nilai uang.
- `COUNT(DISTINCT ...)` menghitung nilai unik.

### GROUP BY

Lokasi: `classes/AdminService.php`

```sql
SELECT p.name AS produk, p.sku, SUM(op.quantity) AS qty, 'kaleng' AS satuan
FROM order_product op
JOIN orders o ON o.id = op.order_id
JOIN products p ON p.id = op.product_id
WHERE o.deleted_at IS NULL
GROUP BY p.id
ORDER BY p.name ASC
```

Yang dipelajari:

- `GROUP BY` mengelompokkan data.
- Dipakai untuk laporan jumlah produk terjual.
- `SUM(op.quantity)` menjumlahkan kuantitas per produk.

### Query Bulanan

Lokasi:

- `admin/dashboard.php`
- `classes/OrderService.php`

```sql
SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
       COUNT(*) as total
FROM orders
WHERE deleted_at IS NULL
  AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY month
ORDER BY month ASC
```

Yang dipelajari:

- `DATE_FORMAT()` mengubah tanggal menjadi format bulan.
- `DATE_SUB()` mengambil rentang waktu tertentu.
- Cocok untuk grafik dashboard.

### LIMIT dan OFFSET

Lokasi: `classes/ActivityLogService.php`

```sql
SELECT al.*, u.name AS causer_name
FROM activity_log al
LEFT JOIN users u ON u.id = al.causer_id
ORDER BY al.created_at DESC
LIMIT 10 OFFSET 0
```

Yang dipelajari:

- `LIMIT` membatasi jumlah data.
- `OFFSET` menentukan posisi awal data.
- Dipakai untuk pagination.

---

## 5. Alur SQL pada Fitur Utama

### Pre-Order Customer

Lokasi: `customer/preorder.php`

Alur:

1. Customer memilih batch dan produk.
2. Backend validasi batch:

```sql
SELECT id, name
FROM batches
WHERE id = ? AND status = 'open' AND deleted_at IS NULL
```

3. Backend ambil produk dan harga dari database:

```sql
SELECT id, name, price, stock
FROM products
WHERE id = ? AND is_active = 1 AND deleted_at IS NULL
```

4. Stok dikurangi secara atomic.
5. Insert ke `orders`.
6. Insert item ke `order_product`.
7. Commit transaction.

Yang harus dipahami:

- Jangan percaya harga dari form frontend.
- Gunakan transaction untuk proses multi-query.
- Validasi stok harus dilakukan sebelum insert order.

### Edit Order Customer

Lokasi: `customer/edit-order.php`

Alur:

1. Ambil order lama.
2. Validasi status harus `pending`.
3. Return stok lama.
4. Hapus detail order lama:

```sql
DELETE FROM order_product WHERE order_id = ?
```

5. Insert detail baru.
6. Update total order.

Yang harus dipahami:

- Edit order lebih rumit dari create order karena stok lama harus dikembalikan.
- Semua proses harus dalam transaction.

### Admin Dashboard

Lokasi:

- `admin/dashboard.php`
- `classes/AdminService.php`

Jenis query:

- Total customer.
- Total omzet.
- Total profit.
- Order ready.
- Grafik bulanan.
- Recent orders.
- Production summary.

Yang harus dipahami:

- Dashboard banyak memakai aggregate query.
- Query dashboard harus dibatasi dengan `LIMIT`.
- Filter batch menggunakan `WHERE batch_id = ?`.

---

## 6. Keamanan Query yang Harus Dipahami

### Prepared Statement

Contoh aman:

```php
$product = Database::getInstance()->fetch(
    "SELECT id, name, price FROM products WHERE id = ?",
    [$productId]
);
```

Hindari:

```php
"SELECT * FROM products WHERE id = " . $_GET['id']
```

Kenapa?

- Mencegah SQL injection.
- Input user tidak langsung masuk ke query string.

### Validasi ID

Pola yang dipakai:

```php
$orderId = (int) ($_GET['id'] ?? 0);
```

Yang dipelajari:

- ID dari URL harus di-cast ke integer.
- Query tetap memakai parameter.

### Soft Delete Filter

Hampir semua query bisnis harus menambahkan:

```sql
deleted_at IS NULL
```

Jika lupa, data yang sudah dihapus bisa muncul kembali di UI.

### Transaction

Dipakai saat beberapa query harus berhasil bersama.

Contoh pola:

```php
$pdo->beginTransaction();

try {
    // update stock
    // insert order
    // insert order items
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
}
```

Yang dipelajari:

- Jika salah satu query gagal, semua perubahan dibatalkan.
- Wajib untuk fitur order dan stok.

---

## 7. Checklist Belajar

### DDL

- Pahami fungsi primary key.
- Pahami foreign key antara `orders`, `customers`, `batches`, `products`, dan `order_product`.
- Pahami kenapa `sku`, `email`, `order_number`, dan `pickup_code` harus unique.
- Pahami soft delete lewat `deleted_at`.
- Pahami tipe data uang memakai `DECIMAL`, bukan `FLOAT`.

### DML

- Bisa menjelaskan proses `INSERT` customer, product, batch, order.
- Bisa menjelaskan proses `UPDATE` data master.
- Bisa menjelaskan soft delete.
- Bisa menjelaskan hard delete pada tabel pivot role.
- Bisa menjelaskan atomic stock update.

### DQL

- Bisa membaca query `SELECT WHERE`.
- Bisa membaca `JOIN` dan `LEFT JOIN`.
- Bisa membaca `GROUP BY`.
- Bisa membaca `COUNT`, `SUM`, `COALESCE`.
- Bisa membaca `LIMIT OFFSET`.
- Bisa menjelaskan query dashboard.
- Bisa menjelaskan subquery `item_count`.

---

## 8. Latihan Praktik

### Latihan 1: Cari Produk Aktif

Buat query untuk mengambil produk aktif yang belum dihapus.

Jawaban:

```sql
SELECT id, name, sku, price, stock
FROM products
WHERE is_active = 1 AND deleted_at IS NULL
ORDER BY name ASC;
```

### Latihan 2: Hitung Total Order Customer

Buat query untuk menghitung total order customer tertentu.

Jawaban:

```sql
SELECT COUNT(*) AS total
FROM orders
WHERE customer_id = ? AND deleted_at IS NULL;
```

### Latihan 3: Tampilkan Pesanan Beserta Customer dan Batch

Jawaban:

```sql
SELECT o.order_number, o.status, o.total_amount,
       c.name AS customer_name,
       b.name AS batch_name
FROM orders o
JOIN customers c ON c.id = o.customer_id
JOIN batches b ON b.id = o.batch_id
WHERE o.deleted_at IS NULL
ORDER BY o.created_at DESC;
```

### Latihan 4: Hitung Total Penjualan Per Produk

Jawaban:

```sql
SELECT p.name, SUM(op.quantity) AS total_qty
FROM order_product op
JOIN products p ON p.id = op.product_id
JOIN orders o ON o.id = op.order_id
WHERE o.deleted_at IS NULL
GROUP BY p.id, p.name
ORDER BY p.name ASC;
```

### Latihan 5: Soft Delete Customer

Jawaban:

```sql
UPDATE customers
SET deleted_at = NOW()
WHERE id = ?;
```

---

## 9. Urutan Belajar yang Disarankan

1. Pelajari struktur tabel utama: `customers`, `products`, `batches`, `orders`, `order_product`.
2. Baca `classes/Database.php` untuk memahami helper query.
3. Baca `classes/ProductService.php` untuk contoh DQL, DML, dan atomic update.
4. Baca `customer/preorder.php` untuk memahami alur insert order dan transaction.
5. Baca `classes/OrderAdminService.php` untuk memahami join kompleks dan update order.
6. Baca `classes/AdminService.php` dan `admin/dashboard.php` untuk memahami aggregate query dashboard.
7. Baca `classes/ActivityLogService.php` untuk memahami pagination dengan `LIMIT` dan `OFFSET`.

---

## 10. Ringkasan

| Kategori | Fungsi | Contoh di Proyek |
| --- | --- | --- |
| DDL | Membentuk struktur database | tabel `orders`, `products`, foreign key, enum status |
| DML | Mengubah isi data | insert order, update stok, soft delete customer |
| DQL | Membaca data | list orders, dashboard stats, activity log pagination |

Hal paling penting dari backend proyek ini:

- Query harus aman dengan prepared statement.
- Data bisnis tidak dihapus fisik, tetapi memakai soft delete.
- Order dan stok harus diproses dalam transaction.
- DQL paling sering dipakai untuk halaman list, dashboard, PDF, dan notifikasi WhatsApp.
- Aggregate query seperti `COUNT`, `SUM`, `GROUP BY`, dan `COALESCE` sangat penting untuk statistik admin.
