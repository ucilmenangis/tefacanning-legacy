<?php

/**
 * CustomerService — handles customer-related database operations.
 *
 * Usage:
 *   $service = new CustomerService();
 *   $customer = $service->getById($id);
 *   $service->updateProfile($id, $data);
 *   $service->changePassword($id, $currentPassword, $newPassword);
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class CustomerService extends BaseService
{
    /**
     * Get customer by ID.
     * Returns null if not found or soft-deleted.
     */
    public function getById(int $customerId): ?array
    {
        return $this->fetch(
            "SELECT id, name, email, phone, organization, address, created_at
             FROM customers WHERE id = ? AND deleted_at IS NULL",
            [$customerId]
        ) ?: null;
    }

    /**
     * Check if customer has active (non-pending) orders.
     * If true, profile editing should be locked.
     */
    public function hasActiveOrders(int $customerId): bool
    {
        $result = $this->fetch(
            "SELECT COUNT(*) as count FROM orders
             WHERE customer_id = ? AND status IN ('processing', 'ready') AND deleted_at IS NULL",
            [$customerId]
        );
        return $result['count'] > 0;
    }

    /**
     * Update customer profile fields.
     */
    public function updateProfile(int $customerId, array $data): void
    {
        $this->db->getPdo()->prepare(
            "UPDATE customers SET name = ?, phone = ?, organization = ?, address = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL"
        )->execute([
            $data['name'],
            $data['phone'],
            $data['organization'],
            $data['address'],
            $customerId,
        ]);
    }

    /**
     * Change customer password.
     *
     * @return bool true if changed, false if current password wrong
     */
    public function changePassword(int $customerId, string $currentPassword, string $newPassword): bool
    {
        $customer = $this->fetch(
            "SELECT password FROM customers WHERE id = ? AND deleted_at IS NULL",
            [$customerId]
        );

        if (!$customer || !password_verify($currentPassword, $customer['password'])) {
            return false;
        }

        $this->db->getPdo()->prepare("UPDATE customers SET password = ?, updated_at = NOW() WHERE id = ?")
            ->execute([password_hash($newPassword, PASSWORD_BCRYPT), $customerId]);

        return true;
    }
}
