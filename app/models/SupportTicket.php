<?php

class SupportTicket
{
    public static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS support_tickets (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NULL,
                    name VARCHAR(160) NOT NULL,
                    email VARCHAR(160) NOT NULL,
                    subject VARCHAR(180) NOT NULL,
                    message TEXT NOT NULL,
                    admin_reply TEXT NULL,
                    status VARCHAR(30) NOT NULL DEFAULT "open",
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )'
            );
        } catch (PDOException $exception) {
        }
    }

    public static function all(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureSchema();
        $statement = $db->query('SELECT * FROM support_tickets ORDER BY FIELD(status, "open", "resolved"), created_at DESC');

        return $statement->fetchAll();
    }

    public static function forUser(int $userId): array
    {
        $db = Database::connection();

        if (!$db || $userId <= 0) {
            return [];
        }

        self::ensureSchema();
        $statement = $db->prepare('SELECT * FROM support_tickets WHERE user_id = :user_id ORDER BY created_at DESC');
        $statement->execute(['user_id' => $userId]);

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
            'INSERT INTO support_tickets (user_id, name, email, subject, message)
             VALUES (:user_id, :name, :email, :subject, :message)'
        );
        $statement->execute([
            'user_id' => $data['user_id'] ?? null,
            'name' => trim((string) ($data['name'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'subject' => trim((string) ($data['subject'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
        ]);
    }

    public static function reply(int $id, string $reply, string $status = 'resolved'): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare('UPDATE support_tickets SET admin_reply = :reply, status = :status WHERE id = :id');
        $statement->execute(['reply' => $reply, 'status' => $status, 'id' => $id]);
    }

    public static function updateStatus(int $id, string $status): void
    {
        $allowed = ['open', 'resolved'];

        if (!in_array($status, $allowed, true)) {
            return;
        }

        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $db->prepare('UPDATE support_tickets SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
    }

    public static function openCount(): int
    {
        $db = Database::connection();

        if (!$db) {
            return 0;
        }

        self::ensureSchema();

        try {
            return (int) $db->query('SELECT COUNT(*) FROM support_tickets WHERE status = "open"')->fetchColumn();
        } catch (PDOException $exception) {
            return 0;
        }
    }
}
