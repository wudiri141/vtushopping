<?php
$reviews = $reviews ?? [];
$pending = array_values(array_filter($reviews, static fn ($r) => $r['status'] === 'pending'));
$others = array_values(array_filter($reviews, static fn ($r) => $r['status'] !== 'pending'));
?>

<div class="admin-page-head">
    <div>
        <p class="eyebrow">Customers</p>
        <h1>Reviews</h1>
        <p class="admin-page-sub">New reviews are pending until approved — <?= count($pending) ?> waiting now</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Pending approval</h2>
            <p>Not visible on the storefront yet</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Customer</th><th>Product</th><th>Rating</th><th>Comment</th><th>Action</th></tr>
            <?php foreach ($pending as $review): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($review['customer_name']) ?></td>
                    <td><?= htmlspecialchars($review['product_name'] ?? 'Deleted product') ?></td>
                    <td><span class="admin-rating-stars"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span></td>
                    <td><?= htmlspecialchars($review['comment']) ?></td>
                    <td>
                        <div class="admin-btn-row">
                            <form action="<?= app_url('admin/reviews/status') ?>" method="post">
                                <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                                <input type="hidden" name="status" value="approved">
                                <button class="admin-btn sm primary" type="submit"><?= admin_icon('check') ?> Approve</button>
                            </form>
                            <form action="<?= app_url('admin/reviews/status') ?>" method="post">
                                <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button class="admin-btn sm" type="submit"><?= admin_icon('x') ?> Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($pending)): ?>
            <div class="admin-table-empty">Nothing pending — you're all caught up.</div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Approved &amp; rejected</h2>
            <p>Review history</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Customer</th><th>Product</th><th>Rating</th><th>Comment</th><th>Status</th><th></th></tr>
            <?php foreach ($others as $review): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($review['customer_name']) ?></td>
                    <td><?= htmlspecialchars($review['product_name'] ?? 'Deleted product') ?></td>
                    <td><span class="admin-rating-stars"><?= str_repeat('★', (int) $review['rating']) . str_repeat('☆', 5 - (int) $review['rating']) ?></span></td>
                    <td><?= htmlspecialchars($review['comment']) ?></td>
                    <td><span class="admin-badge <?= $review['status'] === 'approved' ? 'success' : 'danger' ?>"><?= htmlspecialchars(ucfirst($review['status'])) ?></span></td>
                    <td>
                        <form action="<?= app_url('admin/reviews/delete') ?>" method="post" onsubmit="return confirm('Delete this review?');">
                            <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                            <button class="admin-btn sm danger" type="submit"><?= admin_icon('trash') ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($others)): ?>
            <div class="admin-table-empty">No reviewed history yet.</div>
        <?php endif; ?>
    </div>
</div>
