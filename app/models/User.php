<?php

class User
{
    public static function ensureAuthSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        $columns = [
            'email_verified' => "ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 1",
            'verification_token' => "ALTER TABLE users ADD COLUMN verification_token VARCHAR(128) NULL",
            'reset_token' => "ALTER TABLE users ADD COLUMN reset_token VARCHAR(128) NULL",
            'reset_expires' => "ALTER TABLE users ADD COLUMN reset_expires DATETIME NULL",
            'login_otp' => "ALTER TABLE users ADD COLUMN login_otp VARCHAR(10) NULL",
            'login_otp_expires' => "ALTER TABLE users ADD COLUMN login_otp_expires DATETIME NULL",
            'last_otp_verified_at' => "ALTER TABLE users ADD COLUMN last_otp_verified_at DATETIME NULL",
            'password_changed_at' => "ALTER TABLE users ADD COLUMN password_changed_at DATETIME NULL",
        ];

        foreach ($columns as $column => $sql) {
            if (!self::columnExists('users', $column)) {
                try {
                    $db->exec($sql);
                } catch (PDOException $exception) {
                }
            }
        }
    }

    private static function columnExists(string $table, string $column): bool
    {
        $db = Database::connection();

        if (!$db) {
            return false;
        }

        $statement = $db->prepare(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
        );
        $statement->execute(['table' => $table, 'column' => $column]);

        return (int) $statement->fetchColumn() > 0;
    }

    public static function all(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        try {
            $statement = $db->query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC, id DESC');
            return $statement->fetchAll();
        } catch (PDOException $exception) {
            return [];
        }
    }

    public static function create(array $data): int
    {
        $db = Database::connection();

        if (!$db) {
            throw new RuntimeException('Database connection failed.');
        }

        self::ensureAuthSchema();

        $statement = $db->prepare(
            'INSERT INTO users (name, email, phone, password, role, email_verified, verification_token)
             VALUES (:name, :email, :phone, :password, :role, :email_verified, :verification_token)'
        );

        $statement->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user',
            'email_verified' => (int) ($data['email_verified'] ?? 0),
            'verification_token' => $data['verification_token'] ?? null,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        $statement = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        $statement = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public static function findByToken(string $column, string $token): ?array
    {
        if (!in_array($column, ['verification_token', 'reset_token'], true)) {
            return null;
        }

        $db = Database::connection();

        if (!$db) {
            return null;
        }

        self::ensureAuthSchema();
        $statement = $db->prepare("SELECT * FROM users WHERE {$column} = :token LIMIT 1");
        $statement->execute(['token' => $token]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public static function updateAuthFields(int $id, array $fields): void
    {
        $db = Database::connection();

        if (!$db || !$fields) {
            return;
        }

        self::ensureAuthSchema();
        $allowed = [
            'email_verified',
            'verification_token',
            'reset_token',
            'reset_expires',
            'login_otp',
            'login_otp_expires',
            'last_otp_verified_at',
            'password_changed_at',
            'password',
        ];
        $updates = [];
        $params = ['id' => $id];

        foreach ($fields as $field => $value) {
            if (!in_array($field, $allowed, true)) {
                continue;
            }
            $updates[] = "{$field} = :{$field}";
            $params[$field] = $value;
        }

        if (!$updates) {
            return;
        }

        $statement = $db->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = :id');
        $statement->execute($params);
    }
}
