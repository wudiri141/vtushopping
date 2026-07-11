<main class="luxe-home">
    <section class="luxe-hero">
        <div class="luxe-hero-copy">
            <p class="luxe-kicker">Modern African boutique</p>
            <h1><?= htmlspecialchars($heroBanner['title'] ?? 'Luxury Fashion & Beauty for Modern Women') ?></h1>
            <p><?= htmlspecialchars($heroBanner['subtitle'] ?? 'Discover curated styles, beauty essentials, jewelry, and wedding accessories for elegant everyday shopping.') ?></p>
            <div class="luxe-hero-actions">
                <a class="luxe-primary" href="<?= htmlspecialchars($heroBanner['link_url'] ?? '#new-arrivals') ?>"><?= htmlspecialchars($heroBanner['button_text'] ?? 'Shop New Arrivals') ?></a>
                <a class="luxe-secondary" href="#collections">Explore Collections</a>
            </div>
        </div>
        <div class="luxe-hero-media">
            <img src="<?= !empty($heroBanner['image']) ? media_url($heroBanner['image']) : asset('images/product-listing-reference.png') ?>" alt="Premium fashion and beauty collection">
            <div class="luxe-floating-card">
                <span>New drop</span>
                <strong>Wedding Collection 2026</strong>
            </div>
        </div>
    </section>

    <section class="luxe-section" id="collections">
        <div class="luxe-section-head">
            <p class="luxe-kicker">Shop by mood</p>
            <h2>Featured Categories</h2>
        </div>
        <div class="luxe-category-grid">
            <?php
            $categories = [
                ['Women Fashion', 'Editorial looks for every occasion', 'women'],
                ['Beauty Products', 'Glow-ready skincare and cosmetics', 'beauty-skincare'],
                ['Jewelry', 'Finishing touches with presence', 'women'],
                ['Wedding Accessories', 'Bridal pieces for 2026 celebrations', 'deals'],
                ['Bags & Shoes', 'Polished accessories to complete the fit', 'men'],
            ];
            foreach ($categories as [$name, $copy, $slug]):
            ?>
                <a class="luxe-category-card" href="<?= app_url('products?collection=' . $slug) ?>">
                    <img src="<?= asset($slug === 'beauty-skincare' ? 'images/product-necklace.png' : 'images/product-listing-reference.png') ?>" alt="<?= htmlspecialchars($name) ?>">
                    <span><?= htmlspecialchars($name) ?></span>
                    <p><?= htmlspecialchars($copy) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="luxe-section" id="new-arrivals">
        <div class="luxe-section-head">
            <p class="luxe-kicker"><?= htmlspecialchars($collectionTitle ?? 'New arrivals') ?></p>
            <h2>Curated for You</h2>
            <span><?= count($products) ?> pieces</span>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $item): ?>
                <article class="product-card">
                    <?php $shortName = $item['short_name'] ?: $item['name']; ?>
                    <?php
                    $price = (float) $item['price'];
                    $originalPrice = (float) ($item['original_price'] ?? 0);
                    $discount = (int) ($item['discount_percent'] ?? 0);
                    ?>
                    <?php if ($discount > 0): ?>
                        <span class="discount-badge">-<?= $discount ?>%</span>
                    <?php endif; ?>
                    <a class="wishlist-chip" href="#" aria-label="Add <?= htmlspecialchars($shortName) ?> to wishlist">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"/>
                        </svg>
                    </a>
                    <a class="product-image" href="<?= app_url('product?id=' . $item['id']) ?>">
                        <img src="<?= media_url($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </a>
                    <div class="product-meta">
                        <a class="product-name" href="<?= app_url('product?id=' . $item['id']) ?>"><?= htmlspecialchars($shortName) ?></a>
                        <div class="product-row">
                            <strong>₦<?= number_format($price, 2) ?></strong>
                            <span><b>★★★★★</b> <?= number_format((float) $item['rating'], 1) ?></span>
                        </div>
                        <?php if ($originalPrice > $price): ?>
                            <p class="old-price">₦<?= number_format($originalPrice, 2) ?></p>
                        <?php endif; ?>
                        <button
                            class="outline-cart"
                            type="button"
                            data-cart-add
                            data-product-id="<?= (int) $item['id'] ?>"
                            data-product-name="<?= htmlspecialchars($item['name']) ?>"
                            data-product-short-name="<?= htmlspecialchars($shortName) ?>"
                            data-product-category="<?= htmlspecialchars($item['category']) ?>"
                            data-product-price="<?= $price ?>"
                            data-product-image="<?= htmlspecialchars(media_url($item['image'])) ?>"
                            data-product-discount="<?= $discount ?>"
                        >Add to Cart</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="luxe-wedding-banner">
        <div>
            <p class="luxe-kicker">Occasion dressing</p>
            <h2><?= htmlspecialchars($weddingBanner['title'] ?? 'Wedding Collection 2026') ?></h2>
            <p><?= htmlspecialchars($weddingBanner['subtitle'] ?? 'Luxury bridal styling, jewelry, accessories, and polished pieces for Nigerian celebrations.') ?></p>
            <a class="luxe-primary" href="<?= htmlspecialchars($weddingBanner['link_url'] ?? app_url('products?collection=deals')) ?>"><?= htmlspecialchars($weddingBanner['button_text'] ?? 'Shop Wedding Looks') ?></a>
        </div>
        <img src="<?= !empty($weddingBanner['image']) ? media_url($weddingBanner['image']) : asset('images/product-necklace.png') ?>" alt="Wedding accessories collection">
    </section>

    <section class="luxe-section">
        <div class="luxe-section-head">
            <p class="luxe-kicker">Customer love</p>
            <h2>Reviews from Nigerian Shoppers</h2>
        </div>
        <div class="luxe-testimonials">
            <article><span>AO</span><strong>★★★★★</strong><p>Beautiful packaging and the necklace looked even better in person. Delivery to Abuja was smooth.</p></article>
            <article><span>KM</span><strong>★★★★★</strong><p>The checkout was easy, and the beauty set felt premium. I will shop again.</p></article>
            <article><span>TI</span><strong>★★★★★</strong><p>I ordered wedding accessories for my sister and the styling was exactly what we needed.</p></article>
        </div>
    </section>

    <section class="luxe-newsletter">
        <p class="luxe-kicker">Exclusive access</p>
        <h2>Get Exclusive Drops & Beauty Offers</h2>
        <form>
            <input type="email" placeholder="Enter your email" aria-label="Email address">
            <button type="button">Subscribe</button>
        </form>
    </section>
</main>
