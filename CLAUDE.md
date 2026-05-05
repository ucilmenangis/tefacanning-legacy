# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

TEFA Canning SIP Legacy — PHP Native version of the TEFA Canning Transaction & Monitoring System. This is a **rewrite** from Laravel 10 + FilamentPHP 3 to pure PHP Native (no framework).

**Original Laravel repo:** `../tefa-canning-system/` (reference for business logic, UI, and features)
**Database:** Shared with Laravel version (`tefa_canning_db` on 127.0.0.1:3306)

**Why this exists:** Workshop Proyek Perangkat Lunak (Semester 2) assignment requires PHP Native, not framework.

## Tech Stack

- **PHP 8.3** (Native, no framework)
- **PDO MySQL** — Raw queries, no ORM
- **Tailwind CSS** — Loaded via CDN (`cdn.tailwindcss.com`) with inline config. No build step.
- **MariaDB** — Shared database with Laravel version
- **Composer** — `vlucas/phpdotenv` (env loader), `dompdf/dompdf` (PDF generation)

## Commands

```bash
# Start dev server
php -S localhost:8000

# Install PHP dependencies
composer install
```

No build step, no linter, no tests. Tailwind via CDN.

## UI Identity & Colors

Defined in Tailwind inline config in `index.php`:

```css
primary: #E02424  (Red-600 equivalent)
accent:  #F05252  (Red-500 equivalent)
dark:    #9B1C1C  (Red-800 equivalent)
navy:    #111827  (Gray-900, used for headings)
```

Font: **Inter** (Google Fonts). Icons: **Phosphor Icons** (`@phosphor-icons/web`). Max content width: `1200px`.

## Team Structure

| Role | Name | Responsibility |
|------|------|---------------|
| **Backend** | Ivan | PHP logic, DB queries, auth flow, services, routing |
| **Frontend** | Alif | HTML/Tailwind pages, UI layout, forms |

### Development Workflow

Every phase follows: **Plan → Execute → Verify → Show changes → Update docs → Next phase → Repeat**

### Rules for Frontend Developer (Alif)

1. **ALL files MUST be `.php`, NOT `.html`.** This is a PHP project. Never rename `.php` to `.html`. Every page: `dashboard.php`, `orders.php`, `profile.php`.
2. **Use `$variables` for dynamic data.** Don't hardcode values in HTML body. Put data at top of file:
   ```php
   <?php
   // Data section (Ivan will wire this to DB later)
   $products = [
       ['name' => 'Sarden Kaleng', 'price' => 25000],
   ];
   ?>
   <!-- HTML section (Alif builds this) -->
   ```
3. **Don't build auth/login logic.** Build login forms only (HTML + Tailwind). Backend will handle session, validation, password check.
4. **Use shared layout system.** Include header/footer in every page — don't duplicate `<head>`, sidebar, navbar, footer:
   ```php
   <?php
   $pageTitle = 'Dashboard';
   $currentPage = 'dashboard';
   include __DIR__ . '/../includes/header-customer.php';
   ?>
   <!-- page content here -->
   <?php include __DIR__ . '/../includes/footer-customer.php'; ?>
   ```
5. **Same color theme:** primary `#E02424`, accent `#F05252`, dark `#9B1C1C`, navy `#111827`.
6. **Don't modify files in:** `config/`, `includes/`, `services/`. Those are backend-only.
7. **CSRF token required** in every form: `<?php echo csrfField(); ?>`. Form tanpa ini akan ditolak saat submit.
8. **Don't duplicate sidebar/navbar.** Already in header includes. Just write page content between header and footer.
9. **Commit message format:** `feat: [page/feature description]` or `fix: [what fixed]`.

## Current Implementation Status

**Priority 1 (Core Infrastructure): DONE (OOP refactored)**
- `includes/auth.php` — OOP bootstrap (loads classes + starts session)
- `includes/functions.php` — OOP bootstrap (loads Database + BaseService)
- `includes/header-admin.php` + `footer-admin.php` — Admin layout
- `includes/header-customer.php` + `footer-customer.php` — Customer layout

**Priority 2 (Auth Pages): DONE (UI only — backend wiring pending)**
- `auth/login-admin.php`, `auth/login-customer.php`, `auth/register.php`, `auth/forgot-password.php`

**Priority 3 (Landing Page): DONE**
- `index.php` — Hero, product catalog, batch info, SNI disclaimer, footer with Google Maps
- ✅ Products & batches wired to DB (dynamic data)

**Priority 5 (Customer Panel): DONE**
- `customer/dashboard.php` — ✅ wired to DB (stats, batch, products, sparkline charts)
- `customer/preorder.php` — ✅ wired to DB (submit order, price from DB, order history)
- `customer/orders.php` — ✅ wired to DB (order list, search, cancel pending)
- `customer/edit-order.php` — ✅ wired to DB (edit pending order items, price from DB)
- `customer/profile.php` — ✅ wired to DB (edit profile, change password, active order lock)

**OOP Classes (all extend BaseService unless noted):**

Core (no parent):
- `classes/Database.php` — Singleton PDO wrapper: `getInstance()`, `fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()`, `getPdo()`
- `classes/FormatHelper.php` — static helpers: `rupiah()`, `tanggal()`, `orderStatus()`, `batchStatus()`

Services (extend BaseService):
- `classes/AdminService.php` — admin operations: `getRole()`, `isSuperAdmin()`, `getAll()`, dashboard methods (profit from picked_up orders), `getDashboardStatsByBatch()`, `getAllBatchesForDropdown()`, `getRecentOrdersByBatch()`
- `classes/OrderService.php` — order operations: `getByCustomer()`, `getById()`, `cancel()` (with stock return), `getStats()`
- `classes/OrderAdminService.php` — admin order operations: `getAll()`, `getById()`, `getItems()`, `getActiveProducts()`, `getCustomersForDropdown()`, `getOpenBatchesForDropdown()`, `createOrder()`, `updateOrder()`, `deleteOrder()`
- `classes/CustomerService.php` — customer operations: `getById()`, `updateProfile()`, `changePassword()`, `hasActiveOrders()`
- `classes/ProductService.php` — product CRUD + stock: `getAll()`, `getById()`, `create()`, `updateById()`, `softDelete()`, `hasStock()`, `deductStock()`, `returnStock()`
- `classes/BatchService.php` — batch CRUD: `getAll()`, `getById()`, `getOpenBatches()`, `create()`, `updateById()`, `softDelete()`
- `classes/CustomerAdminService.php` — customer admin CRUD: `getAll()`, `getById()`, `getStats()`, `create()`, `updateById()`, `softDelete()`
- `classes/ActivityLogService.php` — activity log: `log()`, `getAll()`, `countAll()`
- `classes/OrderAdminService.php` — admin order CRUD: `getAll()`, `getById()`, `getItems()`, `getActiveProducts()`, `getCustomersForDropdown()`, `getOpenBatchesForDropdown()`, `createOrder()`, `updateOrder()`, `deleteOrder()`
- `classes/PdfService.php` — PDF generation: `generateOrderPdf()`, `download()`

Auth & Session:
- `classes/SessionGuard.php` — Interface: `isLoggedIn()`, `getId()`, `login()`, `logout()`, `requireAuth()`
- `classes/AdminGuard.php` — implements SessionGuard + `isSuperAdmin()`, `requireSuperAdmin()`, `getRole()`
- `classes/CustomerGuard.php` — implements SessionGuard
- `classes/Auth.php` — Facade: `startSession()`, `admin()`, `customer()`

Static Helpers:
- `classes/CsrfService.php` — `generate()`, `verify()`, `field()`
- `classes/FlashMessage.php` — `set()`, `get()`, `render()`, `has()`

Base:
- `classes/BaseService.php` — Abstract base with protected DB wrappers (`fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()`)

Exceptions:
- `classes/exceptions/AppException.php` — Base exception
- `classes/exceptions/DatabaseException.php`, `AuthException.php`, `CsrfException.php`

**Priority 4 (Admin Panel): DONE (wired to DB)**
- `admin/activity-log.php` — real logs from activity_log table (super_admin only)

**Frontend Migration (Native CSS → Tailwind): IN PROGRESS**
- **Phase 1 (Auth Pages):** ✅ 100% (login-admin, login-customer, register, forgot-password)
- **Phase 2 (Admin List Pages):** ✅ 100% (orders, products, batches, customers)
- **Phase 3 (Admin Form Pages):** ✅ 100% (edit-product, create-product, create-batch, edit-batch, edit-customer, create-order, edit-order, view-order)
- **Phase 4 (Admin Special Pages):** ✅ 100% (dashboard, pengaturan, activity-log)
- **Phase 5 (Customer Pages):** ✅ 100% (dashboard, preorder, orders, edit-order, profile)
- **Cleanup:** ✅ 100% (dashboard status badges fixed)

**Not yet built:** FonnteService (WhatsApp notifications).

## Refactoring Plan — COMPLETE ✅

**Completed 2026-04-28.** All procedural code refactored to OOP. See design spec: `docs/superpowers/specs/2026-04-28-oop-refactor-design.md`

| OOP Concept | Implementation |
|-------------|---------------|
| Encapsulation | `Database` (private $pdo), guards (private const SESSION_KEY) |
| Inheritance | `BaseService` → all 9 service classes |
| Interface | `SessionGuard` with `isLoggedIn()`, `getId()`, `login()`, `logout()`, `requireAuth()` |
| Polymorphism | `AdminGuard` vs `CustomerGuard` — same interface, different behavior |
| Abstract class | `BaseService` with protected DB wrappers |
| Exception hierarchy | `AppException` → `DatabaseException` / `AuthException` / `CsrfException` |
| Singleton | `Database::getInstance()` with clone/wakeup prevention |
| Static methods | `CsrfService`, `FlashMessage`, `Auth` facade |

## Project Structure

```
tefa-canning-legacy/
├── index.php                ← Landing page (NOT a router yet)
├── .env                     ← Environment config (DB, Fonnte token)
├── includes/
│   ├── functions.php        ← OOP bootstrap (loads Database + BaseService)
│   ├── auth.php             ← OOP bootstrap (loads all auth classes + starts session)
│   ├── header-admin.php     ← Admin layout header
│   ├── header-customer.php  ← Customer layout header
│   ├── footer-admin.php     ← Admin layout footer
│   └── footer-customer.php  ← Customer layout footer
├── api/
│   └── download-pdf.php     ← PDF download endpoint (dual auth)
├── admin/                   ← Admin panel (all wired to DB)
│   ├── dashboard.php
│   ├── products.php
│   ├── batches.php
│   ├── customers.php
│   ├── orders.php
│   ├── activity-log.php
│   ├── pengaturan.php
│   ├── create-product.php
│   ├── create-batch.php
│   ├── create-order.php
│   ├── view-order.php
│   ├── edit-customer.php
│   ├── edit-order.php
│   ├── edit-product.php
│   └── edit-batch.php
├── customer/                ← Customer pages (UI done, backend pending)
│   ├── dashboard.php
│   ├── preorder.php
│   ├── orders.php
│   └── profile.php
├── auth/                    ← Auth pages (backend done)
│   ├── login-admin.php
│   ├── login-customer.php
│   ├── register.php
│   ├── logout.php
│   └── forgot-password.php
├── assets/
│   ├── css/                 ← Empty (using CDN)
│   ├── js/                  ← Empty
│   └── images/              ← Static images (8 files)
├── database/                ← Empty — SQL dump to be added
├── docs/                    ← Internal documentation
│   ├── 01-query-helpers.md
│   ├── 02-session-management.md
│   ├── 03-layout-system.md
│   ├── 05-flash-message.md
│   └── 06-rbac-role-system.md
├── classes/                 ← OOP business logic
│   ├── Database.php         ← Singleton PDO wrapper
│   ├── BaseService.php      ← Abstract base with DB wrappers
│   ├── SessionGuard.php     ← Interface for auth guards
│   ├── AdminGuard.php       ← Admin auth (implements SessionGuard)
│   ├── CustomerGuard.php    ← Customer auth (implements SessionGuard)
│   ├── Auth.php             ← Facade: startSession(), admin(), customer()
│   ├── CsrfService.php      ← CSRF token static methods
│   ├── FlashMessage.php     ← Flash message static methods
│   ├── FormatHelper.php     ← Static: rupiah, tanggal, status labels
│   ├── AdminService.php     ← Admin operations, dashboard queries, RBAC
│   ├── OrderService.php     ← Order CRUD (customer-side)
│   ├── OrderAdminService.php ← Order CRUD (admin-side, with stock management)
│   ├── CustomerService.php  ← Customer profile, password, active order check
│   ├── ProductService.php   ← Product CRUD
│   ├── BatchService.php     ← Batch CRUD
│   ├── CustomerAdminService.php ← Customer admin CRUD
│   ├── ActivityLogService.php   ← Activity log read/write
│   ├── PdfService.php           ← PDF generation (DomPDF)
│   └── exceptions/          ← Custom exception hierarchy
│       ├── AppException.php
│       ├── DatabaseException.php
│       ├── AuthException.php
│       └── CsrfException.php
├── services/                ← To be created (Fonnte)
└── views/
    └── pdf/
        └── order-report.php ← PDF template for order reports
```

## Architecture Decisions

### Routing
- **Direct file access** — no router, no `.htaccess`. URLs like `/admin/dashboard.php`, `/auth/login-admin.php`.
- `index.php` serves as landing page only (not a router).
- Each page includes shared files (`includes/auth.php`, `includes/functions.php`) at the top.

### Authentication (Dual Guard) — OOP
Two separate session namespaces via `SessionGuard` interface:
- **Admin guard:** `Auth::admin()` → `AdminGuard` → `$_SESSION['admin_id']` → references `users` table
- **Customer guard:** `Auth::customer()` → `CustomerGuard` → `$_SESSION['customer_id']` → references `customers` table
- Both can be logged in simultaneously without conflict
- Polymorphism: same `SessionGuard` interface, different redirect URLs

### Database Access — OOP
- **`Database::getInstance()`** — Singleton PDO wrapper
- **`BaseService`** — Abstract class with protected `fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()`
- All service classes extend `BaseService` and use `$this->fetch()` etc.
- **PDO prepared statements only** — never concatenate user input into queries
- No ORM — raw SQL with prepared params

### Template System
- PHP include-based templates (no Blade equivalent)
- Layout files with `$content` variable or `include` for sections
- Keep logic in separate files from presentation where practical

### Middleware Pattern — OOP
```php
// Protect admin routes
Auth::admin()->requireAuth();

// Protect customer routes
Auth::customer()->requireAuth();

// Super admin only
Auth::admin()->requireSuperAdmin();
    }
}
```

## Database Schema

Shared database with Laravel version. 17 tables total. **Core business tables:**

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | Admin accounts (web guard) | id, name, email, password, phone |
| `customers` | Customer accounts (customer guard) | id, name, email, password, phone, address, organization, deleted_at |
| `batches` | Pre-order batch periods | id, name, event_name, event_date, status(enum), deleted_at |
| `products` | Product catalog | id, name, sku, price(decimal 15,2), stock, is_active, deleted_at |
| `orders` | Transaction header | id, customer_id(FK), batch_id(FK), order_number, pickup_code, status(enum), total_amount, profit, picked_up_at, deleted_at |
| `order_product` | Order line items (pivot) | id, order_id(FK), product_id(FK), quantity, unit_price, subtotal |

**Enum values:**
- `batches.status`: open, processing, ready, closed
- `orders.status`: pending, processing, ready, picked_up

**Auth seed data (from Laravel):**
- Super Admin: `superadmin@tefa.polije.ac.id` / `password`
- Teknisi: `teknisi@tefa.polije.ac.id` / `password`
- Customer: `customer@customer.com` / `customer`

**Passwords are bcrypt hashed.** Use `password_verify()` for login checks.

## Features to Implement

### Completed

| Phase | Description | Owner |
|-------|-------------|-------|
| 1.1 | Query helpers (`includes/functions.php`) | Ivan |
| 1.2 | Session + Auth (`includes/auth.php`) | Ivan |
| 1.3 | Layout system (header/footer admin + customer) | Ivan |
| 1.4 | CSRF token | Ivan |
| 1.5 | Flash message | Ivan |
| 2.1 | Admin login page (`auth/login-admin.php`) | Alif |
| 2.2 | Customer login page (`auth/login-customer.php`) | Alif |
| 2.3 | Customer registration (`auth/register.php`) | Alif |
| 3.1–3.5 | Landing page (hero, catalog, batch, SNI, footer) | Alif |
| 3.6–3.7 | Landing page dynamic data (products & batches from DB) | Ivan |
| 5.0 | Dashboard sparkline charts (Chart.js CDN) | Ivan |
| 5.1 | Customer dashboard (UI) | Alif |
| 5.1 | Customer dashboard (wired to DB) | Ivan |
| 5.2 | Customer preorder submit (wired to DB) | Ivan |
| 5.3 | Customer order history (wired to DB) | Ivan |
| 5.4 | Customer edit order (wired to DB) | Ivan |
| 5.5 | Customer profile (wired to DB) | Ivan |
| 5.2 | Customer preorder form (UI) | Alif |
| 5.3 | Customer order history (UI) | Alif |
| 5.4 | Customer edit order (UI) | Alif |
| 5.5 | Customer profile edit (UI) | Alif |
| 4.1 | Admin dashboard (UI) | Alif |
| 4.2 | Admin CRUD Products (UI) | Alif |
| 4.3 | Admin CRUD Batches (UI) | Alif |
| 4.4 | Admin CRUD Customers (UI) | Alif |
| 4.5 | Admin CRUD Orders (UI) | Alif |
| 4.6 | Admin User management (UI) | Alif |
| 4.7 | Admin Activity log (UI) | Alif |

### Remaining

#### Priority 4 — Admin Panel

| Phase | Description | Owner | Status |
|-------|-------------|-------|--------|
| 4.1 | Dashboard | Alif → Ivan | [x] |
| 4.2 | CRUD Products (price protection) | Alif → Ivan | [x] |
| 4.3 | CRUD Batches (status lifecycle) | Alif → Ivan | [x] |
| 4.4 | CRUD Customers | Alif → Ivan | [x] |
| 4.5 | CRUD Orders (status, pickup validation) | Alif → Ivan | [x] |
| 4.6 | User management (super_admin only) | Ivan | [x] |
| 4.7 | Activity log viewer (super_admin only) | Ivan | [x] |

#### Priority 5 — Customer Panel (Backend Wiring)

| Phase | Description | Owner | Status |
|-------|-------------|-------|--------|
| 5.1 | Dashboard data from DB | Ivan | [x] |
| 5.2 | Pre-order submit + price from DB | Ivan | [x] |
| 5.3 | Order history data + edit/delete | Ivan | [x] |
| 5.4 | Edit order (pending only, batch locked) | Ivan | [x] |
| 5.5 | Profile update + active order lock | Ivan | [x] |
| 5.6 | Download PDF per order | Ivan | [x] |

#### Priority 6 — Services

| Phase | Description | Owner | Status |
|-------|-------------|-------|--------|
| 6.1 | FonnteService (3 WhatsApp triggers) | Ivan | [ ] |
| 6.2 | PDF generation (DomPDF) | Ivan | [x] |

#### Priority 7 — Security

| Phase | Description | Owner | Status |
|-------|-------------|-------|--------|
| 7.1 | XSS prevention (htmlspecialchars on output) | Ivan | [x] |
| 7.2 | Role-based access control (super_admin vs teknisi) | Ivan | [x] |
| 7.3 | Product price protection | Ivan | [x] |
| 7.4 | Core product deletion protection (3 SKUs) | Ivan | [x] |
| 7.5 | Stock management (deduct on order, return on cancel/delete) | Ivan | [x] |
| 7.6 | Profit = total_amount of picked_up orders | Ivan | [x] |

**Owner legend:** `Ivan` = backend, `Alif` = frontend UI, `Alif → Ivan` = Alif buat UI dulu, Ivan wiring ke DB.

## Coding Conventions

1. **Language:** UI labels in Bahasa Indonesia. Code comments and variables in English.
2. **No framework patterns:** Don't try to rebuild Laravel. Keep it simple PHP.
3. **Security first:** PDO prepared statements always. `htmlspecialchars()` on all user-facing output.
4. **Guard awareness:** Admin uses `$_SESSION['admin_id']`, customer uses `$_SESSION['customer_id']`.
5. **Password:** Always `password_hash()` and `password_verify()`. Never store plaintext.
6. **Price from DB:** Never trust form-submitted prices. Always lookup from `products` table.
7. **Soft deletes:** Use `WHERE deleted_at IS NULL` in all queries on business tables.
8. **Red theme:** Use `#E02424` (primary), `#F05252` (accent), `#9B1C1C` (dark). Check Laravel version for UI reference.
9. **Stock management:** Stock deducted at order creation (atomic `stock - ? WHERE stock >= ?`). Returned on cancel/delete/edit. Products with 0 stock cannot be ordered.
10. **Profit calculation:** Omzet = `SUM(total_amount)` all orders. Profit = `SUM(total_amount)` where `status = 'picked_up'`.

## Role-Based Access

| Role | Capabilities |
|------|-------------|
| **super_admin** | Full access: edit prices, view financial data, manage users, view audit log, force delete |
| **teknisi** | Operational: update batch status, view production metrics, validate pickup. NO financial data, NO price editing |

Role stored in `model_has_roles` table. Query to check:
```sql
SELECT r.name FROM roles r
JOIN model_has_roles mhr ON mhr.role_id = r.id
WHERE mhr.model_type = 'App\Models\User' AND mhr.model_id = ?
```

## Notification Triggers (Fonnte API)

**3 triggers:**
1. Admin creates order → WhatsApp to customer (order details + pickup code)
2. Batch → "ready" → WhatsApp to all customers in batch
3. Customer submits pre-order → WhatsApp to all super_admin users

**Config from .env:** `FONNTE_TOKEN`, `FONNTE_DEVICE`

## Key Differences from Laravel Version

| Aspect | Laravel Version | PHP Native Version |
|--------|----------------|-------------------|
| Routing | Laravel Router | index.php + .htaccess |
| Auth | Laravel Auth (2 guards) | Manual sessions |
| DB | Eloquent ORM | PDO raw queries |
| Admin UI | FilamentPHP v3 | Custom HTML + Tailwind |
| Templates | Blade | PHP include |
| CSRF | @csrf token | Manual token |
| Activity Log | Spatie package | Manual DB inserts |

## Development Server

```bash
php -S localhost:8000
```

No `.htaccess`. No clean URLs. Direct file access only (`/auth/login-admin.php`, `/admin/dashboard.php`, etc.).

## Reference

When implementing features, reference the Laravel version at `../tefa-canning-system/`:
- Business logic: `app/Models/`, `app/Services/`, `app/Filament/`
- UI structure: `resources/views/`
- Database schema: `database/migrations/`
- Business rules: `../tefa-canning-system/CLAUDE.md`
