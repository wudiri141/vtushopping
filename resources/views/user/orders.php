<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a href="<?= app_url('user/profile') ?>">Profile</a>
        <a class="active" href="<?= app_url('user/orders') ?>">Orders</a>
        <a href="<?= app_url('track-order') ?>">Track Order</a>
        <a href="<?= app_url('logout') ?>">Logout</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head"><div><p>Orders</p><h1>My Orders</h1></div></div>
        <section class="portal-card">
            <table class="data-table">
                <tr><th>Reference</th><th>Payment</th><th>Delivery</th><th>Total</th><th>Created</th></tr>
                <?php foreach (($orders ?? []) as $order): ?>
                    <?php $status = strtolower((string) ($order['status'] ?? 'pending')); ?>
                    <tr>
                        <td><?= htmlspecialchars($order['reference']) ?></td>
                        <td><?= in_array($status, ['paid', 'packed', 'shipped', 'delivered'], true) ? 'Paid' : 'Pending' ?></td>
                        <td><span class="status <?= in_array($status, ['paid', 'delivered'], true) ? 'good' : 'warn' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                        <td>₦<?= number_format((float) $order['total'], 2) ?></td>
                        <td><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at'] ?? 'now'))) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="5">You do not have any orders yet.</td></tr>
                <?php endif; ?>
            </table>
        </section>
    </section>
</main>
