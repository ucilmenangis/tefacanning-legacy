<?php

/**
 * CustomerAdminService — handles customer CRUD from admin panel.
 *
 * Methods:
 *   getAll()              — customers with order counts
 *   getById($id)         — single customer
 *   getStats($id)        — order stats for a customer
 *   update($id, $data)   — update customer profile
 *   softDelete($id)      — soft delete customer
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class CustomerAdminService extends BaseService
{
    /**
     * Get all customers with order counts.
     */
    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT c.id, c.name, c.organization, c.phone, c.email, c.address, c.created_at,
                    COUNT(DISTINCT o.id) AS order_count
             FROM customers c
             LEFT JOIN orders o ON o.customer_id = c.id AND o.deleted_at IS NULL
             WHERE c.deleted_at IS NULL
             GROUP BY c.id
             ORDER BY c.name ASC"
        );
    }

    /**
     * Get single customer by ID.
     */
    public function getById(int $id): ?array
    {
        return $this->fetch(
            "SELECT id, name, organization, phone, email, address, created_at
             FROM customers
             WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    /**
     * Get stats for a customer: total orders, total spent, joined date.
     */
    public function getStats(int $id): array
    {
        $stats = $this->fetch(
            "SELECT COUNT(o.id) AS total_orders,
                    COALESCE(SUM(o.total_amount), 0) AS total_spent
             FROM orders o
             WHERE o.customer_id = ? AND o.deleted_at IS NULL",
            [$id]
        );

        $customer = $this->getById($id);

        return [
            'total_orders' => (int) ($stats['total_orders'] ?? 0),
            'total_spent' => (float) ($stats['total_spent'] ?? 0),
            'joined_at' => $customer['created_at'] ?? null,
        ];
    }

    /**
     * Update customer profile.
     */
    public function update(int $id, array $data): void
    {
        $this->db->update(
            "UPDATE customers SET name = ?, organization = ?, phone = ?, email = ?, address = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL",
            [
                $data['name'],
                $data['organization'] ?? null,
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['address'] ?? null,
                $id,
            ]
        );
    }

    /**
     * Soft delete customer.
     */
    public function softDelete(int $id): void
    {
        $this->db->update(
            "UPDATE customers SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}
