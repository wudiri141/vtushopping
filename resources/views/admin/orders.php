<?php $badgeMap = ['pending' => 'warning', 'paid' => 'success', 'packed' => 'info', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger']; ?>

<div class="admin-page-head">
    <div>
        <p class="eyebrow">Sales</p>
        <h1>Orders</h1>
        <p class="admin-page-sub"><?= count($orders ?? []) ?> order(s) total</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Reference</th><th>Customer</th><th>Status</th><th>Total</th><th>Created</th><th>Update status</th></tr>
            <?php foreach (($orders ?? []) as $order): ?>
                <?php $status = strtolower((string) $order['status']); ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($order['reference']) ?></td>
                    <td>
                        <?= htmlspecialchars($order['customer_name'] ?: $order['customer_email'] ?: 'Guest') ?>
                        <span class="admin-cell-sub"><?= htmlspecialchars($order['customer_phone'] ?? '') ?></span>
                    </td>
                    <td><span class="admin-badge <?= $badgeMap[$status] ?? 'neutral' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                    <td>₦<?= number_format((float) $order['total'], 2) ?></td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at'] ?? 'now'))) ?></td>
                    <td>
                        <form class="admin-inline-form" action="<?= app_url('admin/orders/status') ?>" method="post">
                            <input type="hidden" name="id" value="<?= (int) $order['id'] ?>">
                            <select name="status">
                                <?php foreach (['pending', 'paid', 'packed', 'shipped', 'delivered', 'cancelled'] as $option): ?>
                                    <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="admin-btn sm primary" type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($orders)): ?>
            <div class="admin-table-empty">No orders yet.</div>
        <?php endif; ?>
    </div>
</div>
