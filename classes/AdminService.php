<?php

/**
 * AdminService — handles admin-related operations.
 *
 * Methods:
 *   getRole($userId)           — check role from DB (super_admin or teknisi)
 *   isSuperAdmin($userId)      — true if super_admin
 *   getById($userId)           — get admin user data
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class AdminService extends BaseService
{
  /**
   * Get the role name for a user from model_has_roles table.
   * Returns 'super_admin', 'teknisi', or null if not found.
   */
  public function getRole(int $userId): ?string
  {
    $row = $this->fetch(
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
   * Get admin user data by ID.
   */
  public function getById(int $userId): ?array
  {
    return $this->fetch("SELECT id, name, email FROM users WHERE id = ?", [
      $userId,
    ]);
  }

  /**
   * Get all admin users with their roles.
   */
  public function getAll(): array
  {
    return $this->fetchAll(
      "SELECT u.id, u.name, u.email, u.created_at, r.name as role
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
    $product = $this->fetch(
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
    require_once __DIR__ . '/Auth.php';
    require_once __DIR__ . '/AdminGuard.php';
    require_once __DIR__ . '/SessionGuard.php';
    $adminId = Auth::admin()->getId();
    return $adminId && $this->isSuperAdmin($adminId);
  }

  /**
   * Check if a product is a core product that cannot be deleted.
   * Core products are identified by their SKU.
   */
  public function isCoreProduct(int $productId): bool
  {
    $coreSkus = ["TEFA-ASN-001", "TEFA-SSC-001", "TEFA-SST-001"];

    $product = $this->fetch("SELECT sku FROM products WHERE id = ?", [$productId]);

    return $product && in_array($product["sku"], $coreSkus);
  }

  // ── Dashboard Methods ──

  /**
   * Get aggregate stats for admin dashboard.
   */
  public function getDashboardStats(): array
  {
    $customerCount = $this->fetch(
      "SELECT COUNT(*) AS total FROM customers WHERE deleted_at IS NULL",
    );
    $omset = $this->fetch(
      "SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE deleted_at IS NULL",
    );
    $profit = $this->fetch(
      "SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE status = 'picked_up' AND deleted_at IS NULL",
    );
    $readyCount = $this->fetch(
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
    return $this->fetch(
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
    return $this->fetchAll(
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
    return $this->fetchAll(
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
    return $this->fetchAll(
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
    $row = $this->fetch(
      "SELECT COUNT(*) AS total FROM orders WHERE batch_id = ? AND deleted_at IS NULL",
      [$batchId],
    );
    return (int) ($row["total"] ?? 0);
  }
}
