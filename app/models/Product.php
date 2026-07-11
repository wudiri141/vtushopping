<?php

class Product
{
    public static function ensureMediaSchema(): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        try {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS product_images (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    product_id INT UNSIGNED NOT NULL,
                    image VARCHAR(255) NOT NULL,
                    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (product_id)
                )'
            );
            $db->exec(
                'CREATE TABLE IF NOT EXISTS product_reviews (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    product_id INT UNSIGNED NOT NULL,
                    customer_name VARCHAR(120) NOT NULL,
                    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
                    comment TEXT NOT NULL,
                    status VARCHAR(30) NOT NULL DEFAULT "approved",
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX (product_id)
                )'
            );
        } catch (PDOException $exception) {
        }
    }

    public static function all(): array
    {
        $db = Database::connection();

        if (!$db) {
            return self::fallbackProducts();
        }

        try {
            $statement = $db->query('SELECT * FROM products ORDER BY created_at DESC, id DESC');
            $products = $statement->fetchAll();
        } catch (PDOException $exception) {
            return self::fallbackProducts();
        }

        return $products ?: self::fallbackProducts();
    }

    public static function find(int $id): array
    {
        $db = Database::connection();

        if ($db) {
            try {
                $statement = $db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
                $statement->execute(['id' => $id]);
                $product = $statement->fetch();

                if ($product) {
                    return $product;
                }
            } catch (PDOException $exception) {
            }
        }

        $products = self::fallbackProducts();
        return $products[$id] ?? reset($products);
    }

    public static function create(array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            throw new RuntimeException('Database connection failed. Check config/database.php and create the simple_vtu_shop database.');
        }

        $statement = $db->prepare(
            'INSERT INTO products (name, short_name, category, collection, description, price, original_price, discount_percent, image, stock, rating, reviews_count)
             VALUES (:name, :short_name, :category, :collection, :description, :price, :original_price, :discount_percent, :image, :stock, :rating, :reviews_count)'
        );

        $statement->execute([
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?: $data['name'],
            'category' => $data['category'],
            'collection' => $data['collection'],
            'description' => $data['description'],
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?: null,
            'discount_percent' => $data['discount_percent'],
            'image' => $data['image'],
            'stock' => $data['stock'],
            'rating' => $data['rating'],
            'reviews_count' => $data['reviews_count'],
        ]);

        $productId = (int) $db->lastInsertId();
        self::replaceImages($productId, $data['images'] ?? [$data['image']]);
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            throw new RuntimeException('Database connection failed.');
        }

        $statement = $db->prepare(
            'UPDATE products
             SET name = :name, short_name = :short_name, category = :category, collection = :collection,
                 description = :description, price = :price, original_price = :original_price,
                 discount_percent = :discount_percent, image = :image, stock = :stock,
                 rating = :rating, reviews_count = :reviews_count
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => $data['name'],
            'short_name' => $data['short_name'] ?: $data['name'],
            'category' => $data['category'],
            'collection' => $data['collection'],
            'description' => $data['description'],
            'price' => $data['price'],
            'original_price' => $data['original_price'] ?: null,
            'discount_percent' => $data['discount_percent'],
            'image' => $data['image'],
            'stock' => $data['stock'],
            'rating' => $data['rating'],
            'reviews_count' => $data['reviews_count'],
        ]);

        if (!empty($data['images'])) {
            self::replaceImages($id, $data['images']);
        }
    }

    public static function delete(int $id): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureMediaSchema();
        $db->prepare('DELETE FROM product_images WHERE product_id = :id')->execute(['id' => $id]);
        $db->prepare('DELETE FROM product_reviews WHERE product_id = :id')->execute(['id' => $id]);
        $db->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
    }

    public static function images(int $productId, ?string $fallback = null): array
    {
        $db = Database::connection();

        if ($db) {
            self::ensureMediaSchema();
            try {
                $statement = $db->prepare('SELECT image FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC');
                $statement->execute(['product_id' => $productId]);
                $images = array_column($statement->fetchAll(), 'image');

                if ($images) {
                    return $images;
                }
            } catch (PDOException $exception) {
            }
        }

        return [$fallback ?: 'images/product-necklace.png'];
    }

    public static function replaceImages(int $productId, array $images): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureMediaSchema();
        $images = array_values(array_filter(array_unique($images)));
        $images = array_slice($images, 0, 5);

        if (!$images) {
            return;
        }

        $db->prepare('DELETE FROM product_images WHERE product_id = :product_id')->execute(['product_id' => $productId]);
        $statement = $db->prepare('INSERT INTO product_images (product_id, image, sort_order) VALUES (:product_id, :image, :sort_order)');

        foreach ($images as $index => $image) {
            $statement->execute([
                'product_id' => $productId,
                'image' => $image,
                'sort_order' => $index,
            ]);
        }
    }

    public static function reviews(int $productId): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureMediaSchema();
        $statement = $db->prepare('SELECT * FROM product_reviews WHERE product_id = :product_id AND status = "approved" ORDER BY created_at DESC, id DESC');
        $statement->execute(['product_id' => $productId]);

        return $statement->fetchAll();
    }

    public static function addReview(int $productId, array $data): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureMediaSchema();
        $rating = max(1, min(5, (int) ($data['rating'] ?? 5)));
        $statement = $db->prepare(
            'INSERT INTO product_reviews (product_id, customer_name, rating, comment, status)
             VALUES (:product_id, :customer_name, :rating, :comment, :status)'
        );
        $statement->execute([
            'product_id' => $productId,
            'customer_name' => trim($data['customer_name'] ?? 'Customer') ?: 'Customer',
            'rating' => $rating,
            'comment' => trim($data['comment'] ?? ''),
            'status' => 'pending',
        ]);

        self::refreshRatingSummary($productId);
    }

    private static function refreshRatingSummary(int $productId): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        $summary = $db->prepare('SELECT AVG(rating) AS rating, COUNT(*) AS total FROM product_reviews WHERE product_id = :product_id AND status = "approved"');
        $summary->execute(['product_id' => $productId]);
        $row = $summary->fetch() ?: ['rating' => 0, 'total' => 0];
        $db->prepare('UPDATE products SET rating = :rating, reviews_count = :reviews_count WHERE id = :id')->execute([
            'rating' => $row['total'] ? round((float) $row['rating'], 1) : 3.5,
            'reviews_count' => (int) $row['total'],
            'id' => $productId,
        ]);
    }

    /**
     * All reviews (any status) joined with the product name, for the admin
     * moderation screen. Newest / pending first.
     */
    public static function reviewsForAdmin(): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        self::ensureMediaSchema();

        try {
            $statement = $db->query(
                'SELECT r.*, p.name AS product_name
                 FROM product_reviews r
                 LEFT JOIN products p ON p.id = r.product_id
                 ORDER BY FIELD(r.status, "pending", "approved", "rejected"), r.created_at DESC'
            );

            return $statement->fetchAll();
        } catch (PDOException $exception) {
            return [];
        }
    }

    public static function setReviewStatus(int $reviewId, string $status): void
    {
        $allowed = ['pending', 'approved', 'rejected'];

        if (!in_array($status, $allowed, true)) {
            return;
        }

        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureMediaSchema();
        $statement = $db->prepare('SELECT product_id FROM product_reviews WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $reviewId]);
        $productId = (int) ($statement->fetchColumn() ?: 0);

        $db->prepare('UPDATE product_reviews SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $reviewId]);

        if ($productId) {
            self::refreshRatingSummary($productId);
        }
    }

    public static function deleteReview(int $reviewId): void
    {
        $db = Database::connection();

        if (!$db) {
            return;
        }

        self::ensureMediaSchema();
        $statement = $db->prepare('SELECT product_id FROM product_reviews WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $reviewId]);
        $productId = (int) ($statement->fetchColumn() ?: 0);

        $db->prepare('DELETE FROM product_reviews WHERE id = :id')->execute(['id' => $reviewId]);

        if ($productId) {
            self::refreshRatingSummary($productId);
        }
    }

    public static function pendingReviewCount(): int
    {
        $db = Database::connection();

        if (!$db) {
            return 0;
        }

        self::ensureMediaSchema();

        try {
            return (int) $db->query('SELECT COUNT(*) FROM product_reviews WHERE status = "pending"')->fetchColumn();
        } catch (PDOException $exception) {
            return 0;
        }
    }

    public static function lowStock(int $threshold = 5): array
    {
        $db = Database::connection();

        if (!$db) {
            return [];
        }

        try {
            $statement = $db->prepare('SELECT * FROM products WHERE stock <= :threshold ORDER BY stock ASC');
            $statement->execute(['threshold' => $threshold]);

            return $statement->fetchAll();
        } catch (PDOException $exception) {
            return [];
        }
    }

    private static function fallbackProducts(): array
    {
        return require __DIR__ . '/../../database/products.php';
    }
}
