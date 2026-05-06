# Sprint Planning — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:** Ivan (Backend), Alif Taran Ihsan (Frontend)
**Period:** 25 Februari 2026 – 6 Mei 2026
**Framework:** Scrum (adaptasi untuk tim 2 orang)

---

## Sprint 1: Project Setup & Database (25–26 Feb 2026)

**Sprint Goal:** Inisialisasi repositori, koneksi database, dan konfigurasi dasar project.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Init repositori git | Ivan | High | Done |
| Setup database config (.env, PDO) | Ivan | High | Done |
| Setup README dan dokumentasi dasar | Ivan | Medium | Done |
| Fix image path pada README | Ivan | Low | Done |

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
| Landing page (hero, catalog, batch, SNI, footer) | Ivan | High | Done |
| Fix hero section alignment | Ivan | Medium | Done |
| Fix batch card 3-column layout | Ivan | Medium | Done |
| Fix logo & footer positioning | Ivan | Medium | Done |
| Core infrastructure (functions.php, auth.php) | Ivan | High | Done |
| Auth system (session, guards, CSRF) | Ivan | High | Done |
| Layout system (header/footer admin + customer) | Ivan | High | Done |
| Role-based task documentation | Ivan | Medium | Done |
| Convert images to PHP pages | Alif | Medium | Done |
| Auth login pages (HTML + Tailwind) | Ivan | High | Done |

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
| Sync sidebar/footer/navbar customer | Ivan | Medium | Done |
| Notification status feature | Ivan | Medium | Done |
| Convert HTML files to PHP | Ivan | Medium | Done |
| Logout for customer/admin | Ivan | High | Done |
| Admin panel frontend (all pages) | Alif | High | Done |
| Pengaturan page frontend | Alif | Medium | Done |
| Pre-order feature (backend wiring) | Ivan | High | Done |
| Profile user customer (backend wiring) | Ivan | High | Done |
| FormatHelper & order limit fix | Ivan | Medium | Done |
| Chart.js integration (sparklines) | Ivan | Medium | Done |

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
| Refactor CSS native ke Tailwind | Ivan | High | Done |
| Edit customer/order page | Alif | Medium | Done |
| Sidebar link fix (Windows path) | Ivan | Medium | Done |
| RBAC (super_admin vs teknisi) | Ivan | High | Done |
| Price protection | Ivan | High | Done |
| Core product deletion protection | Ivan | High | Done |
| Admin panel wiring ke DB (4.1–4.7) | Ivan | High | Done |
| Logout fix across all pages | Ivan | Medium | Done |
| SQL escape fix (backslash issue) | Ivan | Medium | Done |

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
| PDF download (DomPDF) | Ivan | High | Done |
| Create order page (admin) | Ivan | High | Done |
| Edit product page (admin) | Ivan | High | Done |
| View order page (admin) | Ivan | High | Done |
| Edit batch page (admin) | Ivan | High | Done |
| Real data chart (replace dummy) | Ivan | Medium | Done |
| Multiple UI bug fixes | Ivan | Medium | Done |
| Frontend optimization & cleanup | Alif | Medium | Done |
| RBAC documentation | Ivan | Low | Done |

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
| OOP refactoring design spec | Ivan | High | Done |
| Refactor all pages to OOP | Ivan | High | Done |
| Fix post-refactor access issues | Ivan | Medium | Done |
| Total profit dashboard feature | Ivan | Medium | Done |

**Commits:**
- `9eade60` docs: add OOP refactoring design spec
- `87c2a57` refactor: all page by using OOP
- `018d792` fix: product, batch and customer cannot be accessed
- `b1bef8e` feat: total profit for admin dashboard

---

## Sprint 7: OOP Completion & Bug Fixes (4–5 Mei 2026)

**Sprint Goal:** Menyelesaikan refactoring OOP, memperbaiki bug dari merge, dan fitur baru.

| Task | Assignee | Priority | Status |
|------|----------|----------|--------|
| Stock management fix | Ivan | High | Done |
| Order CRUD refactor to OOP | Ivan | High | Done |
| Batch/Order/Dashboard OOP refactor | Ivan | High | Done |
| Fix UI and popup CRUD (Alif's merge) | Alif | Medium | Done |
| Fix admin page crash (phone column) | Ivan | Critical | Done |
| Fix create-customer insert to wrong table | Ivan | Critical | Done |
| Dashboard batch filter | Ivan | Medium | Done |
| FonnteService (WhatsApp notifications) | Ivan | Medium | Done |

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

## Velocity Summary

| Sprint | Duration | Commits | Ivan | Alif |
|--------|----------|---------|------|------|
| 1 | 2 days | 4 | 4 | 0 |
| 2 | 1 day | 10 | 9 | 1 |
| 3 | 3 days | 14 | 10 | 4 |
| 4 | 4 days | 9 | 8 | 1 |
| 5 | 2 days | 15 | 14 | 1 |
| 6 | 2 days | 4 | 4 | 0 |
| 7 | 2 days | 10 | 8 | 3 |
| **Total** | **16 days** | **66** | **57** | **10** |
