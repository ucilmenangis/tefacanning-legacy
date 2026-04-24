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
             WHERE mhr.model_type = 'App\\Models\\User' AND mhr.model_id = ?",
            [$userId]
        );

        return $row ? $row['name'] : null;
    }

    /**
     * Check if user has super_admin role.
     */
    public function isSuperAdmin(int $userId): bool
    {
        return $this->getRole($userId) === 'super_admin';
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
            setFlash('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            header('Location: dashboard.php');
            exit;
        }
    }

    /**
     * Get admin user data by ID.
     */
    public function getById(int $userId): ?array
    {
        return db_fetch(
            "SELECT id, name, email, phone FROM users WHERE id = ?",
            [$userId]
        );
    }

    /**
     * Get all admin users with their roles.
     */
    public function getAll(): array
    {
        return db_fetch_all(
            "SELECT u.id, u.name, u.email, u.phone, r.name as role
             FROM users u
             LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\\Models\\User'
             LEFT JOIN roles r ON mhr.role_id = r.id
             ORDER BY u.name ASC"
        );
    }
}
