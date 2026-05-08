# Sprint Planning — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:**
| Role | Name |
|------|------|
| Project Manager | Rizky |
| System Analyst | Lily |
| UI/UX (Figma) | Alfia |
| Frontend | Alif Taran Ihsan |
| Backend | Irfan |

**Period:** 25 Februari 2026 – 13 Mei 2026
**Framework:** Scrum (adaptasi untuk tim 5 orang)

---

## Sprint 1: Project Setup & Database (25–26 Feb 2026)

**Sprint Goal:** Inisialisasi repositori, koneksi database, dan konfigurasi dasar project.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Init repositori git | Irfan | High | Done |
| Setup database config (.env, PDO) | Irfan | High | Done |
| Setup README dan dokumentasi dasar | Irfan | Medium | Done |
| Fix image path pada README | Irfan | Low | Done |
| Project kickoff meeting, define scope & timeline | Rizky | High | Done |
| Requirements gathering, SRS document draft | Lily | High | Done |
| Koordinasi kebutuhan data dari stakeholder TEFA | Lily | Medium | Done |
| Design system setup di Figma (colors, typography, spacing) | Alfia | Medium | Done |

**Commits:**
- `fbea7df` first commit
- `2c487b5` add database config and setup readme file
- `c4cb4ac` fix readme image not loaded
- `a006e18` connection database config

---

## Sprint 2: Landing Page & Core Infrastructure (18 Apr 2026)

**Sprint Goal:** Membangun landing page dan core infrastructure (auth, session, CSRF, layout system).

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Landing page (hero, catalog, batch, SNI, footer) | Irfan | High | Done |
| Fix hero section alignment | Irfan | Medium | Done |
| Fix batch card 3-column layout | Irfan | Medium | Done |
| Fix logo & footer positioning | Irfan | Medium | Done |
| Core infrastructure (functions.php, auth.php) | Irfan | High | Done |
| Auth system (session, guards, CSRF) | Irfan | High | Done |
| Layout system (header/footer admin + customer) | Irfan | High | Done |
| Role-based task documentation | Irfan | Medium | Done |
| Convert images to PHP pages | Alif | Medium | Done |
| Auth login pages (HTML + Tailwind) | Irfan | High | Done |
| Sprint planning & review Sprint 2 | Rizky | High | Done |
| Analisis kebutuhan sistem, use case diagram | Lily | High | Done |
| Bridge ke stakeholder terkait konten landing page | Lily | Medium | Done |
| Landing page mockup di Figma (hero, catalog, footer) | Alfia | High | Done |

**Commits:**
- `2da7123` feat: landing page
- `2e9df52` fix: hero section design
- `8ed9836` fix: batch produksi card 3-column
- `b555fb9` fix: polije logo positioning
- `ed54dd1` fix: map location and footer logo
- `03075be` feat: core infrastructure (phase 1)
- `bd2e5cf` feat: core infrastructure, add docs
- `b0a9ae0` feat: authentication (phase 2)
- `88316ab` feat: update role based for teamwork
- `4f5576f` (Alif) added for 4 images convert to page php native
- `c806ffc` feat: auth login and includes component html

---

## Sprint 3: Customer Panel — Frontend & Backend (19–21 Apr 2026)

**Sprint Goal:** Menyelesaikan seluruh halaman customer (dashboard, preorder, orders, profile).

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Customer dashboard frontend | Alif | High | Done |
| Customer riwayat pesanan frontend | Alif | High | Done |
| Customer profile frontend | Alif | High | Done |
| Edit order page frontend | Alif | Medium | Done |
| Sync sidebar/footer/navbar customer | Irfan | Medium | Done |
| Notification status feature | Irfan | Medium | Done |
| Convert HTML files to PHP | Irfan | Medium | Done |
| Logout for customer/admin | Irfan | High | Done |
| Admin panel frontend (all pages) | Alif | High | Done |
| Pengaturan page frontend | Alif | Medium | Done |
| Pre-order feature (backend wiring) | Irfan | High | Done |
| Profile user customer (backend wiring) | Irfan | High | Done |
| FormatHelper & order limit fix | Irfan | Medium | Done |
| Chart.js integration (sparklines) | Irfan | Medium | Done |
| Koordinasi task frontend-backend, sprint review | Rizky | High | Done |
| Analisis data kebutuhan customer panel, dokumentasi sistem | Lily | High | Done |
| Bridge ke stakeholder terkait fitur customer | Lily | Medium | Done |
| Customer panel mockups di Figma (dashboard, orders, profile) | Alfia | High | Done |
| Admin panel mockups di Figma | Alfia | High | Done |

**Commits:**
- `cecfa44` (Alif) membuat frontend halaman dashboard customer
- `1e95c3f` (Alif) menambahkan customer frontend riwayat pesanan dan profile
- `41089e8` (Alif) create file edit-order.php
- `14a209a` (Alif) added frontend at admin folder
- `6ec8f1e` fix: sync sidebar/footer/navbar customer
- `43b73e6` feat: notification status
- `ae39da3` fix: sync sidebar and convert HTML to PHP
- `bccd6b5` fix: db connection return null
- `eb44201` add: logout for customer/admin
- `a60a2a0` add: real data to the database
- `8009ca9` (Alif) added customer-php and pengaturan.php
- `16a1a62` add: pre-order feature for customer
- `174dc2a` add: profile user for customer
- `505a080` fix: format rupiah and order limit
- `0f79a75` add: chart feature replacing placeholder

---

## Sprint 4: Tailwind Migration & Admin Panel Wiring (22–25 Apr 2026)

**Sprint Goal:** Migrasi CSS native ke Tailwind dan menyambungkan semua halaman admin ke database.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Refactor CSS native ke Tailwind | Irfan | High | Done |
| Edit customer/order page | Alif | Medium | Done |
| Sidebar link fix (Windows path) | Irfan | Medium | Done |
| RBAC (super_admin vs teknisi) | Irfan | High | Done |
| Price protection | Irfan | High | Done |
| Core product deletion protection | Irfan | High | Done |
| Admin panel wiring ke DB (4.1–4.7) | Irfan | High | Done |
| Logout fix across all pages | Irfan | Medium | Done |
| SQL escape fix (backslash issue) | Irfan | Medium | Done |
| Sprint review & prioritization bug fixes | Rizky | High | Done |
| Analisis kebutuhan RBAC, dokumentasi sistem keamanan | Lily | High | Done |
| Laporan progress ke stakeholder | Lily | Medium | Done |
| Dark mode design variants di Figma | Alfia | Medium | Done |
| Refinement admin panel mockups | Alfia | Medium | Done |

**Commits:**
- `91de9da` refactor: from css native to tailwindcss
- `5975e7f` (Alif) add edit customer/order page
- `ae2db18` fix: sidebar button link issue
- `e21f037` feat: RBAC multi role user
- `98a9f35` feat: price protection (7.3)
- `3d852cf` feat: product protection for 3 SKU
- `5cf9e4c` feat: admin panel wiring (4.1-4.7)
- `5263fbe` fix: logout and missing function.php includes
- `ef2db09` fix: pages permission issue (backslash)

---

## Sprint 5: PDF, Admin CRUD Pages & Bug Fixes (27–28 Apr 2026)

**Sprint Goal:** Implementasi PDF generation, halaman CRUD admin yang belum ada, dan perbaikan bug UI.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| PDF download (DomPDF) | Irfan | High | Done |
| Create order page (admin) | Irfan | High | Done |
| Edit product page (admin) | Irfan | High | Done |
| View order page (admin) | Irfan | High | Done |
| Edit batch page (admin) | Irfan | High | Done |
| Real data chart (replace dummy) | Irfan | Medium | Done |
| Multiple UI bug fixes | Irfan | Medium | Done |
| Frontend optimization & cleanup | Alif | Medium | Done |
| RBAC documentation | Irfan | Low | Done |
| Sprint review & sprint planning Sprint 6 | Rizky | High | Done |
| Laporan sprint 5, dokumentasi CRUD workflow | Lily | High | Done |
| Analisis data transaksi untuk laporan | Lily | Medium | Done |
| PDF report template design di Figma | Alfia | High | Done |
| Icon & button updates di Figma | Alfia | Low | Done |

**Commits:**
- `94c04dc` feat: PDF download on customer and admin
- `1e8b96f` feat: create order for admin
- `3c59614` feat: create edit product for admin
- `d8b561a` feat: create view order for admin
- `91ce1d9` feat: edit batch for admin
- `cb90d6a` replace: dummy chart to real data chart
- `1e7b186`, `f033d2d`, `aea5203`, `d3e015b`, `9552fdc` multiple UI fixes
- `04ac5ee` (Alif) optimize frontend cleanup
- `079a141` docs: add rbac role system documentation

---

## Sprint 6: OOP Refactoring (28–29 Apr 2026)

**Sprint Goal:** Refaktor seluruh kode procedural menjadi OOP (classes, services, interfaces, exceptions).

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| OOP refactoring design spec | Irfan | High | Done |
| Refactor all pages to OOP | Irfan | High | Done |
| Fix post-refactor access issues | Irfan | Medium | Done |
| Total profit dashboard feature | Irfan | Medium | Done |
| Sprint review & risk assessment refactoring | Rizky | High | Done |
| Review arsitektur OOP, dokumentasi sistem | Lily | High | Done |
| Laporan progress sprint 6 | Lily | Medium | Done |
| UI consistency audit di Figma | Alfia | Medium | Done |

**Commits:**
- `9eade60` docs: add OOP refactoring design spec
- `87c2a57` refactor: all page by using OOP
- `018d792` fix: product, batch and customer cannot be accessed
- `b1bef8e` feat: total profit for admin dashboard

---

## Sprint 7: OOP Completion, Bug Fixes & Retrospective (4–6 Mei 2026)

**Sprint Goal:** Menyelesaikan refactoring OOP, memperbaiki bug dari merge, fitur baru, dan retrospective.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Stock management fix | Irfan | High | Done |
| Order CRUD refactor to OOP | Irfan | High | Done |
| Batch/Order/Dashboard OOP refactor | Irfan | High | Done |
| Fix UI and popup CRUD (Alif's merge) | Alif | Medium | Done |
| Fix admin page crash (phone column) | Irfan | Critical | Done |
| Fix create-customer insert to wrong table | Irfan | Critical | Done |
| Dashboard batch filter | Irfan | Medium | Done |
| FonnteService (WhatsApp notifications) | Irfan | Medium | Done |
| Koordinasi merge conflict resolution | Rizky | Critical | Done |
| Sprint review final, retrospective | Rizky | High | Done |
| Laporan final project, slide presentasi | Rizky | High | Done |
| Laporan analisis sistem final | Lily | High | Done |
| Dokumentasi sistem lengkap (SRS, DFD, ERD) | Lily | High | Done |
| Final Figma polish, export assets presentasi | Alfia | High | Done |

**Commits:**
- `667661e` fix: method signature, profit calculation, stock management
- `1532e28` refactor: CRUD page order with OOP
- `ec51e98` refactor: some code to use OOP
- `5d02e27` (Alif) fix UI and popup CRUD
- `704cc52` (Alif) create create-customer.php
- `5865b43` (Alif) clean up and fix UI in dashboard admin
- `4d58cd9` fix: admin page crash (phone column)
- `37e9b73` fix: return phone column (was migration issue)
- `29849b7` fix: insert to customers table instead of users
- `1d7e1a2` feat: dashboard stats view based on batch

---

## Sprint 8: Bug Fixes, Full Testing & Final Delivery (Week 13 — Mei 2026)

**Sprint Goal:** Perbaikan bug remaining, full testing seluruh fitur oleh semua role, dan persiapan delivery.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Fix batch CSRF token issue semua role | Irfan | Critical | Pending |
| Auto increment SKU produk | Irfan | High | Pending |
| Tambah tanggal batch info saat customer buat order | Irfan | High | Pending |
| Full testing backend (CRUD, auth, RBAC, stock, profit, PDF, WhatsApp) | Irfan | High | Pending |
| Responsive web semua pages | Alif | High | Pending |
| Text alasan jelas kenapa produk utama tidak bisa dihapus | Alif | Medium | Pending |
| Full testing frontend (responsive, form validation, dark mode) | Alif | High | Pending |
| Full end-to-end flow testing & stakeholder demo | Rizky | High | Pending |
| Bug tracking & koordinasi perbaikan | Rizky | High | Pending |
| Verifikasi semua requirement terpenuhi | Lily | High | Pending |
| Finalisasi dokumentasi akhir | Lily | High | Pending |
| Visual & usability testing semua halaman | Alfia | High | Pending |
| Finalisasi Figma assets & dokumentasi desain | Alfia | High | Pending |

---

## Velocity Summary

| Sprint | Duration | Commits | Irfan | Alif | Rizky | Lily | Alfia |
|--------|----------|---------|-------|------|-------|------|-------|
| 1 | 2 days | 4 | 4 | 0 | 1 | 2 | 1 |
| 2 | 1 day | 10 | 9 | 1 | 1 | 2 | 1 |
| 3 | 3 days | 14 | 10 | 4 | 1 | 2 | 2 |
| 4 | 4 days | 9 | 8 | 1 | 1 | 2 | 2 |
| 5 | 2 days | 15 | 14 | 1 | 1 | 2 | 2 |
| 6 | 2 days | 4 | 4 | 0 | 1 | 2 | 1 |
| 7 | 3 days | 10 | 8 | 3 | 3 | 2 | 1 |
| 8 | 1 week | - | 4 | 3 | 2 | 2 | 2 |
| **Total** | **~4 weeks** | **66** | **61** | **13** | **13** | **18** | **14** |
