<?php
$items = array_values($cartItems ?? []);
$subtotal = array_reduce($items, fn ($sum, $item) => $sum + (float) $item['price'], 0);
?>
<script type="application/json" data-cart-seed><?= json_encode(array_map(static fn ($item) => [
    'id' => (string) $item['id'],
    'name' => $item['name'],
    'shortName' => $item['short_name'] ?: $item['name'],
    'category' => $item['category'],
    'price' => (float) $item['price'],
    'image' => media_url($item['image']),
    'discount' => (int) ($item['discount_percent'] ?? 0),
    'quantity' => 1,
], $items), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?></script>
<main class="store-cart-page">
    <div class="cart-title-row">
        <h1>Your cart</h1>
        <a href="<?= app_url('products') ?>">Continue shopping</a>
    </div>

    <section class="cart-items-panel" data-cart-page-items>
    </section>

    <section class="cart-page-empty" data-cart-page-empty>
        <h2>Your cart is currently empty.</h2>
        <p>Try one of these collections to get started.</p>
        <div>
            <a href="<?= app_url('products?collection=women') ?>">Women's Fashion</a>
            <a href="<?= app_url('products?collection=men') ?>">Men's Fashion</a>
            <a href="<?= app_url('products?collection=beauty-skincare') ?>">Beauty & Skincare</a>
            <a href="<?= app_url('products?collection=deals') ?>">Deals</a>
        </div>
    </section>

    <section class="cart-tools-grid">
        <div class="cart-tool-card">
            <h2>Estimate shipping</h2>
            <select><option>Nigeria</option></select>
            <select><option>Abia</option><option>Lagos</option><option>Federal Capital Territory</option></select>
            <input type="text" placeholder="Postal/ZIP code">
            <button type="button" data-shipping-calculate>Calculate</button>
            <p class="shipping-note" data-shipping-note>Enter your address to estimate shipping.</p>
        </div>

        <div class="cart-tool-card">
            <h2>Discount</h2>
            <input type="text" placeholder="Discount code" data-discount-code>
            <button type="button" data-discount-apply>Apply</button>
            <p class="discount-note" data-discount-note></p>
        </div>
    </section>

    <section class="cart-summary-panel">
        <p data-shipping-message>Spend ₦200,000.00 more to reach free shipping!</p>
        <div class="shipping-progress"><span data-shipping-progress></span></div>
        <div class="summary-row discount-row" data-cart-discount-row hidden>
            <span data-cart-discount-label>Discount</span>
            <strong data-cart-discount>-₦0.00</strong>
        </div>
        <div class="summary-row">
            <span>Total</span>
            <strong data-cart-subtotal>₦<?= number_format($subtotal, 2) ?> NGN</strong>
        </div>
        <small>Taxes and shipping calculated at checkout</small>
        <a class="checkout-button" href="<?= app_url('checkout') ?>">Check out</a>
    </section>
</main>
