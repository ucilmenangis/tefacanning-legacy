<?php

/**
 * ProductService — handles product CRUD operations.
 *
 * Methods:
 *   getAll()              — all products ordered by name
 *   getById($id)         — single product
 *   create($data)        — insert, return new ID
 *   update($id, $data)   — update product
 *   softDelete($id)      — set deleted_at
 */
class ProductService
{
    /**
     * Get all active products ordered by name.
     */
    public function getAll(): array
    {
        return db_fetch_all(
            "SELECT id, name, sku, price, stock, is_active
             FROM products
             WHERE deleted_at IS NULL
             ORDER BY name ASC"
        );
    }

    /**
     * Get single product by ID.
     */
    public function getById(int $id): ?array
    {
        return db_fetch(
            "SELECT id, name, sku, price, stock, is_active
             FROM products
             WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    /**
     * Create new product. Returns new ID.
     */
    public function create(array $data): int
    {
        return db_insert(
            "INSERT INTO products (name, sku, price, stock, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['sku'],
                $data['price'],
                $data['stock'],
                $data['is_active'] ?? 1,
            ]
        );
    }

    /**
     * Update product by ID.
     */
    public function update(int $id, array $data): void
    {
        db_update(
            "UPDATE products SET name = ?, sku = ?, price = ?, stock = ?, is_active = ?, updated_at = NOW()
             WHERE id = ? AND deleted_at IS NULL",
            [
                $data['name'],
                $data['sku'],
                $data['price'],
                $data['stock'],
                $data['is_active'] ?? 1,
                $id,
            ]
        );
    }

    /**
     * Soft delete product.
     */
    public function softDelete(int $id): void
    {
        db_update(
            "UPDATE products SET deleted_at = NOW(), updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}
