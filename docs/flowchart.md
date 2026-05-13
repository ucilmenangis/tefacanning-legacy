# Flowchart Sistem TEFA Canning SIP

## Flowchart Overview (Ringkas)

Gambaran umum alur sistem dalam 1 diagram.

```mermaid
flowchart TD
    START([Mulai]) --> LANDING[Landing Page]
    LANDING --> LOGIN{Login sebagai?}

    LOGIN -->|Admin| ADMIN[Dashboard Admin]
    LOGIN -->|Pelanggan| CUST[Dashboard Pelanggan]

    ADMIN --> KELOLA[Kelola Produk dan Batch]
    KELOLA --> SIAP[Status Batch Siap]
    SIAP --> NOTIF1[Kirim Notifikasi WA]

    CUST --> ORDER[Buat Pre-order]
    ORDER --> STOK{Stok Tersedia?}
    STOK -->|Ya| SIMPAN[Simpan Pesanan]
    STOK -->|Tidak| ORDER
    SIMPAN --> NOTIF2[Kirim Notifikasi WA ke Admin]

    NOTIF1 --> AMBIL[Pelanggan Ambil Pesanan]
    AMBIL --> SELESAI([Selesai])
    NOTIF2 --> SELESAI
```

### Keterangan Simbol

| Simbol | Bentuk | Keterangan |
|--------|--------|------------|
| Mulai / Selesai | Oval `( [...])` | Terminator awal dan akhir |
| Dashboard / Landing Page | Persegi `[ ... ]` | Proses sistem |
| Login sebagai? / Stok Tersedia? | Belah ketupat `{ ... }` | Keputusan / percabangan |

---

## Flowchart Detail — Admin / Teknisi

```mermaid
flowchart TD
    START([Mulai]) --> LANDING[Tampilkan Landing Page]
    LANDING --> LOGIN_ADMIN[/Form Login Admin/]
    LOGIN_ADMIN --> AUTH_ADMIN{Autentikasi Valid?}
    AUTH_ADMIN -->|Tidak| LOGIN_ADMIN
    AUTH_ADMIN -->|Ya| CEK_ROLE{Cek Role}

    CEK_ROLE -->|Super Admin| DASHBOARD_ADMIN[Dashboard Super Admin]
    CEK_ROLE -->|Teknisi| DASHBOARD_TEKNISI[Dashboard Teknisi]

    DASHBOARD_ADMIN --> KELOLA_ADMIN[Kelola Produk, Batch, Pesanan, Pelanggan]
    DASHBOARD_TEKNISI --> KELOLA_TEKNISI[Kelola Batch dan Pesanan]

    KELOLA_ADMIN --> UBAH_STATUS[Ubah Status Batch menjadi Siap]
    KELOLA_TEKNISI --> UBAH_STATUS
    UBAH_STATUS --> NOTIF_SIAP[Kirim Notifikasi WA ke Pelanggan]
    NOTIF_SIAP --> AMBIL[Pelanggan Mengambil Pesanan]
    AMBIL --> SELESAI([Selesai])
```

### Keterangan Simbol

| Simbol | Bentuk | Keterangan |
|--------|--------|------------|
| Mulai / Selesai | Oval `( [...])` | Terminator awal dan akhir |
| Tampilkan Landing Page | Persegi `[ ... ]` | Proses sistem |
| Form Login Admin | Jajar genjang `[/ ... /]` | Input / Output dari pengguna |
| Autentikasi Valid? / Cek Role | Belah ketupat `{ ... }` | Keputusan / percabangan |

## Flowchart Detail — Pelanggan

```mermaid
flowchart TD
    START([Mulai]) --> LANDING[Tampilkan Landing Page]
    LANDING --> PILIH_AKSI{Sudah Punya Akun?}

    PILIH_AKSI -->|Belum| REGISTER[/Form Registrasi/]
    REGISTER --> REG_VALID{Data Valid?}
    REG_VALID -->|Tidak| REGISTER
    REG_VALID -->|Ya| LOGIN_CUSTOMER[/Form Login Pelanggan/]

    PILIH_AKSI -->|Sudah| LOGIN_CUSTOMER
    LOGIN_CUSTOMER --> AUTH_CUST{Autentikasi Valid?}
    AUTH_CUST -->|Tidak| LOGIN_CUSTOMER
    AUTH_CUST -->|Ya| DASHBOARD_CUST[Dashboard Pelanggan]

    DASHBOARD_CUST --> BUAT_ORDER[Buat Pre-order]
    BUAT_ORDER --> PILIH_BATCH[Pilih Batch dan Produk]
    PILIH_BATCH --> QTY_VALID{Stok Tersedia?}
    QTY_VALID -->|Tidak| PILIH_BATCH
    QTY_VALID -->|Ya| SIMPAN_ORDER[Simpan Pesanan]
    SIMPAN_ORDER --> KURANGI_STOK[Kurangi Stok Produk]
    KURANGI_STOK --> NOTIF_ORDER[Kirim Notifikasi WA ke Admin]

    DASHBOARD_CUST --> LIHAT_ORDER[Lihat Riwayat Pesanan]
    LIHAT_ORDER --> UNDUH_PDF[Unduh PDF Pesanan]

    NOTIF_ORDER --> SELESAI([Selesai])
```

### Keterangan Simbol

| Simbol | Bentuk | Keterangan |
|--------|--------|------------|
| Mulai / Selesai | Oval `( [...])` | Terminator awal dan akhir |
| Dashboard Pelanggan | Persegi `[ ... ]` | Proses sistem |
| Form Login / Registrasi | Jajar genjang `[/ ... /]` | Input / Output dari pengguna |
| Stok Tersedia? / Data Valid? | Belah ketupat `{ ... }` | Keputusan / percabangan |
| Simpan Pesanan | Persegi `[ ... ]` | Proses penyimpanan data |

## File Diagram

| File | Keterangan |
|------|------------|
| `docs/diagrams/flowchart-admin.mmd` | Flowchart Admin/Teknisi (standalone Mermaid) |
| `docs/diagrams/flowchart-customer.mmd` | Flowchart Pelanggan (standalone Mermaid) |
| `docs/diagrams/flowchart-sistem.mmd` | Flowchart gabungan (referensi lama) |
