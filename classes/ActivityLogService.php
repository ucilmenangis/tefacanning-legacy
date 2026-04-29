<?php

/**
 * ActivityLogService — handles activity log read/write.
 *
 * Matches Spatie activity_log table schema from Laravel version.
 * causer_type = 'App\Models\User', subject_type = full model class string.
 *
 * Methods:
 *   log($event, $subjectType, $subjectId, $description, $properties)
 *   getAll($limit, $offset, $filters)
 *   countAll($filters)
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/AdminGuard.php';
require_once __DIR__ . '/SessionGuard.php';

class ActivityLogService extends BaseService
{
    /**
     * Log an activity.
     */
    public function log(string $event, string $subjectType, int $subjectId, string $description, array $properties = []): void
    {
        $adminId = Auth::admin()->getId();

        $this->insert(
            "INSERT INTO activity_log (log_name, description, subject_type, subject_id, causer_type, causer_id, properties, created_at, updated_at)
             VALUES ('default', ?, ?, ?, 'App\\\\Models\\\\User', ?, ?, NOW(), NOW())",
            [
                $description,
                $subjectType,
                $subjectId,
                $adminId ?? 0,
                json_encode($properties),
            ]
        );
    }

    /**
     * Get activity logs with optional filters.
     */
    public function getAll(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['event'])) {
            $where[] = "al.description LIKE ?";
            $params[] = '%' . $filters['event'] . '%';
        }

        if (!empty($filters['subject_type'])) {
            $where[] = "al.subject_type LIKE ?";
            $params[] = '%' . $filters['subject_type'] . '%';
        }

        $whereClause = implode(' AND ', $where);
        $limit = (int) $limit;
        $offset = (int) $offset;

        return $this->fetchAll(
            "SELECT al.*, u.name AS causer_name
             FROM activity_log al
             LEFT JOIN users u ON u.id = al.causer_id AND al.causer_type = 'App\\\\Models\\\\User'
             WHERE {$whereClause}
             ORDER BY al.created_at DESC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    /**
     * Count all logs matching filters.
     */
    public function countAll(array $filters = []): int
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['event'])) {
            $where[] = "description LIKE ?";
            $params[] = '%' . $filters['event'] . '%';
        }

        if (!empty($filters['subject_type'])) {
            $where[] = "subject_type LIKE ?";
            $params[] = '%' . $filters['subject_type'] . '%';
        }

        $whereClause = implode(' AND ', $where);

        $row = $this->fetch(
            "SELECT COUNT(*) AS total FROM activity_log WHERE {$whereClause}",
            $params
        );

        return (int) ($row['total'] ?? 0);
    }
}
