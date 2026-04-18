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

1. **Use `$variables` for dynamic data.** Don't hardcode values in HTML body. Put data at top of file:
   ```php
   <?php
   // Data section (Ivan will wire this to DB later)
   $products = [
       ['name' => 'Sarden Kaleng', 'price' => 25000],
   ];
   ?>
   <!-- HTML section (Alif builds this) -->
   ```
2. **Don't build auth/login logic.** Build login forms only (HTML + Tailwind). Backend will handle session, validation, password check.
3. **Include auth guard at top** of protected pages:
   ```php
   <?php require_once __DIR__ . '/../includes/auth.php'; requireAdmin(); ?>
   ```
4. **Follow existing file naming:** `admin/dashboard.php`, `admin/products.php`, `customer/orders.php`, etc.
5. **Use shared layout system** when available (Phase 1.5). Don't duplicate `<head>`, navbar, footer in every page.
6. **Same color theme:** primary `#E02424`, accent `#F05252`, dark `#9B1C1C`, navy `#111827`.
7. **Don't modify files in:** `config/`, `includes/`, `services/`. Those are backend-only.
8. **Commit message format:** `feat: [page/feature description]` or `fix: [what fixed]`.

## Current Implementation Status

**What exists:** Landing page only (`index.php`). All other dirs are empty shells.

**Implemented:**
- `index.php` — Full landing page (hero, product catalog, batch info, SNI disclaimer, about, footer with Google Maps)
- `config/database.php` — PDO connection
- `.env` / `.env.example` — Environment config
- `assets/images/` — 8 image assets

**Not yet built:** Routing, auth, admin panel, customer panel, query helpers, services, views/layouts.

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
- **Simple file-based routing** through `index.php` with `$_GET['page']` or `$_SERVER['REQUEST_URI']`
- No complex router library — keep it simple
- Route groups: `/admin/*`, `/customer/*`, `/auth/*`, `/` (public)

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

### Priority 1 — Core Infrastructure
- [ ] Router + .htaccess (clean URLs)
- [ ] Session management (dual guard)
- [ ] Auth helpers (login, logout, requireAuth)
- [ ] Query helper functions (PDO wrapper)
- [ ] View/layout system

### Priority 2 — Authentication Pages
- [ ] Admin login page
- [ ] Customer login page
- [ ] Customer registration page

### Priority 3 — Landing Page (Public)
- [x] Hero section
- [x] Product catalog (3 products — currently hardcoded, needs DB)
- [x] Active batch info (currently hardcoded, needs DB)
- [x] SNI disclaimer
- [x] Footer with Google Maps

### Priority 4 — Admin Panel
- [ ] Dashboard (stats widgets: revenue, orders, production summary)
- [ ] CRUD Products (price protection for non-super_admin)
- [ ] CRUD Batches (status lifecycle)
- [ ] CRUD Customers
- [ ] CRUD Orders (status management, pickup validation)
- [ ] User management (super_admin only)
- [ ] Activity log viewer (super_admin only)

### Priority 5 — Customer Panel
- [ ] Dashboard (4 widgets: welcome, order summary, latest batch, available products)
- [ ] Pre-order form (batch + product selection, price from DB)
- [ ] Order history (table with edit/delete for pending only)
- [ ] Edit order (pending only, batch locked)
- [ ] Edit profile (with active order lock)
- [ ] Download PDF per order

### Priority 6 — Services
- [ ] FonnteService (3 notification triggers)
- [ ] PDF report generation (DomPDF)

### Priority 7 — Security
- [ ] CSRF token on all forms
- [ ] XSS prevention (htmlspecialchars on output)
- [ ] Role-based access control (super_admin vs teknisi)
- [ ] Product price protection
- [ ] Core product deletion protection (3 SKUs)

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

No `.htaccess` yet. No clean URLs. Direct file access only (`/auth/login-admin.php`, `/admin/dashboard.php`, etc.).

## Reference

When implementing features, reference the Laravel version at `../tefa-canning-system/`:
- Business logic: `app/Models/`, `app/Services/`, `app/Filament/`
- UI structure: `resources/views/`
- Database schema: `database/migrations/`
- Business rules: `../tefa-canning-system/CLAUDE.md`
