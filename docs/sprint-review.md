# Sprint Review — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:**
| Role | Name |
|------|------|
| Project Manager | Rizky |
| System Analyst | Lily |
| UI/UX (Figma) | Alfia |
| Frontend | Alif Taran Ihsan |
| Backend | Irfan |

**Period:** 25 Februari 2026 – 6 Mei 2026 (Sprint 1–7)
**Framework:** Scrum (adaptasi untuk tim 5 orang)

---

## Sprint 1 — Project Setup & Database (25–26 Feb 2026)

**Sprint Goal:** Inisialisasi repositori, koneksi database, dan konfigurasi dasar project.

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | Git repositori terinisialisasi | Irfan | Done |
| 2 | Database config (.env + PDO connection) | Irfan | Done |
| 3 | README documentation | Irfan | Done |
| 4 | Fix image path README | Irfan | Done |
| 5 | Project kickoff meeting, scope & timeline | Rizky | Done |
| 6 | SRS document draft | Lily | Done |
| 7 | Stakeholder data requirements | Lily | Done |
| 8 | Figma design system setup | Alfia | Done |

### Sprint Goal Achievement
**100%** — Semua deliverable selesai. Repositori ready, database connected, documentation complete, design system defined.

### Product Backlog Update
- Backlog item "Setup koneksi PDO MySQL" → Done, dipindah ke completed
- Item baru ditambahkan: "Landing page development" untuk Sprint 2

### Demo Highlights
- Live demo: `php -S localhost:8000` berhasil dijalankan
- Database connection verified via PDO
- Figma design system: color palette (#E02424 primary, Inter font, spacing system)

### Stakeholder Feedback
- Stakeholder menyepakati scope project: landing page + admin panel + customer panel + WhatsApp notifications
- Timeline disesuaikan dengan jadwal kuliah (Sprint 2 dimulai April)

---

## Sprint 2 — Landing Page & Core Infrastructure (18 Apr 2026)

**Sprint Goal:** Membangun landing page dan core infrastructure (auth, session, CSRF, layout system).

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | Landing page (hero, catalog, batch, SNI, footer) | Irfan | Done |
| 2 | Core infrastructure (functions.php, auth.php) | Irfan | Done |
| 3 | Auth system (session, guards, CSRF) | Irfan | Done |
| 4 | Layout system (header/footer admin + customer) | Irfan | Done |
| 5 | Auth login pages (HTML + Tailwind) | Irfan | Done |
| 6 | Convert images to PHP pages | Alif | Done |
| 7 | Sprint 2 planning & review | Rizky | Done |
| 8 | Use case diagram | Lily | Done |
| 9 | Stakeholder bridge (landing page content) | Lily | Done |
| 10 | Landing page Figma mockup | Alfia | Done |

### Sprint Goal Achievement
**100%** — Landing page live dengan dynamic data. Auth system berfungsi. Layout system siap untuk halaman admin & customer.

### Product Backlog Update
- 10 backlog items completed
- Prioritas berikutnya: Customer panel frontend & backend wiring

### Demo Highlights
- Landing page menampilkan produk & batch dari database (dynamic)
- Hero section responsive, batch card 3-column layout
- Auth login admin & customer berfungsi dengan session management
- CSRF protection aktif di semua form
- Figma mockup landing page konsisten dengan implementasi

### Stakeholder Feedback
- Landing page design disetujui, warna merah (#E02424) sesuai branding TEFA
- Konten produk dan batch perlu di-update berkala oleh admin

---

## Sprint 3 — Customer Panel Frontend & Backend (19–21 Apr 2026)

**Sprint Goal:** Menyelesaikan seluruh halaman customer (dashboard, preorder, orders, profile).

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | Customer dashboard frontend | Alif | Done |
| 2 | Customer riwayat pesanan frontend | Alif | Done |
| 3 | Customer profile frontend | Alif | Done |
| 4 | Edit order page frontend | Alif | Done |
| 5 | Admin panel frontend (all pages) | Alif | Done |
| 6 | Pengaturan page frontend | Alif | Done |
| 7 | Pre-order feature (backend wiring) | Irfan | Done |
| 8 | Profile user customer (backend wiring) | Irfan | Done |
| 9 | Notification status, logout, Chart.js sparklines | Irfan | Done |
| 10 | Koordinasi frontend-backend | Rizky | Done |
| 11 | Customer panel documentation & stakeholder bridge | Lily | Done |
| 12 | Customer & admin panel Figma mockups | Alfia | Done |

### Sprint Goal Achievement
**100%** — Customer panel lengkap: dashboard, pre-order, riwayat pesanan, edit order, profile. Admin panel frontend selesai. Backend wiring untuk customer panel selesai.

### Product Backlog Update
- 14 commits (10 Irfan, 4 Alif) — sprint paling banyak commit
- Backlog diupdate: admin panel wiring menjadi prioritas Sprint 4

### Demo Highlights
- Customer dashboard menampilkan stats, batch aktif, produk, sparkline charts
- Pre-order form: pilih produk → submit → order tercatat di database
- Riwayat pesanan: list, search, cancel pending orders
- Profile: edit nama, email, password
- Figma mockups customer & admin panel konsisten

### Stakeholder Feedback
- Fitur pre-order sesuai kebutuhan operasional TEFA
- Admin panel perlu segera di-wiring ke database untuk testing end-to-end

---

## Sprint 4 — Tailwind Migration & Admin Panel Wiring (22–25 Apr 2026)

**Sprint Goal:** Migrasi CSS native ke Tailwind dan menyambungkan semua halaman admin ke database.

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | CSS native → Tailwind CDN migration | Irfan | Done |
| 2 | RBAC system (super_admin vs teknisi) | Irfan | Done |
| 3 | Price protection (teknisi tidak bisa edit harga) | Irfan | Done |
| 4 | Core product deletion protection (3 SKU) | Irfan | Done |
| 5 | Admin panel wiring ke DB (7 halaman) | Irfan | Done |
| 6 | Edit customer/order page | Alif | Done |
| 7 | Sprint review & bug prioritization | Rizky | Done |
| 8 | RBAC documentation & stakeholder report | Lily | Done |
| 9 | Dark mode Figma variants | Alfia | Done |

### Sprint Goal Achievement
**100%** — Tailwind migration sukses. RBAC system aktif. Semua 7 halaman admin terhubung ke database. Security features (price protection, deletion protection) implemented.

### Product Backlog Update
- Admin panel wiring (4.1–4.7) all done
- Remaining: PDF generation, CRUD pages yang belum ada (create order, edit product, view order, edit batch)

### Demo Highlights
- Admin dashboard: real data dari database (stats, products, batches, customers, orders)
- RBAC demo: login sebagai teknisi → harga disabled, audit log hidden
- Price protection: teknisi tidak bisa mengubah harga produk
- Core product deletion: 3 SKU utama tidak bisa dihapus
- Dark mode design variants di Figma

### Stakeholder Feedback
- RBAC system sesuai kebutuhan — teknisi hanya operasional, super_admin full akses
- Tailwind migration meningkatkan konsistensi UI

---

## Sprint 5 — PDF, CRUD Pages & Bug Fixes (27–28 Apr 2026)

**Sprint Goal:** Implementasi PDF generation, halaman CRUD admin yang belum ada, dan perbaikan bug UI.

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | PDF download (DomPDF) — customer & admin | Irfan | Done |
| 2 | Create order page (admin) | Irfan | Done |
| 3 | Edit product page (admin) | Irfan | Done |
| 4 | View order page (admin) | Irfan | Done |
| 5 | Edit batch page (admin) | Irfan | Done |
| 6 | Real data chart (replace dummy) | Irfan | Done |
| 7 | Multiple UI bug fixes (5 commits) | Irfan | Done |
| 8 | Frontend optimization & cleanup | Alif | Done |
| 9 | RBAC documentation | Irfan | Done |
| 10 | Sprint review & planning | Rizky | Done |
| 11 | CRUD workflow documentation, data analysis | Lily | Done |
| 12 | PDF report template Figma design | Alfia | Done |

### Sprint Goal Achievement
**100%** — PDF generation berfungsi. 4 halaman CRUD admin baru selesai. Real data charts. 15 commits — sprint paling produktif.

### Product Backlog Update
- Semua halaman CRUD admin selesai
- Remaining: OOP refactoring (procedural → OOP classes)

### Demo Highlights
- PDF download: admin dan customer bisa download order report (A4, branded)
- Create order: pilih customer, batch, produk → hitung subtotal otomatis → submit
- Edit product: ubah nama, harga, stock, status aktif
- View order: detail lengkap + items + pickup code
- Edit batch: ubah nama, event, tanggal, status lifecycle (open → processing → ready → closed)
- Charts menampilkan data real dari database

### Stakeholder Feedback
- PDF report berguna untuk dokumentasi transaksi
- Fitur CRUD admin lengkap, siap untuk operasional

---

## Sprint 6 — OOP Refactoring (28–29 Apr 2026)

**Sprint Goal:** Refaktor seluruh kode procedural menjadi OOP (classes, services, interfaces, exceptions).

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | OOP refactoring design spec | Irfan | Done |
| 2 | Database singleton + BaseService abstract | Irfan | Done |
| 3 | SessionGuard interface + AdminGuard + CustomerGuard | Irfan | Done |
| 4 | Auth facade | Irfan | Done |
| 5 | CsrfService + FlashMessage + FormatHelper | Irfan | Done |
| 6 | 9 Service classes (Admin, Order, OrderAdmin, Customer, Product, Batch, CustomerAdmin, ActivityLog, Pdf) | Irfan | Done |
| 7 | Exception hierarchy (App → Database, Auth, Csrf) | Irfan | Done |
| 8 | Total profit dashboard feature | Irfan | Done |
| 9 | Sprint review & risk assessment | Rizky | Done |
| 10 | OOP architecture review & documentation | Lily | Done |
| 11 | UI consistency audit Figma | Alfia | Done |

### Sprint Goal Achievement
**100%** — Seluruh kode procedural berhasil direfaktor ke OOP. Semua halaman menggunakan service classes. Code maintainability meningkat signifikan.

### Product Backlog Update
- OOP refactoring selesai
- Remaining: Stock management fix, OrderAdminService, dashboard batch filter, FonnteService

### Demo Highlights
- Polymorphism demo: `Auth::admin()` vs `Auth::customer()` — same interface, different behavior
- Singleton pattern: `Database::getInstance()` — one connection per request
- Inheritance: semua services extend `BaseService` with protected DB wrappers
- Exception handling: custom exceptions untuk Database, Auth, CSRF errors
- Total profit dashboard: `SUM(total_amount) WHERE status = 'picked_up'`

### Stakeholder Feedback
- OOP refactoring tidak mengubah UI/UX — semua fitur tetap berfungsi
- Code quality improvement visible dari commit history

---

## Sprint 7 — OOP Completion, Bug Fixes & Retrospective (4–6 Mei 2026)

**Sprint Goal:** Menyelesaikan refactoring OOP, memperbaiki bug dari merge, fitur baru, dan retrospective.

### Delivered Items

| # | Deliverable | Assignee | Status |
|---|-------------|----------|--------|
| 1 | Stock management fix (deduct on order, return on cancel) | Irfan | Done |
| 2 | OrderAdminService class (9 methods) | Irfan | Done |
| 3 | Batch/Order/Dashboard OOP refactor | Irfan | Done |
| 4 | Fix admin crash (phone column migration) | Irfan | Done |
| 5 | Fix create-customer insert flow (customers table) | Irfan | Done |
| 6 | Dashboard batch filter (filter stats per batch) | Irfan | Done |
| 7 | FonnteService — WhatsApp notifications (3 triggers) | Irfan | Done |
| 8 | Fix UI, popup CRUD, create-customer.php | Alif | Done |
| 9 | Merge conflict resolution | Rizky | Done |
| 10 | Sprint review final, retrospective, presentation slides | Rizky | Done |
| 11 | Final system documentation (SRS, DFD, ERD) | Lily | Done |
| 12 | Final Figma polish & export assets | Alfia | Done |

### Sprint Goal Achievement
**100%** — Semua fitur selesai. Bug fixes resolved. FonnteService live. Retrospective documented. Presentation ready.

### Product Backlog Update
- **Sprint 1–7: ALL ITEMS DONE**
- Sprint 8 added: Full Testing & Final Delivery (Week 13)

### Demo Highlights
- Stock management: stock deducted saat order, returned saat cancel/delete
- Dashboard batch filter: dropdown selector → stats filtered per batch
- WhatsApp notifications:
  - Trigger 1: Customer pre-order → WA ke owner
  - Trigger 2: Admin create order → WA ke customer (order details + pickup code)
  - Trigger 3: Batch → "ready" → WA ke semua customer di batch
- create-customer.php: sekarang insert ke `customers` table (bukan `users`)
- Phone column restored setelah migration dijalankan

### Stakeholder Feedback
- WhatsApp notifications fitur value-added untuk operasional TEFA
- Stock management penting untuk mencegah over-selling
- Dashboard batch filter membantu monitoring per event/batch

---

## Overall Project Delivery Summary

### Sprint Goal Achievement

| Sprint | Goal | Achievement |
|--------|------|-------------|
| 1 | Setup project & database | 100% |
| 2 | Landing page & core infrastructure | 100% |
| 3 | Customer panel frontend & backend | 100% |
| 4 | Tailwind migration & admin wiring | 100% |
| 5 | PDF, CRUD pages & bug fixes | 100% |
| 6 | OOP refactoring | 100% |
| 7 | OOP completion, bug fixes & retrospective | 100% |
| 8 | Full testing & final delivery | Pending (Week 13) |

### Feature Completeness

| Feature | Status | Details |
|---------|--------|---------|
| Landing page | Done | Hero, catalog, batch, SNI, footer — dynamic data |
| Auth system | Done | Dual guard (admin + customer), session, CSRF |
| Admin dashboard | Done | Stats, batch filter, real data charts |
| Admin CRUD products | Done | Create, edit, soft delete, stock management |
| Admin CRUD batches | Done | Create, edit, soft delete, status lifecycle |
| Admin CRUD customers | Done | Create, edit, soft delete |
| Admin CRUD orders | Done | Create, view, edit, delete, stock management |
| Admin activity log | Done | Super_admin only, paginated |
| Admin RBAC | Done | super_admin vs teknisi, price/deletion protection |
| Customer dashboard | Done | Stats, batch, products, sparklines |
| Customer pre-order | Done | Submit, stock validation, price from DB |
| Customer orders | Done | List, search, cancel pending, download PDF |
| Customer profile | Done | Edit profile, change password, active order lock |
| PDF generation | Done | DomPDF, A4 branded, order report |
| WhatsApp notifications | Done | Fonnte API, 3 triggers |
| OOP architecture | Done | 14 classes, interface, abstract, singleton, exceptions |
| Dark mode | Done | Global toggle, synced across all elements |

### Velocity & Metrics

| Metric | Value |
|--------|-------|
| Total sprints | 7 (development) + 1 (testing) |
| Total active days | 16 days |
| Total commits | 66 |
| Total OOP classes | 14 |
| Total admin pages | 14 |
| Total customer pages | 4 |
| Total service classes | 9 |
| Total auth guards | 2 |
| Total notification triggers | 3 |

### Per-Role Contributions

| Role | Name | Key Contributions |
|------|------|-------------------|
| Project Manager | Rizky | Sprint planning (7x), kickoff, merge conflict resolution, presentation slides, acceptance testing |
| System Analyst | Lily | SRS document, use case diagrams, RBAC analysis, OOP architecture review, system documentation (SRS, DFD, ERD), data analysis, stakeholder bridge |
| UI/UX (Figma) | Alfia | Design system, landing page mockup, customer panel mockups, admin panel mockups, dark mode variants, PDF template design, UI consistency audit |
| Frontend | Alif | Customer dashboard, riwayat pesanan, profile, edit order, admin panel (all pages), pengaturan page, UI fixes & optimization |
| Backend | Irfan | Core infrastructure, auth system, RBAC, all DB wiring, PDF generation, OOP refactoring, FonnteService, stock management, batch filter |

### Product Backlog Final Status

| Priority | Category | Total Items | Done | Pending |
|----------|----------|-------------|------|---------|
| 1 | Core Infrastructure | 5 | 5 | 0 |
| 2 | Auth Pages | 4 | 4 | 0 |
| 3 | Landing Page | 5 | 5 | 0 |
| 4 | Admin Panel | 15 | 15 | 0 |
| 5 | Customer Panel | 8 | 8 | 0 |
| 6 | Services (PDF, WhatsApp) | 2 | 2 | 0 |
| 7 | Security | 6 | 6 | 0 |
| 8 | Full Testing | 8 | 0 | 8 |
| **Total** | | **53** | **45** | **8** |

**Completion Rate: 84.9% (45/53 items done. Remaining 8 items = Sprint 8 full testing)**
