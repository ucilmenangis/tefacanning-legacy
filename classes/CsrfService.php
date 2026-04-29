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
