<?php

class Transaction
{
    public static function all(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureSchema();
        $statement = $db->query('SELECT * FROM transactions ORDER BY created_at DESC, id DESC');

        return $statement->fetchAll();
    }

    public static function create(array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'INSERT INTO transactions (order_id, provider, reference, amount, status)
             VALUES (:order_id, :provider, :reference, :amount, :status)
             ON DUPLICATE KEY UPDATE status = VALUES(status), amount = VALUES(amount)'
        );
        $statement->execute([
            'order_id' => $data['order_id'] ?? null,
            'provider' => $data['provider'] ?? 'paystack',
            'reference' => $data['reference'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'pending',
        ]);
    }

    public static function markSuccessful(string $reference): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare('UPDATE transactions SET status = :status WHERE reference = :reference');
        $statement->execute(['status' => 'success', 'reference' => $reference]);
    }

    private static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        $db->exec(
            "CREATE TABLE IF NOT EXISTS transactions (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id INT UNSIGNED NULL,
                provider VARCHAR(40) NOT NULL DEFAULT 'paystack',
                reference VARCHAR(100) NOT NULL UNIQUE,
                amount DECIMAL(12, 2) NOT NULL DEFAULT 0,
                status VARCHAR(40) NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        );
    }
}
