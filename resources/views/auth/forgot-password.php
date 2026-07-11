<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 4h16v16H4z"/>
                    <path d="m4 7 8 6 8-6"/>
                </svg>
            </div>
            <h1>Forgot Password</h1>
            <p>Enter your email address to receive a reset link</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="form-alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="<?= app_url('forgot-password') ?>" method="post">
            <label>
                Email Address
                <input type="email" name="email" placeholder="you@example.com" required>
            </label>
            <button class="auth-submit" type="submit">Send reset link</button>
            <p class="auth-switch">Remember your password? <a href="<?= app_url('login') ?>">Sign in</a></p>
        </form>
    </section>
</main>
