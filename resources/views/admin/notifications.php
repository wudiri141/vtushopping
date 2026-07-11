<div class="admin-page-head">
    <div>
        <p class="eyebrow">Insights</p>
        <h1>Notifications</h1>
        <p class="admin-page-sub">A live feed of things in the store that need attention</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Low stock products</h2>
            <p><?= count($lowStockProducts ?? []) ?> item(s)</p>
        </div>
        <a class="admin-btn sm" href="<?= app_url('admin/products') ?>">Manage products</a>
    </div>
    <div>
        <?php foreach (($lowStockProducts ?? []) as $product): ?>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title"><?= htmlspecialchars($product['name']) ?></div>
                    <div class="admin-list-row-sub">Only <?= (int) $product['stock'] ?> left in stock</div>
                </div>
                <span class="admin-badge warning"><?= admin_icon('alert') ?> Low stock</span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($lowStockProducts)): ?>
            <div class="admin-table-empty">No low stock alerts.</div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Pending orders</h2>
            <p>Awaiting payment confirmation</p>
        </div>
        <a class="admin-btn sm" href="<?= app_url('admin/orders') ?>">Manage orders</a>
    </div>
    <div>
        <?php foreach (($pendingOrders ?? []) as $order): ?>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title"><?= htmlspecialchars($order['reference']) ?></div>
                    <div class="admin-list-row-sub"><?= htmlspecialchars($order['customer_name'] ?: $order['customer_email'] ?: 'Guest') ?> · ₦<?= number_format((float) $order['total'], 2) ?></div>
                </div>
                <span class="admin-badge neutral"><?= htmlspecialchars(date('M j', strtotime($order['created_at'] ?? 'now'))) ?></span>
            </div>
        <?php endforeach; ?>
        <?php if (empty($pendingOrders)): ?>
            <div class="admin-table-empty">No pending orders.</div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('reviews') ?></span></div>
        <span class="label">Reviews to moderate</span>
        <strong><?= (int) ($pendingReviewCount ?? 0) ?></strong>
        <a class="admin-btn sm" style="margin-top:8px;" href="<?= app_url('admin/reviews') ?>">Open</a>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('support') ?></span></div>
        <span class="label">Open support tickets</span>
        <strong><?= (int) ($openTicketCount ?? 0) ?></strong>
        <a class="admin-btn sm" style="margin-top:8px;" href="<?= app_url('admin/support') ?>">Open</a>
    </div>
</div>
