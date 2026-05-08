# Konsep Desain Navbar dan Sidebar Admin

Berdasarkan implementasi pada `includes/header-admin.php`, berikut adalah konsep utama dan interaksi yang diterapkan pada Navbar dan Sidebar halaman Admin:

## 1. Konsep Sidebar (Menu Navigasi Samping)

- **Collapsible (Dapat Diciutkan):** Sidebar memiliki mode *full-width* (lebar normal) dan *collapsed* (diciutkan menjadi ikon saja). State ini diatur dengan toggle class `.sidebar-collapsed` pada elemen `<body>` dan disimpan di `localStorage` agar persisten.
- **Hover to Expand (Mekar saat Disorot):** Ketika sidebar dalam mode *collapsed*, mengarahkan kursor (*hover*) ke area sidebar akan memunculkannya secara sementara (mekar menjadi 220px) dan menampilkan seluruh teks menu serta submenu, memberikan pengalaman pengguna (UX) yang sangat dinamis dan hemat tempat.
- **Accordion Submenu (Menu Bertingkat):** Submenu seperti "Transaksi" dan "Master Data" menggunakan sistem akordeon. Animasi buka-tutup dikendalikan melalui transisi CSS pada properti `max-height` (dari 0 ke 400px). Fungsi Javascript sederhana (`toggleGroup`) digunakan untuk menambah/menghapus kelas `.open`.
- **Active State Indicator:** Menu yang sedang aktif (sesuai dengan halaman yang sedang dibuka) akan diberikan *highlight* khusus (warna primary kemerahan) agar pengguna tahu posisinya saat ini.

## 2. Konsep Navbar (Top Bar)

- **Sticky Header:** Navbar dibuat tetap berada di atas (`sticky top-0`) saat pengguna melakukan *scroll* ke bawah.
- **Theme Toggle (Mode Gelap/Terang):** Terdapat tombol untuk mengganti tema UI antara *Light Mode* dan *Dark Mode*. Pilihan ini disimpan di `localStorage` sehingga preferensi pengguna tetap tersimpan saat berpindah halaman.
- **Profile Dropdown:** 
  - Menampilkan inisial pengguna (diambil dari nama), nama lengkap, dan *role* (sebagai Administrator).
  - Terdapat menu *dropdown* saat diklik yang berisi jalan pintas ke "Pengaturan Akun" dan tombol untuk "Logout".
  - *(Catatan: Fitur ikon notifikasi/lonceng saat ini dihilangkan/disembunyikan untuk penyederhanaan UI).*

## 3. Teknologi dan Styling
- **Tailwind CSS:** Digunakan untuk *styling* utamanya, menggunakan *utility classes* yang memudahkan responsivitas dan *Dark Mode* (`.dark:`).
- **Phosphor Icons:** Digunakan untuk seluruh ikonografi di sistem (baik pada menu maupun tombol) untuk memberikan kesan desain yang modern dan bersih.
- **Vanilla Javascript:** Interaksi UI seperti membuka/menutup dropdown, toggle sidebar, toggle mode gelap, dan jam *real-time* dibangun murni menggunakan Javascript bawaan tanpa framework tambahan.
