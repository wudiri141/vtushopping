<?php
$tickets = $tickets ?? [];
$open = array_values(array_filter($tickets, static fn ($t) => $t['status'] === 'open'));
$resolved = array_values(array_filter($tickets, static fn ($t) => $t['status'] === 'resolved'));
?>

<div class="admin-page-head">
    <div>
        <p class="eyebrow">Customers</p>
        <h1>Support</h1>
        <p class="admin-page-sub"><?= count($open) ?> open, <?= count($resolved) ?> resolved</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Open tickets</h2>
            <p>Messages sent from the customer help center</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Customer</th><th>Subject</th><th>Message</th><th>Received</th><th>Reply &amp; resolve</th></tr>
            <?php foreach ($open as $ticket): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($ticket['name']) ?><span class="admin-cell-sub"><?= htmlspecialchars($ticket['email']) ?></span></td>
                    <td><?= htmlspecialchars($ticket['subject']) ?></td>
                    <td style="max-width:260px;"><?= htmlspecialchars($ticket['message']) ?></td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($ticket['created_at'] ?? 'now'))) ?></td>
                    <td>
                        <form action="<?= app_url('admin/support/reply') ?>" method="post" class="admin-inline-form">
                            <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                            <input type="text" name="reply" placeholder="Type a reply..." required style="border:1px solid var(--admin-border-strong);border-radius:8px;padding:6px 8px;font-size:12.5px;min-width:160px;">
                            <button class="admin-btn sm primary" type="submit">Send &amp; resolve</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($open)): ?>
            <div class="admin-table-empty">No open tickets — inbox zero.</div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Resolved</h2>
            <p>Past conversations</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Customer</th><th>Subject</th><th>Reply sent</th><th></th></tr>
            <?php foreach ($resolved as $ticket): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($ticket['name']) ?></td>
                    <td><?= htmlspecialchars($ticket['subject']) ?></td>
                    <td><?= htmlspecialchars($ticket['admin_reply'] ?? '') ?></td>
                    <td>
                        <form action="<?= app_url('admin/support/status') ?>" method="post">
                            <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
                            <input type="hidden" name="status" value="open">
                            <button class="admin-btn sm" type="submit">Reopen</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($resolved)): ?>
            <div class="admin-table-empty">No resolved tickets yet.</div>
        <?php endif; ?>
    </div>
</div>
