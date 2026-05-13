# Blackbox Testing — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Metode:** Blackbox Testing (Equivalence Partitioning + Boundary Value Analysis)
**Tester:** [Nama]
**Tanggal:** Mei 2026

---

## 1. Login Admin

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 1 | Login super_admin dengan kredensial valid | Input email `superadmin@tefa.polije.ac.id` dan password `password`, klik Sign in | Redirect ke dashboard admin, menu lengkap terlihat | | |
| 2 | Login teknisi dengan kredensial valid | Input email `teknisi@tefa.polije.ac.id` dan password `password`, klik Sign in | Redirect ke dashboard admin, menu terbatas | | |
| 3 | Login dengan password salah | Input email valid, password `salah123`, klik Sign in | Tampil error "Email atau password tidak valid" | | |
| 4 | Login dengan email kosong | Biarkan email kosong, isi password, klik Sign in | Form tidak submit (validasi HTML5) | | |
| 5 | Login dengan password kosong | Isi email valid, biarkan password kosong, klik Sign in | Form tidak submit (validasi HTML5) | | |

---

## 2. Login Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 6 | Login customer valid | Input email `customer@customer.com` dan password `customer`, klik Sign in | Redirect ke dashboard customer | | |
| 7 | Login password salah | Input email valid, password salah, klik Sign in | Tampil error "Email atau password tidak valid" | | |
| 8 | Login email tidak terdaftar | Input email `tidakada@email.com`, password apapun, klik Sign in | Tampil error "Email atau password tidak valid" | | |
| 9 | Akses halaman customer tanpa login | Buka langsung URL `customer/dashboard.php` | Redirect ke halaman login customer | | |

---

## 3. Registrasi Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 10 | Registrasi dengan data valid | Isi semua field (nama, email baru, phone, alamat, password >= 8 char, konfirmasi cocok), klik Sign up | Akun dibuat, auto-login, redirect dashboard | | |
| 11 | Registrasi email sudah terdaftar | Input email `customer@customer.com`, field lain valid, klik Sign up | Tampil error "Email sudah terdaftar" | | |
| 12 | Registrasi email format invalid | Input email `bukanemail`, field lain valid, klik Sign up | Form tidak submit (validasi HTML5) | | |
| 13 | Registrasi password < 8 karakter | Input password `pass` (4 char), konfirmasi cocok, klik Sign up | Tampil error "Password minimal 8 karakter" | | |
| 14 | Registrasi konfirmasi password tidak cocok | Input password `password1`, konfirmasi `beda12345`, klik Sign up | Tampil error "Konfirmasi password tidak cocok" | | |
| 15 | Registrasi field wajib kosong | Biarkan nama kosong, isi field lain, klik Sign up | Tampil error "Semua field bertanda * wajib diisi" | | |
| 16 | Registrasi lalu login | Registrasi akun baru, logout, login dengan akun tersebut | Berhasil login ke dashboard customer | | |

---

## 4. Forgot Password

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 17 | Kirim OTP ke email terdaftar | Input email customer yang terdaftar, klik "Kirim Kode OTP" | Tampil sukses, link "Masukkan Kode OTP" muncul | | |
| 18 | Kirim OTP ke email tidak terdaftar | Input email `tidakada@email.com`, klik "Kirim Kode OTP" | Tampil sukses (anti enumeration) | | |
| 19 | Reset password dengan OTP valid | Kirim OTP, buka halaman reset, input OTP benar + password baru, klik Reset | Password berhasil diubah, redirect ke login | | |
| 20 | Reset password dengan OTP salah | Buka halaman reset, input OTP `000000` (salah), password baru, klik Reset | Tampil error "Kode OTP tidak valid atau sudah kadaluarsa" | | |
| 21 | Reset password OTP kadaluarsa | Kirim OTP, tunggu > 15 menit, input OTP, klik Reset | Tampil error "Kode OTP tidak valid atau sudah kadaluarsa" | | |
| 22 | Reset password konfirmasi tidak cocok | Input OTP valid, password baru `baru123`, konfirmasi `beda456`, klik Reset | Tampil error "Konfirmasi password tidak cocok" | | |

---

## 5. Pre-order Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 23 | Pre-order dengan data valid | Pilih batch open, pilih produk, input quantity 1, klik Submit | Pesanan tersimpan, stok berkurang | | |
| 24 | Pre-order quantity melebihi stok | Pilih produk stok = 2, input quantity = 5, klik Submit | Tampil error stok tidak tersedia | | |
| 25 | Pre-order tanpa pilih batch | Buka halaman preorder, tidak pilih batch, klik Submit | Form tidak submit / error validasi | | |
| 26 | Pre-order batch closed (tidak ada batch open) | Buka halaman preorder saat semua batch closed | Dropdown batch kosong, tidak bisa buat order | | |
| 27 | Pre-order produk stok 0 | Coba pesan produk dengan stok 0 | Produk tidak bisa dipesan / tidak muncul | | |
| 28 | Notifikasi WA ke admin setelah pre-order | Submit pre-order sukses, cek WhatsApp admin | Admin menerima WA "Pesanan Baru Masuk" | | |

---

## 6. Edit & Cancel Order Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 29 | Edit order pending | Buka order pending, ubah quantity, klik Simpan | Order terupdate, total amount berubah | | |
| 30 | Edit order melebihi stok | Edit quantity ke > stok tersedia, klik Simpan | Tampil error stok tidak cukup | | |
| 31 | Edit order status bukan pending | Coba akses edit order dengan status "processing" | Tidak bisa edit (redirect / error) | | |
| 32 | Cancel order pending | Buka order pending, klik Batalkan, konfirmasi | Order status jadi cancelled, stok dikembalikan | | |
| 33 | Cancel order processing | Coba cancel order status "processing" | Tombol cancel tidak muncul / tidak bisa diklik | | |

---

## 7. Profil Customer

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 34 | Edit profil valid | Ubah nama / organisasi / alamat, klik Simpan | Data terupdate di halaman profil | | |
| 35 | Ganti password valid | Input password lama benar, password baru, konfirmasi cocok, klik Simpan | Password berhasil diubah | | |
| 36 | Ganti password lama salah | Input password lama salah, password baru, klik Simpan | Tampil error "Password lama tidak sesuai" | | |
| 37 | Ganti password saat punya order aktif | Buat order pending, coba ganti password | Gagal, tampil error "Tidak dapat mengubah password saat masih memiliki pesanan aktif" | | |

---

## 8. CRUD Produk (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 38 | Create produk valid | Isi nama, harga, stok, SKU auto-generate, klik Simpan | Produk tersimpan, SKU format TEFA-SKU-XXX | | |
| 39 | Create produk tanpa nama | Biarkan nama kosong, isi harga dan stok, klik Simpan | Tampil error validasi | | |
| 40 | Edit produk oleh super_admin | Buka edit produk, ubah harga, klik Simpan | Harga terupdate | | |
| 41 | Edit produk oleh teknisi | Login teknisi, buka edit produk, coba ubah harga | Field harga disabled / read-only | | |
| 42 | Hapus produk non-core | Pilih produk non-core, klik Hapus, konfirmasi | Produk soft-delete, hilang dari list | | |
| 43 | Hapus produk core (protected) | Coba hapus produk core (Sarden, dll) | Gagal, tampil pesan alasan produk tidak bisa dihapus | | |
| 44 | Hapus produk yang punya order | Coba hapus produk yang sudah ada di order | Gagal, tampil pesan produk memiliki pesanan | | |

---

## 9. CRUD Batch (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 45 | Create batch valid | Isi nama, event, tanggal, klik Simpan | Batch tersimpan | | |
| 46 | Edit batch status lifecycle | Ubah status open → processing → ready → closed | Status berubah sesuai urutan | | |
| 47 | Batch ready → notifikasi WA | Ubah batch status ke "ready", cek WhatsApp customer | Customer di batch menerima WA "Pesanan Siap Diambil" | | |
| 48 | Hapus batch tanpa order | Hapus batch yang belum punya order, konfirmasi | Batch soft-delete | | |
| 49 | Hapus batch dengan order | Coba hapus batch yang sudah punya order | Gagal, tampil error | | |

---

## 10. CRUD Order (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 50 | Create order oleh admin | Pilih customer, batch, tambah produk + quantity, klik Simpan | Order terbuat, stok berkurang, WA terkirim ke customer | | |
| 51 | Edit order oleh admin | Buka edit order, ubah quantity produk, klik Simpan | Order terupdate, stok disesuaikan | | |
| 52 | Delete order oleh admin | Hapus order, konfirmasi | Order terhapus, stok dikembalikan | | |
| 53 | Update status ke picked_up | Ubah status order ke picked_up, input pickup code | Status berubah, picked_up_at terisi, profit terhitung | | |
| 54 | Download PDF order | Buka view order, klik Download PDF | File PDF terdownload dengan data order lengkap | | |

---

## 11. CRUD Customer (Admin)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 55 | Edit customer oleh admin | Buka edit customer, ubah nama/phone/organisasi, klik Simpan | Data customer terupdate | | |
| 56 | Hapus customer tanpa order aktif | Hapus customer tanpa order aktif, konfirmasi | Customer soft-delete | | |
| 57 | Hapus customer dengan order aktif | Coba hapus customer yang punya order aktif | Gagal, tampil error | | |

---

## 12. RBAC (Role-Based Access Control)

| No | Skenario Pengujian | Test Case | Hasil yang Diharapkan | Hasil Pengujian | Kesimpulan |
|----|--------------------|-----------|-----------------------|-----------------|------------|
| 58 | Teknisi akses user management | Login teknisi, coba akses create-user / manage users | Akses ditolak / menu tidak muncul | | |
| 59 | Teknisi akses activity log | Login teknisi, coba akses activity-log.php | Akses ditolak / menu tidak muncul | | |
| 60 | Super_admin akses semua fitur | Login super_admin, cek semua menu dan fitur | Semua menu dan fitur bisa diakses | | |

---

## Ringkasan

| Metrik | Nilai |
|--------|-------|
| Total modul | 12 |
| Total test case | 60 |
| Metode | Equivalence Partitioning + Boundary Value Analysis |
| Status | Pending testing |

### Kredensial Testing

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@tefa.polije.ac.id` | `password` |
| Teknisi | `teknisi@tefa.polije.ac.id` | `password` |
| Customer | `customer@customer.com` | `customer` |
