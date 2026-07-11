<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M16 21v-2a4 4 0 0 0-8 0v2M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM19 8v6M22 11h-6"/>
                </svg>
            </div>
            <h1>Create Account</h1>
            <p>Join VTU Shopping Store</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="form-alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="<?= app_url('register') ?>" method="post">
            <label>
                Full Name
                <input type="text" name="name" placeholder="Sarah M." required>
            </label>
            <label>
                Email Address
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>
            <label>
                Phone Number
                <input type="tel" name="phone" placeholder="+234 800 123 4567">
            </label>
            <label>
                Password
                <span class="password-control">
                    <input type="password" name="password" placeholder="Password" required data-password>
                    <button type="button" data-toggle-password>Show</button>
                </span>
            </label>
            <button class="auth-submit" type="submit">Create Account</button>
            <p class="auth-switch">Already have an account? <a href="<?= app_url('login') ?>">Sign in</a></p>
        </form>
    </section>
</main>
