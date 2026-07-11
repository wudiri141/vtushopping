<?php
$range = $range ?? '30';
$ranges = ['today' => 'Today', '7' => 'Last 7 days', '30' => 'Last 30 days', 'all' => 'All time'];
$maxProductCount = !empty($topProducts) ? max($topProducts) : 1;
?>

<div class="admin-page-head">
    <div>
        <p class="eyebrow">Insights</p>
        <h1>Reports &amp; analytics</h1>
        <p class="admin-page-sub">Revenue and product performance, computed from real order data</p>
    </div>
</div>

<div class="admin-tabs">
    <?php foreach ($ranges as $key => $label): ?>
        <a class="admin-tab <?= $range === $key ? 'is-active' : '' ?>" href="<?= app_url('admin/reports') ?>?range=<?= $key ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('payments') ?></span></div>
        <span class="label">Revenue</span>
        <strong>₦<?= number_format($revenue, 2) ?></strong>
        <small>From paid, packed, shipped &amp; delivered orders</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('orders') ?></span></div>
        <span class="label">Orders in range</span>
        <strong><?= $orderCount ?></strong>
        <small><?= $paidCount ?> paid · <?= $pendingCount ?> pending</small>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-top"><span class="admin-stat-icon"><?= admin_icon('products') ?></span></div>
        <span class="label">Low stock</span>
        <strong><?= count($lowStock ?? []) ?></strong>
        <small class="<?= count($lowStock ?? []) ? 'warn' : '' ?>">products need restocking</small>
    </div>
</div>

<div class="admin-grid-2">
    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Top products</h2>
                <p>By quantity sold in this range</p>
            </div>
        </div>
        <div class="admin-panel-body">
            <?php if (empty($topProducts)): ?>
                <div class="admin-empty-state">
                    <?= admin_icon('reports') ?>
                    <h3>No sales in this range</h3>
                    <p>Try a wider date range.</p>
                </div>
            <?php else: ?>
                <?php foreach ($topProducts as $name => $qty): ?>
                    <div style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:600;margin-bottom:5px;">
                            <span><?= htmlspecialchars($name) ?></span>
                            <span><?= (int) $qty ?> sold</span>
                        </div>
                        <div style="background:var(--admin-bg);border-radius:99px;height:8px;overflow:hidden;">
                            <div style="width:<?= max(4, round(($qty / $maxProductCount) * 100)) ?>%;height:100%;background:var(--admin-accent);border-radius:99px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Low stock products</h2>
                <p>At or below the restock threshold</p>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <tr><th>Product</th><th>Stock</th></tr>
                <?php foreach (($lowStock ?? []) as $product): ?>
                    <tr>
                        <td class="admin-cell-title"><?= htmlspecialchars($product['name']) ?></td>
                        <td><span class="admin-badge warning"><?= (int) $product['stock'] ?> left</span></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php if (empty($lowStock)): ?>
                <div class="admin-table-empty">Stock levels look healthy.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
