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
