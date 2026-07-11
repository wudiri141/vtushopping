<?php

class Banner
{
    public static function ensureSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS banners (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(180) NOT NULL,
                    subtitle TEXT NULL,
                    button_text VARCHAR(80) NULL,
                    link_url VARCHAR(255) NULL,
                    image VARCHAR(255) NULL,
                    placement VARCHAR(40) NOT NULL DEFAULT "hero",
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
        $statement = $db->query('SELECT * FROM banners ORDER BY sort_order ASC, id DESC');

        return $statement->fetchAll();
    }

    public static function active(string $placement = 'hero'): ?array
    {
        $db = Database::connection();

        if (!$db) {
            return null;
        }

        self::ensureSchema();
        $statement = $db->prepare('SELECT * FROM banners WHERE placement = :placement AND is_active = 1 ORDER BY sort_order ASC, id DESC LIMIT 1');
        $statement->execute(['placement' => $placement]);
        $banner = $statement->fetch();

        return $banner ?: null;
    }

    public static function create(array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'INSERT INTO banners (title, subtitle, button_text, link_url, image, placement, is_active, sort_order)
             VALUES (:title, :subtitle, :button_text, :link_url, :image, :placement, :is_active, :sort_order)'
        );
        $statement->execute(self::params($data));
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $statement = $db->prepare(
            'UPDATE banners
             SET title = :title, subtitle = :subtitle, button_text = :button_text, link_url = :link_url,
                 image = :image, placement = :placement, is_active = :is_active, sort_order = :sort_order
             WHERE id = :id'
        );
        $params = self::params($data);
        $params['id'] = $id;
        $statement->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureSchema();
        $db->prepare('DELETE FROM banners WHERE id = :id')->execute(['id' => $id]);
    }

    private static function params(array $data): array
    {
        return [
            'title' => trim($data['title'] ?? ''),
            'subtitle' => trim($data['subtitle'] ?? ''),
            'button_text' => trim($data['button_text'] ?? 'Shop now'),
            'link_url' => trim($data['link_url'] ?? 'products'),
            'image' => $data['image'] ?? null,
            'placement' => trim($data['placement'] ?? 'hero') ?: 'hero',
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
