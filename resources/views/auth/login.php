<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v10H6z"/>
                </svg>
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to continue shopping</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="form-alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="<?= app_url('login') ?>" method="post">
            <label>
                Email Address
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>
            <label>
                Password
                <span class="password-control">
                    <input type="password" name="password" placeholder="Password" required data-password>
                    <button type="button" data-toggle-password>Show</button>
                </span>
            </label>
            <div class="form-row">
                <label class="check-label"><input type="checkbox" name="remember"> Remember me</label>
                <a href="<?= app_url('forgot-password') ?>">Forgot password?</a>
            </div>
            <button class="auth-submit" type="submit">Sign In</button>
            <p class="auth-switch">Don't have an account? <a href="<?= app_url('register') ?>">Sign up</a></p>
        </form>
    </section>
</main>
