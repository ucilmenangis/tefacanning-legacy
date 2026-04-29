<?php

/**
 * AdminGuard — admin authentication (implements SessionGuard).
 * Uses $_SESSION['admin_id'] and $_SESSION['admin_role'].
 */
class AdminGuard implements SessionGuard
{
  private const SESSION_KEY = "admin_id";
  private const ROLE_KEY = "admin_role";

  public function isLoggedIn(): bool
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    return isset($_SESSION[self::SESSION_KEY]);
  }

  public function getId(): ?int
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    return $_SESSION[self::SESSION_KEY] ?? null;
  }

  public function login(int $id): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    session_regenerate_id(true);
    $_SESSION[self::SESSION_KEY] = $id;

    // Cache role in session to avoid DB query on every page
    require_once __DIR__ . "/Database.php";
    require_once __DIR__ . "/BaseService.php";
    require_once __DIR__ . "/AdminService.php";
    $adminService = new AdminService();
    $_SESSION[self::ROLE_KEY] = $adminService->getRole($id);
  }

  public function logout(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    unset($_SESSION[self::SESSION_KEY]);
  }

  public function requireAuth(): void
  {
    if (!$this->isLoggedIn()) {
      header("Location: /auth/login-admin.php");
      exit();
    }
  }

  // ── Admin-specific methods (not in interface) ──

  public function getRole(): ?string
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    return $_SESSION[self::ROLE_KEY] ?? null;
  }

  public function isSuperAdmin(): bool
  {
    $role = $this->getRole();
    if ($role !== null) {
      return $role === "super_admin";
    }

    // Fallback: query DB if role not in session
    $adminId = $this->getId();
    if (!$adminId) {
      return false;
    }

    require_once __DIR__ . "/Database.php";
    require_once __DIR__ . "/BaseService.php";
    require_once __DIR__ . "/AdminService.php";
    $adminService = new AdminService();
    $role = $adminService->getRole($adminId);

    $_SESSION[self::ROLE_KEY] = $role;
    return $role === "super_admin";
  }

  public function requireSuperAdmin(): void
  {
    if (!$this->isSuperAdmin()) {
      require_once __DIR__ . "/FlashMessage.php";
      FlashMessage::set(
        "error",
        "Anda tidak memiliki akses ke halaman tersebut.",
      );
      header("Location: dashboard.php");
      exit();
    }
  }
}
