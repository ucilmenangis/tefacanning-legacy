<?php

/**
 * OrderService — handles all order-related database operations.
 *
 * Usage:
 *   $service = new OrderService();
 *   $orders  = $service->getByCustomer($customerId);
 *   $order   = $service->getById($orderId);
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class OrderService extends BaseService
{
    /**
     * Get all orders for a customer, newest first.
     * Includes batch name and item count.
     */
    public function getByCustomer(int $customerId, int $limit = 20): array
    {
        return $this->fetchAll(
            "SELECT o.id, o.order_number, o.status, o.total_amount, o.created_at,
                    b.name as batch_name,
                    (SELECT COUNT(*) FROM order_product WHERE order_id = o.id) as item_count
             FROM orders o
             JOIN batches b ON o.batch_id = b.id
             WHERE o.customer_id = ? AND o.deleted_at IS NULL
             ORDER BY o.created_at DESC
             LIMIT " . (int) $limit,
            [$customerId]
        );
    }

    /**
     * Get a single order with batch name, items, and product details.
     * Returns null if not found or doesn't belong to this customer.
     */
    public function getById(int $orderId, int $customerId): ?array
    {
        $order = $this->fetch(
            "SELECT o.*, b.name as batch_name, b.event_name, b.event_date
             FROM orders o
             JOIN batches b ON o.batch_id = b.id
             WHERE o.id = ? AND o.customer_id = ? AND o.deleted_at IS NULL",
            [$orderId, $customerId]
        );

        if (!$order) {
            return null;
        }

        // Attach order items with product names
        $order['items'] = $this->fetchAll(
            "SELECT op.*, p.name as product_name, p.sku
             FROM order_product op
             JOIN products p ON op.product_id = p.id
             WHERE op.order_id = ?",
            [$orderId]
        );

        return $order;
    }

    /**
     * Cancel (soft-delete) a pending order.
     * Only pending orders can be cancelled.
     */
    public function cancel(int $orderId, int $customerId): bool
    {
        $order = $this->fetch(
            "SELECT id, status FROM orders WHERE id = ? AND customer_id = ? AND deleted_at IS NULL",
            [$orderId, $customerId]
        );

        if (!$order || $order['status'] !== 'pending') {
            return false;
        }

        $this->db->getPdo()->prepare("UPDATE orders SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?")
            ->execute([$orderId]);

        return true;
    }

    /**
     * Get order stats for a customer dashboard.
     * Returns total, spent, pending, ready counts.
     */
    public function getStats(int $customerId): array
    {
        $total = $this->fetch(
            "SELECT COUNT(*) as count FROM orders WHERE customer_id = ? AND deleted_at IS NULL",
            [$customerId]
        )['count'];

        $spent = $this->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders
             WHERE customer_id = ? AND deleted_at IS NULL AND status != 'pending'",
            [$customerId]
        )['total'];

        $pending = $this->fetch(
            "SELECT COUNT(*) as count FROM orders
             WHERE customer_id = ? AND status = 'pending' AND deleted_at IS NULL",
            [$customerId]
        )['count'];

        $ready = $this->fetch(
            "SELECT COUNT(*) as count FROM orders
             WHERE customer_id = ? AND status = 'ready' AND deleted_at IS NULL",
            [$customerId]
        )['count'];

        return [
            'total_orders'  => (int) $total,
            'total_spent'   => (float) $spent,
            'pending_count' => (int) $pending,
            'ready_count'   => (int) $ready,
        ];
    }
}
