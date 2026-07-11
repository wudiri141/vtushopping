<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M7 11V8a5 5 0 0 1 10 0v3M6 11h12v10H6z"/>
                </svg>
            </div>
            <h1>Reset Password</h1>
            <p>Create a new password for your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="form-alert"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form class="auth-form" action="<?= app_url('reset-password') ?>" method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
            <label>
                New Password
                <span class="password-control">
                    <input type="password" name="password" placeholder="New password" required data-password>
                    <button type="button" data-toggle-password>Show</button>
                </span>
            </label>
            <label>
                Confirm Password
                <span class="password-control">
                    <input type="password" name="confirm_password" placeholder="Confirm password" required data-password>
                    <button type="button" data-toggle-password>Show</button>
                </span>
            </label>
            <button class="auth-submit" type="submit">Update password</button>
        </form>
    </section>
</main>
