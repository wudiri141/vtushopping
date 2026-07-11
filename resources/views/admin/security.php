<div class="admin-page-head">
    <div>
        <p class="eyebrow">System</p>
        <h1>Security</h1>
        <p class="admin-page-sub">Account protection and recent admin activity</p>
    </div>
</div>

<div class="admin-grid-2">
    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Change password</h2>
                <p><?= htmlspecialchars($admin['email'] ?? '') ?></p>
            </div>
        </div>
        <div class="admin-panel-body">
            <form action="<?= app_url('admin/security/password') ?>" method="post">
                <div class="admin-form-grid">
                    <label class="admin-field wide">Current password
                        <input type="password" name="current_password" required>
                    </label>
                    <label class="admin-field">New password
                        <input type="password" name="new_password" minlength="6" required>
                    </label>
                    <label class="admin-field">Confirm new password
                        <input type="password" name="confirm_password" minlength="6" required>
                    </label>
                    <div class="admin-form-actions">
                        <button class="admin-btn primary" type="submit"><?= admin_icon('key') ?> Update password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Session</h2>
                <p>Login sits on PHP's built-in secure session handling</p>
            </div>
        </div>
        <div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Two-factor login</div>
                    <div class="admin-list-row-sub">Email OTP required on every login</div>
                </div>
                <span class="admin-badge success"><?= admin_icon('check') ?> Enabled</span>
            </div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">Role-based access</div>
                    <div class="admin-list-row-sub">Admin routes require the admin role</div>
                </div>
                <span class="admin-badge success"><?= admin_icon('check') ?> Enabled</span>
            </div>
            <div class="admin-list-row">
                <div>
                    <div class="admin-list-row-title">CSRF protection on forms</div>
                    <div class="admin-list-row-sub">Not yet implemented on admin/auth forms</div>
                </div>
                <span class="admin-badge warning"><?= admin_icon('alert') ?> Todo</span>
            </div>
        </div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Recent admin activity</h2>
            <p>Last 30 logged actions</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Admin</th><th>Action</th><th>When</th></tr>
            <?php foreach (($logs ?? []) as $log): ?>
                <tr>
                    <td class="admin-cell-title"><?= htmlspecialchars($log['user_name'] ?? 'Admin') ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars(date('M j, Y g:ia', strtotime($log['created_at'] ?? 'now'))) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($logs)): ?>
            <div class="admin-table-empty">No activity logged yet.</div>
        <?php endif; ?>
    </div>
</div>
