<?php

/**
 * Session & Auth helper functions
 *
 * Dual guard system — admin and customer login terpisah.
 * Admin pakai $_SESSION['admin_id'], customer pakai $_SESSION['customer_id'].
 * Keduanya bisa login bersamaan tanpa konflik.
 *
 * Usage:
 *   require_once __DIR__ . '/auth.php';
 *   startSession();          // wajib di awal setiap halaman
 *   requireAdmin();          // redirect ke login kalau belum login
 *   $id = getAdminId();      // ambil ID admin yang login
 */

/**
 * Start session with secure settings.
 * Call this ONCE at the top of every page, before any HTML output.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Security settings for session cookies
        ini_set('session.use_strict_mode', '1');       // reject uninitialized session ID
        ini_set('session.use_only_cookies', '1');       // only accept session ID from cookies
        ini_set('session.cookie_httponly', '1');        // JS cannot read session cookie
        ini_set('session.cookie_samesite', 'Lax');      // protect against CSRF

        session_start();
    }
}

// ─── Admin Guard ───────────────────────────────────────

/**
 * Login admin — set session and regenerate ID.
 * Regenerate ID prevents session fixation attack (hacker steal session ID).
 */
function loginAdmin(int $userId): void
{
    startSession();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $userId;

    // Cache role in session to avoid DB query on every page
    require_once __DIR__ . '/functions.php';
    require_once __DIR__ . '/../classes/AdminService.php';
    $adminService = new AdminService();
    $_SESSION['admin_role'] = $adminService->getRole($userId);
}

/**
 * Logout admin — unset session data.
 */
function logoutAdmin(): void
{
    startSession();
    unset($_SESSION['admin_id']);
}

/**
 * Check if admin is logged in.
 */
function isAdminLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['admin_id']);
}

/**
 * Get current admin ID. Returns null if not logged in.
 */
function getAdminId(): ?int
{
    startSession();
    return $_SESSION['admin_id'] ?? null;
}

/**
 * Get cached admin role from session.
 * Returns 'super_admin', 'teknisi', or null.
 */
function getAdminRole(): ?string
{
    startSession();
    return $_SESSION['admin_role'] ?? null;
}

/**
 * Check if logged-in admin is super_admin.
 * Falls back to DB query if session role not cached.
 */
function isSuperAdmin(): bool
{
    $role = getAdminRole();
    if ($role !== null) {
        return $role === 'super_admin';
    }

    // Fallback: query DB if role not in session (e.g. logged in before AdminService fix)
    $adminId = getAdminId();
    if (!$adminId) {
        return false;
    }

    require_once __DIR__ . '/functions.php';
    require_once __DIR__ . '/../classes/AdminService.php';
    $adminService = new AdminService();
    $role = $adminService->getRole($adminId);

    // Cache for future requests
    $_SESSION['admin_role'] = $role;

    return $role === 'super_admin';
}

/**
 * Protect page — only super_admin can access.
 * Call after requireAdmin() on restricted pages.
 */
function requireSuperAdmin(): void
{
    if (!isSuperAdmin()) {
        setFlash('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Protect admin pages — redirect to login if not authenticated.
 */
function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: /auth/login-admin.php');
        exit;
    }
}

// ─── Customer Guard ────────────────────────────────────

/**
 * Login customer — set session and regenerate ID.
 */
function loginCustomer(int $customerId): void
{
    startSession();
    session_regenerate_id(true);
    $_SESSION['customer_id'] = $customerId;
}

/**
 * Logout customer — unset session data.
 */
function logoutCustomer(): void
{
    startSession();
    unset($_SESSION['customer_id']);
}

/**
 * Check if customer is logged in.
 */
function isCustomerLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['customer_id']);
}

/**
 * Get current customer ID. Returns null if not logged in.
 */
function getCustomerId(): ?int
{
    startSession();
    return $_SESSION['customer_id'] ?? null;
}

/**
 * Protect customer pages — redirect to login if not authenticated.
 */
function requireCustomer(): void
{
    if (!isCustomerLoggedIn()) {
        header('Location: /auth/login-customer.php');
        exit;
    }
}

// ─── CSRF Protection ──────────────────────────────────

/**
 * Generate CSRF token and store in session.
 */
function generateCsrfToken(): string
{
    startSession();
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

/**
 * Output a hidden input field with CSRF token.
 * Put inside every <form>: <?php echo csrfField(); ?>
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Verify CSRF token from form submission.
 * Returns true if valid, false if invalid.
 */
function verifyCsrf(): bool
{
    startSession();
    $token = $_POST['csrf_token'] ?? '';

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }

    unset($_SESSION['csrf_token']);
    return true;
}

// ─── Flash Messages ───────────────────────────────────

/**
 * Set a flash message (stored in session, read once, then deleted).
 *
 * Types: 'success', 'error', 'warning', 'info'
 *
 * Example:
 *   setFlash('success', 'Produk berhasil ditambahkan!');
 *   header('Location: /admin/products.php');
 *   exit;
 */
function setFlash(string $type, string $message): void
{
    startSession();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Get flash message and delete from session.
 * Returns ['type' => 'success', 'message' => '...'] or null.
 */
function getFlash(): ?array
{
    startSession();
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Display flash message as HTML alert.
 * Call this in layout files after <main> tag.
 * Auto-hides after reading.
 *
 * Example:
 *   <?php echo renderFlash(); ?>
 */
function renderFlash(): string
{
    $flash = getFlash();
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
