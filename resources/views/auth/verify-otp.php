<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2v20M2 12h20"/>
                </svg>
            </div>
            <h1>Enter OTP</h1>
            <p>We sent a login code to your email</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="form-alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if (!empty($_GET['resent'])): ?>
            <p class="form-alert success-alert">A new OTP has been sent.</p>
        <?php endif; ?>

        <form class="auth-form" action="<?= app_url('verify-login-otp') ?>" method="post">
            <label>
                OTP Code
                <input class="otp-input" type="text" name="otp" placeholder="0000" maxlength="6" inputmode="numeric" required>
            </label>
            <button class="auth-submit" type="submit">Verify login</button>
            <p class="auth-switch">Did not receive it? <a href="<?= app_url('resend-login-otp') ?>">Resend OTP</a></p>
        </form>
    </section>
</main>
