# Buku Panduan Pengguna
## TEFA Canning SIP — Sistem Informasi Perikanan

**Versi:** 1.0
**Tanggal:** Mei 2026
**Dibuat oleh:** Tim Pengembang TEFA Canning SIP

---

## Daftar Isi

1. [Pendahuluan](#bagian-1-pendahuluan)
2. [Panduan Pelanggan (Customer)](#bagian-2-panduan-pelanggan-customer)
   - 2.1 Registrasi Akun
   - 2.2 Login Pelanggan
   - 2.3 Lupa Password
   - 2.4 Dashboard Pelanggan
   - 2.5 Pre-Order Sarden
   - 2.6 Riwayat Pesanan
   - 2.7 Edit Pesanan
   - 2.8 Profil
3. [Panduan Admin](#bagian-3-panduan-admin)
   - 3.1 Login Admin
   - 3.2 Dashboard Admin
   - 3.3 Kelola Produk
   - 3.4 Kelola Batch
   - 3.5 Kelola Pelanggan
   - 3.6 Kelola Pesanan
   - 3.7 Pengaturan User
   - 3.8 Log Aktivitas
4. [Panduan Landing Page](#bagian-4-panduan-landing-page)
5. [Notifikasi WhatsApp](#bagian-5-notifikasi-whatsapp)

---

## Bagian 1: Pendahuluan

### Tentang Sistem

**TEFA Canning SIP** (Sistem Informasi Perikanan) adalah sistem informasi berbasis web untuk mengelola pre-order produk kaleng ikan di unit TEFA Canning Politeknik Negeri Jember. Sistem ini memfasilitasi pelanggan untuk melakukan pemesanan kaleng ikan secara online dan membantu admin mengelola produksi, batch, dan distribusi.

### Fitur Utama

- **Pre-Order Online** — Pelanggan dapat memesan produk kaleng ikan secara online dengan memilih batch dan produk yang tersedia.
- **Monitoring Batch** — Admin dapat mengelola siklus batch pre-order (Open → Processing → Ready → Closed).
- **Manajemen Pesanan** — Pelacakan status pesanan dari pembuatan hingga pengambilan (Pick Up).
- **Laporan PDF** — Generate laporan pesanan dalam format PDF untuk dokumentasi.
- **Notifikasi WhatsApp** — Pengiriman notifikasi otomatis melalui WhatsApp pada tahap-tahap penting pesanan.

### Role Pengguna

Sistem memiliki **2 role** utama:

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Pelanggan (Customer)** | Pembeli/pemesan produk kaleng ikan | Dashboard, Pre-Order, Riwayat Pesanan, Profil |
| **Admin** | Pengelola sistem | Dashboard, Produk, Batch, Pelanggan, Pesanan, Pengaturan, Log Aktivitas |
| — *Super Admin* | Admin utama dengan akses penuh | Semua fitur + manajemen user + log aktivitas + data finansial |
| — *Teknisi* | Admin operasional | Semua fitur kecuali manajemen user, log aktivitas, dan data finansial |

### Cara Mengakses Sistem

Buka browser (Chrome/Firefox) dan akses alamat:

```
http://localhost:8000
```

---

## Bagian 2: Panduan Pelanggan (Customer)

### 2.1 Registrasi Akun

**Deskripsi:** Membuat akun baru untuk dapat melakukan pre-order produk kaleng ikan.

`[Screenshot: halaman-registrasi.png — Screenshot penuh form registrasi di auth/register.php]`

**Langkah-langkah:**

1. Buka halaman utama sistem di `http://localhost:8000`.
2. Klik tombol **"Daftar"** di navbar, atau langsung buka `http://localhost:8000/auth/register.php`.
3. Isi form registrasi:
   - **Name** *(wajib)* — Nama lengkap Anda
   - **Email address** *(wajib)* — Alamat email aktif Anda
   - **No. Telepon** *(wajib)* — Nomor WhatsApp aktif (format: 08xxxxxxxxxx)
   - **Organisasi / Instansi** *(opsional)* — Nama organisasi atau instansi Anda
   - **Alamat** *(wajib)* — Alamat lengkap pengiriman
   - **Password** *(wajib)* — Minimal 8 karakter
   - **Confirm password** *(wajib)* — Ulangi password yang sama
4. Klik tombol **"Sign up"**.
5. Jika berhasil, Anda akan otomatis login dan diarahkan ke Dashboard Pelanggan.

**Catatan:**
- Email harus unik (belum terdaftar di sistem).
- Password minimal 8 karakter.
- Nomor telepon digunakan untuk notifikasi WhatsApp, pastikan nomor WhatsApp aktif.

---

### 2.2 Login Pelanggan

**Deskripsi:** Masuk ke akun pelanggan yang sudah terdaftar.

`[Screenshot: halaman-login-customer.png — Screenshot form login di auth/login-customer.php]`

**Langkah-langkah:**

1. Buka `http://localhost:8000/auth/login-customer.php`.
2. Masukkan **Email address** yang sudah terdaftar.
3. Masukkan **Password**.
4. Klik tombol **"Sign in"**.
5. Jika berhasil, Anda akan diarahkan ke Dashboard Pelanggan.

**Catatan:**
- Jika lupa password, klik link **"Forgot password?"** di bawah field password (lihat bagian 2.3).
- Jika email atau password salah, akan muncul pesan error berwarna merah.

---

### 2.3 Lupa Password

**Deskripsi:** Mereset password akun melalui kode OTP yang dikirim via WhatsApp.

#### Langkah 1: Kirim Kode OTP

`[Screenshot: halaman-forgot-password.png — Screenshot form input email di auth/forgot-password.php]`

1. Dari halaman login pelanggan, klik link **"Forgot password?"**.
2. Masukkan **Email address** yang terdaftar di akun Anda.
3. Klik tombol **"Kirim Kode OTP"**.
4. Sistem akan mengirimkan kode OTP 6 digit ke nomor WhatsApp yang terdaftar.

#### Langkah 2: Input OTP & Password Baru

`[Screenshot: halaman-reset-password.png — Screenshot form OTP + password baru di auth/reset-password.php]`

1. Setelah mengirim OTP, klik tombol **"Masukkan Kode OTP"** yang muncul.
2. Di halaman Reset Password, masukkan:
   - **Kode OTP** — 6 digit angka yang dikirim via WhatsApp
   - **Password Baru** — Minimal 6 karakter
   - **Konfirmasi Password** — Ulangi password baru
3. Klik tombol **"Reset Password"**.
4. Jika berhasil, klik **"Kembali ke Login"** untuk login dengan password baru.

**Catatan:**
- Kode OTP berlaku selama **15 menit** sejak dikirim.
- Jika kode OTP kadaluarsa, kembali ke halaman "Forgot password" untuk mengirim ulang.
- Pastikan nomor WhatsApp yang terdaftar di akun masih aktif.

---

### 2.4 Dashboard Pelanggan

**Deskripsi:** Halaman utama setelah login yang menampilkan ringkasan statistik, batch aktif, dan produk yang tersedia.

`[Screenshot: dashboard-customer.png — Screenshot halaman dashboard di customer/dashboard.php]`

**Informasi yang ditampilkan:**

1. **Statistik Kartu** (di bagian atas):
   - **Total Pesanan** — Jumlah seluruh pesanan Anda
   - **Total Belanja** — Total nominal pesanan
   - **Menunggu** — Jumlah pesanan berstatus pending
   - **Siap Diambil** — Jumlah pesanan berstatus ready

2. **Batch Aktif** — Informasi batch yang sedang open (bisa dipesan), termasuk nama batch, event, dan tanggal.

3. **Produk Tersedia** — Daftar produk kaleng ikan yang bisa dipesan beserta harga dan stok.

4. **Grafik Sparkline** — Grafik mini yang menunjukkan tren pesanan 6 bulan terakhir.

**Navigasi:**
- Dari Dashboard, gunakan sidebar/menu untuk berpindah ke halaman lain (Pre-Order, Riwayat Pesanan, Profil).

---

### 2.5 Pre-Order Sarden

**Deskripsi:** Melakukan pemesanan produk kaleng ikan pada batch yang sedang open.

`[Screenshot: halaman-preorder.png — Screenshot form pre-order di customer/preorder.php]`

**Langkah-langkah:**

1. Buka menu **"Pre-Order"** di sidebar, atau buka `http://localhost:8000/customer/preorder.php`.
2. **Pilih Batch** — Klik pada batch yang tersedia di bagian "Pilih Batch Pre-Order". Hanya batch berstatus "Open" yang bisa dipilih.
3. **Pilih Produk** — Klik pada produk yang ingin dipesan. Produk yang dipilih akan ditandai dengan garis merah (outline).
4. **Atur Jumlah** — Untuk setiap produk yang dipilih, masukkan jumlah pemesanan:
   - Minimum: **100 kaleng**
   - Maksimum: **3.000 kaleng**
   - Pastikan stok mencukupi (lihat info stok di samping produk).
5. **Tambah Catatan** *(opsional)* — Klik bagian "Catatan" untuk membuka kolom catatan, lalu isi jika ada pesan khusus.
6. **Periksa Ringkasan** — Di bagian bawah, pastikan ringkasan pesanan sudah benar (produk, jumlah, total harga).
7. Klik tombol **"Kirim Pre-Order"**.
8. Jika berhasil, muncul notifikasi hijau berisi nomor pesanan (contoh: `ORD-A1B2C3D4`).

**Catatan:**
- Harga diambil otomatis dari database, tidak bisa diubah manual.
- Stok berkurang langsung saat pesanan dibuat. Jika stok tidak cukup, pesanan gagal.
- Pesanan yang berhasil akan otomatis mengirim notifikasi WhatsApp ke admin.
- Batch yang sudah ditutup (Closed/Processing/Ready) tidak bisa dipesan.

---

### 2.6 Riwayat Pesanan

**Deskripsi:** Melihat daftar seluruh pesanan yang pernah dibuat, mencari, membatalkan, dan mengunduh PDF.

`[Screenshot: halaman-riwayat-pesanan.png — Screenshot tabel pesanan di customer/orders.php]`

**Fitur yang tersedia:**

1. **Daftar Pesanan** — Tabel berisi semua pesanan Anda dengan kolom:
   - No. Pesanan
   - Batch
   - Jumlah Item
   - Total Harga
   - Status (Menunggu / Diproses / Siap Diambil / Selesai)
   - Tanggal Pesan
   - Aksi

2. **Pencarian** — Ketik nomor pesanan di kolom "Cari no. pesanan..." untuk mencari pesanan tertentu.

3. **Membatalkan Pesanan:**
   - Hanya pesanan berstatus **"Menunggu" (Pending)** yang bisa dibatalkan.
   - Klik ikon/tombol batalkan pada pesanan tersebut.
   - Stok akan dikembalikan secara otomatis.

4. **Mengunduh PDF:**
   - Klik tombol **"Preview"** (ikon mata) untuk melihat PDF di browser.
   - Klik tombol **"Download"** (ikon unduh) untuk mengunduh file PDF.
   - PDF berisi detail pesanan lengkap: info pelanggan, produk, harga, dan kode pickup.

5. **Mengedit Pesanan:**
   - Klik tombol **"Edit"** (ikon pensil) pada pesanan berstatus "Menunggu" untuk mengubah pesanan (lihat bagian 2.7).

---

### 2.7 Edit Pesanan

**Deskripsi:** Mengubah isi pesanan (produk & jumlah) untuk pesanan yang masih berstatus "Menunggu" (Pending).

`[Screenshot: halaman-edit-pesanan.png — Screenshot form edit pesanan di customer/edit-order.php]`

**Langkah-langkah:**

1. Dari halaman Riwayat Pesanan, klik tombol **"Edit"** pada pesanan berstatus "Menunggu".
2. Ubah jumlah produk yang diinginkan:
   - Tambah atau kurangi jumlah kaleng per produk.
   - Minimum: 100 kaleng, Maksimum: 3.000 kaleng per produk.
   - Bisa menambah produk baru atau menghapus produk dari pesanan.
3. Periksa total harga di bagian ringkasan.
4. Klik tombol **"Simpan Perubahan"**.

**Catatan:**
- Hanya pesanan berstatus **Pending** yang bisa diedit.
- Jika pesanan sudah berstatus "Diproses" atau lebih lanjut, tombol Edit tidak muncul.
- Jika pesanan memiliki status aktif (Pending/Diproses/Siap Diambil), profil tidak bisa diubah.

---

### 2.8 Profil

**Deskripsi:** Mengubah data diri dan password akun pelanggan.

`[Screenshot: halaman-profil.png — Screenshot halaman profil di customer/profile.php]`

#### Mengubah Data Profil

1. Buka menu **"Profil"** di sidebar.
2. Ubah informasi yang diinginkan:
   - Nama
   - No. Telepon
   - Organisasi / Instansi
   - Alamat
3. Klik tombol **"Simpan Profil"**.

#### Mengubah Password

1. Di bagian bawah halaman profil, isi form "Ubah Password":
   - **Password Saat Ini** — Password yang sekarang digunakan
   - **Password Baru** — Minimal 8 karakter
   - **Konfirmasi Password Baru** — Ulangi password baru
2. Klik tombol **"Ubah Password"**.

**Catatan:**
- Jika ada pesanan yang sedang aktif (status Pending/Diproses/Siap Diambil), profil **tidak bisa diubah**. Muncul pesan peringatan di halaman profil.
- Password baru minimal 8 karakter.

---

## Bagian 3: Panduan Admin

### 3.1 Login Admin

**Deskripsi:** Masuk ke panel admin untuk mengelola sistem.

`[Screenshot: halaman-login-admin.png — Screenshot form login admin di auth/login-admin.php]`

**Langkah-langkah:**

1. Buka `http://localhost:8000/auth/login-admin.php`.
2. Masukkan **Email address** akun admin.
3. Masukkan **Password**.
4. Klik tombol **"Sign in"**.
5. Jika berhasil, Anda akan diarahkan ke Dashboard Admin.

**Catatan:**
- Akun default Super Admin: `superadmin@tefa.polije.ac.id` / `password`
- Akun default Teknisi: `teknisi@tefa.polije.ac.id` / `password`
- Hanya akun yang terdaftar di tabel `users` yang bisa login.

---

### 3.2 Dashboard Admin

**Deskripsi:** Halaman utama admin yang menampilkan statistik keseluruhan sistem, grafik tren, dan ringkasan pesanan per batch.

`[Screenshot: dashboard-admin.png — Screenshot dashboard admin di admin/dashboard.php]`

**Informasi yang ditampilkan:**

1. **Filter Batch** — Dropdown di bagian atas untuk memfilter statistik berdasarkan batch tertentu, atau pilih "Semua Batch" untuk melihat keseluruhan.

2. **Kartu Statistik** (6 kartu):
   - **Batch Aktif** — Nama batch yang sedang berjalan
   - **Total Pesanan** — Jumlah pesanan (di batch terpilih / keseluruhan)
   - **Siap Diambil** — Pesanan yang siap diambil pelanggan
   - **Total Pelanggan** — Jumlah pelanggan terdaftar
   - **Total Omset** — Total revenue dari semua pesanan
   - **Total Profit** — Keuntungan dari pesanan yang sudah diambil (status Picked Up)

3. **Grafik Sparkline** — Grafik mini tren 6 bulan terakhir untuk: pesanan, omset, pesanan siap, pelanggan baru, dan profit.

4. **Tabel Ringkasan Pesanan** — Daftar pesanan terbaru di batch terpilih.

5. **Tabel Ringkasan Produk** — Jumlah pemesanan per produk di batch terpilih.

**Catatan:**
- **Profit** hanya dihitung dari pesanan berstatus **Picked Up** (sudah diambil).
- Teknisi tidak melihat kartu Omset dan Profit (hanya Super Admin).

---

### 3.3 Kelola Produk

**Deskripsi:** Menambah, mengubah, dan menghapus produk kaleng ikan yang tersedia untuk pre-order.

`[Screenshot: halaman-produk.png — Screenshot daftar produk di admin/products.php]`

#### Melihat Daftar Produk

1. Buka menu **"Produk"** di sidebar admin.
2. Tampil tabel berisi semua produk: Nama, SKU, Harga, Stok, Status (Aktif/Nonaktif).
3. Gunakan kolom **Search** untuk mencari produk berdasarkan nama.
4. Gunakan dropdown **"Per page"** untuk mengatur jumlah produk per halaman.

#### Menambah Produk Baru

`[Screenshot: halaman-tambah-produk.png — Screenshot form tambah produk di admin/create-product.php]`

1. Klik tombol **"New Produk"** di halaman daftar produk.
2. Isi form:
   - **Nama Produk** *(wajib)* — Nama produk (contoh: "Sarden Kaleng Premium")
   - **SKU** — Otomatis terisi dengan format `TEFA-SKU-XXX` (tidak perlu diisi manual)
   - **Harga** *(wajib)* — Harga per kaleng (dalam Rupiah)
   - **Stok** *(wajib)* — Jumlah stok tersedia
   - **Status** — Centang "Aktif" agar produk bisa dipesan
3. Klik tombol **"Simpan"**.

#### Mengedit Produk

`[Screenshot: halaman-edit-produk.png — Screenshot form edit produk di admin/edit-product.php]`

1. Klik ikon **edit** (pensil) pada produk yang ingin diubah di tabel daftar produk.
2. Ubah informasi yang diinginkan.
3. Klik tombol **"Update"**.

#### Menghapus Produk

1. Klik ikon **hapus** (tempat sampah) pada produk yang ingin dihapus.
2. Konfirmasi penghapusan.
3. Produk akan di-soft delete (tidak benar-benar hilang dari database).

**Catatan:**
- **Produk inti** (3 produk utama bawaan sistem TEFA) **tidak bisa dihapus**. Akan muncul pesan: "Produk inti tidak dapat dihapus. Alasan: Produk ini merupakan produk utama bawaan sistem TEFA."
- Hanya Super Admin yang bisa mengubah **harga** produk. Teknisi hanya bisa melihat harga tanpa bisa mengeditnya.

---

### 3.4 Kelola Batch

**Deskripsi:** Mengelola periode pre-order (batch) beserta siklus statusnya.

`[Screenshot: halaman-batch.png — Screenshot daftar batch di admin/batches.php]`

#### Status Batch

Batch memiliki 4 status yang menggambarkan siklus pre-order:

| Status | Warna | Arti |
|--------|-------|------|
| **Open** | Hijau | Batch dibuka untuk pre-order pelanggan |
| **Processing** | Biru | Batch sedang diproses/produksi |
| **Ready** | Kuning | Produk siap diambil pelanggan |
| **Closed** | Abu-abu | Batch sudah selesai/ditutup |

#### Melihat Daftar Batch

1. Buka menu **"Batches"** di sidebar admin.
2. Tampil tabel berisi semua batch: Nama, Event, Tanggal, Status, Jumlah Pesanan.
3. Gunakan kolom **Search** untuk mencari batch.
4. Gunakan dropdown **filter status** untuk memfilter batch berdasarkan statusnya.
5. Gunakan dropdown **"Per page"** untuk mengatur jumlah data per halaman.

#### Menambah Batch Baru

`[Screenshot: halaman-tambah-batch.png — Screenshot form tambah batch di admin/create-batch.php]`

1. Klik tombol **"New batch"** di halaman daftar batch.
2. Isi form:
   - **Nama Batch** *(wajib)* — Nama batch (contoh: "Batch Perdana 2026")
   - **Nama Event** *(wajib)* — Nama event terkait
   - **Tanggal Event** *(wajib)* — Tanggal pelaksanaan event
   - **Status** — Pilih status awal (biasanya "Open")
3. Klik tombol **"Simpan"**.

#### Mengedit Batch & Mengubah Status

`[Screenshot: halaman-edit-batch.png — Screenshot form edit batch di admin/edit-batch.php]`

1. Klik ikon **edit** (pensil) pada batch yang ingin diubah.
2. Ubah informasi atau **ubah status batch**:
   - Set status ke **"Ready"** akan mengirim notifikasi WhatsApp ke semua pelanggan di batch tersebut.
3. Klik tombol **"Update"**.

#### Menghapus Batch

1. Klik ikon **hapus** (tempat sampah) pada batch yang ingin dihapus.
2. Konfirmasi penghapusan.

---

### 3.5 Kelola Pelanggan

**Deskripsi:** Melihat dan mengelola data pelanggan yang terdaftar di sistem.

`[Screenshot: halaman-pelanggan.png — Screenshot daftar pelanggan di admin/customers.php]`

#### Melihat Daftar Pelanggan

1. Buka menu **"Pelanggan"** di sidebar admin.
2. Tampil tabel berisi: Nama, Email, Telepon, Organisasi, Alamat, Tanggal Daftar.
3. Gunakan kolom **Search** untuk mencari pelanggan.
4. Gunakan dropdown **"Per page"** untuk mengatur jumlah data per halaman.

#### Menambah Pelanggan Baru

`[Screenshot: halaman-tambah-pelanggan.png — Screenshot form tambah pelanggan di admin/create-customer.php]`

1. Klik tombol **"New Pelanggan"** di halaman daftar pelanggan.
2. Isi data pelanggan: Nama, Email, Telepon, Organisasi, Alamat, Password.
3. Klik tombol **"Simpan"**.

#### Mengedit Data Pelanggan

1. Klik ikon **edit** (pensil) pada pelanggan yang ingin diubah.
2. Ubah informasi yang diinginkan.
3. Klik tombol **"Update"**.

#### Menghapus Pelanggan

1. Klik ikon **hapus** (tempat sampah) pada pelanggan.
2. Konfirmasi penghapusan. Pelanggan akan di-soft delete.

---

### 3.6 Kelola Pesanan

**Deskripsi:** Melihat, membuat, mengedit status, melihat detail, dan menghapus pesanan pelanggan.

`[Screenshot: halaman-pesanan-admin.png — Screenshot daftar pesanan di admin/orders.php]`

#### Status Pesanan

| Status | Warna | Arti |
|--------|-------|------|
| **Pending** | Kuning | Menunggu konfirmasi/pembayaran |
| **Processing** | Biru | Sedang diproses/produksi |
| **Ready** | Hijau | Siap diambil pelanggan |
| **Picked Up** | Abu-abu | Sudah diambil pelanggan |

#### Melihat Daftar Pesanan

1. Buka menu **"Pesanan"** di sidebar admin.
2. Tampil tabel berisi: No. Pesanan, Pelanggan, Batch, Total, Status, Tanggal.
3. Gunakan **Search** untuk mencari berdasarkan nomor pesanan atau nama pelanggan.
4. Gunakan **filter status** untuk memfilter pesanan.
5. Gunakan dropdown **"Per page"** untuk mengatur jumlah data per halaman.

#### Membuat Pesanan Baru (Admin)

`[Screenshot: halaman-tambah-pesanan.png — Screenshot form buat pesanan di admin/create-order.php]`

1. Klik tombol **"New Pesanan"** di halaman daftar pesanan.
2. Isi form:
   - **Pelanggan** *(wajib)* — Pilih pelanggan dari dropdown
   - **Batch** *(wajib)* — Pilih batch dari dropdown (hanya batch Open)
   - **Produk** — Pilih produk dan masukkan jumlah
   - **Catatan** *(opsional)* — Catatan tambahan
3. Klik tombol **"Simpan"**.
4. Sistem akan otomatis mengirim notifikasi WhatsApp ke pelanggan.

#### Melihat Detail Pesanan

`[Screenshot: halaman-detail-pesanan.png — Screenshot detail pesanan di admin/view-order.php]`

1. Klik ikon **view** (mata) pada pesanan yang ingin dilihat.
2. Tampil detail lengkap: info pelanggan, produk yang dipesan, harga, kode pickup, timeline status.
3. Di halaman ini juga tersedia tombol **Preview PDF** dan **Download PDF**.

#### Mengedit Pesanan & Mengubah Status

`[Screenshot: halaman-edit-pesanan-admin.png — Screenshot form edit pesanan di admin/edit-order.php]`

1. Klik ikon **edit** (pensil) pada pesanan.
2. Ubah informasi atau **ubah status pesanan**:
   - Mengubah status ke **"Ready"** akan mengirim notifikasi WhatsApp ke pelanggan.
   - Mengubah status ke **"Picked Up"** mencatat waktu pengambilan.
3. Klik tombol **"Update"**.

#### Menghapus Pesanan

1. Klik ikon **hapus** (tempat sampah) pada pesanan.
2. Konfirmasi penghapusan.
3. Stok produk akan dikembalikan secara otomatis.

---

### 3.7 Pengaturan User

**Deskripsi:** Mengelola akun admin (hanya tersedia untuk Super Admin).

`[Screenshot: halaman-pengaturan.png — Screenshot daftar user di admin/pengaturan.php]`

**Catatan:** Fitur ini **hanya bisa diakses oleh Super Admin**. Teknisi tidak bisa melihat menu ini.

#### Melihat Daftar User Admin

1. Buka menu **"Pengguna"** di sidebar admin.
2. Tampil tabel berisi: Nama, Email, Telepon, Role (Super Admin / Teknisi).

#### Menambah User Admin Baru

`[Screenshot: halaman-tambah-user.png — Screenshot form tambah user di admin/create-user.php]`

1. Klik tombol **"New Pengguna"**.
2. Isi form: Nama, Email, Telepon, Password, Role.
3. Klik tombol **"Simpan"**.

#### Mengedit User Admin

1. Klik ikon **edit** pada user yang ingin diubah.
2. Ubah informasi atau role.
3. Klik tombol **"Update"**.

#### Menghapus User Admin

1. Klik ikon **hapus** pada user.
2. Konfirmasi penghapusan.
3. **Tidak bisa menghapus akun sendiri.**

---

### 3.8 Log Aktivitas

**Deskripsi:** Melihat catatan semua aktivitas yang dilakukan di sistem (hanya Super Admin).

`[Screenshot: halaman-log-aktivitas.png — Screenshot daftar log di admin/activity-log.php]`

**Catatan:** Fitur ini **hanya bisa diakses oleh Super Admin**.

**Informasi yang ditampilkan:**

1. **Tabel Log** berisi:
   - Waktu aktivitas
   - Aktor (siapa yang melakukan)
   - Aksi (Dibuat / Diubah / Dihapus)
   - Target (tipe objek yang diubah, contoh: Product, Batch, Order, Customer)
   - ID Target

2. **Filter:**
   - Filter berdasarkan **jenis aksi** (created/updated/deleted)
   - Filter berdasarkan **tipe target** (Product/Batch/Order/Customer/User)
   - Atur jumlah log per halaman via dropdown **"Per page"**

3. **Navigasi halaman** di bagian bawah tabel.

---

## Bagian 4: Panduan Landing Page

**Deskripsi:** Halaman utama sistem yang bisa diakses oleh siapa saja tanpa login.

`[Screenshot: landing-page.png — Screenshot penuh halaman utama di index.php]`

### Bagian-bagian Landing Page

#### 1. Navbar

- Logo TEFA Canning SIP + Politeknik Negeri Jember
- Menu navigasi: **Beranda**, **Produk**, **Batch**, **Kontak**
- Tombol **"Masuk"** untuk login dan **"Daftar"** untuk registrasi

#### 2. Hero Section

- Judul dan deskripsi singkat tentang TEFA Canning SIP
- Jumlah produk yang tersedia (diambil dari data terkini)
- Tombol CTA untuk langsung mendaftar

#### 3. Katalog Produk

- Daftar produk kaleng ikan yang tersedia (data dari database)
- Setiap produk menampilkan: Nama, SKU, dan Harga

#### 4. Info Batch

- Daftar batch yang sedang open (bisa dipesan)
- Informasi: Nama batch, Event, Tanggal, dan jumlah pesanan masuk

#### 5. Sertifikasi SNI

- Informasi bahwa produk TEFA Canning sudah tersertifikasi SNI

#### 6. Footer

- Informasi kontak TEFA Canning
- Peta lokasi (Google Maps embed)
- Link ke media sosial

### Cara Navigasi

1. **Scroll ke bawah** untuk melihat semua bagian.
2. Klik menu di **navbar** untuk langsung menuju bagian tertentu (Produk, Batch, Kontak).
3. Klik **"Masuk"** untuk login pelanggan atau admin.
4. Klik **"Daftar"** untuk membuat akun baru.

---

## Bagian 5: Notifikasi WhatsApp

**Deskripsi:** Sistem mengirimkan notifikasi otomatis melalui WhatsApp pada momen-momen penting. Notifikasi dikirim menggunakan layanan Fonnte API.

### 4 Trigger Notifikasi

| No | Trigger | Penerima | Isi Pesan |
|----|---------|----------|-----------|
| 1 | Pelanggan submit pre-order | **Admin/Owner** | Notifikasi pesanan baru berisi nama pelanggan, nomor pesanan, total |
| 2 | Admin membuat pesanan manual | **Pelanggan** | Konfirmasi pesanan berisi nomor pesanan, produk, total, kode pickup |
| 3 | Batch diubah ke status "Ready" | **Semua pelanggan di batch** | Pemberitahuan bahwa pesanan siap diambil, menyertakan kode pickup |
| 4 | Pelanggan lupa password | **Pelanggan** | Kode OTP 6 digit untuk reset password |

### Contoh Alur Notifikasi

#### Contoh 1: Pelanggan Pre-Order

```
Pelanggan → Submit Pre-Order → Sistem simpan pesanan
                                    ↓
                            WhatsApp ke Admin/Owner
                        "Pesanan baru dari [Nama]"
                        No: ORD-XXXXXXXX
                        Total: Rp XXX.XXX
```

#### Contoh 2: Batch Ready

```
Admin → Ubah status batch ke "Ready"
                ↓
    Sistem kirim WA ke semua pelanggan di batch
    "Pesanan Anda siap diambil!
     Kode Pickup: XXXXXX"
```

#### Contoh 3: Lupa Password

```
Pelanggan → Input email → Klik "Kirim Kode OTP"
                              ↓
                    WhatsApp ke pelanggan
              "Kode verifikasi Anda: 123456
               Berlaku 15 menit"
```

**Catatan:**
- Nomor WhatsApp yang digunakan adalah nomor yang terdaftar di profil masing-masing.
- Pastikan nomor telepon di profil selalu aktif dan benar.
- Notifikasi bersifat otomatis, tidak perlu dikirim manual.

---

## Lampiran: Status Pesanan & Batch

### Alur Status Pesanan

```
Pending → Processing → Ready → Picked Up
(Menunggu)  (Diproses)   (Siap)   (Selesai)
```

- **Pending:** Pesanan baru dibuat, menunggu konfirmasi.
- **Processing:** Pesanan sedang diproduksi/diproses.
- **Ready:** Pesanan siap diambil pelanggan. Notifikasi WA dikirim.
- **Picked Up:** Pesanan sudah diambil pelanggan. Profit dihitung.

### Alur Status Batch

```
Open → Processing → Ready → Closed
```

- **Open:** Batch dibuka untuk pre-order.
- **Processing:** Batch sedang diproses/produksi.
- **Ready:** Produk batch siap diambil.
- **Closed:** Batch selesai/ditutup.

---

*Dokumen ini merupakan panduan penggunaan sistem TEFA Canning SIP. Untuk pertanyaan lebih lanjut, hubungi tim pengembang.*
