<div class="admin-page-head">
    <div>
        <p class="eyebrow">Customers</p>
        <h1>Users</h1>
        <p class="admin-page-sub"><?= count($users ?? []) ?> registered account(s)</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th></tr>
            <?php foreach (($users ?? []) as $user): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                    <td><span class="admin-badge <?= $user['role'] === 'admin' ? 'info' : 'neutral' ?>"><?= htmlspecialchars($user['role']) ?></span></td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($user['created_at'] ?? 'now'))) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($users)): ?>
            <div class="admin-table-empty">No registered users yet.</div>
        <?php endif; ?>
    </div>
</div>
