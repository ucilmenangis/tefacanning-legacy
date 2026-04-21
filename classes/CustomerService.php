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
class CustomerService
{
    /**
     * Get customer by ID.
     * Returns null if not found or soft-deleted.
     */
    public function getById(int $customerId): ?array
    {
        return db_fetch(
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
        $result = db_fetch(
            "SELECT COUNT(*) as count FROM orders
             WHERE customer_id = ? AND status IN ('processing', 'ready') AND deleted_at IS NULL",
            [$customerId]
        );
        return $result['count'] > 0;
    }

    /**
     * Update customer profile fields.
     *
     * @param array $data ['name' => ..., 'phone' => ..., 'organization' => ..., 'address' => ...]
     */
    public function updateProfile(int $customerId, array $data): void
    {
        db()->prepare(
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
        $customer = db_fetch(
            "SELECT password FROM customers WHERE id = ? AND deleted_at IS NULL",
            [$customerId]
        );

        if (!$customer || !password_verify($currentPassword, $customer['password'])) {
            return false;
        }

        db()->prepare("UPDATE customers SET password = ?, updated_at = NOW() WHERE id = ?")
            ->execute([password_hash($newPassword, PASSWORD_BCRYPT), $customerId]);

        return true;
    }
}
