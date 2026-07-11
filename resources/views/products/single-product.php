<main class="product-page">
    <?php $images = $productImages ?? [$product['image']]; ?>
    <a class="back-link" href="<?= app_url('products') ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
        Back to Collection
    </a>

    <section class="product-detail">
        <div class="gallery">
            <div class="gallery-main">
                <img id="mainProductImage" src="<?= media_url($images[0] ?? $product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="thumb-row">
                <?php foreach (array_slice($images, 0, 5) as $index => $image): ?>
                    <button type="button" class="thumb" data-image="<?= media_url($image) ?>">
                        <img src="<?= media_url($image) ?>" alt="<?= htmlspecialchars($product['name']) ?> thumbnail <?= $index + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="detail-copy">
            <p class="eyebrow"><?= htmlspecialchars($product['category']) ?></p>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div class="rating-line">
                <span class="stars">★★★★☆</span>
                <span><?= number_format($product['rating'], 1) ?> &nbsp; (<?= (int) $product['reviews_count'] ?> reviews)</span>
            </div>
            <strong class="detail-price">₦<?= number_format((float) $product['price'], 2) ?></strong>
            <?php if ((float) ($product['original_price'] ?? 0) > (float) $product['price']): ?>
                <p class="detail-old-price">
                    <span>₦<?= number_format((float) $product['original_price'], 2) ?></span>
                    <?= (int) ($product['discount_percent'] ?? 0) ?>% off
                </p>
            <?php endif; ?>
            <p class="description"><?= htmlspecialchars($product['description']) ?></p>

            <div class="quantity-box">
                <span>Quantity</span>
                <div>
                    <button type="button" data-qty-minus aria-label="Decrease quantity">−</button>
                    <output id="quantityValue">1</output>
                    <button type="button" data-qty-plus aria-label="Increase quantity">+</button>
                </div>
            </div>

            <div class="detail-actions">
                <button
                    type="button"
                    class="primary-cart"
                    data-cart-add
                    data-product-id="<?= (int) $product['id'] ?>"
                    data-product-name="<?= htmlspecialchars($product['name']) ?>"
                    data-product-short-name="<?= htmlspecialchars($product['short_name'] ?: $product['name']) ?>"
                    data-product-category="<?= htmlspecialchars($product['category']) ?>"
                    data-product-price="<?= (float) $product['price'] ?>"
                    data-product-image="<?= htmlspecialchars(media_url($product['image'])) ?>"
                    data-product-discount="<?= (int) ($product['discount_percent'] ?? 0) ?>"
                    data-product-quantity-source="quantityValue"
                >Add to Cart</button>
                <button type="button" class="heart-button" aria-label="Add to wishlist">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="reviews" id="reviews">
        <h2>Customer Reviews</h2>
        <p>See what customers are saying and add your own review.</p>

        <form class="review-form" action="<?= app_url('product/review') ?>" method="post">
            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
            <label>
                Name
                <input type="text" name="customer_name" placeholder="Your name" required>
            </label>
            <label>
                Rating
                <select name="rating" required>
                    <option value="5">5 stars</option>
                    <option value="4">4 stars</option>
                    <option value="3">3 stars</option>
                    <option value="2">2 stars</option>
                    <option value="1">1 star</option>
                </select>
            </label>
            <label class="review-wide">
                Review
                <textarea name="comment" rows="4" placeholder="Write your review" required></textarea>
            </label>
            <button class="auth-submit review-wide" type="submit">Submit review</button>
        </form>

        <?php foreach (($reviews ?? []) as $review): ?>
            <article class="review-card">
                <div>
                    <strong><?= htmlspecialchars($review['customer_name'] ?? 'Customer') ?></strong>
                    <span>verified</span>
                </div>
                <p class="review-stars"><?= str_repeat('★', (int) ($review['rating'] ?? 5)) ?><?= str_repeat('☆', 5 - (int) ($review['rating'] ?? 5)) ?> <small><?= htmlspecialchars(date('M j, Y', strtotime($review['created_at'] ?? 'now'))) ?></small></p>
                <p><?= htmlspecialchars($review['comment'] ?? '') ?></p>
            </article>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?>
            <article class="review-card">
                <p>No reviews yet. Be the first customer to review this product.</p>
            </article>
        <?php endif; ?>
    </section>
</main>
