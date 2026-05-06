# Daily Scrum — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:** Ivan (Backend), Alif Taran Ihsan (Frontend)
**Format:** Setiap hari kerja, jawab 3 pertanyaan: (1) Apa yang sudah dikerjakan? (2) Apa yang akan dikerjakan? (3) Ada hambatan?

---

## Sprint 1 — 25–26 Februari 2026

### Day 1 — Selasa, 25 Februari 2026

**Ivan:**
- Done: Init repositori git, setup project structure dasar
- Next: Setup koneksi database, konfigurasi .env
- Blocker: -

### Day 2 — Rabu, 26 Februari 2026

**Ivan:**
- Done: Setup .env, PDO connection, README, fix image path
- Next: Menunggu sprint planning berikutnya (gap ~7 minggu karena jadwal kuliah)
- Blocker: Jadwal kuliah, sprint berikutnya dimulai April

---

## Sprint 2 — 18 April 2026

### Day 1 — Sabtu, 18 April 2026

**Ivan:**
- Done: Landing page lengkap (hero, catalog, batch, SNI, footer), fix hero alignment, fix batch card layout, fix logo & footer
- Next: Core infrastructure (functions.php, auth.php, layout system)
- Blocker: -

**Alif:**
- Done: Convert 4 images ke PHP native pages
- Next: Auth login page frontend

**Ivan (malam):**
- Done: Core infrastructure phase 1, auth system phase 2, layout system, role documentation
- Next: Customer panel frontend syncing

---

## Sprint 3 — 19–21 April 2026

### Day 1 — Minggu, 19 April 2026

**Alif:**
- Done: Frontend halaman dashboard customer
- Next: Riwayat pesanan dan profile frontend

**Ivan:**
- Done: Sync sidebar/footer/navbar customer, notification status
- Next: Convert HTML ke PHP, fix DB connection

### Day 2 — Senin, 20 April 2026

**Alif:**
- Done: Riwayat pesanan dan profile frontend, edit order page, admin panel frontend
- Next: Pengaturan page

**Ivan:**
- Done: Convert HTML ke PHP, fix DB connection null issue, logout implementasi
- Next: Pre-order feature, profile backend

### Day 3 — Selasa, 21 April 2026

**Alif:**
- Done: Customer-php dan pengaturan.php
- Next: Edit customer/order page

**Ivan:**
- Done: Pre-order feature, profile user, FormatHelper, Chart.js sparklines
- Next: Tailwind migration, RBAC

---

## Sprint 4 — 22–25 April 2026

### Day 1 — Rabu, 22 April 2026

**Ivan:**
- Done: Refactor CSS native ke Tailwind CSS
- Next: RBAC system

**Alif:**
- Done: Edit customer/order page
- Next: -

### Day 2 — Kamis, 23 April 2026

**Ivan:**
- Done: Sidebar link fix (Windows path issue)
- Next: RBAC implementation

### Day 3 — Jumat, 24 April 2026

**Ivan:**
- Done: RBAC system (super_admin vs teknisi), price protection, core product deletion protection
- Next: Admin panel wiring ke DB

### Day 4 — Sabtu, 25 April 2026

**Ivan:**
- Done: Admin panel wiring (semua 7 halaman ke DB), logout fix, SQL escape fix, LICENSE
- Next: PDF generation, halaman CRUD admin yang belum ada

---

## Sprint 5 — 27–28 April 2026

### Day 1 — Minggu, 27 April 2026

**Ivan:**
- Done: PDF download (DomPDF), create order, edit product, view order, edit batch pages, real data chart, multiple UI fixes
- Next: Frontend cleanup, OOP refactoring planning

### Day 2 — Senin, 28 April 2026

**Alif:**
- Done: Frontend optimization & cleanup
- Next: -

**Ivan:**
- Done: RBAC documentation, remove view button activity log, sidebar hover fix, OOP refactoring design spec
- Next: OOP refactoring implementation

---

## Sprint 6 — 28–29 April 2026

### Day 1 — Senin, 28 April 2026 (malam)

**Ivan:**
- Done: OOP refactoring design spec
- Next: Implementasi OOP (besok pagi)

### Day 2 — Selasa, 29 April 2026

**Ivan:**
- Done: Refactor seluruh halaman ke OOP (Database singleton, BaseService, 9 service classes, SessionGuard interface, AdminGuard, CustomerGuard, Auth facade, CsrfService, FlashMessage, FormatHelper, exception hierarchy)
- Next: Fix post-refactor bugs

**Ivan (siang):**
- Done: Fix product, batch, customer access issues, total profit dashboard
- Next: Stock management fix, OrderAdminService

---

## Sprint 7 — 4–5 Mei 2026

### Day 1 — Minggu, 4 Mei 2026

**Ivan:**
- Done: Stock management fix, OrderAdminService class, refactor CRUD order page OOP, refactor Batch/Order/Dashboard
- Next: Menunggu merge dari Alif

### Day 2 — Senin, 5 Mei 2026

**Alif:**
- Done: Fix UI and popup CRUD, create create-customer.php, clean up dashboard admin UI
- Blocker: Merge conflict dengan branch Ivan

**Ivan:**
- Done: Fix admin page crash (phone column), restore phone column, fix create-customer insert flow, dashboard batch filter
- Blocker: Alif's merge menyebabkan semua admin page error (u.phone column). Fixed dalam sesi yang sama.
- Next: FonnteService (WhatsApp notifications)

---

## Retrospective Summary

### What went well
- Clear role separation: Ivan (backend) + Alif (frontend) workflow efektif
- CLAUDE.md documentation helped onboard Alif tanpa verbal communication panjang
- OOP refactoring improved code maintainability significantly
- Git commit history well-documented

### What could be improved
- Gap 7 minggu antara Sprint 1 dan 2 (jadwal kuliah)
- Merge conflict pada Sprint 7 — bisa dicegah dengan komunikasi lebih baik
- Alif perlu menjalankan `php artisan migrate` sebelum mulai coding
- Beberapa commit tidak mengikuti conventional commit format

### Action items for future
- Selalu pull & migrate sebelum mulai coding
- Konsisten gunakan conventional commit format
- Komunikasi aktif ketika ada perubahan schema database
