<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a class="active" href="<?= app_url('user/orders') ?>">Orders</a>
        <a href="<?= app_url('track-order') ?>">Track Order</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head"><div><p>Order Details</p><h1>Order #<?= htmlspecialchars($_GET['id'] ?? '1021') ?></h1></div></div>
        <div class="portal-grid">
            <section class="portal-card">
                <h2>Product Ordered</h2>
                <p>Personalized Necklace · Qty 1</p>
                <strong>₦50,000.00</strong>
            </section>
            <section class="portal-card">
                <h2>Delivery Address</h2>
                <p>Customer delivery address appears here.</p>
            </section>
            <section class="portal-card">
                <h2>Tracking Number</h2>
                <p>VTU-1021-SHIP</p>
            </section>
            <section class="portal-card">
                <h2>Payment Info</h2>
                <p>Paid with card through Paystack.</p>
            </section>
        </div>
    </section>
</main>
