<?php

/**
 * OrderAdminService — handles admin-side order operations.
 *
 * Encapsulates all order queries and mutations used by admin pages:
 *   - Listing, viewing, creating, editing, deleting orders
 *   - Stock management (deduct/return) wrapped in DB transactions
 *   - Dropdown data for customers, batches, products
 *
 * Extends BaseService → uses $this->fetch(), $this->fetchAll(), etc.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class OrderAdminService extends BaseService
{
    // ── Read Methods ──

    /**
     * Get all orders with customer + batch info (for list page).
     */
    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT o.id, o.order_number, o.status, o.pickup_code, o.total_amount,
                    o.picked_up_at, o.created_at,
                    c.name AS customer_name, c.phone AS customer_phone,
                    b.name AS batch_name
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.deleted_at IS NULL
             ORDER BY o.created_at DESC"
        );
    }

    /**
     * Get single order with full customer + batch info (no customer_id filter).
     * Used by view-order and edit-order pages.
     */
    public function getById(int $id): ?array
    {
        return $this->fetch(
            "SELECT o.id, o.order_number, o.pickup_code, o.status, o.total_amount, o.profit,
                    o.picked_up_at, o.created_at, o.updated_at, o.customer_id, o.batch_id,
                    c.name AS customer_name, c.phone AS customer_phone, c.email AS customer_email,
                    c.organization, c.address,
                    b.name AS batch_name, b.event_name, b.event_date
             FROM orders o
             JOIN customers c ON c.id = o.customer_id
             JOIN batches b ON b.id = o.batch_id
             WHERE o.id = ? AND o.deleted_at IS NULL",
            [$id]
        );
    }

    /**
     * Get order items with product name and SKU.
     */
    public function getItems(int $orderId): array
    {
        return $this->fetchAll(
            "SELECT op.id, op.product_id, op.quantity, op.unit_price, op.subtotal,
                    p.name AS product_name, p.sku
             FROM order_product op
             JOIN products p ON p.id = op.product_id
             WHERE op.order_id = ?",
            [$orderId]
        );
    }

    /**
     * Get active products for dropdowns and JS product data.
     */
    public function getActiveProducts(): array
    {
        return $this->fetchAll(
            "SELECT id, name, sku, price FROM products WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC"
        );
    }

    /**
     * Get customers for dropdown (id, name, phone).
     */
    public function getCustomersForDropdown(): array
    {
        return $this->fetchAll(
            "SELECT id, name, phone FROM customers WHERE deleted_at IS NULL ORDER BY name ASC"
        );
    }

    /**
     * Get open batches for dropdown.
     */
    public function getOpenBatchesForDropdown(): array
    {
        return $this->fetchAll(
            "SELECT id, name, event_name, event_date FROM batches WHERE status = 'open' AND deleted_at IS NULL ORDER BY created_at DESC"
        );
    }

    // ── Write Methods ──

    /**
     * Create a new order with transaction.
     * Generates order number + pickup code, validates stock, deducts stock,
     * inserts order + order_product rows.
     *
     * Returns new order ID.
     * @throws RuntimeException on stock failure or empty items
     */
    public function createOrder(int $customerId, int $batchId, array $productIds, array $quantities): int
    {
        require_once __DIR__ . '/AdminService.php';
        require_once __DIR__ . '/ProductService.php';
        $adminService   = new AdminService();
        $productService = new ProductService();

        $orderNumber = 'ORD-' . strtoupper(bin2hex(random_bytes(4)));
        $pickupCode  = strtoupper(bin2hex(random_bytes(3)));

        // Calculate total from DB prices + check stock
        $totalAmount = 0;
        $orderItems  = [];
        foreach ($productIds as $i => $productId) {
            $productId = intval($productId);
            $qty       = max(1, intval($quantities[$i] ?? 1));
            $price     = $adminService->verifyProductPrice($productId);
            if ($price <= 0) continue;

            if (!$productService->hasStock($productId, $qty)) {
                $product = $productService->getById($productId);
                $name    = $product ? $product['name'] : 'Produk #' . $productId;
                throw new RuntimeException('Stok ' . $name . ' tidak cukup.');
            }

            $subtotal      = $price * $qty;
            $totalAmount  += $subtotal;
            $orderItems[]  = [$productId, $qty, $price, $subtotal];
        }

        if (empty($orderItems)) {
            throw new RuntimeException('Tidak ada produk valid yang dipilih.');
        }

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Deduct stock atomically
            foreach ($orderItems as $item) {
                if (!$productService->deductStock($item[0], $item[1])) {
                    throw new RuntimeException('Stok produk tidak cukup.');
                }
            }

            // Insert order
            $orderId = $this->insert(
                "INSERT INTO orders (customer_id, batch_id, order_number, pickup_code, status, total_amount, profit, created_at, updated_at)
                 VALUES (?, ?, ?, ?, 'pending', ?, 0, NOW(), NOW())",
                [$customerId, $batchId, $orderNumber, $pickupCode, $totalAmount]
            );

            // Insert order items
            foreach ($orderItems as $item) {
                $this->insert(
                    "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                    [$orderId, $item[0], $item[1], $item[2], $item[3]]
                );
            }

            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Update order — return old stock, deduct new stock, replace items.
     *
     * @param int   $id          Order ID
     * @param string $status     New status
     * @param array $productIds  New product IDs
     * @param array $quantities  New quantities
     * @param array $oldItems    Current items (from getItems()) for stock return
     * @throws RuntimeException on stock failure
     */
    public function updateOrder(int $id, string $status, array $productIds, array $quantities, array $oldItems): void
    {
        require_once __DIR__ . '/AdminService.php';
        require_once __DIR__ . '/ProductService.php';
        $adminService   = new AdminService();
        $productService = new ProductService();

        $validStatuses = ['pending', 'processing', 'ready', 'picked_up'];
        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException('Status tidak valid.');
        }

        // Check picked_up timestamp
        $order      = $this->getById($id);
        $pickedUpAt = $order['picked_up_at'];
        if ($status === 'picked_up' && !$pickedUpAt) {
            $pickedUpAt = date('Y-m-d H:i:s');
        }

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            // Return stock for old items
            foreach ($oldItems as $oldItem) {
                $productService->returnStock($oldItem['product_id'], $oldItem['quantity']);
            }

            // Delete existing order_product rows
            $this->delete("DELETE FROM order_product WHERE order_id = ?", [$id]);

            // Recalculate and insert new items
            $totalAmount = 0;
            foreach ($productIds as $i => $productId) {
                $productId = intval($productId);
                $qty       = max(1, intval($quantities[$i] ?? 1));

                if (!$productService->deductStock($productId, $qty)) {
                    throw new RuntimeException('Stok produk tidak cukup.');
                }

                $price      = $adminService->verifyProductPrice($productId);
                $subtotal   = $price * $qty;
                $totalAmount += $subtotal;

                $this->insert(
                    "INSERT INTO order_product (order_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                    [$id, $productId, $qty, $price, $subtotal]
                );
            }

            $this->update(
                "UPDATE orders SET status = ?, total_amount = ?, picked_up_at = ?, updated_at = NOW() WHERE id = ?",
                [$status, $totalAmount, $pickedUpAt, $id]
            );

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Soft-delete an order and return stock for all items.
     */
    public function deleteOrder(int $id): void
    {
        require_once __DIR__ . '/ProductService.php';
        $productService = new ProductService();

        $items = $this->fetchAll(
            "SELECT product_id, quantity FROM order_product WHERE order_id = ?",
            [$id]
        );

        foreach ($items as $item) {
            $productService->returnStock($item['product_id'], $item['quantity']);
        }

        $this->update("UPDATE orders SET deleted_at = NOW() WHERE id = ?", [$id]);
    }
}
