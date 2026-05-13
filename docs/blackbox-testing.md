# Blackbox Testing — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Metode:** Blackbox Testing (Equivalence Partitioning + Boundary Value Analysis)
**Tester:** [Nama]
**Tanggal:** Mei 2026

---

## Daftar Modul yang Ditest

| No | Modul | Jumlah Test Case |
|----|-------|-----------------|
| 1 | Login Admin | 5 |
| 2 | Login Customer | 4 |
| 3 | Registrasi Customer | 7 |
| 4 | Forgot Password | 4 |
| 5 | Pre-order Customer | 6 |
| 6 | Edit Order Customer | 3 |
| 7 | Cancel Order Customer | 2 |
| 8 | Profil Customer | 4 |
| 9 | CRUD Produk (Admin) | 7 |
| 10 | CRUD Batch (Admin) | 5 |
| 11 | CRUD Order (Admin) | 5 |
| 12 | CRUD Customer (Admin) | 3 |
| 13 | RBAC (Role Access) | 4 |
| | **Total** | **59** |

---

## 1. Login Admin

**Pre-condition:** Browser terbuka, user sudah di halaman `auth/login-admin.php`

### Equivalence Partitioning

| Kelas | Partisi | Contoh Input |
|-------|---------|-------------|
| Email | Valid | `superadmin@tefa.polije.ac.id` |
| Email | Invalid format | `superadmin` |
| Email | Kosong | (tidak diisi) |
| Password | Valid | `password` |
| Password | Salah | `salah123` |
| Password | Kosong | (tidik diisi) |

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-01 | Login super_admin valid | 1. Input email `superadmin@tefa.polije.ac.id` 2. Input password `password` 3. Klik Sign in | Redirect ke `admin/dashboard.php`, tampil menu lengkap | | |
| TC-02 | Login teknisi valid | 1. Input email `teknisi@tefa.polije.ac.id` 2. Input password `password` 3. Klik Sign in | Redirect ke `admin/dashboard.php`, menu terbatas (tanpa financial data, tanpa price editing) | | |
| TC-03 | Login password salah | 1. Input email `superadmin@tefa.polije.ac.id` 2. Input password `salah123` 3. Klik Sign in | Tampil error "Email atau password tidak valid", tetap di halaman login | | |
| TC-04 | Login email kosong | 1. Biarkan email kosong 2. Input password `password` 3. Klik Sign in | Form tidak submit (HTML5 required validation) | | |
| TC-05 | Login password kosong | 1. Input email `superadmin@tefa.polije.ac.id` 2. Biarkan password kosong 3. Klik Sign in | Form tidak submit (HTML5 required validation) | | |

---

## 2. Login Customer

**Pre-condition:** Browser terbuka, user sudah di halaman `auth/login-customer.php`

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-06 | Login customer valid | 1. Input email `customer@customer.com` 2. Input password `customer` 3. Klik Sign in | Redirect ke `customer/dashboard.php` | | |
| TC-07 | Login password salah | 1. Input email `customer@customer.com` 2. Input password `salah` 3. Klik Sign in | Tampil error "Email atau password tidak valid" | | |
| TC-08 | Login email tidak terdaftar | 1. Input email `tidakada@email.com` 2. Input password `apapun` 3. Klik Sign in | Tampil error "Email atau password tidak valid" | | |
| TC-09 | Akses dashboard tanpa login | 1. Buka langsung `customer/dashboard.php` tanpa login | Redirect ke `auth/login-customer.php` | | |

---

## 3. Registrasi Customer

**Pre-condition:** Browser terbuka, user sudah di halaman `auth/register.php`

### Equivalence Partitioning

| Kelas | Partisi | Contoh Input |
|-------|---------|-------------|
| Email | Valid unik | `baru@test.com` |
| Email | Sudah terdaftar | `customer@customer.com` |
| Email | Format invalid | `bukanemail` |
| Password | >= 8 karakter | `password1` |
| Password | < 8 karakter | `pass` |
| Konfirmasi password | Cocok | `password1` |
| Konfirmasi password | Tidak cocok | `beda12345` |
| Field wajib | Semua terisi | Data lengkap |
| Field wajib | Ada yang kosong | Name kosong |

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-10 | Registrasi valid | 1. Isi semua field dengan data valid 2. Password >= 8 char, konfirmasi cocok 3. Klik Sign up | Akun dibuat, auto-login, redirect ke `customer/dashboard.php` | | |
| TC-11 | Email sudah terdaftar | 1. Input email `customer@customer.com` 2. Isi field lain valid 3. Klik Sign up | Tampil error "Email sudah terdaftar" | | |
| TC-12 | Email format invalid | 1. Input email `bukanemail` 2. Isi field lain valid 3. Klik Sign up | Form tidak submit (HTML5 email validation) | | |
| TC-13 | Password < 8 karakter | 1. Input password `pass` (4 char) 2. Isi field lain valid 3. Klik Sign up | Tampil error "Password minimal 8 karakter" | | |
| TC-14 | Konfirmasi password tidak cocok | 1. Input password `password1` 2. Input confirm `beda12345` 3. Klik Sign up | Tampil error "Konfirmasi password tidak cocok" | | |
| TC-15 | Field wajib kosong | 1. Biarkan nama kosong 2. Isi field lain valid 3. Klik Sign up | Tampil error "Semua field yang bertanda * wajib diisi" | | |
| TC-16 | Registrasi lalu cek login | 1. Registrasi akun baru 2. Logout 3. Login dengan akun baru | Berhasil login ke dashboard | | |

---

## 4. Forgot Password

**Pre-condition:** Browser terbuka, user sudah di halaman `auth/forgot-password.php`, customer punya nomor WhatsApp terdaftar

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-17 | Kirim OTP valid | 1. Input email customer terdaftar 2. Klik "Kirim Kode OTP" | Tampil sukses, link "Masukkan Kode OTP" muncul | | |
| TC-18 | Kirim OTP email tidak terdaftar | 1. Input email `tidakada@email.com` 2. Klik "Kirim Kode OTP" | Tampil sukses (anti enumeration), tapi tidak ada WA terkirim | | |
| TC-19 | Reset password OTP valid | 1. Kirim OTP 2. Buka link reset 3. Input OTP yang benar 4. Input password baru 5. Klik Reset Password | Password berhasil diubah, redirect ke login | | |
| TC-20 | Reset password OTP salah | 1. Buka halaman reset 2. Input OTP salah `000000` 3. Input password baru 4. Klik Reset Password | Tampil error "Kode OTP tidak valid atau sudah kadaluarsa" | | |

### Boundary Value Analysis

| Kelas | Boundary | Expected |
|-------|----------|---------|
| OTP | 5 digit (kurang dari 6) | Form tidak submit (maxlength 6, required) |
| OTP | 6 digit benar | Password berhasil diubah |
| OTP | 6 digit salah | Error "Kode OTP tidak valid" |
| OTP expiry | Setelah 15 menit | Error "Kode OTP sudah kadaluarsa" |
| Password baru | < 6 karakter | Error "Password minimal 6 karakter" |
| Password baru | 6 karakter | Berhasil |
| Konfirmasi password | Tidak cocok | Error "Konfirmasi password tidak cocok" |

---

## 5. Pre-order Customer

**Pre-condition:** Customer sudah login, ada batch dengan status "open", ada produk dengan stok > 0

### Boundary Value Analysis

| Kelas | Boundary | Expected |
|-------|----------|---------|
| Quantity | 0 | Error / tidak bisa submit |
| Quantity | 1 | Berhasil |
| Quantity | = stok tersedia | Berhasil, stok habis setelah order |
| Quantity | > stok tersedia | Error stok tidak cukup |

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-21 | Pre-order valid | 1. Pilih batch open 2. Pilih produk 3. Input quantity 1 4. Klik Submit | Pesanan tersimpan, stok berkurang, tampil di riwayat pesanan | | |
| TC-22 | Pre-order stok tidak cukup | 1. Pilih produk dengan stok = 2 2. Input quantity = 5 3. Klik Submit | Tampil error stok tidak tersedia | | |
| TC-23 | Pre-order tanpa pilih batch | 1. Buka halaman preorder 2. Tidak pilih batch 3. Pilih produk 4. Klik Submit | Form tidak submit / error validasi | | |
| TC-24 | Pre-order batch closed | 1. Buka halaman preorder 2. Tidak ada batch open di dropdown | Dropdown kosong, tidak bisa buat order | | |
| TC-25 | Pre-order produk stok 0 | 1. Pilih batch open 2. Produk dengan stok 0 tidak muncul / tidak bisa dipilih | Produk stok 0 tidak bisa dipesan | | |
| TC-26 | Cek notifikasi WA ke admin | 1. Submit pre-order sukses 2. Cek WhatsApp admin | Admin menerima pesan WA "Pesanan Baru Masuk" | | |

---

## 6. Edit Order Customer

**Pre-condition:** Customer punya order dengan status "pending"

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-27 | Edit order pending | 1. Buka order pending 2. Ubah quantity 3. Klik Simpan | Order terupdate, total amount berubah | | |
| TC-28 | Edit order menambah melebihi stok | 1. Edit quantity ke > stok tersedia 2. Klik Simpan | Tampil error stok tidak cukup | | |
| TC-29 | Edit order status bukan pending | 1. Coba akses edit order dengan status "processing" | Tidak bisa edit (redirect atau error) | | |

---

## 7. Cancel Order Customer

**Pre-condition:** Customer punya order dengan status "pending"

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-30 | Cancel order pending | 1. Buka order pending 2. Klik Batalkan 3. Konfirmasi | Order status jadi "cancelled", stok dikembalikan | | |
| TC-31 | Cancel order processing | 1. Coba cancel order status "processing" | Tombol cancel tidak muncul / tidak bisa diklik | | |

---

## 8. Profil Customer

**Pre-condition:** Customer sudah login

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-32 | Edit profil valid | 1. Ubah nama / organisasi / alamat 2. Klik Simpan | Data terupdate, tampil di halaman profil | | |
| TC-33 | Ganti password valid | 1. Input password lama benar 2. Input password baru 3. Konfirmasi cocok 4. Klik Simpan | Password berhasil diubah | | |
| TC-34 | Ganti password lama salah | 1. Input password lama salah 2. Input password baru 3. Klik Simpan | Tampil error "Password lama tidak sesuai" | | |
| TC-35 | Edit profil saat punya order aktif | 1. Buat order pending 2. Coba ganti password | Gagal, tampil error "Tidak dapat mengubah password saat masih memiliki pesanan aktif" | | |

---

## 9. CRUD Produk (Admin)

**Pre-condition:** Admin sudah login sebagai super_admin

### Boundary Value Analysis

| Kelas | Boundary | Expected |
|-------|----------|---------|
| Harga | 0 | Error / tidak valid |
| Harga | 1 | Berhasil |
| Harga | 99999999.99 | Berhasil |
| Stok | 0 | Berhasil ( produk tersedia tapi habis) |
| Stok | 1 | Berhasil |
| Stok | -1 | Error / tidak valid |

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-36 | Create produk valid | 1. Isi nama, harga, stok 2. SKU auto-generate 3. Klik Simpan | Produk tersimpan, SKU format TEFA-SKU-XXX | | |
| TC-37 | Create produk tanpa nama | 1. Biarkan nama kosong 2. Isi harga, stok 3. Klik Simpan | Tampil error validasi | | |
| TC-38 | Edit produk (super_admin) | 1. Buka edit produk 2. Ubah harga 3. Klik Simpan | Harga terupdate | | |
| TC-39 | Edit produk (teknisi) | 1. Login teknisi 2. Coba edit harga produk | Tombol/field harga disabled atau tidak bisa akses halaman edit | | |
| TC-40 | Hapus produk biasa | 1. Pilih produk non-core 2. Klik Hapus 3. Konfirmasi | Produk soft-delete, tidak muncul di list | | |
| TC-41 | Hapus produk core (protected) | 1. Coba hapus produk dengan SKU core (Sarden, etc) 2. Klik Hapus | Gagal, tampil pesan alasan produk tidak bisa dihapus | | |
| TC-42 | Hapus produk yang punya order | 1. Coba hapus produk yang sudah ada di order | Gagal, tampil pesan "Produk tidak bisa dihapus karena sudah memiliki pesanan" | | |

---

## 10. CRUD Batch (Admin)

**Pre-condition:** Admin sudah login sebagai super_admin

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-43 | Create batch valid | 1. Isi nama, event, tanggal, status 2. Klik Simpan | Batch tersimpan | | |
| TC-44 | Edit batch status lifecycle | 1. Ubah status open → processing 2. Ubah processing → ready 3. Ubah ready → closed | Status berubah sesuai urutan | | |
| TC-45 | Batch ready → notifikasi WA | 1. Ubah batch status ke "ready" 2. Cek WhatsApp customer | Semua customer di batch menerima WA "Pesanan Siap Diambil" | | |
| TC-46 | Hapus batch tanpa order | 1. Hapus batch yang belum punya order | Batch soft-delete | | |
| TC-47 | Hapus batch dengan order | 1. Hapus batch yang sudah punya order | Gagal, tampil error | | |

---

## 11. CRUD Order (Admin)

**Pre-condition:** Admin sudah login, ada customer terdaftar, ada batch open, ada produk

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-48 | Create order admin | 1. Pilih customer 2. Pilih batch 3. Tambah produk + quantity 4. Klik Simpan | Order terbuat, stok berkurang, WA terkirim ke customer | | |
| TC-49 | Edit order admin | 1. Buka edit order 2. Ubah quantity produk 3. Klik Simpan | Order terupdate, stok disesuaikan | | |
| TC-50 | Delete order admin | 1. Hapus order 2. Konfirmasi | Order terhapus, stok dikembalikan | | |
| TC-51 | Update status ke picked_up | 1. Ubah status order ke picked_up 2. Input pickup code | Status berubah, picked_up_at terisi, profit terhitung | | |
| TC-52 | Download PDF order | 1. Buka view order 2. Klik Download PDF | File PDF terdownload dengan data order lengkap | | |

---

## 12. CRUD Customer (Admin)

**Pre-condition:** Admin sudah login sebagai super_admin

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-53 | Edit customer | 1. Buka edit customer 2. Ubah nama/phone/organisasi 3. Klik Simpan | Data customer terupdate | | |
| TC-54 | Hapus customer tanpa order aktif | 1. Hapus customer tanpa order aktif 2. Konfirmasi | Customer soft-delete | | |
| TC-55 | Hapus customer dengan order aktif | 1. Coba hapus customer yang punya order aktif | Gagal, tampil error | | |

---

## 13. RBAC (Role-Based Access Control)

**Pre-condition:** Memiliki akun super_admin dan teknisi

### Test Cases

| TC ID | Skenario | Langkah | Expected Result | Actual | Status |
|-------|----------|---------|-----------------|--------|--------|
| TC-56 | Teknisi akses halaman user management | 1. Login teknisi 2. Coba akses create-user / manage users | Akses ditolak / redirect / menu tidak muncul | | |
| TC-57 | Teknisi edit harga produk | 1. Login teknisi 2. Buka edit produk 3. Coba ubah harga | Field harga disabled / read-only | | |
| TC-58 | Teknisi akses activity log | 1. Login teknisi 2. Coba akses activity-log.php | Akses ditolak / menu tidak muncul | | |
| TC-59 | Super_admin akses semua fitur | 1. Login super_admin 2. Cek semua menu dan fitur | Semua menu dan fitur bisa diakses | | |

---

## Ringkasan

### Statistik

| Metrik | Nilai |
|--------|-------|
| Total modul ditest | 13 |
| Total test case | 59 |
| Metode | Equivalence Partitioning + Boundary Value Analysis |
| Status keseluruhan | Pending testing |

### Kredensial Testing

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@tefa.polije.ac.id` | `password` |
| Teknisi | `teknisi@tefa.polije.ac.id` | `password` |
| Customer | `customer@customer.com` | `customer` |
