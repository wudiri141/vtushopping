CREATE DATABASE IF NOT EXISTS simple_vtu_shop
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE simple_vtu_shop;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(40) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    email_verified TINYINT(1) NOT NULL DEFAULT 1,
    verification_token VARCHAR(128) NULL,
    reset_token VARCHAR(128) NULL,
    reset_expires DATETIME NULL,
    login_otp VARCHAR(10) NULL,
    login_otp_expires DATETIME NULL,
    last_otp_verified_at DATETIME NULL,
    password_changed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

CREATE TABLE IF NOT EXISTS product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (product_id)
);

CREATE TABLE IF NOT EXISTS product_reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    comment TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (product_id)
);

CREATE TABLE IF NOT EXISTS banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    subtitle TEXT NULL,
    button_text VARCHAR(80) NULL,
    link_url VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    placement VARCHAR(40) NOT NULL DEFAULT 'hero',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    reference VARCHAR(80) NOT NULL UNIQUE,
    total DECIMAL(12, 2) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    customer_email VARCHAR(160) NULL,
    customer_name VARCHAR(160) NULL,
    customer_phone VARCHAR(60) NULL,
    items_json LONGTEXT NULL,
    payment_provider VARCHAR(40) NOT NULL DEFAULT 'paystack',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NULL,
    provider VARCHAR(40) NOT NULL,
    reference VARCHAR(100) NOT NULL UNIQUE,
    amount DECIMAL(12, 2) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, phone, password, role)
VALUES ('Admin User', 'admin@vtu.test', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin';

INSERT INTO products (name, short_name, category, collection, description, price, original_price, discount_percent, image, stock, rating, reviews_count)
SELECT 'Personalized Necklace', 'Personalised necklace', 'Women''s Jewelry', 'Women''s Fashion',
       'Elegant personalized necklace crafted with premium materials. Perfect for gifting or treating yourself to something special. Each piece is uniquely customized to your specifications.',
       50000.00, 65000.00, 23, 'images/product-necklace.png', 20, 3.5, 12
WHERE NOT EXISTS (SELECT 1 FROM products);
