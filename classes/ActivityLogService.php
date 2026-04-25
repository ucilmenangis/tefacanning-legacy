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
class ActivityLogService
{
    /**
     * Log an activity.
     */
    public function log(string $event, string $subjectType, int $subjectId, string $description, array $properties = []): void
    {
        $adminId = getAdminId();

        db_insert(
            "INSERT INTO activity_log (log_name, description, subject_type, subject_id, causer_type, causer_id, properties, created_at, updated_at)
             VALUES ('default', ?, ?, ?, 'App\\Models\\User', ?, ?, NOW(), NOW())",
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
        $params[] = $limit;
        $params[] = $offset;

        return db_fetch_all(
            "SELECT al.*, u.name AS causer_name
             FROM activity_log al
             LEFT JOIN users u ON u.id = al.causer_id AND al.causer_type = 'App\\Models\\User'
             WHERE {$whereClause}
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
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

        $row = db_fetch(
            "SELECT COUNT(*) AS total FROM activity_log WHERE {$whereClause}",
            $params
        );

        return (int) ($row['total'] ?? 0);
    }
}
