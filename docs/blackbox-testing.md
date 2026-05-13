# Blackbox Testing — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Metode:** Blackbox Testing (Equivalence Partitioning + Boundary Value Analysis)
**Tester:** [Nama]
**Tanggal:** Mei 2026

---

## 1. Autentikasi Admin

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 1 | Login admin valid | Input email `superadmin@tefa.polije.ac.id` dan password `password`, klik Sign in | Redirect ke dashboard admin | | |
| 2 | Login admin gagal | Input email valid dan password salah, klik Sign in | Tampil error "Email atau password tidak valid" | | |

---

## 2. Autentikasi Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 3 | Login customer valid | Input email `customer@customer.com` dan password `customer`, klik Sign in | Redirect ke dashboard customer | | |
| 4 | Login customer gagal | Input email valid dan password salah, klik Sign in | Tampil error "Email atau password tidak valid" | | |
| 5 | Akses halaman tanpa login | Buka langsung URL `customer/dashboard.php` | Redirect ke halaman login | | |

---

## 3. Registrasi Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 6 | Registrasi valid | Isi semua field valid, password >= 8 char, konfirmasi cocok, klik Sign up | Akun dibuat, auto-login, redirect dashboard | | |
| 7 | Registrasi email sudah terdaftar | Input email `customer@customer.com`, klik Sign up | Tampil error "Email sudah terdaftar" | | |
| 8 | Registrasi password tidak cocok | Input password dan konfirmasi berbeda, klik Sign up | Tampil error "Konfirmasi password tidak cocok" | | |

---

## 4. Forgot Password

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 9 | Kirim OTP valid | Input email customer terdaftar, klik "Kirim Kode OTP" | Tampil sukses, link "Masukkan Kode OTP" muncul | | |
| 10 | Reset password OTP valid | Input OTP benar + password baru + konfirmasi, klik Reset | Password berhasil diubah, redirect ke login | | |
| 11 | Reset password OTP salah | Input OTP salah, klik Reset | Tampil error "Kode OTP tidak valid atau sudah kadaluarsa" | | |

---

## 5. Pre-order Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 12 | Pre-order valid | Pilih batch, pilih produk, input quantity, klik Submit | Pesanan tersimpan, stok berkurang | | |
| 13 | Pre-order stok tidak cukup | Input quantity melebihi stok tersedia, klik Submit | Tampil error stok tidak tersedia | | |
| 14 | Notifikasi WA ke admin | Submit pre-order sukses, cek WhatsApp admin | Admin menerima WA "Pesanan Baru Masuk" | | |

---

## 6. Edit & Cancel Order Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 15 | Edit order pending | Ubah quantity produk pada order pending, klik Simpan | Order terupdate, total berubah | | |
| 16 | Cancel order pending | Klik Batalkan pada order pending, konfirmasi | Order cancelled, stok dikembalikan | | |

---

## 7. Profil Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 17 | Edit profil valid | Ubah nama/organisasi/alamat, klik Simpan | Data terupdate | | |
| 18 | Ganti password valid | Input password lama benar, password baru, konfirmasi cocok, klik Simpan | Password berhasil diubah | | |
| 19 | Ganti password lama salah | Input password lama salah, klik Simpan | Tampil error "Password lama tidak sesuai" | | |

---

## 8. CRUD Produk (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 20 | Create produk valid | Isi nama, harga, stok, klik Simpan | Produk tersimpan, SKU auto TEFA-SKU-XXX | | |
| 21 | Edit produk valid | Ubah harga/nama produk, klik Simpan | Produk terupdate | | |
| 22 | Hapus produk core (protected) | Coba hapus produk core (Sarden, dll) | Gagal, tampil pesan alasan tidak bisa dihapus | | |

---

## 9. CRUD Batch (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 23 | Create batch valid | Isi nama, event, tanggal, klik Simpan | Batch tersimpan | | |
| 24 | Edit batch status | Ubah status open → processing → ready → closed | Status berubah sesuai urutan | | |
| 25 | Batch ready → notifikasi WA | Ubah status batch ke "ready", cek WA customer | Customer menerima WA "Pesanan Siap Diambil" | | |

---

## 10. CRUD Order (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 26 | Create order admin | Pilih customer, batch, produk, quantity, klik Simpan | Order terbuat, stok berkurang, WA ke customer | | |
| 27 | Update status ke picked_up | Ubah status ke picked_up | Status berubah, profit terhitung | | |
| 28 | Download PDF order | Klik Download PDF pada view order | File PDF terdownload lengkap | | |

---

## 11. RBAC (Role-Based Access Control)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 29 | Teknisi edit harga produk | Login teknisi, buka edit produk, coba ubah harga | Field harga disabled / read-only | | |
| 30 | Teknisi akses user management | Login teknisi, coba akses create-user | Akses ditolak / menu tidak muncul | | |

---

## Ringkasan

| Metrik | Nilai |
|--------|-------|
| Total modul | 11 |
| Total test case | 30 |
| Metode | Equivalence Partitioning + Boundary Value Analysis |
| Status | Pending testing |

### Kredensial Testing

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@tefa.polije.ac.id` | `password` |
| Teknisi | `teknisi@tefa.polije.ac.id` | `password` |
| Customer | `customer@customer.com` | `customer` |
