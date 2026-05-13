# Daily Scrum — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:**
| Role | Name |
|------|------|
| Project Manager | Rizky |
| System Analyst | Lily |
| UI/UX (Figma) | Alfia |
| Frontend | Alif Taran Ihsan |
| Backend | Irfan |

**Format:** Setiap hari kerja, jawab 3 pertanyaan: (1) Apa yang sudah dikerjakan? (2) Apa yang akan dikerjakan? (3) Ada hambatan?

---

## Sprint 1 — 25–26 Februari 2026

### Day 1 — Selasa, 25 Februari 2026

**Irfan:**
- Done: Init repositori git, setup project structure dasar
- Next: Setup koneksi database, konfigurasi .env
- Blocker: -

**Rizky:**
- Done: Kickoff meeting, define project scope & timeline, sprint 1 planning
- Next: Sprint review setelah backend setup selesai
- Blocker: -

**Lily:**
- Done: Requirements gathering dari stakeholder TEFA, SRS document draft
- Next: Koordinasi kebutuhan data, use case diagram
- Blocker: -

**Alfia:**
- Done: Design system setup di Figma (colors, typography, spacing)
- Next: Landing page mockup
- Blocker: -

### Day 2 — Rabu, 26 Februari 2026

**Irfan:**
- Done: Setup .env, PDO connection, README, fix image path
- Next: Menunggu sprint planning berikutnya (gap ~7 minggu karena jadwal kuliah)
- Blocker: Jadwal kuliah, sprint berikutnya dimulai April

**Rizky:**
- Done: Sprint 1 review, catatan progress tim
- Next: Sprint planning Sprint 2 setelah gap

**Lily:**
- Done: Finalisasi SRS draft, koordinasi data stakeholder
- Next: Use case diagram di sprint berikutnya

---

## Sprint 2 — 18 April 2026

### Day 1 — Sabtu, 18 April 2026

**Irfan:**
- Done: Landing page lengkap (hero, catalog, batch, SNI, footer), fix hero alignment, fix batch card layout, fix logo & footer
- Next: Core infrastructure (functions.php, auth.php, layout system)
- Blocker: -

**Alif:**
- Done: Convert 4 images ke PHP native pages
- Next: Auth login page frontend

**Irfan (malam):**
- Done: Core infrastructure phase 1, auth system phase 2, layout system, role documentation
- Next: Customer panel frontend syncing

**Rizky:**
- Done: Sprint 2 planning & review, task assignment untuk tim
- Next: Koordinasi sprint 3

**Lily:**
- Done: Use case diagram, analisis kebutuhan sistem, bridge ke stakeholder terkait konten
- Next: Analisis data customer panel

**Alfia:**
- Done: Landing page mockup di Figma (hero, catalog, footer)
- Next: Customer panel mockups

---

## Sprint 3 — 19–21 April 2026

### Day 1 — Minggu, 19 April 2026

**Alif:**
- Done: Frontend halaman dashboard customer
- Next: Riwayat pesanan dan profile frontend

**Irfan:**
- Done: Sync sidebar/footer/navbar customer, notification status
- Next: Convert HTML ke PHP, fix DB connection

**Lily:**
- Done: Analisis data kebutuhan customer panel, dokumentasi sistem
- Next: Bridge ke stakeholder terkait fitur customer

**Alfia:**
- Done: Customer panel mockups Figma (dashboard, orders, profile)
- Next: Admin panel mockups

### Day 2 — Senin, 20 April 2026

**Alif:**
- Done: Riwayat pesanan dan profile frontend, edit order page, admin panel frontend
- Next: Pengaturan page

**Irfan:**
- Done: Convert HTML ke PHP, fix DB connection null issue, logout implementasi
- Next: Pre-order feature, profile backend

**Rizky:**
- Done: Koordinasi task frontend-backend, sprint review
- Next: Sprint planning Sprint 4

**Alfia:**
- Done: Admin panel mockups Figma
- Next: Refinement mockups

### Day 3 — Selasa, 21 April 2026

**Alif:**
- Done: Customer-php dan pengaturan.php
- Next: Edit customer/order page

**Irfan:**
- Done: Pre-order feature, profile user, FormatHelper, Chart.js sparklines
- Next: Tailwind migration, RBAC

**Lily:**
- Done: Dokumentasi sistem customer, bridge ke stakeholder
- Next: Analisis kebutuhan RBAC

---

## Sprint 4 — 22–25 April 2026

### Day 1 — Rabu, 22 April 2026

**Irfan:**
- Done: Refactor CSS native ke Tailwind CSS
- Next: RBAC system

**Alif:**
- Done: Edit customer/order page
- Next: -

**Rizky:**
- Done: Sprint review, prioritas bug fixes
- Next: Sprint planning Sprint 5

**Alfia:**
- Done: Dark mode design variants Figma
- Next: Refinement admin panel mockups

### Day 2 — Kamis, 23 April 2026

**Irfan:**
- Done: Sidebar link fix (Windows path issue)
- Next: RBAC implementation

**Lily:**
- Done: Analisis kebutuhan RBAC, dokumentasi sistem keamanan
- Next: Laporan progress ke stakeholder

### Day 3 — Jumat, 24 April 2026

**Irfan:**
- Done: RBAC system (super_admin vs teknisi), price protection, core product deletion protection
- Next: Admin panel wiring ke DB

**Alfia:**
- Done: Refinement admin panel mockups Figma
- Next: PDF template design

### Day 4 — Sabtu, 25 April 2026

**Irfan:**
- Done: Admin panel wiring (semua 7 halaman ke DB), logout fix, SQL escape fix, LICENSE
- Next: PDF generation, halaman CRUD admin yang belum ada

**Lily:**
- Done: Laporan progress ke stakeholder
- Next: Laporan sprint 5

---

## Sprint 5 — 27–28 April 2026

### Day 1 — Minggu, 27 April 2026

**Irfan:**
- Done: PDF download (DomPDF), create order, edit product, view order, edit batch pages, real data chart, multiple UI fixes
- Next: Frontend cleanup, OOP refactoring planning

**Alfia:**
- Done: PDF report template design Figma, icon & button updates
- Next: UI consistency audit

### Day 2 — Senin, 28 April 2026

**Alif:**
- Done: Frontend optimization & cleanup
- Next: -

**Irfan:**
- Done: RBAC documentation, remove view button activity log, sidebar hover fix, OOP refactoring design spec
- Next: OOP refactoring implementation

**Rizky:**
- Done: Sprint review & sprint planning Sprint 6
- Next: Risk assessment refactoring

**Lily:**
- Done: Laporan sprint 5, dokumentasi CRUD workflow, analisis data transaksi
- Next: Review arsitektur OOP

---

## Sprint 6 — 28–29 April 2026

### Day 1 — Senin, 28 April 2026 (malam)

**Irfan:**
- Done: OOP refactoring design spec
- Next: Implementasi OOP (besok pagi)

**Rizky:**
- Done: Sprint review & risk assessment refactoring
- Next: Sprint planning Sprint 7

### Day 2 — Selasa, 29 April 2026

**Irfan:**
- Done: Refactor seluruh halaman ke OOP (Database singleton, BaseService, 9 service classes, SessionGuard interface, AdminGuard, CustomerGuard, Auth facade, CsrfService, FlashMessage, FormatHelper, exception hierarchy)
- Next: Fix post-refactor bugs

**Irfan (siang):**
- Done: Fix product, batch, customer access issues, total profit dashboard
- Next: Stock management fix, OrderAdminService

**Lily:**
- Done: Review arsitektur OOP, dokumentasi sistem, laporan progress sprint 6
- Next: Dokumentasi sistem final

**Alfia:**
- Done: UI consistency audit Figma
- Next: Final polish

---

## Sprint 7 — 4–6 Mei 2026 (Week 12)

### Day 1 — Minggu, 4 Mei 2026

**Irfan:**
- Done: Stock management fix, OrderAdminService class, refactor CRUD order page OOP, refactor Batch/Order/Dashboard
- Next: Menunggu merge dari Alif

**Alfia:**
- Done: Final Figma polish, export assets untuk presentasi
- Next: -

**Lily:**
- Done: Laporan analisis sistem final, dokumentasi lengkap (SRS, DFD, ERD)
- Next: Finalisasi dokumentasi

### Day 2 — Senin, 5 Mei 2026

**Alif:**
- Done: Fix UI and popup CRUD, create create-customer.php, clean up dashboard admin UI
- Blocker: Merge conflict dengan branch Irfan

**Irfan:**
- Done: Fix admin page crash (phone column), restore phone column, fix create-customer insert flow, dashboard batch filter, FonnteService (WhatsApp notifications)
- Blocker: Alif's merge menyebabkan semua admin page error (u.phone column). Fixed dalam sesi yang sama.
- Next: Retrospective & sprint review

**Rizky:**
- Done: Koordinasi merge conflict resolution, sprint review final
- Next: Laporan final project, slide presentasi, retrospective

**Lily:**
- Done: Finalisasi dokumentasi sistem
- Next: Full testing week 13

**Alfia:**
- Done: Finalisasi Figma assets
- Next: Full testing UI/UX week 13

### Day 3 — Selasa, 6 Mei 2026

**Irfan:**
- Done: Sprint retrospective, review semua sprint documentation
- Next: Full testing week 13

**Alif:**
- Done: Sprint retrospective
- Next: Full testing week 13

**Rizky:**
- Done: Retrospective, laporan final project, slide presentasi
- Next: Full testing & stakeholder demo week 13

**Lily:**
- Done: Finalisasi laporan & dokumentasi, retrospective
- Next: Full testing data & requirement validation week 13

**Alfia:**
- Done: Retrospective
- Next: Full testing UI/UX review week 13

---

## Sprint 8 — Bug Fixes, Full Testing & Final Delivery (Week 13 — 10 Mei 2026)

### Day 1 — Sabtu, 10 Mei 2026

**Irfan:**
- Done: Fix batch CSRF token (dual form overwrite), auto increment SKU produk (TEFA-SKU-XXX), tambah tanggal batch di preorder dropdown, dashboard "Semua Batch" data fix, dashboard query LIMIT, FonnteService live tested + parent::__construct() fix
- Next: Full testing backend
- Blocker: -

**Alif:**
- Done: Responsive web semua pages, text alasan produk utama tidak bisa dihapus
- Next: Full testing frontend
- Blocker: -

**Rizky:**
- Done: End-to-end testing fase 1, bug tracking
- Next: Stakeholder demo
- Blocker: -

**Lily:**
- Done: Requirement validation fase 1
- Next: Finalisasi dokumentasi
- Blocker: -

**Alfia:**
- Done: Visual & usability testing fase 1
- Next: Finalisasi Figma assets
- Blocker: -

### Day 2 — Minggu, 11 Mei 2026

**Alif:**
- Done: Fix UI activity log & header admin, fix bug UI landing page & customer orders, clean up & fix hover UI index.php, header code refactor with formatter, frontend tech documentation
- Next: -

**Rizky:**
- Done: Acceptance testing, stakeholder demo preparation
- Next: Project delivery

**Lily:**
- Done: Finalisasi dokumentasi akhir
- Next: -

**Alfia:**
- Done: Finalisasi Figma assets & dokumentasi desain
- Next: -

### Day 3 — Senin, 12 Mei 2026

**Irfan:**
- Done: Full testing backend (CRUD, auth, RBAC, stock, profit, PDF, WhatsApp), database analysis documentation
- Next: Forgot password feature, cleanup

**All:**
- Done: Full testing selesai

---

## Sprint 9 — Feature Completion & Diagrams (Week 14 — 13 Mei 2026)

### Day 1 — Selasa, 13 Mei 2026

**Irfan:**
- Done: Forgot password via WhatsApp OTP (PasswordResetService, FonnteService sendResetCode, reset-password.php page, forgot-password.php wiring), remove Remember Me feature from both login pages, flowchart diagrams (overview + admin + customer + markdown doc), remove scratch directory, update sprint documentation
- Next: Phone validation, code audit optimizations

**Alif:**
- Done: Sprint 9 review
- Next: -

---

## Retrospective Summary

### What went well
- Clear role separation: Rizky (PM), Lily (SA), Alfia (UI/UX), Irfan (backend), Alif (frontend)
- CLAUDE.md documentation helped onboard Alif tanpa verbal communication panjang
- OOP refactoring improved code maintainability significantly
- Git commit history well-documented
- Figma design system konsisten dari awal sampai akhir
- Sprint 8 bug fixes selesai cepat berkat dokumentasi yang baik
- Forgot password feature selesai 1 hari penuh termasuk testing

### What could be improved
- Gap 7 minggu antara Sprint 1 dan 2 (jadwal kuliah)
- Merge conflict pada Sprint 7 — bisa dicegah dengan komunikasi lebih baik
- Alif perlu menjalankan `php artisan migrate` sebelum mulai coding
- Beberapa commit tidak mengikuti conventional commit format
- Alif push file scratch (Python) tanpa review — harus selalu review sebelum push
- Phone validation belum diimplementasi di register — perlu ditambahkan

### Action items for future
- Selalu pull & migrate sebelum mulai coding
- Konsisten gunakan conventional commit format
- Komunikasi aktif ketika ada perubahan schema database
- Sinkronisasi antar role (PM, SA, UI/UX, dev) lebih sering di luar sprint
- Selalu review code sebelum push ke repository
- Validasi input format (phone, email) di semua form
