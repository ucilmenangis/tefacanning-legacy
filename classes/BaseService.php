<?php

/**
 * BaseService — abstract base for all service classes.
 * Provides Database instance and query convenience methods.
 * Subclasses use $this->fetch() instead of db_fetch().
 */
abstract class BaseService
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function fetch(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    protected function insert(string $sql, array $params = []): int
    {
        return $this->db->insert($sql, $params);
    }

    protected function update(string $sql, array $params = []): int
    {
        return $this->db->update($sql, $params);
    }

    protected function delete(string $sql, array $params = []): int
    {
        return $this->db->delete($sql, $params);
    }
}
