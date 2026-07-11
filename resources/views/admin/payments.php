<div class="admin-page-head">
    <div>
        <p class="eyebrow">Sales</p>
        <h1>Payments</h1>
        <p class="admin-page-sub">Transactions recorded through Paystack</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Reference</th><th>Provider</th><th>Amount</th><th>Status</th><th>Created</th></tr>
            <?php foreach (($transactions ?? []) as $transaction): ?>
                <?php $status = strtolower((string) ($transaction['status'] ?? 'pending')); ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($transaction['reference']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($transaction['provider'] ?? 'Paystack')) ?></td>
                    <td>₦<?= number_format((float) $transaction['amount'], 2) ?></td>
                    <td><span class="admin-badge <?= $status === 'success' ? 'success' : 'warning' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($transaction['created_at'] ?? 'now'))) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($transactions)): ?>
            <div class="admin-table-empty">No payment records yet.</div>
        <?php endif; ?>
    </div>
</div>
