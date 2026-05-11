# Frontend Tech Stack

Dokumen ini merangkum teknologi frontend yang digunakan pada proyek TEFA Canning SIP Legacy. Lingkup dokumen ini hanya mencakup bagian tampilan, layout, styling, aset visual, dan JavaScript di sisi browser.

## Ringkasan

Proyek ini menggunakan pendekatan frontend sederhana berbasis file `.php` sebagai template halaman. Tidak ada framework frontend modern seperti React, Vue, Angular, atau Svelte. UI dibuat langsung dengan HTML, Tailwind CSS via CDN, sedikit CSS kustom inline, dan JavaScript vanilla untuk interaksi ringan.

## Framework dan Library Frontend

| Teknologi | Penggunaan |
| --- | --- |
| HTML dalam file PHP | Struktur markup halaman ditulis langsung di file `.php`. |
| Tailwind CSS | Framework CSS utama untuk styling UI. Dimuat melalui CDN `https://cdn.tailwindcss.com`. |
| JavaScript Vanilla | Digunakan untuk interaksi UI seperti dark mode, sidebar collapse, mobile sidebar, dropdown profil, accordion menu, dan jam real-time. |
| Chart.js | Digunakan untuk grafik/sparkline pada dashboard admin dan customer. Dimuat melalui CDN `https://cdn.jsdelivr.net/npm/chart.js@4`. |
| Phosphor Icons | Library ikon utama. Dimuat melalui CDN `https://unpkg.com/@phosphor-icons/web`. |

## CSS dan Styling

Styling utama menggunakan Tailwind CSS via CDN dengan konfigurasi inline di halaman/layout. Karena Tailwind dimuat dari CDN, proyek ini tidak memiliki proses build frontend seperti Vite, Webpack, PostCSS, atau Tailwind CLI.

Konfigurasi Tailwind memperluas tema dengan font dan warna berikut:

```js
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: { sans: ['Inter', 'sans-serif'] },
      colors: {
        primary: '#E02424',
        accent: '#F05252',
        dark: '#9B1C1C',
        navy: '#111827',
      }
    }
  }
}
```

Warna identitas UI:

| Token | Warna | Penggunaan |
| --- | --- | --- |
| `primary` | `#E02424` | Warna utama merah TEFA, tombol, state aktif, aksen utama. |
| `accent` | `#F05252` | Aksen merah pendukung. |
| `dark` | `#9B1C1C` | Variasi merah gelap. |
| `navy` | `#111827` | Heading dan teks gelap utama. |

Selain utility Tailwind, proyek juga memakai CSS kustom inline di layout untuk beberapa kebutuhan global seperti accordion menu, transisi sidebar, dark mode override, table styling, dan input styling.

## Font

Font utama adalah **Inter** dari Google Fonts.

Font dimuat dari:

```html
https://fonts.googleapis.com/css2?family=Inter...
```

Tailwind dikonfigurasi agar `font-sans` menggunakan `Inter` dengan fallback `sans-serif`.

## Ikon

Ikon menggunakan **Phosphor Icons**. Pemakaiannya terlihat dari class seperti:

```html
<i class="ph-bold ph-house-simple"></i>
<i class="ph ph-list"></i>
<i class="ph-bold ph-shopping-cart-simple"></i>
```

## JavaScript Browser

JavaScript yang digunakan bersifat ringan dan langsung ditulis di file layout atau halaman. Fungsi utamanya meliputi:

- Toggle light/dark mode menggunakan class `dark` pada elemen `<html>` dan penyimpanan preferensi di `localStorage`.
- Collapse sidebar desktop.
- Toggle sidebar mobile dengan backdrop.
- Dropdown profil.
- Accordion/submenu navigasi.
- Jam real-time.
- Sparkline dashboard menggunakan Chart.js.

Tidak ditemukan penggunaan jQuery, Alpine.js, React, Vue, Angular, atau library state management frontend.

## Layout dan Template

Frontend memakai sistem layout berbasis PHP include:

| File | Fungsi |
| --- | --- |
| `includes/header-admin.php` | Layout utama admin: head, sidebar, topbar, konfigurasi Tailwind, font, ikon, dark mode, dan script UI global. |
| `includes/footer-admin.php` | Penutup layout admin. |
| `includes/header-customer.php` | Layout utama customer: head, sidebar, topbar, konfigurasi Tailwind, font, ikon, dark mode, dan script UI global. |
| `includes/footer-customer.php` | Penutup layout customer. |

Halaman admin dan customer mengisi konten di antara include header dan footer. Pola ini menggantikan sistem komponen atau template engine frontend modern.

## Responsiveness

Responsiveness ditangani dengan utility class Tailwind seperti:

```html
grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4
flex-col sm:flex-row
hidden md:flex
overflow-x-auto
```

Layout mendukung sidebar desktop, off-canvas sidebar untuk mobile, tabel horizontal scroll, dan grid responsif untuk card dashboard.

## Dark Mode

Dark mode menggunakan mode class Tailwind:

```js
darkMode: 'class'
```

Preferensi tema disimpan di `localStorage`:

- `admin-theme` untuk area admin.
- `customer-theme` untuk area customer.

Layout juga memiliki CSS override global untuk menyesuaikan background, border, teks, tabel, badge, input, select, dan textarea ketika class `dark` aktif.

## Aset Visual

Aset frontend disimpan di:

```text
assets/images/
```

Contoh aset yang digunakan:

- Logo Politeknik.
- Logo TEFA/BLU.
- Gambar landing page.
- Gambar dashboard admin/customer.
- Gambar halaman login.
- Gambar produk.

Tidak ditemukan pipeline khusus untuk optimasi aset frontend.

## Hal yang Tidak Digunakan

Dalam lingkup frontend proyek saat ini, tidak ditemukan penggunaan:

- React.
- Vue.
- Angular.
- Svelte.
- Bootstrap sebagai framework UI.
- jQuery.
- Alpine.js.
- TypeScript.
- Sass/SCSS.
- Vite.
- Webpack.
- Tailwind CLI build.
- npm/yarn/pnpm frontend workflow.
- SPA routing.

## Referensi File Utama

Teknologi frontend di atas terutama terlihat pada file berikut:

- `index.php`
- `includes/header-admin.php`
- `includes/header-customer.php`
- `auth/login-admin.php`
- `auth/login-customer.php`
- `auth/register.php`
- `auth/forgot-password.php`
- `admin/dashboard.php`
- `customer/dashboard.php`

