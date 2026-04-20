<?php

/**
 * Query helper functions — PDO wrapper
 *
 * These functions make database queries shorter to write
 * while still using prepared statements (safe from SQL injection).
 *
 * Usage:
 *   $product = db_fetch("SELECT * FROM products WHERE id = ?", [1]);
 *   $products = db_fetch_all("SELECT * FROM products WHERE is_active = ?", [1]);
 *   $id = db_insert("INSERT INTO products (name, price) VALUES (?, ?)", ["Sarden", 25000]);
 */

/**
 * Get the PDO connection instance.
 * Loads config/database.php once, then returns the same $conn.
 */
function db(): PDO
{
  static $conn = null;

  if ($conn === null) {
    require_once __DIR__ . "/../config/database.php";
    // $conn is created in config/database.php
    global $conn;
  }

  return $conn;
}

/**
 * Fetch a single row from the database.
 * Returns associative array or null if not found.
 *
 * Example: $user = db_fetch("SELECT * FROM users WHERE id = ?", [1]);
 * Result:  ['id' => 1, 'name' => 'Admin', 'email' => '...'] or null
 */
function db_fetch(string $sql, array $params = []): ?array
{
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

/**
 * Fetch all rows from the database.
 * Returns array of associative arrays. Empty array if no results.
 *
 * Example: $products = db_fetch_all("SELECT * FROM products WHERE is_active = ?", [1]);
 * Result:  [['id' => 1, ...], ['id' => 2, ...]]
 */
function db_fetch_all(string $sql, array $params = []): array
{
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Insert a row into the database.
 * Returns the auto-increment ID of the new row.
 *
 * Example: $id = db_insert("INSERT INTO products (name, price) VALUES (?, ?)", ["Sarden", 25000]);
 * Result:  15  (the new product's id)
 */
function db_insert(string $sql, array $params = []): int
{
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return (int) db()->lastInsertId();
}

/**
 * Update rows in the database.
 * Returns the number of rows affected.
 *
 * Example: $count = db_update("UPDATE products SET price = ? WHERE id = ?", [30000, 1]);
 * Result:  1  (one row was updated)
 */
function db_update(string $sql, array $params = []): int
{
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt->rowCount();
}

/**
 * Delete rows from the database.
 * Returns the number of rows deleted.
 *
 * Note: For soft deletes, use db_update() with "SET deleted_at = NOW()" instead.
 *
 * Example: $count = db_delete("DELETE FROM products WHERE id = ?", [1]);
 * Result:  1  (one row was deleted)
 */
function db_delete(string $sql, array $params = []): int
{
  $stmt = db()->prepare($sql);
  $stmt->execute($params);
  return $stmt->rowCount();
}
