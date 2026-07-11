<?php

class Category
{
    public static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS categories (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(120) NOT NULL UNIQUE,
                    description VARCHAR(255) NULL,
                    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )'
            );
        } catch (PDOException $exception) {
        }
    }

    /**
     * Categories with a live count of how many products currently use that
     * category name (case-insensitive match against products.category).
     */
    public static function withCounts(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureSchema();

        try {
            $statement = $db->query(
                'SELECT c.*, (
                    SELECT COUNT(*) FROM products p WHERE LOWER(p.category) = LOWER(c.name)
                 ) AS product_count
                 FROM categories c
                 ORDER BY c.sort_order ASC, c.name ASC'
            );

            return $statement->fetchAll();
        } catch (PDOException $exception) {
            return [];
        }
    }

    public static function create(array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'INSERT INTO categories (name, description, sort_order) VALUES (:name, :description, :sort_order)'
        );
        $statement->execute([
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'UPDATE categories SET name = :name, description = :description, sort_order = :sort_order WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $db->prepare('DELETE FROM categories WHERE id = :id')->execute(['id' => $id]);
    }
}
