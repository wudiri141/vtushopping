<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a class="active" href="<?= app_url('wishlist') ?>">Wishlist</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head"><div><p>Wishlist</p><h1>Saved Products</h1></div></div>
        <section class="portal-card">
            <p>Your saved products will appear here.</p>
            <a class="login-pill" href="<?= app_url('products') ?>">Browse Products</a>
        </section>
    </section>
</main>
