# Pemeliharaan Sistem

## Peningkatan Fitur

### Penambahan Fitur Notifikasi WhatsApp
Sistem akan ditambahkan fitur notifikasi otomatis melalui WhatsApp untuk memberikan informasi real-time kepada pengguna. Notifikasi dikirim ketika pelanggan melakukan pemesanan baru, pesanan sudah siap diambil, dan saat batch pre-order dibuka atau ditutup. Selain itu, fitur lupa password juga menggunakan WhatsApp untuk mengirimkan kode OTP verifikasi.

### Penambahan Fitur Cetak Laporan PDF
Ditambahkan fitur untuk mengunduh bukti pesanan dalam format PDF. Pelanggan dan admin dapat mencetak atau menyimpan bukti pesanan yang berisi detail produk, jumlah, harga, dan kode pengambilan pesanan.

### Penambahan Fitur Manajemen Pengguna
Admin super dapat menambah, mengedit, dan mengelola akun pengguna (admin dan teknisi) melalui panel admin. Hal ini memudahkan pengelolaan tim operasional tanpa perlu akses langsung ke database.

### Penambahan Fitur Riwayat Aktivitas
Sistem mencatat seluruh aktivitas penting yang dilakukan oleh admin, seperti penambahan produk, perubahan status batch, pembuatan pesanan, dan perubahan data pelanggan. Riwayat ini hanya dapat diakses oleh super admin untuk keperluan audit.

### Penambahan Fitur Edit Pesanan
Pelanggan dapat mengubah isi pesanan selama status pesanan masih "menunggu" (pending). Fitur ini memberikan fleksibilitas bagi pelanggan untuk menyesuaikan jumlah atau jenis produk sebelum pesanan diproses.

---

## Peningkatan Keamanan

### Perlindungan Terhadap Serangan CSRF
Setiap formulir dalam sistem dilindungi oleh token CSRF untuk mencegah serangan pemalsuan permintaan lintas situs. Token ini diverifikasi secara otomatis saat formulir dikirim, sehingga hanya permintaan yang sah dari situs asli yang dapat diproses.

### Penggunaan Prepared Statement pada Query Database
Seluruh query database menggunakan prepared statement PDO untuk mencegah serangan SQL Injection. Data yang dimasukkan pengguna tidak pernah digabung langsung ke dalam query, melainkan dipisahkan melalui parameter binding.

### Sanitasi Output untuk Mencegah XSS
Semua data yang ditampilkan ke pengguna melalui fungsi `htmlspecialchars()` untuk mencegah serangan Cross-Site Scripting (XSS). Ini memastikan kode JavaScript berbahaya yang mungkin dimasukkan pengguna tidak dapat dieksekusi di browser pengguna lain.

### Perlindungan Harga Produk
Harga produk tidak diambil dari input formulir, melainkan selalu diambil langsung dari database saat pesanan dibuat. Hal ini mencegah pelanggan atau pihak tidak bertanggung jawab memanipulasi harga melalui inspect element atau modifikasi formulir.

### Perlindungan Produk Utama
Tiga produk utama sistem (Sarden Kaleng, Tuna Kaleng, Kembung Kaleng) tidak dapat dihapus karena merupakan produk inti. Sistem menolak penghapusan dan menampilkan pesan penjelasan mengapa produk tersebut tidak boleh dihapus.

---

## Peningkatan Sistem Registrasi

### Validasi Nomor Telepon
Formulir registrasi dan edit profil pelanggan ditambahkan validasi nomor telepon dengan format Indonesia. Nomor yang dimasukkan pengguna dengan awalan "08" akan otomatis dikonversi menjadi format internasional "628" agar kompatibel dengan pengiriman notifikasi WhatsApp.

### Validasi Kekuatan Password
Sistem memberikan aturan minimum untuk password yang dibuat oleh pelanggan, yaitu minimal 6 karakter. Validasi ini diterapkan pada saat registrasi, perubahan password di profil, dan saat reset password melalui OTP.

### Pencegahan Duplikasi Email dan Telepon
Sistem memeriksa apakah alamat email dan nomor telepon sudah terdaftar sebelum membuat akun baru. Jika sudah ada, pendaftaran ditolak dan pengguna diberi pesan untuk menggunakan data yang berbeda.

### Alur Lupa Password melalui WhatsApp
Pelanggan yang lupa password dapat meminta kode OTP yang dikirimkan melalui WhatsApp ke nomor telepon yang terdaftar. Kode berlaku selama 15 menit dan hanya dapat digunakan sekali. Setelah memasukkan kode yang benar, pelanggan dapat membuat password baru.

---

## Peningkatan Antarmuka Pengguna

### Desain Responsif
Seluruh halaman sistem didesain responsif agar dapat diakses dengan nyaman dari berbagai perangkat, termasuk laptop, tablet, dan smartphone. Sidebar dan tabel menyesuaikan tampilan secara otomatis pada layar kecil.

### Mode Gelap (Dark Mode)
Panel admin dilengkapi fitur mode gelap yang dapat diaktifkan melalui toggle di header. Fitur ini memberikan kenyamanan bagi admin yang bekerja dalam kondisi pencahayaan rendah.

### Navigasi yang Konsisten
Setiap halaman menggunakan layout header dan footer yang konsisten, sehingga pengguna tidak kehilangan konteks saat berpindah halaman. Sidebar dan navbar menampilkan halaman aktif dengan indikator visual yang jelas.

### Penggunaan Warna Tema Merah
Sistem menggunakan tema warna merah (#E02424) yang konsisten di seluruh halaman sebagai identitas visual TEFA Polije. Warna ini diterapkan pada tombol utama, header, badge status, dan elemen navigasi aktif.

---

## Peningkatan Manajemen Stok

### Pengurangan Stok Otomatis
Stok produk berkurang secara otomatis ketika pesanan dibuat. Pengurangan dilakukan secara atomik untuk memastikan stok tidak minus, yaitu pesanan hanya berhasil jika stok mencukupi.

### Pengembalian Stok Otomatis
Ketika pesanan dibatalkan oleh pelanggan atau dihapus oleh admin, stok produk dikembalikan secara otomatis sesuai jumlah yang dipesan. Hal ini memastikan ketersediaan stok selalu akurat.

### Indikator Ketersediaan Stok
Produk dengan stok nol tidak dapat dipesan oleh pelanggan. Pada halaman pre-order, produk yang stoknya habis ditandai secara visual agar pelanggan mengetahui ketersediaan tanpa perlu mencoba memesan.

---

## Peningkatan Alur Pesanan

### Kode Pengambilan Pesanan
Setiap pesanan yang dibuat mendapatkan kode pengambilan unik berupa 6 karakter alfanumerik. Kode ini ditampilkan pada bukti pesanan dan digunakan oleh pelanggan saat mengambil pesanan di TEFA.

### Pembatasan Edit dan Batalkan Pesanan
Pesanan hanya dapat diedit atau dibatalkan selama statusnya masih "menunggu" (pending). Setelah pesanan diproses oleh admin, pelanggan tidak dapat lagi mengubah atau membatalkan pesanan tersebut.

### Profil Terkunci Saat Pesanan Aktif
Pelanggan yang memiliki pesanan aktif (status pending atau processing) tidak dapat mengubah nomor telepon pada profilnya. Hal ini untuk mencegah ketidaksesuaian data kontak saat pengiriman notifikasi WhatsApp.
