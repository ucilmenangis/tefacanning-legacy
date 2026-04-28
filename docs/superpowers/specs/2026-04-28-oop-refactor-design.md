# OOP Refactoring Design Spec

**Date:** 2026-04-28
**Status:** Approved
**Approach:** Interface-Driven (Approach A)

## Goal

Refactor procedural code in `functions.php` and `auth.php` into proper OOP classes. Extend refactoring to service classes via a shared base class. Clean break — no backward-compatible wrappers.

## Scope

- **Core:** `includes/functions.php` (6 db functions) + `includes/auth.php` (21 functions)
- **Services:** All 9 classes in `classes/` extend new `BaseService`
- **Call sites:** ~250 references across 24 page files updated
- **Delete:** `config/database.php` (dead file, zero usages)

## OOP Concepts Demonstrated

| Concept | Implementation |
|---------|---------------|
| Encapsulation | `Database` (private $pdo), guards (private const SESSION_KEY) |
| Inheritance | `BaseService` -> all 9 service classes |
| Interface | `SessionGuard` with `isLoggedIn()`, `getId()`, `login()`, `logout()`, `requireAuth()` |
| Polymorphism | `AdminGuard` vs `CustomerGuard` — same interface, different redirect URLs and session keys |
| Abstract class | `BaseService` with protected db wrappers |
| Exception handling | `AppException` hierarchy with 3 subtypes |
| Singleton pattern | `Database::getInstance()` with clone/wakeup prevention |
| Static methods | `CsrfService`, `FlashMessage`, `Auth` facade |

## New Files to Create

### `classes/Database.php` — Singleton PDO wrapper

```php
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct() {
        // Load .env via vlucas/phpdotenv
        // Create PDO with config from $_ENV
        // Set ATTR_ERRMODE => ERRMODE_EXCEPTION, ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC
    }
    private function __clone() {}
    public function __wakeup(): void { throw new \Exception("Cannot unserialize singleton"); }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetch(string $sql, array $params = []): ?array
    public function fetchAll(string $sql, array $params = []): array
    public function insert(string $sql, array $params = []): int
    public function update(string $sql, array $params = []): int
    public function delete(string $sql, array $params = []): int
}
```

Replaces: `db()`, `db_fetch()`, `db_fetch_all()`, `db_insert()`, `db_update()`, `db_delete()` from `functions.php`.

### `classes/exceptions/AppException.php`

```php
class AppException extends \Exception {}
```

### `classes/exceptions/DatabaseException.php`

```php
class DatabaseException extends AppException {}
```

### `classes/exceptions/AuthException.php`

```php
class AuthException extends AppException {}
```

### `classes/exceptions/CsrfException.php`

```php
class CsrfException extends AppException {}
```

### `classes/SessionGuard.php` — Interface

```php
interface SessionGuard
{
    public function isLoggedIn(): bool;
    public function getId(): ?int;
    public function login(int $id): void;
    public function logout(): void;
    public function requireAuth(): void;
}
```

### `classes/AdminGuard.php`

```php
class AdminGuard implements SessionGuard
{
    private const SESSION_KEY = 'admin_id';
    private const ROLE_KEY = 'admin_role';

    public function isLoggedIn(): bool;
    public function getId(): ?int;
    public function login(int $id): void;      // set session + cache role via AdminService
    public function logout(): void;             // unset session keys
    public function requireAuth(): void;        // redirect to /auth/login-admin.php

    // Admin-specific (not in interface)
    public function isSuperAdmin(): bool;       // check cached role, DB fallback
    public function requireSuperAdmin(): void;  // redirect if not super_admin
    public function getRole(): ?string;
}
```

Replaces: `requireAdmin()`, `getAdminId()`, `isAdminLoggedIn()`, `loginAdmin()`, `logoutAdmin()`, `isSuperAdmin()`, `requireSuperAdmin()`, `getAdminRole()`.

### `classes/CustomerGuard.php`

```php
class CustomerGuard implements SessionGuard
{
    private const SESSION_KEY = 'customer_id';

    public function isLoggedIn(): bool;
    public function getId(): ?int;
    public function login(int $id): void;
    public function logout(): void;
    public function requireAuth(): void;        // redirect to /auth/login-customer.php
}
```

Replaces: `requireCustomer()`, `getCustomerId()`, `isCustomerLoggedIn()`, `loginCustomer()`, `logoutCustomer()`.

### `classes/Auth.php` — Facade

```php
class Auth
{
    private static ?AdminGuard $admin = null;
    private static ?CustomerGuard $customer = null;

    public static function admin(): AdminGuard
    public static function customer(): CustomerGuard

    public static function startSession(): void  // moved from startSession()
}
```

Usage: `Auth::admin()->requireAuth()`, `Auth::customer()->getId()`, etc.

### `classes/CsrfService.php`

```php
class CsrfService
{
    private const SESSION_KEY = 'csrf_token';

    public static function generate(): string;
    public static function verify(): bool;
    public static function field(): string;      // returns <input type="hidden" name="csrf_token" value="...">
}
```

Replaces: `generateCsrfToken()`, `verifyCsrf()`, `csrfField()`.

### `classes/FlashMessage.php`

```php
class FlashMessage
{
    private const SESSION_KEY = 'flash';

    public static function set(string $type, string $message): void;
    public static function get(): ?array;
    public static function render(): string;
    public static function has(): bool;
}
```

Replaces: `setFlash()`, `getFlash()`, `renderFlash()`.

### `classes/BaseService.php` — Abstract base

```php
abstract class BaseService
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function fetch(string $sql, array $params = []): ?array
    protected function fetchAll(string $sql, array $params = []): array
    protected function insert(string $sql, array $params = []): int
    protected function update(string $sql, array $params = []): int
    protected function delete(string $sql, array $params = []): int
}
```

All 9 existing service classes extend `BaseService` and replace `db_fetch()` calls with `$this->fetch()`.

## Existing Files to Modify

### Service Classes (9 files)

| File | Change |
|------|--------|
| `classes/AdminService.php` | `extends BaseService`, `db_fetch()` -> `$this->fetch()`, `db_fetch_all()` -> `$this->fetchAll()`, `db_insert()` -> `$this->insert()`, `isSuperAdmin()` -> `Auth::admin()->isSuperAdmin()` |
| `classes/OrderService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/CustomerService.php` | `extends BaseService`, replace all `db_*` calls, `getCustomerId()` -> `Auth::customer()->getId()` |
| `classes/ProductService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/BatchService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/CustomerAdminService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/ActivityLogService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/PdfService.php` | `extends BaseService`, replace all `db_*` calls |
| `classes/FormatHelper.php` | Unchanged (static helpers, no DB access) |

### Page Files (24 files) — Call Site Updates

**Admin pages (15):**
- All change `requireAdmin()` -> `Auth::admin()->requireAuth()`
- All change `verifyCsrf()` -> `CsrfService::verify()`
- All change `csrfField()` -> `CsrfService::field()`
- All change `setFlash()` -> `FlashMessage::set()`
- All change `renderFlash()` -> `FlashMessage::render()`
- Super-admin pages change `requireSuperAdmin()` -> `Auth::admin()->requireSuperAdmin()`
- Dashboard changes `db_fetch_all()` -> `Database::getInstance()->fetchAll()`

**Customer pages (4):**
- All change `requireCustomer()` -> `Auth::customer()->requireAuth()`
- Same CSRF/flash/db replacements

**Auth pages (5):**
- `login-admin.php`: `loginAdmin()` -> `Auth::admin()->login()`
- `login-customer.php`: `loginCustomer()` -> `Auth::customer()->login()`
- `logout.php`: `logoutAdmin()` / `logoutCustomer()` -> `Auth::admin()->logout()` / `Auth::customer()->logout()`
- `register.php`: `loginCustomer()` -> `Auth::customer()->login()`

**API (1):**
- `api/download-pdf.php`: auth checks + db calls updated

**Includes (2):**
- `includes/functions.php`: gutted — becomes minimal bootstrap that loads composer autoload + new classes
- `includes/auth.php`: gutted — becomes `require_once` for new auth classes

### Include Headers (2 files)

- `includes/header-admin.php`: `renderFlash()` -> `FlashMessage::render()`, `isSuperAdmin()` -> `Auth::admin()->isSuperAdmin()`
- `includes/header-customer.php`: same pattern

## File to Delete

- `config/database.php` — dead file, zero usages

## Migration Mapping

### Database Functions

| Old (Procedural) | New (OOP) | In Services |
|------------------|-----------|-------------|
| `db_fetch($sql, $params)` | `Database::getInstance()->fetch($sql, $params)` | `$this->fetch($sql, $params)` |
| `db_fetch_all($sql, $params)` | `Database::getInstance()->fetchAll($sql, $params)` | `$this->fetchAll($sql, $params)` |
| `db_insert($sql, $params)` | `Database::getInstance()->insert($sql, $params)` | `$this->insert($sql, $params)` |
| `db_update($sql, $params)` | `Database::getInstance()->update($sql, $params)` | `$this->update($sql, $params)` |
| `db_delete($sql, $params)` | `Database::getInstance()->delete($sql, $params)` | `$this->delete($sql, $params)` |

### Auth Functions

| Old (Procedural) | New (OOP) |
|------------------|-----------|
| `requireAdmin()` | `Auth::admin()->requireAuth()` |
| `requireCustomer()` | `Auth::customer()->requireAuth()` |
| `requireSuperAdmin()` | `Auth::admin()->requireSuperAdmin()` |
| `getAdminId()` | `Auth::admin()->getId()` |
| `getCustomerId()` | `Auth::customer()->getId()` |
| `isAdminLoggedIn()` | `Auth::admin()->isLoggedIn()` |
| `isCustomerLoggedIn()` | `Auth::customer()->isLoggedIn()` |
| `isSuperAdmin()` | `Auth::admin()->isSuperAdmin()` |
| `getAdminRole()` | `Auth::admin()->getRole()` |
| `loginAdmin($id)` | `Auth::admin()->login($id)` |
| `loginCustomer($id)` | `Auth::customer()->login($id)` |
| `logoutAdmin()` | `Auth::admin()->logout()` |
| `logoutCustomer()` | `Auth::customer()->logout()` |
| `startSession()` | `Auth::startSession()` |

### CSRF + Flash

| Old (Procedural) | New (OOP) |
|------------------|-----------|
| `verifyCsrf()` | `CsrfService::verify()` |
| `csrfField()` | `CsrfService::field()` |
| `generateCsrfToken()` | `CsrfService::generate()` |
| `setFlash($type, $msg)` | `FlashMessage::set($type, $msg)` |
| `getFlash()` | `FlashMessage::get()` |
| `renderFlash()` | `FlashMessage::render()` |

## Implementation Order

1. Create `classes/Database.php` (no dependencies)
2. Create `classes/exceptions/` (no dependencies)
3. Create `classes/CsrfService.php` (session only)
4. Create `classes/FlashMessage.php` (session only)
5. Create `classes/SessionGuard.php` interface
6. Create `classes/AdminGuard.php` + `classes/CustomerGuard.php`
7. Create `classes/Auth.php` facade
8. Create `classes/BaseService.php` abstract
9. Update all 9 service classes to extend `BaseService`
10. Update all 24 page files + includes to use new classes
11. Gut `includes/functions.php` and `includes/auth.php`
12. Delete `config/database.php`
13. Test all pages (admin dashboard, customer dashboard, auth flow, CRUD operations)

## Constraints

- No new features — behavior stays identical
- Big bang — no backward-compatible wrappers
- `$_SESSION` still used directly inside guard classes (PHP has no session abstraction)
- Service constructor pattern: `new ServiceClass()` auto-gets DB via `BaseService::__construct()`
- FormatHelper stays unchanged (static helpers, no DB)
