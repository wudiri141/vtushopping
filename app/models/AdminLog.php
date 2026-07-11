<?php

class AdminLog
{
    public static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS admin_activity_logs (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT UNSIGNED NULL,
                    user_name VARCHAR(160) NULL,
                    action VARCHAR(160) NOT NULL,
                    entity_type VARCHAR(60) NULL,
                    entity_id INT UNSIGNED NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (created_at)
                )'
            );
        } catch (PDOException $exception) {
        }
    }

    public static function log(string $action, ?string $entityType = null, ?int $entityId = null): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();

        try {
            $statement = $db->prepare(
                'INSERT INTO admin_activity_logs (user_id, user_name, action, entity_type, entity_id)
                 VALUES (:user_id, :user_name, :action, :entity_type, :entity_id)'
            );
            $statement->execute([
                'user_id' => $_SESSION['user_id'] ?? null,
                'user_name' => $_SESSION['user_name'] ?? 'Admin',
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);
        } catch (PDOException $exception) {
        }
    }

    public static function recent(int $limit = 25): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureSchema();

        try {
            $statement = $db->prepare('SELECT * FROM admin_activity_logs ORDER BY created_at DESC, id DESC LIMIT :limit');
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();

            return $statement->fetchAll();
        } catch (PDOException $exception) {
            return [];
        }
    }
}
