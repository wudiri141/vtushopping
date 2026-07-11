ALTER TABLE products
    ADD COLUMN short_name VARCHAR(180) NULL AFTER name,
    ADD COLUMN collection VARCHAR(120) NOT NULL DEFAULT 'Women''s Fashion' AFTER category,
    ADD COLUMN original_price DECIMAL(12, 2) NULL AFTER price,
    ADD COLUMN discount_percent INT UNSIGNED NOT NULL DEFAULT 0 AFTER original_price,
    ADD COLUMN rating DECIMAL(2, 1) NOT NULL DEFAULT 3.5 AFTER stock,
    ADD COLUMN reviews_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER rating;
