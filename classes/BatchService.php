<?php

/**
 * BatchService — handles batch CRUD operations.
 *
 * Methods:
 *   getAll()              — batches with order counts
 *   getById($id)         — single batch
 *   getOpenBatches()     — status = 'open'
 *   create($data)        — insert, return new ID
 *   updateById($id, $data)   — update batch
 *   softDelete($id)      — set deleted_at
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';

class BatchService extends BaseService
{
    /**
     * Get all batches with order count, ordered by newest first.
     */
    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT b.id, b.name, b.event_name, b.event_date, b.status, b.created_at,
                    COUNT(DISTINCT o.id) AS order_count
             FROM batches b
             LEFT JOIN orders o ON o.batch_id = b.id AND o.deleted_at IS NULL
             WHERE b.deleted_at IS NULL
             GROUP BY b.id
             ORDER BY b.created_at DESC"
        );
    }

    /**
     * Get single batch by ID.
     */
    public function getById(int $id): ?array
    {
        return $this->fetch(
            "SELECT id, name, event_name, event_date, status, created_at
             FROM batches
             WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    /**
     * Get batches with status 'open'.
     */
    public function getOpenBatches(): array
    {
        return $this->fetchAll(
            "SELECT id, name, event_name, event_date, status
             FROM batches
             WHERE status = 'open' AND deleted_at IS NULL
             ORDER BY event_date ASC"
        );
    }

    /**
     * Create new batch. Returns new ID.
     */
    public function create(array $data): int
    {
        return $this->insert(
            "INSERT INTO batches (name, event_name, event_date, status, created_at, updated_at)
             VALUES (?, ?, ?, 'open', NOW(), NOW())",
            [
                $data['name'],
                $data['event_name'],
                $data['event_date'],
            ]
        );
    }

    /**
     * Update batch by ID.
     */
    public function updateById(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $params[] = $data['name'];
        }
        if (isset($data['event_name'])) {
            $fields[] = 'event_name = ?';
            $params[] = $data['event_name'];
        }
        if (isset($data['event_date'])) {
            $fields[] = 'event_date = ?';
            $params[] = $data['event_date'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $params[] = $data['status'];
        }

        if (empty($fields)) return;

        $fields[] = 'updated_at = NOW()';
        $params[] = $id;

        $this->db->update(
            "UPDATE batches SET " . implode(', ', $fields) . " WHERE id = ? AND deleted_at IS NULL",
            $params
        );
    }

    /**
     * Soft delete batch.
     */
    public function softDelete(int $id): void
    {
        $this->db->update(
            "UPDATE batches SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}
