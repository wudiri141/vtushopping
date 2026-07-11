<?php
$statusOrder = ['pending', 'paid', 'packed', 'shipped', 'delivered'];
$currentStatus = strtolower((string) ($trackedOrder['status'] ?? ''));
$currentIndex = array_search($currentStatus, $statusOrder, true);
$currentIndex = $currentIndex === false ? -1 : $currentIndex;
$items = [];
if (!empty($trackedOrder['items_json'])) {
    $decodedItems = json_decode((string) $trackedOrder['items_json'], true);
    $items = is_array($decodedItems) ? $decodedItems : [];
}
?>

<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a href="<?= app_url('user/orders') ?>">Orders</a>
        <a class="active" href="<?= app_url('track-order') ?>">Track Order</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head">
            <div>
                <p>Track Order</p>
                <h1>Delivery Progress</h1>
            </div>
        </div>

        <section class="portal-card tracking-panel">
            <form class="track-form" action="<?= app_url('track-order') ?>" method="post">
                <label>
                    Order reference
                    <input type="text" name="reference" placeholder="Example: VTU-20260523-1234" value="<?= htmlspecialchars($_POST['reference'] ?? '') ?>" required>
                </label>
                <label>
                    Email address
                    <input type="email" name="email" placeholder="Email used at checkout" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </label>
                <button type="submit">Track Order</button>
            </form>
        </section>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($trackedOrder)): ?>
            <section class="portal-card track-empty">
                <h2>No order found</h2>
                <p>Check the reference number and email address, then try again.</p>
            </section>
        <?php endif; ?>

        <?php if (!empty($trackedOrder)): ?>
            <section class="portal-card tracking-panel">
                <div class="track-result-head">
                    <div>
                        <span>Order Reference</span>
                        <strong><?= htmlspecialchars($trackedOrder['reference']) ?></strong>
                    </div>
                    <span class="status <?= in_array($currentStatus, ['paid', 'delivered'], true) ? 'good' : 'warn' ?>"><?= htmlspecialchars(ucfirst($trackedOrder['status'])) ?></span>
                </div>

                <div class="status-steps">
                    <?php foreach ($statusOrder as $index => $status): ?>
                        <span class="<?= $index <= $currentIndex ? 'done' : '' ?>"><?= htmlspecialchars(ucfirst($status)) ?></span>
                    <?php endforeach; ?>
                </div>

                <div class="track-result-grid">
                    <div><span>Customer</span><strong><?= htmlspecialchars($trackedOrder['customer_name'] ?: 'Guest') ?></strong></div>
                    <div><span>Email</span><strong><?= htmlspecialchars($trackedOrder['customer_email'] ?: 'Not provided') ?></strong></div>
                    <div><span>Phone</span><strong><?= htmlspecialchars($trackedOrder['customer_phone'] ?: 'Not provided') ?></strong></div>
                    <div><span>Total</span><strong>₦<?= number_format((float) $trackedOrder['total'], 2) ?></strong></div>
                </div>

                <?php if (!empty($items)): ?>
                    <div class="track-items">
                        <h2>Items</h2>
                        <?php foreach ($items as $item): ?>
                            <p><?= htmlspecialchars($item['name'] ?? 'Product') ?> <span>x<?= (int) ($item['quantity'] ?? 1) ?></span></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </section>
</main>
