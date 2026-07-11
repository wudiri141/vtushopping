<?php

class Order
{
    public static function all(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureSchema();
        $statement = $db->query('SELECT * FROM orders ORDER BY created_at DESC, id DESC');

        return $statement->fetchAll();
    }

    public static function forUser(int $userId): array
    {
        $db = Database::connection();

        if (!$db || $userId <= 0) {
            return [];
        }

        self::ensureSchema();
        $statement = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC, id DESC');
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public static function create(array $data): ?int
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'INSERT INTO orders (user_id, reference, total, status, customer_email, customer_name, customer_phone, items_json, payment_provider)
             VALUES (:user_id, :reference, :total, :status, :customer_email, :customer_name, :customer_phone, :items_json, :payment_provider)'
        );
        $statement->execute([
            'user_id' => $data['user_id'] ?? 0,
            'reference' => $data['reference'],
            'total' => $data['total'],
            'status' => $data['status'] ?? 'pending',
            'customer_email' => $data['customer_email'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'items_json' => json_encode($data['items'] ?? [], JSON_UNESCAPED_SLASHES),
            'payment_provider' => $data['payment_provider'] ?? 'paystack',
        ]);

        return (int) $db->lastInsertId();
    }

    public static function markPaid(string $reference): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare('UPDATE orders SET status = :status WHERE reference = :reference');
        $statement->execute(['status' => 'paid', 'reference' => $reference]);
    }

    public static function findByReference(string $reference): ?array
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        self::ensureSchema();
        $statement = $db->prepare('SELECT * FROM orders WHERE reference = :reference LIMIT 1');
        $statement->execute(['reference' => $reference]);
        $order = $statement->fetch();

        return $order ?: null;
    }

    public static function findForTracking(string $reference, string $email = ''): ?array
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        self::ensureSchema();
        if ($email !== '') {
            $statement = $db->prepare('SELECT * FROM orders WHERE reference = :reference AND customer_email = :email LIMIT 1');
            $statement->execute(['reference' => $reference, 'email' => $email]);
        } else {
            $statement = $db->prepare('SELECT * FROM orders WHERE reference = :reference LIMIT 1');
            $statement->execute(['reference' => $reference]);
        }
        $order = $statement->fetch();

        return $order ?: null;
    }

    public static function updateStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'paid', 'packed', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            return;
        }

        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $statement->execute(['status' => $status, 'id' => $id]);
    }

    private static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        $db->exec(
            "CREATE TABLE IF NOT EXISTS orders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL DEFAULT 0,
                reference VARCHAR(80) NOT NULL UNIQUE,
                total DECIMAL(12, 2) NOT NULL DEFAULT 0,
                status VARCHAR(40) NOT NULL DEFAULT 'pending',
                customer_email VARCHAR(160) NULL,
                customer_name VARCHAR(160) NULL,
                customer_phone VARCHAR(60) NULL,
                items_json LONGTEXT NULL,
                payment_provider VARCHAR(40) NOT NULL DEFAULT 'paystack',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $columns = [
            'customer_email' => "ALTER TABLE orders ADD COLUMN customer_email VARCHAR(160) NULL",
            'customer_name' => "ALTER TABLE orders ADD COLUMN customer_name VARCHAR(160) NULL",
            'customer_phone' => "ALTER TABLE orders ADD COLUMN customer_phone VARCHAR(60) NULL",
            'items_json' => "ALTER TABLE orders ADD COLUMN items_json LONGTEXT NULL",
            'payment_provider' => "ALTER TABLE orders ADD COLUMN payment_provider VARCHAR(40) NOT NULL DEFAULT 'paystack'",
        ];

        foreach ($columns as $column => $sql) {
            try {
                $statement = $db->prepare(
                    'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
                );
                $statement->execute(['table' => 'orders', 'column' => $column]);
                if ((int) $statement->fetchColumn() === 0) {
                    $db->exec($sql);
                }
            } catch (PDOException $exception) {
            }
        }
    }
}
