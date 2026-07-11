<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a class="active" href="<?= app_url('notifications') ?>">Notifications</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head"><div><p>Notifications</p><h1>Latest Updates</h1></div></div>
        <section class="portal-card">
            <p>Payment successful.</p>
            <p>Order delivered.</p>
            <p>Promo discount available.</p>
        </section>
    </section>
</main>
