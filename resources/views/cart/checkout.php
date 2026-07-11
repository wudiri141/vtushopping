<?php
$items = array_values($cartItems ?? []);
if (count($items) === 1) {
    $items[] = $items[0];
}
$subtotal = array_reduce($items, fn ($sum, $item) => $sum + (float) $item['price'], 0);
?>
<main class="checkout-page">
    <section class="checkout-summary-strip">
        <span>Order summary</span>
        <strong data-cart-subtotal>₦<?= number_format($subtotal, 2) ?> NGN</strong>
    </section>

    <form class="checkout-form" data-checkout-form>
        <section>
            <div class="checkout-section-head">
                <h1>Contact</h1>
            </div>
            <input type="email" name="email" placeholder="Email address" value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>" required>
            <label class="inline-check"><input type="checkbox" checked> Message me with order updates</label>
        </section>

        <section>
            <h2>Delivery</h2>
            <div class="delivery-tabs">
                <label><input type="radio" name="delivery" checked> Ship</label>
                <label><input type="radio" name="delivery"> Pickup</label>
            </div>
            <select><option>Nigeria</option></select>
            <div class="two-col">
                <input type="text" name="first_name" placeholder="First name" required>
                <input type="text" name="last_name" placeholder="Last name" required>
            </div>
            <input type="text" placeholder="Address">
            <input type="text" placeholder="Apartment, suite, etc. (optional)">
            <div class="three-col">
                <input type="text" placeholder="City">
                <select><option>Federal Capital Territory</option><option>Lagos</option><option>Abia</option></select>
                <input type="text" placeholder="Postal code (optional)">
            </div>
            <input type="tel" name="phone" placeholder="Phone" required>
            <label class="inline-check"><input type="checkbox"> Save this information for next time</label>
        </section>

        <section>
            <h2>Shipping method</h2>
            <div class="checkout-choice">
                <span>Standard Shipping</span>
                <strong><del>₦8,000.00</del> FREE</strong>
            </div>
        </section>

        <section>
            <h2>Payment</h2>
            <p>All transactions are secure and encrypted.</p>
            <div class="checkout-choice">
                <span>Paystack</span>
                <strong>Mastercard · Visa · Verve</strong>
            </div>
            <div class="payment-note">You will be redirected to Paystack to complete your purchase.</div>
        </section>

        <section>
            <h2>Billing address</h2>
            <label class="checkout-choice"><input type="radio" name="billing" checked> Same as shipping address</label>
            <label class="checkout-choice"><input type="radio" name="billing"> Use a different billing address</label>
        </section>

        <section class="checkout-total">
            <button type="button" class="discount-small">Add discount</button>
            <div class="checkout-item-summary" data-checkout-items>
                <span>Your cart is empty.</span>
            </div>
            <div class="discount-row" data-cart-discount-row hidden>
                <span data-cart-discount-label>Discount</span>
                <strong data-cart-discount>-₦0.00</strong>
            </div>
            <div>
                <span>Total</span>
                <strong data-cart-subtotal>₦<?= number_format($subtotal, 2) ?> NGN</strong>
            </div>
            <p class="payment-error" data-payment-message></p>
            <button type="button" class="pay-now" data-paystack-pay>Pay now</button>
        </section>
    </form>
</main>
