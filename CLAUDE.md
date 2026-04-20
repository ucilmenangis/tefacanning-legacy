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
- **Composer** — Only `vlucas/phpdotenv` (env loader)
- **DomPDF** — PDF report generation (to be added via composer)

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

**What exists:** Landing page only (`index.php`). All other dirs are empty shells.

**Implemented:**
- `index.php` — Full landing page (hero, product catalog, batch info, SNI disclaimer, about, footer with Google Maps)
- `config/database.php` — PDO connection
- `.env` / `.env.example` — Environment config
- `assets/images/` — 8 image assets

**Not yet built:** Layout system, CSRF, admin panel, customer panel, services.

## Project Structure

```
tefa-canning-legacy/
├── index.php                ← Landing page (NOT a router yet)
├── .env                     ← Environment config (DB, Fonnte token)
├── config/
│   └── database.php         ← PDO connection
├── includes/
│   └── functions.php        ← Empty — to be filled
├── admin/                   ← Empty — Admin panel pages
├── customer/                ← Empty — Customer panel pages
├── auth/                    ← Empty — Login/register pages
├── assets/
│   ├── css/                 ← Empty (using CDN)
│   ├── js/                  ← Empty
│   └── images/              ← Static images (8 files)
├── database/                ← Empty — SQL dump to be added
├── services/                ← To be created (Fonnte, PDF)
└── views/                   ← To be created (layouts, components)
```

## Architecture Decisions

### Routing
- **Direct file access** — no router, no `.htaccess`. URLs like `/admin/dashboard.php`, `/auth/login-admin.php`.
- `index.php` serves as landing page only (not a router).
- Each page includes shared files (`includes/auth.php`, `includes/functions.php`) at the top.

### Authentication (Dual Guard)
Two separate session namespaces, mirroring Laravel's dual guard:
- **Admin guard:** `$_SESSION['admin_id']` → references `users` table
- **Customer guard:** `$_SESSION['customer_id']` → references `customers` table
- Both can be logged in simultaneously without conflict

### Database Access
- **PDO prepared statements only** — never concatenate user input into queries
- Simple query helper functions in `includes/functions.php`
- No ORM — raw SQL with prepared params

### Template System
- PHP include-based templates (no Blade equivalent)
- Layout files with `$content` variable or `include` for sections
- Keep logic in separate files from presentation where practical

### Middleware Pattern
```php
// Protect admin routes
function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /auth/login-admin.php');
        exit;
    }
}

// Protect customer routes
function requireCustomer() {
    if (!isset($_SESSION['customer_id'])) {
        header('Location: /auth/login-customer.php');
        exit;
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
- Customer: `customer_1@customer.com` through `customer_50@customer.com` / `password`

**Passwords are bcrypt hashed.** Use `password_verify()` for login checks.

## Features to Implement (Full Conversion)

### Priority 1 — Core Infrastructure (Backend)
- [x] Phase 1.1 — Query helpers (`includes/functions.php`)
- [x] Phase 1.2 — Session + Auth (`includes/auth.php`)
- [x] Phase 1.3 — Layout system (`includes/header-admin.php`, `header-customer.php`, footer files)
- [x] Phase 1.4 — CSRF token (security basic, needed before forms)
- [x] Phase 1.5 — Flash message (success/error feedback after actions)

### Priority 2 — Authentication Pages
- [ ] Phase 2.1 — Admin login page (`auth/login-admin.php`)
- [ ] Phase 2.2 — Customer login page (`auth/login-customer.php`)
- [ ] Phase 2.3 — Customer registration (`auth/register.php`)

### Priority 3 — Landing Page (Dynamic)
- [x] Phase 3.1 — Hero section
- [x] Phase 3.2 — Product catalog (hardcoded — needs DB)
- [x] Phase 3.3 — Batch info (hardcoded — needs DB)
- [x] Phase 3.4 — SNI disclaimer
- [x] Phase 3.5 — Footer with Google Maps
- [ ] Phase 3.6 — Products from DB (replace hardcoded)
- [ ] Phase 3.7 — Batches from DB (replace hardcoded)

### Priority 4 — Admin Panel
- [ ] Phase 4.1 — Dashboard (Alif: UI, Ivan: data)
- [ ] Phase 4.2 — CRUD Products (price protection for non-super_admin)
- [ ] Phase 4.3 — CRUD Batches (status lifecycle)
- [ ] Phase 4.4 — CRUD Customers
- [ ] Phase 4.5 — CRUD Orders (status management, pickup validation)
- [ ] Phase 4.6 — User management (super_admin only)
- [ ] Phase 4.7 — Activity log viewer (super_admin only)

### Priority 5 — Customer Panel
- [ ] Phase 5.1 — Dashboard (welcome, order summary, latest batch, available products)
- [ ] Phase 5.2 — Pre-order form (batch + product selection, price from DB)
- [ ] Phase 5.3 — Order history (table with edit/delete for pending only)
- [ ] Phase 5.4 — Edit order (pending only, batch locked)
- [ ] Phase 5.5 — Edit profile (with active order lock)
- [ ] Phase 5.6 — Download PDF per order

### Priority 6 — Services
- [ ] Phase 6.1 — FonnteService (3 WhatsApp notification triggers)
- [ ] Phase 6.2 — PDF generation (DomPDF)

### Priority 7 — Security
- [ ] Phase 7.1 — XSS prevention (htmlspecialchars on output)
- [ ] Phase 7.2 — Role-based access control (super_admin vs teknisi)
- [ ] Phase 7.3 — Product price protection
- [ ] Phase 7.4 — Core product deletion protection (3 SKUs)

## Coding Conventions

1. **Language:** UI labels in Bahasa Indonesia. Code comments and variables in English.
2. **No framework patterns:** Don't try to rebuild Laravel. Keep it simple PHP.
3. **Security first:** PDO prepared statements always. `htmlspecialchars()` on all user-facing output.
4. **Guard awareness:** Admin uses `$_SESSION['admin_id']`, customer uses `$_SESSION['customer_id']`.
5. **Password:** Always `password_hash()` and `password_verify()`. Never store plaintext.
6. **Price from DB:** Never trust form-submitted prices. Always lookup from `products` table.
7. **Soft deletes:** Use `WHERE deleted_at IS NULL` in all queries on business tables.
8. **Red theme:** Use `#E02424` (primary), `#F05252` (accent), `#9B1C1C` (dark). Check Laravel version for UI reference.

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
