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

**Period:** 25 Februari 2026 – 13 Mei 2026 (Sprint 1–9 selesai)
**Framework:** Scrum (adaptasi untuk tim 5 orang)

---

## Project Kanban Board

| To Do | Doing | Done (Sprint 1–9) |
|-------|-------|--------------------|
| Phone validation on register | | Landing page (hero, catalog, batch, SNI, footer) |
| Code audit optimizations (5 items) | | Auth system (dual guard: admin + customer, session, CSRF) |
| | | Admin/teknisi pages (dashboard, products, batches, customers, orders, CRUD, activity log) |
| | | Customer pages (dashboard, preorder, orders, profile, PDF download) |
| | | PDF generation (DomPDF, branded template) |
| | | WhatsApp notifications (Fonnte API, 4 triggers) |
| | | Forgot password via WhatsApp OTP (PasswordResetService) |
| | | RBAC & security (role guard, price protection, deletion protection, stock management) |
| | | OOP architecture (16 classes, interfaces, abstract, singleton, exceptions) |
| | | Dark mode (global toggle, synced) |
| | | Tailwind CSS migration |
| | | Flowchart diagrams (overview, admin, customer) |
| | | Database analysis documentation (DDL, DML, DQL, Aggregate) |
| | | Sprint planning, review & retrospective (9 sprints) |
| | | SRS, use case, DFD, ERD documentation |
| | | Figma design system & mockups |

---

## Sprint History

### Sprint 1 — Project Setup & Database (25–26 Feb 2026)

**Sprint Goal:** Inisialisasi repositori, koneksi database, dan konfigurasi dasar project.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Repositori git + database config + dokumentasi | Irfan | Done |
| Project kickoff, scope & timeline | Rizky | Done |
| SRS document draft & stakeholder requirements | Lily | Done |
| Design system setup Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 4**

---

### Sprint 2 — Landing Page & Core Infrastructure (18 Apr 2026)

**Sprint Goal:** Membangun landing page dan core infrastructure (auth, session, CSRF, layout system).

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Landing page (hero, catalog, batch, footer) | Irfan | Done |
| Core infrastructure (functions, auth, layout, CSRF, flash message) | Irfan | Done |
| Auth login pages | Irfan | Done |
| PHP pages conversion | Alif | Done |
| Sprint planning & review | Rizky | Done |
| Use case diagram & stakeholder bridge | Lily | Done |
| Landing page mockup Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 10**

---

### Sprint 3 — Customer & Admin Panel (19–21 Apr 2026)

**Sprint Goal:** Menyelesaikan seluruh halaman customer dan admin, backend wiring.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Customer panel pages (dashboard, preorder, orders, profile) | Alif → Irfan | Done |
| Admin panel pages (semua halaman) | Alif | Done |
| Backend wiring (pre-order, profile, auth, charts) | Irfan | Done |
| Task koordinasi frontend-backend | Rizky | Done |
| Customer & admin panel documentation | Lily | Done |
| Customer & admin panel mockups Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 14**

---

### Sprint 4 — Tailwind Migration & Security (22–25 Apr 2026)

**Sprint Goal:** Migrasi CSS ke Tailwind, implementasi RBAC, wiring admin ke database.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Tailwind CSS migration | Irfan | Done |
| RBAC system (super_admin vs teknisi) | Irfan | Done |
| Admin panel DB wiring (semua halaman) | Irfan | Done |
| Sprint review & bug prioritization | Rizky | Done |
| RBAC documentation & stakeholder report | Lily | Done |
| Dark mode & admin mockups refinement Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 9**

---

### Sprint 5 — PDF Generation & Admin CRUD (27–28 Apr 2026)

**Sprint Goal:** Implementasi PDF, halaman CRUD admin yang belum ada, bug fixes.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| PDF generation (DomPDF) | Irfan | Done |
| Admin CRUD pages (create order, edit product, view order, edit batch) | Irfan | Done |
| Real data charts & bug fixes | Irfan | Done |
| Frontend optimization | Alif | Done |
| Sprint review & planning | Rizky | Done |
| CRUD workflow documentation & data analysis | Lily | Done |
| PDF template design Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 15**

---

### Sprint 6 — OOP Refactoring (28–29 Apr 2026)

**Sprint Goal:** Refaktor seluruh kode procedural menjadi OOP.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| OOP refactoring (14 classes: Database, BaseService, 9 Services, 2 Guards, Auth facade, helpers, exceptions) | Irfan | Done |
| Sprint review & risk assessment | Rizky | Done |
| OOP architecture review & documentation | Lily | Done |
| UI consistency audit Figma | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 4**

---

### Sprint 7 — Final Features & Retrospective (4–6 Mei 2026)

**Sprint Goal:** Menyelesaikan fitur akhir, bug fixes, WhatsApp notifications, retrospective.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Stock management, OrderAdminService, batch filter | Irfan | Done |
| FonnteService (WhatsApp notifications — 3 triggers) | Irfan | Done |
| Bug fixes (merge conflict, phone column, create-customer) | Irfan + Alif | Done |
| Sprint review, retrospective, presentation slides | Rizky | Done |
| Final system documentation (SRS, DFD, ERD) | Lily | Done |
| Final Figma polish & export assets | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 10**

---

### Sprint 8 — Bug Fixes & Full Testing (Week 13 — 10 Mei 2026)

**Sprint Goal:** Perbaikan bug remaining, full testing seluruh fitur, persiapan delivery.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Bug fixes: batch CSRF, auto increment SKU, tanggal batch, dashboard "Semua Batch", query LIMIT, FonnteService live test | Irfan | Done |
| Full testing backend (CRUD, auth, RBAC, stock, profit, PDF, WhatsApp) | Irfan | Done |
| Bug fixes: responsive web, text alasan produk, UI fixes (activity log, header, landing page, customer orders), header refactor | Alif | Done |
| Full testing frontend | Alif | Done |
| Acceptance testing & stakeholder demo | Rizky | Done |
| Requirement validation & final documentation | Lily | Done |
| Visual & usability testing | Alfia | Done |

**Sprint Goal: 100%** | **Commits: 12**

---

### Sprint 9 — Feature Completion & Diagrams (Week 14 — 13 Mei 2026)

**Sprint Goal:** Forgot password, cleanup fitur tidak berfungsi, diagram sistem, analisis database.

| Deliverable | Assignee | Status |
|-------------|----------|--------|
| Forgot password via WhatsApp OTP (PasswordResetService + FonnteService + reset-password page) | Irfan | Done |
| Remove Remember Me feature (non-functional, removed from both login pages) | Irfan | Done |
| Database analysis (DDL, DML, DQL, Aggregate — 99 queries) | Irfan | Done |
| Flowchart diagrams (overview + admin + customer + documentation) | Irfan | Done |
| Frontend tech documentation | Alif | Done |
| Project cleanup (scratch removal, debug cleanup) | Irfan | Done |

**Sprint Goal: 100%** | **Commits: 4**

---

## Overall Summary

### Velocity

| Metric | Value |
|--------|-------|
| Sprint (development) | 7 |
| Sprint (testing) | 1 |
| Sprint (feature completion) | 1 |
| Total sprints | 9 |
| Total active days | 18 |
| Total commits | 82+ |
| Sprint goal achievement (dev) | 9/9 (100%) |

### Per-Role Summary

| Role | Name | Contribution |
|------|------|-------------|
| Project Manager | Rizky | Sprint planning & review (9x), kickoff, task management, merge coordination, presentation, acceptance testing |
| System Analyst | Lily | SRS, use case, data analysis, stakeholder bridge, RBAC docs, OOP review, system docs (SRS, DFD, ERD), requirement validation |
| UI/UX (Figma) | Alfia | Design system, landing page mockup, customer & admin panel mockups, dark mode, PDF template, UI audit, visual testing |
| Frontend | Alif | Customer pages, admin pages, pengaturan, UI fixes & optimization, responsive web, frontend tech docs |
| Backend | Irfan | Core infrastructure, auth, RBAC, all DB wiring, PDF, OOP refactoring, FonnteService, stock management, batch filter, forgot password, database analysis, flowchart diagrams |

### Product Backlog

| Status | Count |
|--------|-------|
| Done (Sprint 1–9) | 59 |
| Remaining (optimization) | 3 |
| **Total** | **62** |

**Completion Rate: 95.2%** (remaining = phone validation + code audit optimizations)
