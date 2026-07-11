<main class="portal-page">
    <aside class="portal-sidebar">
        <h2>User Menu</h2>
        <a href="<?= app_url('user/dashboard') ?>">Dashboard</a>
        <a class="active" href="<?= app_url('user/profile') ?>">Profile</a>
        <a href="<?= app_url('user/orders') ?>">Orders</a>
        <a href="<?= app_url('logout') ?>">Logout</a>
    </aside>
    <section class="portal-content">
        <div class="portal-head">
            <div>
                <p>Profile</p>
                <h1>Account Details</h1>
            </div>
        </div>
        <form class="portal-form">
            <label>Full Name <input type="text" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>"></label>
            <label>Email <input type="email" placeholder="customer@example.com"></label>
            <label>Phone <input type="tel" placeholder="+234 800 123 4567"></label>
            <label class="wide">Delivery Address <textarea rows="4" placeholder="Your delivery address"></textarea></label>
            <button class="auth-submit wide" type="button">Save Profile</button>
        </form>
    </section>
</main>
