<main class="info-page">
    <section class="info-panel">
        <p class="eyebrow">Payment confirmed</p>
        <h1>Order Successful</h1>
        <p>Your payment was verified and your order has been recorded.</p>
        <?php if (!empty($_GET['reference'])): ?>
            <p><strong>Reference:</strong> <?= htmlspecialchars($_GET['reference']) ?></p>
        <?php endif; ?>
        <a class="checkout-button success-action" href="<?= app_url('products') ?>">Continue shopping</a>
    </section>
</main>
<script>
localStorage.removeItem('vtuCartItems');
localStorage.removeItem('vtuCartDiscount');
localStorage.setItem('vtuCartCount', '0');
</script>
