# Sprint Backlog — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:** Ivan (Backend), Alif Taran Ihsan (Frontend)
**Total Sprints:** 7

---

## Sprint 1 — Project Setup & Database (25–26 Feb 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-1: Repositori & konfigurasi project | Init git repo | Ivan | 0.5h | Done |
| | Setup .env (DB config) | Ivan | 0.5h | Done |
| | Setup README dengan dokumentasi | Ivan | 1h | Done |
| | Fix image path README | Ivan | 0.5h | Done |
| | Setup koneksi PDO MySQL | Ivan | 1h | Done |

---

## Sprint 2 — Landing Page & Core Infrastructure (18 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-2: Landing page | Hero section | Ivan | 2h | Done |
| | Product catalog | Ivan | 1.5h | Done |
| | Batch produksi section | Ivan | 1h | Done |
| | SNI disclaimer & footer | Ivan | 1h | Done |
| | Fix hero alignment | Ivan | 0.5h | Done |
| | Fix batch card 3-column | Ivan | 0.5h | Done |
| | Fix logo & footer | Ivan | 0.5h | Done |
| PBI-3: Core infrastructure | functions.php (query helpers) | Ivan | 2h | Done |
| | auth.php (session management) | Ivan | 2h | Done |
| | Layout system (header/footer) | Ivan | 3h | Done |
| | CSRF token | Ivan | 1h | Done |
| | Flash message | Ivan | 1h | Done |
| PBI-4: Auth pages | Login admin page | Ivan | 1h | Done |
| | Login customer page | Ivan | 1h | Done |
| | Convert images to PHP pages | Alif | 1h | Done |

---

## Sprint 3 — Customer Panel Frontend & Backend (19–21 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-5: Customer frontend | Dashboard customer | Alif | 3h | Done |
| | Riwayat pesanan | Alif | 2h | Done |
| | Profile page | Alif | 2h | Done |
| | Edit order page | Alif | 2h | Done |
| PBI-6: Admin frontend | Admin panel semua halaman | Alif | 6h | Done |
| | Pengaturan page | Alif | 1.5h | Done |
| PBI-7: Backend wiring | Pre-order feature | Ivan | 4h | Done |
| | Profile update | Ivan | 2h | Done |
| | Notification status | Ivan | 1h | Done |
| | Sync sidebar/navbar | Ivan | 1h | Done |
| | Logout implementasi | Ivan | 1h | Done |
| | FormatHelper utility | Ivan | 1h | Done |
| | Chart.js sparklines | Ivan | 2h | Done |

---

## Sprint 4 — Tailwind Migration & Admin Wiring (22–25 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-8: Tailwind migration | CSS native → Tailwind CDN | Ivan | 4h | Done |
| | Edit customer/order page | Alif | 3h | Done |
| PBI-9: RBAC & Security | RBAC system (role guard) | Ivan | 3h | Done |
| | Price protection | Ivan | 1.5h | Done |
| | Core product deletion protection | Ivan | 1h | Done |
| PBI-10: Admin panel wiring | Wire semua admin page ke DB | Ivan | 8h | Done |
| | Sidebar link fix | Ivan | 0.5h | Done |
| | Logout fix semua page | Ivan | 1h | Done |
| | SQL escape fix | Ivan | 0.5h | Done |

---

## Sprint 5 — PDF, CRUD Pages & Bug Fixes (27–28 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-11: PDF generation | Install DomPDF | Ivan | 0.5h | Done |
| | PdfService class | Ivan | 2h | Done |
| | PDF template (port from Laravel) | Ivan | 3h | Done |
| | Download endpoint | Ivan | 1h | Done |
| | Download buttons (customer + admin) | Ivan | 0.5h | Done |
| PBI-12: Admin CRUD pages | Create order page | Ivan | 3h | Done |
| | Edit product page | Ivan | 2h | Done |
| | View order page | Ivan | 2h | Done |
| | Edit batch page | Ivan | 2h | Done |
| PBI-13: Bug fixes | Replace dummy chart | Ivan | 1h | Done |
| | Sidebar hover color fix | Ivan | 0.5h | Done |
| | Dropdown menu fix | Ivan | 0.5h | Done |
| | SQL limit/offset fix | Ivan | 0.5h | Done |
| | Frontend optimization | Alif | 3h | Done |

---

## Sprint 6 — OOP Refactoring (28–29 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-14: OOP design | Design spec documentasi | Ivan | 2h | Done |
| PBI-15: OOP implementation | Database singleton | Ivan | 1h | Done |
| | BaseService abstract class | Ivan | 1h | Done |
| | SessionGuard interface | Ivan | 1h | Done |
| | AdminGuard & CustomerGuard | Ivan | 1.5h | Done |
| | Auth facade | Ivan | 1h | Done |
| | CsrfService, FlashMessage | Ivan | 1h | Done |
| | FormatHelper | Ivan | 0.5h | Done |
| | 9 Service classes | Ivan | 6h | Done |
| | Exception hierarchy | Ivan | 1h | Done |
| | Refactor semua halaman | Ivan | 4h | Done |
| | Fix post-refactor bugs | Ivan | 2h | Done |
| | Total profit dashboard | Ivan | 1h | Done |

---

## Sprint 7 — OOP Completion & New Features (4–5 Mei 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-16: OOP completion | Stock management fix | Ivan | 2h | Done |
| | OrderAdminService class | Ivan | 3h | Done |
| | Batch/Order/Dashboard refactor | Ivan | 3h | Done |
| PBI-17: Merge conflict resolution | Fix Alif's merge UI | Alif | 2h | Done |
| | Fix admin crash (phone column) | Ivan | 1.5h | Done |
| | Fix create-customer flow | Ivan | 1h | Done |
| | Restore phone column support | Ivan | 0.5h | Done |
| PBI-18: New features | Dashboard batch filter | Ivan | 2h | Done |
| | FonnteService (WhatsApp) | Ivan | 3h | Done |
