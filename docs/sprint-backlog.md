# Sprint Backlog — TEFA Canning SIP Legacy

**Project:** TEFA Canning SIP Legacy (PHP Native)
**Team:**
| Role | Name |
|------|------|
| Project Manager | Rizky |
| System Analyst | Lily |
| UI/UX (Figma) | Alfia |
| Frontend | Alif Taran Ihsan |
| Backend | Irfan |

**Total Sprints:** 8

---

## Sprint 1 — Project Setup & Database (25–26 Feb 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-1: Repositori & konfigurasi project | Init git repo | Irfan | 0.5h | Done |
| | Setup .env (DB config) | Irfan | 0.5h | Done |
| | Setup README dengan dokumentasi | Irfan | 1h | Done |
| | Fix image path README | Irfan | 0.5h | Done |
| | Setup koneksi PDO MySQL | Irfan | 1h | Done |
| PBI-2: Project management | Kickoff meeting, define scope & timeline | Rizky | 2h | Done |
| | Sprint 1 planning & task assignment | Rizky | 1h | Done |
| PBI-3: System analysis | Requirements gathering dari stakeholder TEFA | Lily | 3h | Done |
| | SRS document draft | Lily | 3h | Done |
| | Koordinasi kebutuhan data stakeholder | Lily | 1h | Done |
| PBI-4: UI/UX design | Design system setup Figma (colors, typography, spacing) | Alfia | 3h | Done |

---

## Sprint 2 — Landing Page & Core Infrastructure (18 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-5: Landing page | Hero section | Irfan | 2h | Done |
| | Product catalog | Irfan | 1.5h | Done |
| | Batch produksi section | Irfan | 1h | Done |
| | SNI disclaimer & footer | Irfan | 1h | Done |
| | Fix hero alignment | Irfan | 0.5h | Done |
| | Fix batch card 3-column | Irfan | 0.5h | Done |
| | Fix logo & footer | Irfan | 0.5h | Done |
| PBI-6: Core infrastructure | functions.php (query helpers) | Irfan | 2h | Done |
| | auth.php (session management) | Irfan | 2h | Done |
| | Layout system (header/footer) | Irfan | 3h | Done |
| | CSRF token | Irfan | 1h | Done |
| | Flash message | Irfan | 1h | Done |
| PBI-7: Auth pages | Login admin page | Irfan | 1h | Done |
| | Login customer page | Irfan | 1h | Done |
| | Convert images to PHP pages | Alif | 1h | Done |
| PBI-8: Project management | Sprint 2 planning & review | Rizky | 2h | Done |
| PBI-9: System analysis | Use case diagram | Lily | 3h | Done |
| | Analisis kebutuhan sistem, bridge ke stakeholder | Lily | 2h | Done |
| PBI-10: UI/UX design | Landing page mockup Figma (hero, catalog, footer) | Alfia | 4h | Done |

---

## Sprint 3 — Customer Panel Frontend & Backend (19–21 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-11: Customer frontend | Dashboard customer | Alif | 3h | Done |
| | Riwayat pesanan | Alif | 2h | Done |
| | Profile page | Alif | 2h | Done |
| | Edit order page | Alif | 2h | Done |
| PBI-12: Admin frontend | Admin panel semua halaman | Alif | 6h | Done |
| | Pengaturan page | Alif | 1.5h | Done |
| PBI-13: Backend wiring | Pre-order feature | Irfan | 4h | Done |
| | Profile update | Irfan | 2h | Done |
| | Notification status | Irfan | 1h | Done |
| | Sync sidebar/navbar | Irfan | 1h | Done |
| | Logout implementasi | Irfan | 1h | Done |
| | FormatHelper utility | Irfan | 1h | Done |
| | Chart.js sparklines | Irfan | 2h | Done |
| PBI-14: Project management | Koordinasi task frontend-backend, sprint review | Rizky | 2h | Done |
| PBI-15: System analysis | Analisis data kebutuhan customer panel | Lily | 3h | Done |
| | Dokumentasi sistem customer & bridge ke stakeholder | Lily | 2h | Done |
| PBI-16: UI/UX design | Customer panel mockups Figma (dashboard, orders, profile) | Alfia | 5h | Done |
| | Admin panel mockups Figma | Alfia | 4h | Done |

---

## Sprint 4 — Tailwind Migration & Admin Wiring (22–25 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-17: Tailwind migration | CSS native → Tailwind CDN | Irfan | 4h | Done |
| | Edit customer/order page | Alif | 3h | Done |
| PBI-18: RBAC & Security | RBAC system (role guard) | Irfan | 3h | Done |
| | Price protection | Irfan | 1.5h | Done |
| | Core product deletion protection | Irfan | 1h | Done |
| PBI-19: Admin panel wiring | Wire semua admin page ke DB | Irfan | 8h | Done |
| | Sidebar link fix | Irfan | 0.5h | Done |
| | Logout fix semua page | Irfan | 1h | Done |
| | SQL escape fix | Irfan | 0.5h | Done |
| PBI-20: Project management | Sprint review & prioritas bug fixes | Rizky | 2h | Done |
| PBI-21: System analysis | Analisis kebutuhan RBAC, dokumentasi keamanan | Lily | 3h | Done |
| | Laporan progress ke stakeholder | Lily | 1.5h | Done |
| PBI-22: UI/UX design | Dark mode design variants Figma | Alfia | 3h | Done |
| | Refinement admin panel mockups | Alfia | 2h | Done |

---

## Sprint 5 — PDF, CRUD Pages & Bug Fixes (27–28 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-23: PDF generation | Install DomPDF | Irfan | 0.5h | Done |
| | PdfService class | Irfan | 2h | Done |
| | PDF template (port from Laravel) | Irfan | 3h | Done |
| | Download endpoint | Irfan | 1h | Done |
| | Download buttons (customer + admin) | Irfan | 0.5h | Done |
| PBI-24: Admin CRUD pages | Create order page | Irfan | 3h | Done |
| | Edit product page | Irfan | 2h | Done |
| | View order page | Irfan | 2h | Done |
| | Edit batch page | Irfan | 2h | Done |
| PBI-25: Bug fixes | Replace dummy chart | Irfan | 1h | Done |
| | Sidebar hover color fix | Irfan | 0.5h | Done |
| | Dropdown menu fix | Irfan | 0.5h | Done |
| | SQL limit/offset fix | Irfan | 0.5h | Done |
| | Frontend optimization | Alif | 3h | Done |
| PBI-26: Project management | Sprint review & sprint planning Sprint 6 | Rizky | 2h | Done |
| PBI-27: System analysis | Laporan sprint 5, dokumentasi CRUD workflow | Lily | 3h | Done |
| | Analisis data transaksi untuk laporan | Lily | 2h | Done |
| PBI-28: UI/UX design | PDF report template design Figma | Alfia | 3h | Done |
| | Icon & button updates Figma | Alfia | 1h | Done |

---

## Sprint 6 — OOP Refactoring (28–29 Apr 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-29: OOP design | Design spec documentasi | Irfan | 2h | Done |
| PBI-30: OOP implementation | Database singleton | Irfan | 1h | Done |
| | BaseService abstract class | Irfan | 1h | Done |
| | SessionGuard interface | Irfan | 1h | Done |
| | AdminGuard & CustomerGuard | Irfan | 1.5h | Done |
| | Auth facade | Irfan | 1h | Done |
| | CsrfService, FlashMessage | Irfan | 1h | Done |
| | FormatHelper | Irfan | 0.5h | Done |
| | 9 Service classes | Irfan | 6h | Done |
| | Exception hierarchy | Irfan | 1h | Done |
| | Refactor semua halaman | Irfan | 4h | Done |
| | Fix post-refactor bugs | Irfan | 2h | Done |
| | Total profit dashboard | Irfan | 1h | Done |
| PBI-31: Project management | Sprint review & risk assessment refactoring | Rizky | 2h | Done |
| PBI-32: System analysis | Review arsitektur OOP, dokumentasi sistem | Lily | 3h | Done |
| | Laporan progress sprint 6 | Lily | 1.5h | Done |
| PBI-33: UI/UX design | UI consistency audit Figma | Alfia | 2h | Done |

---

## Sprint 7 — OOP Completion, Bug Fixes & Retrospective (4–6 Mei 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-34: OOP completion | Stock management fix | Irfan | 2h | Done |
| | OrderAdminService class | Irfan | 3h | Done |
| | Batch/Order/Dashboard refactor | Irfan | 3h | Done |
| PBI-35: Merge conflict resolution | Fix Alif's merge UI | Alif | 2h | Done |
| | Fix admin crash (phone column) | Irfan | 1.5h | Done |
| | Fix create-customer flow | Irfan | 1h | Done |
| | Restore phone column support | Irfan | 0.5h | Done |
| PBI-36: New features | Dashboard batch filter | Irfan | 2h | Done |
| | FonnteService (WhatsApp) | Irfan | 3h | Done |
| PBI-37: Project management | Koordinasi merge conflict resolution | Rizky | 1.5h | Done |
| | Sprint review final, retrospective | Rizky | 3h | Done |
| | Laporan final project, slide presentasi | Rizky | 4h | Done |
| PBI-38: System analysis | Laporan analisis sistem final | Lily | 4h | Done |
| | Dokumentasi lengkap (SRS, DFD, ERD) | Lily | 4h | Done |
| PBI-39: UI/UX final | Final Figma polish, export assets presentasi | Alfia | 3h | Done |

---

## Sprint 8 — Bug Fixes, Full Testing & Final Delivery (Week 13 — Mei 2026)

### Product Backlog Items → Sprint Backlog

| PBI | Task | Assignee | Estimate | Status |
|-----|------|----------|----------|--------|
| PBI-40: Backend bug fixes | Fix batch CSRF token issue semua role | Irfan | 1.5h | Pending |
| | Auto increment SKU produk | Irfan | 2h | Pending |
| | Tambah tanggal batch info saat customer buat order | Irfan | 1.5h | Pending |
| PBI-41: Full testing backend | Test semua fitur backend (CRUD, auth, RBAC) | Irfan | 4h | Pending |
| | Test stock management, profit calculation, PDF, WhatsApp | Irfan | 3h | Pending |
| PBI-42: Frontend bug fixes | Responsive web semua pages | Alif | 4h | Pending |
| | Text alasan jelas kenapa produk utama tidak bisa dihapus | Alif | 0.5h | Pending |
| PBI-43: Full testing frontend | Test semua halaman (responsive, form validation, dark mode) | Alif | 4h | Pending |
| PBI-44: Acceptance testing | Full end-to-end flow testing | Rizky | 3h | Pending |
| | Stakeholder demo preparation | Rizky | 2h | Pending |
| | Bug tracking & koordinasi perbaikan | Rizky | 2h | Pending |
| PBI-45: Data & requirement validation | Verifikasi semua requirement terpenuhi | Lily | 3h | Pending |
| | Validasi data & laporan final | Lily | 2h | Pending |
| | Finalisasi dokumentasi akhir | Lily | 3h | Pending |
| PBI-46: UI/UX review | Visual testing semua halaman | Alfia | 3h | Pending |
| | Usability testing & feedback | Alfia | 2h | Pending |
| | Finalisasi Figma assets & dokumentasi desain | Alfia | 2h | Pending |
