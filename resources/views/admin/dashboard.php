<?php
$orders = $orders ?? [];
$paidStatuses = ['paid', 'packed', 'shipped', 'delivered'];
$salesTotal = 0.0;
$pendingOrders = 0;
$recentOrders = array_slice($orders, 0, 6);
foreach ($orders as $order) {
    $status = strtolower((string) ($order['status'] ?? 'pending'));
    if (in_array($status, $paidStatuses, true)) {
        $salesTotal += (float) ($order['total'] ?? 0);
    }
    if ($status === 'pending') {
        $pendingOrders++;
    }
}
$lowStock = $lowStock ?? [];
$badgeMap = ['pending' => 'warning', 'paid' => 'success', 'packed' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger'];
?>

<div class="admin-page-head">
    <div>
        <p class="eyebrow">Overview</p>
        <h1>Store dashboard</h1>
        <p class="admin-page-sub">A snapshot of sales, catalog, and what needs your attention.</p>
    </div>
    <div class="admin-head-actions">
        <a class="admin-btn" href="<?= app_url('admin/reports') ?>"><?= admin_icon('reports') ?> View reports</a>
        <a class="admin-btn primary" href="<?= app_url('admin/products') ?>"><?= admin_icon('plus') ?> Add product</a>
    </div>
</div>

<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('payments') ?></span></div>
        <span class="label">Total sales</span>
        <strong>₦<?= number_format($salesTotal, 2) ?></strong>
        <small>Paid order revenue</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('orders') ?></span></div>
        <span class="label">Orders</span>
        <strong><?= count($orders) ?></strong>
        <small class="<?= $pendingOrders ? 'warn' : '' ?>"><?= $pendingOrders ?> pending</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('users') ?></span></div>
        <span class="label">Customers</span>
        <strong><?= count($users ?? []) ?></strong>
        <small>Registered users</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('products') ?></span></div>
        <span class="label">Products</span>
        <strong><?= count($products ?? []) ?></strong>
        <small class="<?= count($lowStock) ? 'warn' : '' ?>"><?= count($lowStock) ?> low stock</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('reviews') ?></span></div>
        <span class="label">Reviews</span>
        <strong><?= (int) ($pendingReviews ?? 0) ?></strong>
        <small class="<?= ($pendingReviews ?? 0) ? 'warn' : '' ?>">awaiting moderation</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('support') ?></span></div>
        <span class="label">Support</span>
        <strong><?= (int) ($openTickets ?? 0) ?></strong>
        <small class="<?= ($openTickets ?? 0) ? 'warn' : '' ?>">open tickets</small>
    </div>
</div>

<div class="admin-grid-2">
    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Recent orders</h2>
                <p>Latest 6 orders across the store</p>
            </div>
            <a class="admin-btn sm" href="<?= app_url('admin/orders') ?>">View all</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <tr><th>Reference</th><th>Customer</th><th>Status</th><th>Total</th></tr>
                <?php foreach ($recentOrders as $order): ?>
                    <?php $status = strtolower((string) ($order['status'] ?? 'pending')); ?>
                    <tr>
                        <td><?= htmlspecialchars($order['reference']) ?></td>
                        <td><?= htmlspecialchars($order['customer_name'] ?: $order['customer_email'] ?: 'Guest') ?></td>
                        <td><span class="admin-badge <?= $badgeMap[$status] ?? 'neutral' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                        <td>₦<?= number_format((float) $order['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php if (empty($recentOrders)): ?>
                <div class="admin-table-empty">No orders yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Needs attention</h2>
                <p>Things worth a look today</p>
            </div>
        </div>
        <div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Low stock products</div>
                    <div class="admin-list-row-sub"><?= count($lowStock) ?> product(s) at or below threshold</div>
                </div>
                <a class="admin-btn sm" href="<?= app_url('admin/products') ?>">Review</a>
            </div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Pending reviews</div>
                    <div class="admin-list-row-sub"><?= (int) ($pendingReviews ?? 0) ?> review(s) waiting for approval</div>
                </div>
                <a class="admin-btn sm" href="<?= app_url('admin/reviews') ?>">Moderate</a>
            </div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Open support tickets</div>
                    <div class="admin-list-row-sub"><?= (int) ($openTickets ?? 0) ?> customer message(s) awaiting reply</div>
                </div>
                <a class="admin-btn sm" href="<?= app_url('admin/support') ?>">Respond</a>
            </div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Pending orders</div>
                    <div class="admin-list-row-sub"><?= $pendingOrders ?> order(s) awaiting payment confirmation</div>
                </div>
                <a class="admin-btn sm" href="<?= app_url('admin/orders') ?>">View</a>
            </div>
        </div>
    </div>
</div>
