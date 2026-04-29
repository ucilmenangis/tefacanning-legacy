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
