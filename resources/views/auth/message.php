<main class="auth-page">
    <section class="auth-card">
        <div class="auth-hero">
            <div class="lock-mark">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20 6 9 17l-5-5"/>
                </svg>
            </div>
            <h1><?= htmlspecialchars($heading ?? 'Done') ?></h1>
            <p><?= htmlspecialchars($message ?? '') ?></p>
        </div>
        <div class="auth-form">
            <a class="auth-submit auth-link-button" href="<?= htmlspecialchars($actionUrl ?? app_url('login')) ?>"><?= htmlspecialchars($actionLabel ?? 'Continue') ?></a>
        </div>
    </section>
</main>
