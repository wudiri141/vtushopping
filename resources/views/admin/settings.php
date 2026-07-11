<div class="admin-page-head">
    <div>
        <p class="eyebrow">System</p>
        <h1>Store settings</h1>
        <p class="admin-page-sub">General storefront configuration</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-body">
        <form action="<?= app_url('admin/settings') ?>" method="post">
            <div class="admin-form-grid">
                <label class="admin-field">Store name
                    <input type="text" name="store_name" value="<?= htmlspecialchars($settings['store_name'] ?? '') ?>">
                </label>
                <label class="admin-field">Support email
                    <input type="email" name="support_email" value="<?= htmlspecialchars($settings['support_email'] ?? '') ?>">
                </label>
                <label class="admin-field">Support phone
                    <input type="text" name="support_phone" value="<?= htmlspecialchars($settings['support_phone'] ?? '') ?>">
                </label>
                <label class="admin-field">Free shipping threshold (₦)
                    <input type="number" name="free_shipping_threshold" value="<?= htmlspecialchars($settings['free_shipping_threshold'] ?? '0') ?>" min="0" step="0.01">
                </label>
                <label class="admin-field">Low stock threshold
                    <input type="number" name="low_stock_threshold" value="<?= htmlspecialchars($settings['low_stock_threshold'] ?? '5') ?>" min="0">
                    <small>Products at or below this stock count show up as "low stock" on the dashboard.</small>
                </label>
                <div class="admin-form-actions">
                    <button class="admin-btn primary" type="submit"><?= admin_icon('check') ?> Save settings</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Payment provider</h2>
            <p>Paystack API keys</p>
        </div>
    </div>
    <div class="admin-panel-body">
        <p style="font-size:13px;color:var(--admin-text-muted);line-height:1.6;">
            Paystack keys are configured via environment variables on the server (<code>PAYSTACK_SECRET_KEY</code> / <code>PAYSTACK_PUBLIC_KEY</code>), not through this page — that keeps live secret keys out of the database and off-screen. Ask your developer to update them directly in the server's environment configuration if they need to change.
        </p>
    </div>
</div>
