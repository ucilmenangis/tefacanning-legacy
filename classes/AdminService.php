<?php

/**
 * AdminService — handles admin-related operations.
 *
 * Methods:
 *   getRole($userId)           — check role from DB (super_admin or teknisi)
 *   isSuperAdmin($userId)      — true if super_admin
 *   requireSuperAdmin()        — redirect if not super_admin (call at top of restricted pages)
 *   getById($userId)           — get admin user data
 */
require_once __DIR__ . '/../includes/functions.php';

class AdminService
{
  /**
   * Get the role name for a user from model_has_roles table.
   * Returns 'super_admin', 'teknisi', or null if not found.
   */
  public function getRole(int $userId): ?string
  {
    $row = db_fetch(
      "SELECT r.name
             FROM roles r
             JOIN model_has_roles mhr ON mhr.role_id = r.id
             WHERE mhr.model_type = 'App\\\\Models\\\\User' AND mhr.model_id = ?",
      [$userId],
    );

    return $row ? $row["name"] : null;
  }

  /**
   * Check if user has super_admin role.
   */
  public function isSuperAdmin(int $userId): bool
  {
    return $this->getRole($userId) === "super_admin";
  }

  /**
   * Protect page — only super_admin can access.
   * Call at top of restricted pages after requireAdmin().
   * Redirects to dashboard with flash message if not authorized.
   */
  public function requireSuperAdmin(): void
  {
    $adminId = getAdminId();
    if (!$adminId || !$this->isSuperAdmin($adminId)) {
      setFlash("error", "Anda tidak memiliki akses ke halaman tersebut.");
      header("Location: dashboard.php");
      exit();
    }
  }

  /**
   * Get admin user data by ID.
   */
  public function getById(int $userId): ?array
  {
    return db_fetch("SELECT id, name, email, phone FROM users WHERE id = ?", [
      $userId,
    ]);
  }

  /**
   * Get all admin users with their roles.
   */
  public function getAll(): array
  {
    return db_fetch_all(
      "SELECT u.id, u.name, u.email, u.phone, r.name as role
             FROM users u
             LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\\\\Models\\\\User'
             LEFT JOIN roles r ON mhr.role_id = r.id
             ORDER BY u.name ASC",
    );
  }

  /**
   * Verify a product price against the database.
   * Returns the actual price from DB, or throws if product not found.
   * Use this to prevent price manipulation from form submissions.
   */
  public function verifyProductPrice(int $productId): float
  {
    $product = db_fetch(
      "SELECT price FROM products WHERE id = ? AND is_active = 1 AND deleted_at IS NULL",
      [$productId],
    );

    if (!$product) {
      throw new \InvalidArgumentException(
        "Produk tidak ditemukan atau tidak aktif.",
      );
    }

    return (float) $product["price"];
  }

  /**
   * Check if current admin can edit product prices.
   * Only super_admin can modify prices.
   */
  public function canEditPrice(): bool
  {
    $adminId = getAdminId();
    return $adminId && $this->isSuperAdmin($adminId);
  }

  /**
   * Check if a product is a core product that cannot be deleted.
   * Core products are identified by their SKU.
   */
  public function isCoreProduct(int $productId): bool
  {
    $coreSkus = ["TEFA-ASN-001", "TEFA-SSC-001", "TEFA-SST-001"];

    $product = db_fetch("SELECT sku FROM products WHERE id = ?", [$productId]);

    return $product && in_array($product["sku"], $coreSkus);
  }

  // ── Dashboard Methods ──

  /**
   * Get aggregate stats for admin dashboard.
   */
  public function getDashboardStats(): array
  {
    $customerCount = db_fetch(
      "SELECT COUNT(*) AS total FROM customers WHERE deleted_at IS NULL",
    );
    $omset = db_fetch(
      "SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE deleted_at IS NULL",
    );
    $profit = db_fetch(
      "SELECT COALESCE(SUM(profit), 0) AS total FROM orders WHERE deleted_at IS NULL",
    );
    $readyCount = db_fetch(
      "SELECT COUNT(*) AS total FROM orders WHERE status = 'ready' AND deleted_at IS NULL",
    );

    return [
      "total_pelanggan" => (int) ($customerCount["total"] ?? 0),
      "total_omset" => (float) ($omset["total"] ?? 0),
      "total_profit" => (float) ($profit["total"] ?? 0),
      "siap_ambil" => (int) ($readyCount["total"] ?? 0),
    ];
  }

  /**
   * Get the currently active batch (first open/processing batch).
   */
  public function getActiveBatch(): ?array
  {
    return db_fetch(
      "SELECT id, name, event_name, event_date, status
             FROM batches
             WHERE status IN ('open', 'processing') AND deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT 1",
    );
  }

  /**
   * Get recent orders across all batches.
   */
  public function getRecentOrders(int $limit = 5): array
  {
    $limit = (int) $limit;
    return db_fetch_all(
      "SELECT o.id, o.order_number, o.status, o.total_amount, o.pickup_code, o.created_at,
                    c.name AS customer_name, c.phone AS customer_phone,
                    b.name AS batch_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.deleted_at IS NULL
             ORDER BY o.created_at DESC
             LIMIT {$limit}",
    );
  }

  /**
   * Get orders in a specific batch.
   */
  public function getBatchOrders(int $batchId): array
  {
    return db_fetch_all(
      "SELECT o.id, o.order_number, o.status, o.pickup_code, o.picked_up_at, o.created_at,
                    c.name AS customer_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             WHERE o.batch_id = ? AND o.deleted_at IS NULL
             ORDER BY o.created_at DESC",
      [$batchId],
    );
  }

  /**
   * Get product quantity summary for a batch.
   */
  public function getBatchProducts(int $batchId): array
  {
    return db_fetch_all(
      "SELECT p.name AS produk, p.sku, SUM(op.quantity) AS qty, 'kaleng' AS satuan
             FROM order_product op
             JOIN orders o ON o.id = op.order_id
             JOIN products p ON p.id = op.product_id
             WHERE o.batch_id = ? AND o.deleted_at IS NULL
             GROUP BY p.id
             ORDER BY p.name ASC",
      [$batchId],
    );
  }

  /**
   * Get count of orders in a batch.
   */
  public function getBatchOrderCount(int $batchId): int
  {
    $row = db_fetch(
      "SELECT COUNT(*) AS total FROM orders WHERE batch_id = ? AND deleted_at IS NULL",
      [$batchId],
    );
    return (int) ($row["total"] ?? 0);
  }
}
