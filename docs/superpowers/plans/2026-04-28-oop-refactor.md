# OOP Refactoring Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace all procedural functions (functions.php + auth.php) with OOP classes, update all service classes to extend BaseService.

**Architecture:** Interface-driven OOP — Database singleton, SessionGuard interface with AdminGuard/CustomerGuard polymorphism, abstract BaseService for inheritance, custom exception hierarchy.

**Tech Stack:** PHP 8.3, PDO MySQL, existing composer dependencies (vlucas/phpdotenv, dompdf/dompdf). No new dependencies.

**Spec:** `docs/superpowers/specs/2026-04-28-oop-refactor-design.md`

---

### Task 1: Create Exception Classes

**Files:**
- Create: `classes/exceptions/AppException.php`
- Create: `classes/exceptions/DatabaseException.php`
- Create: `classes/exceptions/AuthException.php`
- Create: `classes/exceptions/CsrfException.php`

- [ ] **Step 1: Create exception files**

```php
// classes/exceptions/AppException.php
<?php
class AppException extends \Exception {}
```

```php
// classes/exceptions/DatabaseException.php
<?php
class DatabaseException extends AppException {}
```

```php
// classes/exceptions/AuthException.php
<?php
class AuthException extends AppException {}
```

```php
// classes/exceptions/CsrfException.php
<?php
class CsrfException extends AppException {}
```

- [ ] **Step 2: Commit**

```bash
git add classes/exceptions/ && git commit -m "feat: add custom exception hierarchy for OOP refactor"
```

---

### Task 2: Create Database Singleton

**Files:**
- Create: `classes/Database.php`

- [ ] **Step 1: Create Database class**

Port logic from `includes/functions.php` `db()` function (lines 19-44). Same connection params, same singleton pattern but as proper OOP.

```php
<?php

/**
 * Database — Singleton PDO wrapper.
 *
 * Usage:
 *   $db = Database::getInstance();
 *   $row = $db->fetch("SELECT * FROM users WHERE id = ?", [1]);
 *   $rows = $db->fetchAll("SELECT * FROM products");
 *   $id = $db->insert("INSERT INTO products (name) VALUES (?)", ["Sarden"]);
 */
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        require_once __DIR__ . '/../vendor/autoload.php';

        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $database = $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;port=$port;dbname=$database",
                $username,
                $password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DatabaseException("Database connection failed: " . $e->getMessage());
        }
    }

    private function __clone() {}

    public function __wakeup(): void
    {
        throw new AuthException("Cannot unserialize singleton");
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function insert(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get raw PDO instance for direct queries (e.g. prepare + execute patterns).
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add classes/Database.php && git commit -m "feat: add Database singleton class replacing db() procedural functions"
```

---

### Task 3: Create CsrfService + FlashMessage

**Files:**
- Create: `classes/CsrfService.php`
- Create: `classes/FlashMessage.php`

- [ ] **Step 1: Create CsrfService**

Port from `includes/auth.php` lines 197-234.

```php
<?php

/**
 * CsrfService — CSRF token generation and verification.
 *
 * Usage:
 *   echo CsrfService::field();          // in <form>
 *   if (!CsrfService::verify()) { ... }  // on POST
 */
class CsrfService
{
    private const SESSION_KEY = 'csrf_token';

    public static function generate(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;
        return $token;
    }

    public static function verify(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_POST['csrf_token'] ?? '';

        if (empty($token) || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        if (!hash_equals($_SESSION[self::SESSION_KEY], $token)) {
            return false;
        }

        unset($_SESSION[self::SESSION_KEY]);
        return true;
    }

    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
```

- [ ] **Step 2: Create FlashMessage**

Port from `includes/auth.php` lines 248-309.

```php
<?php

/**
 * FlashMessage — one-time session messages.
 *
 * Usage:
 *   FlashMessage::set('success', 'Saved!');
 *   echo FlashMessage::render();
 */
class FlashMessage
{
    private const SESSION_KEY = 'flash';

    public static function set(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION[self::SESSION_KEY] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function get(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return null;
        }
        $flash = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);
        return $flash;
    }

    public static function render(): string
    {
        $flash = self::get();
        if (!$flash) return '';

        $type = $flash['type'];
        $message = htmlspecialchars($flash['message']);

        $colors = [
            'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'error'   => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
            'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
        ];

        $icons = [
            'success' => 'ph-check-circle',
            'error'   => 'ph-x-circle',
            'warning' => 'ph-warning',
            'info'    => 'ph-info',
        ];

        $color = $colors[$type] ?? $colors['info'];
        $icon = $icons[$type] ?? $icons['info'];

        return '<div class="mb-5 rounded-xl border px-5 py-4 flex items-center gap-3 ' . $color . '">'
             . '<i class="ph-bold ' . $icon . ' text-lg flex-shrink-0"></i>'
             . '<span class="text-[13px] font-medium">' . $message . '</span>'
             . '</div>';
    }

    public static function has(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION[self::SESSION_KEY]);
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add classes/CsrfService.php classes/FlashMessage.php && git commit -m "feat: add CsrfService and FlashMessage classes"
```

---

### Task 4: Create SessionGuard Interface + Guard Classes + Auth Facade

**Files:**
- Create: `classes/SessionGuard.php`
- Create: `classes/AdminGuard.php`
- Create: `classes/CustomerGuard.php`
- Create: `classes/Auth.php`

- [ ] **Step 1: Create SessionGuard interface**

```php
<?php

/**
 * SessionGuard — interface for authentication guards.
 * Implemented by AdminGuard and CustomerGuard (polymorphism).
 */
interface SessionGuard
{
    public function isLoggedIn(): bool;
    public function getId(): ?int;
    public function login(int $id): void;
    public function logout(): void;
    public function requireAuth(): void;
}
```

- [ ] **Step 2: Create AdminGuard**

Port from `includes/auth.php` admin functions (lines 40-140). Admin-specific methods (`isSuperAdmin`, `requireSuperAdmin`, `getRole`) added beyond interface.

```php
<?php

/**
 * AdminGuard — admin authentication (implements SessionGuard).
 * Uses $_SESSION['admin_id'] and $_SESSION['admin_role'].
 */
class AdminGuard implements SessionGuard
{
    private const SESSION_KEY = 'admin_id';
    private const ROLE_KEY = 'admin_role';

    public function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public function getId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public function login(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $id;

        // Cache role in session to avoid DB query on every page
        require_once __DIR__ . '/Database.php';
        require_once __DIR__ . '/BaseService.php';
        require_once __DIR__ . '/AdminService.php';
        $adminService = new AdminService();
        $_SESSION[self::ROLE_KEY] = $adminService->getRole($id);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION[self::SESSION_KEY]);
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /auth/login-admin.php');
            exit;
        }
    }

    // ── Admin-specific methods (not in interface) ──

    public function getRole(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION[self::ROLE_KEY] ?? null;
    }

    public function isSuperAdmin(): bool
    {
        $role = $this->getRole();
        if ($role !== null) {
            return $role === 'super_admin';
        }

        // Fallback: query DB if role not in session
        $adminId = $this->getId();
        if (!$adminId) return false;

        require_once __DIR__ . '/Database.php';
        require_once __DIR__ . '/BaseService.php';
        require_once __DIR__ . '/AdminService.php';
        $adminService = new AdminService();
        $role = $adminService->getRole($adminId);

        $_SESSION[self::ROLE_KEY] = $role;
        return $role === 'super_admin';
    }

    public function requireSuperAdmin(): void
    {
        if (!$this->isSuperAdmin()) {
            require_once __DIR__ . '/FlashMessage.php';
            FlashMessage::set('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            header('Location: dashboard.php');
            exit;
        }
    }
}
```

- [ ] **Step 3: Create CustomerGuard**

Port from `includes/auth.php` customer functions (lines 147-190).

```php
<?php

/**
 * CustomerGuard — customer authentication (implements SessionGuard).
 * Uses $_SESSION['customer_id'].
 */
class CustomerGuard implements SessionGuard
{
    private const SESSION_KEY = 'customer_id';

    public function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public function getId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public function login(int $id): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $id;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION[self::SESSION_KEY]);
    }

    public function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            header('Location: /auth/login-customer.php');
            exit;
        }
    }
}
```

- [ ] **Step 4: Create Auth facade**

```php
<?php

/**
 * Auth — facade for accessing authentication guards.
 *
 * Usage:
 *   Auth::startSession();
 *   Auth::admin()->requireAuth();
 *   Auth::customer()->getId();
 */
class Auth
{
    private static ?AdminGuard $admin = null;
    private static ?CustomerGuard $customer = null;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    public static function admin(): AdminGuard
    {
        if (self::$admin === null) {
            self::$admin = new AdminGuard();
        }
        return self::$admin;
    }

    public static function customer(): CustomerGuard
    {
        if (self::$customer === null) {
            self::$customer = new CustomerGuard();
        }
        return self::$customer;
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add classes/SessionGuard.php classes/AdminGuard.php classes/CustomerGuard.php classes/Auth.php && git commit -m "feat: add SessionGuard interface, AdminGuard, CustomerGuard, and Auth facade"
```

---

### Task 5: Create BaseService Abstract Class

**Files:**
- Create: `classes/BaseService.php`

- [ ] **Step 1: Create BaseService**

```php
<?php

/**
 * BaseService — abstract base for all service classes.
 * Provides Database instance and query convenience methods.
 * Subclasses use $this->fetch() instead of db_fetch().
 */
abstract class BaseService
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function fetch(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    protected function insert(string $sql, array $params = []): int
    {
        return $this->db->insert($sql, $params);
    }

    protected function update(string $sql, array $params = []): int
    {
        return $this->db->update($sql, $params);
    }

    protected function delete(string $sql, array $params = []): int
    {
        return $this->db->delete($sql, $params);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add classes/BaseService.php && git commit -m "feat: add BaseService abstract class with DB query wrappers"
```

---

### Task 6: Update All 9 Service Classes to Extend BaseService

**Files:**
- Modify: `classes/AdminService.php`
- Modify: `classes/OrderService.php`
- Modify: `classes/CustomerService.php`
- Modify: `classes/ProductService.php`
- Modify: `classes/BatchService.php`
- Modify: `classes/CustomerAdminService.php`
- Modify: `classes/ActivityLogService.php`
- Modify: `classes/PdfService.php`

**Pattern for all services:**
1. Remove `require_once __DIR__ . '/../includes/functions.php';` (if present)
2. Add `extends BaseService` to class declaration
3. Replace `db_fetch(` → `$this->fetch(`
4. Replace `db_fetch_all(` → `$this->fetchAll(`
5. Replace `db_insert(` → `$this->insert(`
6. Replace `db_update(` → `$this->update(`
7. Replace `db_delete(` → `$this->delete(`
8. Replace `db()->prepare(` → `$this->db->getPdo()->prepare(` (for direct PDO access in CustomerService and OrderService)
9. For AdminService specifically: replace `getAdminId()` → `Auth::admin()->getId()`, replace `setFlash(` → `FlashMessage::set(`
10. Add `require_once` for `Database.php` and `BaseService.php` at top (needed before class extends)

- [ ] **Step 1: Update AdminService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Add requires for Database, BaseService, Auth, FlashMessage
- `class AdminService` → `class AdminService extends BaseService`
- `db_fetch(` → `$this->fetch(` (10 occurrences)
- `db_fetch_all(` → `$this->fetchAll(` (2 occurrences)
- `getAdminId()` → `Auth::admin()->getId()` (2 occurrences: lines 48, 107)
- `setFlash(` → `FlashMessage::set(` (1 occurrence: line 50)

- [ ] **Step 2: Update OrderService.php**

Changes:
- Add requires for Database, BaseService
- `class OrderService` → `class OrderService extends BaseService`
- `db_fetch(` → `$this->fetch(` (6 occurrences)
- `db_fetch_all(` → `$this->fetchAll(` (2 occurrences)
- `db()->prepare(` → `$this->db->getPdo()->prepare(` (1 occurrence: line 84)

- [ ] **Step 3: Update CustomerService.php**

Changes:
- Add requires for Database, BaseService
- `class CustomerService` → `class CustomerService extends BaseService`
- `db_fetch(` → `$this->fetch(` (3 occurrences)
- `db()->prepare(` → `$this->db->getPdo()->prepare(` (2 occurrences: lines 48, 76)

- [ ] **Step 4: Update ProductService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Add requires for Database, BaseService
- `class ProductService` → `class ProductService extends BaseService`
- `db_fetch_all(` → `$this->fetchAll(` (1 occurrence)
- `db_fetch(` → `$this->fetch(` (1 occurrence)
- `db_insert(` → `$this->insert(` (1 occurrence)
- `db_update(` → `$this->update(` (2 occurrences)

- [ ] **Step 5: Update BatchService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Add requires for Database, BaseService
- `class BatchService` → `class BatchService extends BaseService`
- `db_fetch_all(` → `$this->fetchAll(` (1 occurrence)
- `db_fetch(` → `$this->fetch(` (1 occurrence)
- `db_insert(` → `$this->insert(` (1 occurrence)
- `db_update(` → `$this->update(` (1 occurrence)

- [ ] **Step 6: Update CustomerAdminService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Add requires for Database, BaseService
- `class CustomerAdminService` → `class CustomerAdminService extends BaseService`
- Replace all `db_*` calls with `$this->*` equivalents

- [ ] **Step 7: Update ActivityLogService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Add requires for Database, BaseService
- `class ActivityLogService` → `class ActivityLogService extends BaseService`
- Replace all `db_*` calls with `$this->*` equivalents

- [ ] **Step 8: Update PdfService.php**

Changes:
- Remove `require_once __DIR__ . '/../includes/functions.php';`
- Keep `require_once __DIR__ . '/../vendor/autoload.php';` and FormatHelper require
- Add requires for Database, BaseService
- `class PdfService` → `class PdfService extends BaseService`
- `db_fetch(` → `$this->fetch(` (1 occurrence)
- `db_fetch_all(` → `$this->fetchAll(` (1 occurrence)

- [ ] **Step 9: Commit**

```bash
git add classes/AdminService.php classes/OrderService.php classes/CustomerService.php classes/ProductService.php classes/BatchService.php classes/CustomerAdminService.php classes/ActivityLogService.php classes/PdfService.php && git commit -m "refactor: update all service classes to extend BaseService"
```

---

### Task 7: Update Bootstrap Files (auth.php + functions.php + headers)

**Files:**
- Modify: `includes/auth.php`
- Modify: `includes/functions.php`
- Modify: `includes/header-admin.php`
- Modify: `includes/header-customer.php`

- [ ] **Step 1: Rewrite includes/auth.php as bootstrap**

Replace entire content with:

```php
<?php
/**
 * Auth bootstrap — loads OOP classes and starts session.
 *
 * All procedural functions removed. Use instead:
 *   Auth::admin()->requireAuth()      (was requireAdmin())
 *   Auth::customer()->requireAuth()   (was requireCustomer())
 *   CsrfService::field()              (was csrfField())
 *   CsrfService::verify()             (was verifyCsrf())
 *   FlashMessage::set()               (was setFlash())
 *   FlashMessage::render()            (was renderFlash())
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/exceptions/AppException.php';
require_once __DIR__ . '/../classes/exceptions/DatabaseException.php';
require_once __DIR__ . '/../classes/exceptions/AuthException.php';
require_once __DIR__ . '/../classes/exceptions/CsrfException.php';
require_once __DIR__ . '/../classes/BaseService.php';
require_once __DIR__ . '/../classes/CsrfService.php';
require_once __DIR__ . '/../classes/FlashMessage.php';
require_once __DIR__ . '/../classes/SessionGuard.php';
require_once __DIR__ . '/../classes/AdminGuard.php';
require_once __DIR__ . '/../classes/CustomerGuard.php';
require_once __DIR__ . '/../classes/Auth.php';

Auth::startSession();
```

- [ ] **Step 2: Rewrite includes/functions.php as minimal loader**

Replace entire content with:

```php
<?php
/**
 * Functions bootstrap — loads Database class.
 *
 * All procedural db_* functions removed. Use instead:
 *   Database::getInstance()->fetch()     (was db_fetch())
 *   Database::getInstance()->fetchAll()  (was db_fetch_all())
 *   Database::getInstance()->insert()    (was db_insert())
 *   Database::getInstance()->update()    (was db_update())
 *   Database::getInstance()->delete()    (was db_delete())
 *
 * In service classes that extend BaseService, use:
 *   $this->fetch() / $this->fetchAll() / etc.
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/exceptions/AppException.php';
require_once __DIR__ . '/../classes/exceptions/DatabaseException.php';
require_once __DIR__ . '/../classes/BaseService.php';
```

- [ ] **Step 3: Update header-admin.php**

In `/includes/header-admin.php`, find and replace:
- `function_exists('renderFlash')` block → replace with `echo FlashMessage::render();`
- `isSuperAdmin()` → `Auth::admin()->isSuperAdmin()`
- Add `require_once` for Auth, AdminGuard, FlashMessage at top if not already loaded via auth.php

- [ ] **Step 4: Update header-customer.php**

In `/includes/header-customer.php`, find and replace:
- `getCustomerId()` → `Auth::customer()->getId()`
- `db_fetch(` → `Database::getInstance()->fetch(`
- `renderFlash()` → `FlashMessage::render()`
- Replace `require_once __DIR__ . '/auth.php';` + `require_once __DIR__ . '/functions.php';` with just `require_once __DIR__ . '/auth.php';`

- [ ] **Step 5: Commit**

```bash
git add includes/auth.php includes/functions.php includes/header-admin.php includes/header-customer.php && git commit -m "refactor: update bootstrap files to use OOP classes"
```

---

### Task 8: Update All Admin Pages

**Files:** All 16 files in `admin/`

**Find-and-replace pattern for each admin page:**

| Old | New |
|-----|-----|
| `require_once __DIR__ . '/../includes/functions.php';` | *(remove line — already loaded via auth.php)* |
| `requireAdmin();` | `Auth::admin()->requireAuth();` |
| `requireSuperAdmin();` | `Auth::admin()->requireSuperAdmin();` |
| `verifyCsrf()` | `CsrfService::verify()` |
| `csrfField()` | `CsrfService::field()` |
| `setFlash('success',` | `FlashMessage::set('success',` |
| `setFlash('error',` | `FlashMessage::set('error',` |
| `setFlash('warning',` | `FlashMessage::set('warning',` |
| `setFlash('info',` | `FlashMessage::set('info',` |
| `renderFlash()` | `FlashMessage::render()` |
| `isSuperAdmin()` | `Auth::admin()->isSuperAdmin()` |
| `getAdminId()` | `Auth::admin()->getId()` |
| `db_fetch_all(` | `Database::getInstance()->fetchAll(` |
| `db_fetch(` | `Database::getInstance()->fetch(` |

- [ ] **Step 1: Update admin pages with inline POST (7 files)**

Files: `dashboard.php`, `products.php`, `batches.php`, `customers.php`, `orders.php`, `activity-log.php`, `pengaturan.php`

For each file:
1. Remove `require_once __DIR__ . '/../includes/functions.php';` (if present after auth.php require)
2. Replace all procedural calls per table above
3. For `dashboard.php`: also update direct `db_fetch_all(` calls (5 occurrences)
4. For `pengaturan.php` and `activity-log.php`: keep `requireSuperAdmin()` → `Auth::admin()->requireSuperAdmin()`

- [ ] **Step 2: Update admin form/create pages (5 files)**

Files: `create-product.php`, `create-batch.php`, `create-order.php`, `edit-batch.php`, `edit-customer.php`

For each file:
1. Remove `require_once __DIR__ . '/../includes/functions.php';`
2. Replace all procedural calls per table above
3. For `create-order.php`: `db_fetch_all(` → `Database::getInstance()->fetchAll(` (2 occurrences for products and customers queries)

- [ ] **Step 3: Update admin edit/view pages (4 files)**

Files: `edit-order.php`, `edit-product.php`, `view-order.php`, `edit-batch.php`

For each file:
1. Remove `require_once __DIR__ . '/../includes/functions.php';`
2. Replace all procedural calls per table above
3. For `edit-order.php` and `edit-product.php`: `db_insert(` → `Database::getInstance()->insert(`, `db_update(` → `Database::getInstance()->update(`, `db_delete(` → `Database::getInstance()->delete(`

- [ ] **Step 4: Commit**

```bash
git add admin/ && git commit -m "refactor: update all admin pages to use OOP classes"
```

---

### Task 9: Update All Customer Pages + Auth Pages + API

**Files:**
- Modify: `customer/dashboard.php`, `customer/preorder.php`, `customer/orders.php`, `customer/edit-order.php`, `customer/profile.php`
- Modify: `auth/login-admin.php`, `auth/login-customer.php`, `auth/register.php`, `auth/logout.php`
- Modify: `api/download-pdf.php`

- [ ] **Step 1: Update customer pages**

Same find-and-replace pattern as admin, but use:
- `requireCustomer();` → `Auth::customer()->requireAuth();`
- `getCustomerId()` → `Auth::customer()->getId()`

- [ ] **Step 2: Update auth pages**

For `auth/login-admin.php`:
- `loginAdmin($userId)` → `Auth::admin()->login($userId)`
- `isAdminLoggedIn()` → `Auth::admin()->isLoggedIn()`

For `auth/login-customer.php`:
- `loginCustomer($customerId)` → `Auth::customer()->login($customerId)`
- `isCustomerLoggedIn()` → `Auth::customer()->isLoggedIn()`

For `auth/register.php`:
- `loginCustomer($newId)` → `Auth::customer()->login($newId)`

For `auth/logout.php`:
- `logoutAdmin()` → `Auth::admin()->logout()`
- `logoutCustomer()` → `Auth::customer()->logout()`

- [ ] **Step 3: Update api/download-pdf.php**

- Replace auth checks: `getAdminId()` / `getCustomerId()` → OOP equivalents
- Replace `db_fetch(` → `Database::getInstance()->fetch(`

- [ ] **Step 4: Commit**

```bash
git add customer/ auth/ api/ && git commit -m "refactor: update customer pages, auth pages, and API to use OOP classes"
```

---

### Task 10: Cleanup + Verify

**Files:**
- Delete: `config/database.php`

- [ ] **Step 1: Delete dead file**

```bash
rm config/database.php
```

- [ ] **Step 2: Verify no remaining procedural calls**

```bash
grep -rn 'db_fetch\|db_fetch_all\|db_insert\|db_update\|db_delete\|db()' --include="*.php" .
grep -rn 'requireAdmin\|requireCustomer\|requireSuperAdmin\|getAdminId\|getCustomerId\|isSuperAdmin\|isAdminLoggedIn\|isCustomerLoggedIn' --include="*.php" .
grep -rn 'verifyCsrf\|csrfField\|setFlash\|getFlash\|renderFlash\|startSession' --include="*.php" .
```

Expected: zero matches (except possibly in comments/docs).

- [ ] **Step 3: Manual smoke test**

1. `php -S localhost:8000`
2. Login as admin → dashboard loads
3. Navigate to products/batches/customers/orders pages
4. Login as customer → dashboard loads
5. Create/edit order flow
6. Verify flash messages appear
7. Verify CSRF protection works (form submit)

- [ ] **Step 4: Commit**

```bash
git add -A && git commit -m "refactor: cleanup — delete dead config/database.php, verify no procedural remnants"
```

---

### Task 11: Update Documentation

**Files:**
- Modify: `CLAUDE.md`
- Modify: `MEMORY.md`

- [ ] **Step 1: Update CLAUDE.md**

Update:
- Project Structure section — add new class files, remove deleted config/database.php
- OOP Classes section — add Database, BaseService, Auth, AdminGuard, CustomerGuard, CsrfService, FlashMessage, exceptions
- Architecture Decisions — update auth section to reference OOP classes
- Refactoring Plan section — mark as COMPLETE
- Coding Conventions — add OOP class conventions

- [ ] **Step 2: Update MEMORY.md**

- Mark OOP refactoring as completed
- Update known inconsistencies (should be none now)
- Update remaining tasks

- [ ] **Step 3: Commit**

```bash
git add CLAUDE.md && git commit -m "docs: update CLAUDE.md with OOP refactoring completion"
```
