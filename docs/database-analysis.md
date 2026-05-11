# Analisis Database — TEFA Canning SIP

Analisis penggunaan SQL pada sistem TEFA Canning Legacy. Hanya mencantumkan fungsi yang digunakan di proyek ini.

---

## 1. DDL (Data Definition Language)

`CREATE TABLE` : Mendefinisikan struktur tabel dan kolom. Digunakan di semua 17 tabel proyek (users, customers, batches, products, orders, order_product, permissions, roles, dll).
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(15, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    deleted_at TIMESTAMP NULL
);
```

`FOREIGN KEY` : Mendefinisikan relasi antar tabel. Digunakan di tabel orders (ke customers dan batches) dan order_product (ke orders dan products).
```sql
CREATE TABLE orders (
    customer_id BIGINT UNSIGNED NOT NULL,
    batch_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
);
```

`ENUM` : Membatasi nilai kolom ke pilihan tertentu. Digunakan di tabel batches untuk status produksi dan tabel orders untuk status pesanan.
```sql
status ENUM('open', 'processing', 'ready', 'closed') DEFAULT 'open'
status ENUM('pending', 'processing', 'ready', 'picked_up') DEFAULT 'pending'
```

`UNIQUE` : Memastikan nilai kolom tidak duplikat. Digunakan di tabel users untuk email, products untuk SKU, orders untuk order_number dan pickup_code.
```sql
email VARCHAR(255) NOT NULL UNIQUE
sku VARCHAR(255) NOT NULL UNIQUE
order_number VARCHAR(255) NOT NULL UNIQUE
pickup_code VARCHAR(255) NOT NULL UNIQUE
```

`AUTO_INCREMENT` : Generate ID otomatis. Digunakan sebagai primary key di semua tabel bisnis.
```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

`PRIMARY KEY` : Mendefinisikan primary key, termasuk composite key. Digunakan di tabel RBAC untuk composite key (permission_id + model_id + model_type).
```sql
PRIMARY KEY (permission_id, model_id, model_type)
PRIMARY KEY (role_id, model_id, model_type)
```

`INDEX` : Menambahkan index untuk mempercepat query. Digunakan di tabel activity_log untuk kolom log_name dan tabel RBAC untuk model lookup.
```sql
INDEX (log_name)
INDEX (model_id, model_type)
```

`CASCADE` : Menghapus data terkait saat parent dihapus. Digunakan di tabel orders dan order_product agar pesanan dan item-nya terhapus otomatis saat customer/batch/product dihapus.
```sql
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
```

`NULL / NOT NULL` : Mengatur apakah kolom boleh kosong. Digunakan di tabel customers untuk kolom opsional (phone, address, organization) dan wajib (name).
```sql
phone VARCHAR(20) NULL
name VARCHAR(255) NOT NULL
```

`DEFAULT` : Memberikan nilai default pada kolom. Digunakan di tabel products untuk stock (0), is_active (true), dan batches untuk status (open).
```sql
stock INT NOT NULL DEFAULT 0
status ENUM(...) DEFAULT 'open'
is_active BOOLEAN DEFAULT TRUE
```

`DECIMAL(M, D)` : Tipe data untuk nilai uang dengan presisi tinggi. Digunakan di tabel products, orders, dan order_product untuk price, total_amount, profit, subtotal.
```sql
price DECIMAL(15, 2) NOT NULL
total_amount DECIMAL(15, 2) DEFAULT 0
```

`TIMESTAMP` : Tipe data untuk waktu. Digunakan di semua tabel untuk created_at, updated_at, deleted_at, dan picked_up_at di tabel orders.
```sql
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL
deleted_at TIMESTAMP NULL
picked_up_at TIMESTAMP NULL
```

---

## 2. DML (Data Manipulation Language)

`INSERT INTO ... VALUES` : Menambah data baru. Digunakan di ProductService untuk membuat produk baru, BatchService untuk batch baru, dan ActivityLogService untuk mencatat aktivitas admin.
```sql
INSERT INTO products (name, sku, price, stock, is_active, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, NOW(), NOW())
```

`INSERT INTO ... VALUES` : Menambah data dengan named parameter. Digunakan di CustomerAdminService dan AdminService untuk membuat customer/user baru dengan field opsional.
```sql
INSERT INTO customers (name, email, phone, password, organization, address, created_at, updated_at)
VALUES (:name, :email, :phone, :password, :organization, :address, NOW(), NOW())
```

`UPDATE ... SET` : Mengubah data yang sudah ada. Digunakan di ProductService, BatchService, CustomerService untuk mengupdate data master, dan OrderAdminService untuk mengubah status pesanan.
```sql
UPDATE products SET name = ?, sku = ?, price = ?, stock = ?, is_active = ?, updated_at = NOW()
WHERE id = ? AND deleted_at IS NULL
```

`UPDATE ... SET` : Update dengan operasi matematika (atomic). Digunakan di ProductService::deductStock() untuk mengurangi stok saat order dibuat, dengan kondisi `stock >= ?` agar stok tidak negatif.
```sql
UPDATE products SET stock = stock - ?, updated_at = NOW()
WHERE id = ? AND stock >= ? AND deleted_at IS NULL
```

`UPDATE ... SET deleted_at` : Soft delete, menandai data sebagai terhapus tanpa menghapus fisik. Digunakan di ProductService, BatchService, CustomerAdminService, dan OrderAdminService untuk semua data bisnis.
```sql
UPDATE orders SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?
```

`DELETE FROM` : Hard delete, menghapus data secara permanen. Hanya digunakan di AdminService untuk menghapus relasi RBAC (model_has_roles) saat menghapus user admin.
```sql
DELETE FROM model_has_roles WHERE model_id = ? AND model_type = 'App\Models\User'
```

---

## 3. DQL (Data Query Language)

`SELECT ... FROM ... WHERE` : Membaca data dengan filter. Digunakan di semua service untuk mengambil data berdasarkan ID, status, atau kondisi tertentu. Contoh: ProductService::getById() untuk mengambil detail produk.
```sql
SELECT id, name, sku, price, stock, is_active
FROM products
WHERE id = ? AND deleted_at IS NULL
```

`SELECT ... ORDER BY` : Mengurutkan hasil query. Digunakan di ProductService::getAll() untuk mengurutkan produk berdasarkan nama, dan OrderAdminService::getAll() untuk pesanan terbaru di atas.
```sql
SELECT id, name, sku, price, stock, is_active
FROM products
WHERE deleted_at IS NULL
ORDER BY name ASC
```

`SELECT ... LIMIT` : Membatasi jumlah hasil. Digunakan di dashboard admin untuk menampilkan 5 pesanan terbaru dan 20 pesanan di tabel produksi agar data tidak membebani server.
```sql
SELECT o.id, o.order_number, o.status, o.total_amount, o.created_at,
       c.name AS customer_name
FROM orders o
JOIN customers c ON c.id = o.customer_id
WHERE o.deleted_at IS NULL
ORDER BY o.created_at DESC
LIMIT 20
```

`JOIN` : Menggabungkan 2 tabel, hanya mengambil baris yang cocok di kedua tabel. Digunakan di OrderAdminService untuk menampilkan pesanan lengkap dengan nama customer dan nama batch di halaman daftar pesanan admin.
```sql
SELECT o.id, o.order_number, o.status, o.total_amount,
       c.name AS customer_name, c.phone AS customer_phone,
       b.name AS batch_name
FROM orders o
JOIN customers c ON c.id = o.customer_id
JOIN batches b ON b.id = o.batch_id
WHERE o.deleted_at IS NULL
```

`LEFT JOIN` : Menggabungkan tabel, mengambil semua baris kiri meskipun tidak cocok di tabel kanan. Digunakan di AdminService::getAll() untuk menampilkan user admin beserta role-nya (user yang belum punya role tetap muncul).
```sql
SELECT u.id, u.name, u.email, u.phone, r.name as role
FROM users u
LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id
     AND mhr.model_type = 'App\Models\User'
LEFT JOIN roles r ON mhr.role_id = r.id
```

`AS (Alias)` : Memberi nama alias pada kolom atau tabel. Digunakan di hampir semua query JOIN untuk membedakan kolom yang sama dari tabel berbeda (c.name AS customer_name, b.name AS batch_name).
```sql
SELECT c.name AS customer_name, o.order_number, o.total_amount
FROM orders o
JOIN customers c ON c.id = o.customer_id
```

`DISTINCT` : Menghilangkan data duplikat. Digunakan di FonnteService::sendReadyForPickup() untuk memastikan setiap customer hanya menerima 1 notifikasi WhatsApp saat batch berubah jadi "ready".
```sql
SELECT DISTINCT o.order_number, o.pickup_code, c.name AS customer_name, c.phone AS customer_phone
FROM orders o
JOIN customers c ON c.id = o.customer_id
WHERE o.batch_id = ? AND o.deleted_at IS NULL
       AND c.phone IS NOT NULL AND c.phone != ''
```

`IN` : Filter dengan beberapa nilai sekaligus. Digunakan di AdminService::getActiveBatch() untuk mencari batch yang statusnya "open" ATAU "processing".
```sql
SELECT id, name, event_name, event_date, status
FROM batches
WHERE status IN ('open', 'processing') AND deleted_at IS NULL
```

`IS NULL / IS NOT NULL` : Filter berdasarkan nilai NULL. Digunakan di seluruh query bisnis untuk soft delete (`WHERE deleted_at IS NULL`) dan di FonnteService untuk memastikan customer punya nomor WA (`WHERE c.phone IS NOT NULL`).
```sql
WHERE deleted_at IS NULL
WHERE c.phone IS NOT NULL AND c.phone != ''
```

`Subquery` : Query di dalam query. Digunakan di OrderService::getByCustomer() untuk menghitung jumlah item per pesanan tanpa perlu JOIN tambahan.
```sql
SELECT o.id, o.order_number, o.status, o.total_amount,
       (SELECT COUNT(*) FROM order_product WHERE order_id = o.id) as item_count
FROM orders o
WHERE o.customer_id = ? AND o.deleted_at IS NULL
```

---

## 4. Aggregate Functions

`COUNT(*)` : Menghitung jumlah baris. Digunakan di dashboard admin untuk menampilkan total pelanggan terdaftar, jumlah pesanan siap diambil, dan jumlah pesanan per batch.
```sql
SELECT COUNT(*) AS total FROM customers WHERE deleted_at IS NULL
```
```sql
SELECT COUNT(*) AS total FROM orders WHERE status = 'ready' AND deleted_at IS NULL
```

`COUNT(DISTINCT)` : Menghitung jumlah nilai unik. Digunakan di AdminService::getDashboardStatsByBatch() untuk menghitung berapa customer berbeda yang memesan di suatu batch.
```sql
SELECT COUNT(DISTINCT customer_id) AS total
FROM orders WHERE batch_id = ? AND deleted_at IS NULL
```

`SUM()` : Menjumlahkan nilai kolom. Digunakan di dashboard admin untuk menghitung total omset keseluruhan dan profit (dari pesanan yang sudah diambil), serta di tabel ringkasan produk untuk total kuantitas per produk.
```sql
SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE deleted_at IS NULL
```
```sql
SELECT p.name AS produk, SUM(op.quantity) AS qty
FROM order_product op
JOIN orders o ON o.id = op.order_id
JOIN products p ON p.id = op.product_id
WHERE o.batch_id = ? AND o.deleted_at IS NULL
GROUP BY p.id
```

`MAX()` : Mencari nilai terbesar. Digunakan di ProductService::getNextSku() untuk mencari nomor SKU tertinggi agar bisa generate SKU berikutnya (TEFA-SKU-001, 002, 003, ...).
```sql
SELECT MAX(CAST(SUBSTRING(sku, 10) AS UNSIGNED)) AS max_num
FROM products WHERE sku LIKE 'TEFA-SKU-%'
```

`COALESCE()` : Mengembalikan nilai default jika hasil NULL. Digunakan bersama SUM di dashboard admin agar omset/profit menampilkan 0 bukan NULL ketika belum ada pesanan.
```sql
SELECT COALESCE(SUM(total_amount), 0) AS total
FROM orders WHERE status = 'picked_up' AND deleted_at IS NULL
```

`GROUP BY` : Mengelompokkan hasil berdasarkan kolom. Digunakan di BatchService::getAll() untuk menampilkan daftar batch beserta jumlah pesanan per batch, dan di AdminService untuk ringkasan produk per batch.
```sql
SELECT b.id, b.name, b.event_name, b.event_date, b.status,
       COUNT(DISTINCT o.id) AS order_count
FROM batches b
LEFT JOIN orders o ON o.batch_id = b.id AND o.deleted_at IS NULL
WHERE b.deleted_at IS NULL
GROUP BY b.id
ORDER BY b.created_at DESC
```

`CAST()` : Mengubah tipe data. Digunakan bersama MAX dan SUBSTRING di ProductService untuk mengubah bagian SKU dari string menjadi angka agar bisa dicari nilai tertingginya.
```sql
SELECT MAX(CAST(SUBSTRING(sku, 10) AS UNSIGNED)) AS max_num
FROM products WHERE sku LIKE 'TEFA-SKU-%'
```

`SUBSTRING()` : Mengambil sebagian string dari kolom. Digunakan di ProductService untuk mengambil bagian angka dari SKU ("TEFA-SKU-005" → "5") agar bisa di-increment.
```sql
SELECT MAX(CAST(SUBSTRING(sku, 10) AS UNSIGNED)) AS max_num
FROM products WHERE sku LIKE 'TEFA-SKU-%'
```

`DATE_FORMAT()` : Memformat tanggal menjadi format tertentu. Digunakan di OrderService::getMonthlyStats() untuk mengelompokkan pesanan per bulan (format YYYY-MM) di sparkline chart customer dashboard.
```sql
SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
       COUNT(*) as total,
       COALESCE(SUM(total_amount), 0) as amount
FROM orders
WHERE customer_id = ? AND deleted_at IS NULL
      AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month ASC
```

`DATE_SUB()` : Mengurangi tanggal dengan interval tertentu. Digunakan di OrderService untuk memfilter data 6 bulan terakhir di statistik bulanan customer dashboard.
```sql
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
```

`NOW()` : Mendapatkan timestamp saat ini. Digunakan di semua operasi INSERT dan UPDATE untuk mengisi kolom created_at dan updated_at secara otomatis.
```sql
INSERT INTO products (..., created_at, updated_at) VALUES (..., NOW(), NOW())
UPDATE products SET updated_at = NOW() WHERE id = ?
```

---

## Ringkasan

| Kategori | Jumlah | Fungsi yang Digunakan |
|----------|--------|-----------------------|
| DDL | 17 tabel | CREATE TABLE, FOREIGN KEY, ENUM, UNIQUE, AUTO_INCREMENT, PRIMARY KEY, INDEX, CASCADE, NULL/NOT NULL, DEFAULT, DECIMAL, TIMESTAMP |
| DML | 32 query | INSERT INTO VALUES, UPDATE SET, DELETE FROM |
| DQL | 48 query | SELECT FROM WHERE, ORDER BY, LIMIT, JOIN, LEFT JOIN, AS Alias, DISTINCT, IN, IS NULL/IS NOT NULL, Subquery |
| Aggregate | 19 query | COUNT, COUNT DISTINCT, SUM, MAX, COALESCE, GROUP BY, CAST, SUBSTRING, DATE_FORMAT, DATE_SUB, NOW |
