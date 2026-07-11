<div class="admin-page-head">
    <div>
        <p class="eyebrow">Sales</p>
        <h1>Delivery</h1>
        <p class="admin-page-sub">Fulfilment status for paid orders</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Order</th><th>Courier</th><th>Status</th><th>Tracking ref</th></tr>
            <?php foreach (($orders ?? []) as $order): ?>
                <?php $status = strtolower((string) ($order['status'] ?? 'pending')); ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($order['reference']) ?></td>
                    <td><?= in_array($status, ['shipped', 'delivered'], true) ? 'Assigned courier' : 'Unassigned' ?></td>
                    <td><span class="admin-badge <?= in_array($status, ['paid', 'delivered'], true) ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                    <td><?= htmlspecialchars($order['reference']) ?>-SHIP</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($orders)): ?>
            <div class="admin-table-empty">No delivery records yet.</div>
        <?php endif; ?>
    </div>
</div>

<p style="color:var(--admin-text-muted);font-size:13px;margin-top:4px;">Courier assignment and live tracking integration is on the roadmap — for now, delivery status mirrors order status.</p>
