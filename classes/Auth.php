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
