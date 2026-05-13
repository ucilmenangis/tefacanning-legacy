<?php

/**
 * PasswordResetService — Handle password reset flow.
 *
 * Methods:
 *   generateToken(string $email): string     — create 6-digit OTP, store in DB
 *   verifyToken(string $email, string $token): bool  — check token valid + not expired
 *   resetPassword(string $email, string $token, string $newPassword): bool  — verify + update password
 *   cleanExpired(): int                      — remove old expired tokens
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class PasswordResetService extends BaseService
{
    /** Token validity in minutes */
    private const TOKEN_LIFETIME = 15;

    /** OTP length */
    private const TOKEN_LENGTH = 6;

    /**
     * Generate a 6-digit OTP and store in password_resets table.
     * Invalidates any previous unused tokens for this email.
     */
    public function generateToken(string $email): string
    {
        // Invalidate previous unused tokens
        $this->update(
            "UPDATE password_resets SET used_at = NOW() WHERE email = ? AND used_at IS NULL",
            [$email]
        );

        // Generate 6-digit code
        $token = str_pad((string) random_int(0, 999999), self::TOKEN_LENGTH, '0', STR_PAD_LEFT);

        // Store token
        $this->insert(
            "INSERT INTO password_resets (email, token, created_at, expires_at)
             VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL " . self::TOKEN_LIFETIME . " MINUTE))",
            [$email, $token]
        );

        return $token;
    }

    /**
     * Verify token is valid and not expired.
     */
    public function verifyToken(string $email, string $token): bool
    {
        $row = $this->fetch(
            "SELECT id FROM password_resets
             WHERE email = ? AND token = ? AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1",
            [$email, $token]
        );

        return $row !== null;
    }

    /**
     * Reset password: verify token, update customer password, invalidate token.
     */
    public function resetPassword(string $email, string $token, string $newPassword): bool
    {
        // Verify token
        $row = $this->fetch(
            "SELECT id FROM password_resets
             WHERE email = ? AND token = ? AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1",
            [$email, $token]
        );

        if (!$row) {
            return false;
        }

        // Update password in customers table
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->update(
            "UPDATE customers SET password = ?, updated_at = NOW() WHERE email = ? AND deleted_at IS NULL",
            [$hashed, $email]
        );

        // Invalidate token
        $this->update(
            "UPDATE password_resets SET used_at = NOW() WHERE id = ?",
            [$row['id']]
        );

        return true;
    }

    /**
     * Remove expired tokens older than 24 hours. Housekeeping.
     */
    public function cleanExpired(): int
    {
        return $this->delete(
            "DELETE FROM password_resets WHERE expires_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
    }
}
