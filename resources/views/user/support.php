<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a class="active" href="<?= app_url('support') ?>">Support</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head"><div><p>Support</p><h1>Help Center</h1></div></div>

        <?php if (!empty($flash)): ?>
            <p style="padding:12px 16px;border-radius:10px;background:#e7f6ec;color:#15803d;font-weight:600;margin-bottom:16px;"><?= htmlspecialchars($flash['message']) ?></p>
        <?php endif; ?>

        <form class="portal-form" action="<?= app_url('support') ?>" method="post">
            <label class="wide">Subject <input type="text" name="subject" placeholder="Order complaint" required></label>
            <label class="wide">Message <textarea name="message" rows="5" placeholder="Describe the issue" required></textarea></label>
            <button class="auth-submit wide" type="submit">Send Message</button>
        </form>

        <section class="portal-card">
            <h2>Your Messages</h2>
            <table class="data-table">
                <tr><th>Subject</th><th>Message</th><th>Reply</th><th>Status</th><th>Sent</th></tr>
                <?php foreach (($tickets ?? []) as $ticket): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['subject']) ?></td>
                        <td><?= htmlspecialchars($ticket['message']) ?></td>
                        <td><?= $ticket['admin_reply'] ? htmlspecialchars($ticket['admin_reply']) : '—' ?></td>
                        <td><span class="status <?= $ticket['status'] === 'resolved' ? 'good' : 'warn' ?>"><?= htmlspecialchars(ucfirst($ticket['status'])) ?></span></td>
                        <td><?= htmlspecialchars(date('M j, Y', strtotime($ticket['created_at'] ?? 'now'))) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="5">You haven't sent any messages yet.</td></tr>
                <?php endif; ?>
            </table>
        </section>
    </section>
</main>
