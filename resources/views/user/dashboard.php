<?php
$orders = $orders ?? [];
$pendingOrders = 0;
foreach ($orders as $order) {
    if (strtolower((string) ($order['status'] ?? 'pending')) === 'pending') {
        $pendingOrders++;
    }
}
$recentOrders = array_slice($orders, 0, 5);
?>

<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a class="active" href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a href="<?= app_url('user/profile') ?>">Profile</a>
        <a href="<?= app_url('products') ?>">Products</a>
        <a href="<?= app_url('cart') ?>">Cart</a>
        <a href="<?= app_url('checkout') ?>">Checkout</a>
        <a href="<?= app_url('user/orders') ?>">Orders</a>
        <a href="<?= app_url('track-order') ?>">Track Order</a>
        <a href="<?= app_url('wishlist') ?>">Wishlist</a>
        <a href="<?= app_url('notifications') ?>">Notifications</a>
        <a href="<?= app_url('support') ?>">Support</a>
        <a href="<?= app_url('logout') ?>">Logout</a>
    </aside>

    <section class="portal-content">
        <div class="portal-head">
            <div>
                <p>Dashboard</p>
                <h1>Welcome back, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Customer') ?></h1>
            </div>
            <a class="login-pill" href="<?= app_url('products') ?>">Shop Now</a>
        </div>

        <div class="metric-grid">
            <article><span>Recent Orders</span><strong><?= count($orders) ?></strong><small><?= $pendingOrders ?> pending delivery</small></article>
            <article><span>Cart</span><strong>Live</strong><small>Ready for checkout</small></article>
            <article><span>Tracking</span><strong>Open</strong><small>Check delivery progress</small></article>
        </div>

        <div class="portal-grid">
            <section class="portal-card">
                <h2>Recent Orders</h2>
                <table class="data-table">
                    <tr><th>Reference</th><th>Status</th><th>Total</th></tr>
                    <?php foreach ($recentOrders as $order): ?>
                        <?php $status = strtolower((string) ($order['status'] ?? 'pending')); ?>
                        <tr>
                            <td><?= htmlspecialchars($order['reference']) ?></td>
                            <td><span class="status <?= in_array($status, ['paid', 'delivered'], true) ? 'good' : 'warn' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                            <td>₦<?= number_format((float) $order['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="3">No orders yet.</td></tr>
                    <?php endif; ?>
                </table>
            </section>

            <section class="portal-card">
                <h2>Notifications</h2>
                <p>Your order has been shipped.</p>
                <p>New promo discount available this week.</p>
            </section>
        </div>

        <section class="portal-card">
            <h2>Quick Actions</h2>
            <div class="quick-actions">
                <a href="<?= app_url('products') ?>">Shop Now</a>
                <a href="<?= app_url('cart') ?>">View Cart</a>
                <a href="<?= app_url('track-order') ?>">Track Order</a>
            </div>
        </section>
    </section>
</main>
