<?php

class Setting
{
    private static array $defaults = [
        'store_name' => 'VTU Shopping Store',
        'support_email' => 'support@vtutopup.com.ng',
        'support_phone' => '',
        'free_shipping_threshold' => '200000',
        'low_stock_threshold' => '5',
    ];

    public static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS settings (
                    setting_key VARCHAR(80) NOT NULL PRIMARY KEY,
                    setting_value TEXT NULL,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )'
            );
        } catch (PDOException $exception) {
        }
    }

    public static function all(): array
    {
        $db = Database::connection();
        $values = self::$defaults;

        if (!$db) {
            return $values;
        }

        self::ensureSchema();

        try {
            $statement = $db->query('SELECT setting_key, setting_value FROM settings');
            foreach ($statement->fetchAll() as $row) {
                $values[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $exception) {
        }

        return $values;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();

        return (string) ($all[$key] ?? $default);
    }

    public static function setMany(array $values): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );

        foreach ($values as $key => $value) {
            $statement->execute(['key' => $key, 'value' => (string) $value]);
        }
    }
}
