CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    short_name VARCHAR(180) NULL,
    category VARCHAR(120) NOT NULL,
    collection VARCHAR(120) NOT NULL DEFAULT 'Women''s Fashion',
    description TEXT NULL,
    price DECIMAL(12, 2) NOT NULL,
    original_price DECIMAL(12, 2) NULL,
    discount_percent INT UNSIGNED NOT NULL DEFAULT 0,
    image VARCHAR(255) NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    rating DECIMAL(2, 1) NOT NULL DEFAULT 3.5,
    reviews_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, short_name, category, collection, description, price, original_price, discount_percent, image, stock, rating, reviews_count)
SELECT 'Personalized Necklace', 'Personalised necklace', 'Women''s Jewelry', 'Women''s Fashion',
       'Elegant personalized necklace crafted with premium materials. Perfect for gifting or treating yourself to something special. Each piece is uniquely customized to your specifications.',
       50000.00, 65000.00, 23, 'images/product-necklace.png', 20, 3.5, 12
WHERE NOT EXISTS (SELECT 1 FROM products);
