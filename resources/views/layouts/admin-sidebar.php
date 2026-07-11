<?php
$currentRoute = $route ?? '';
$adminName = $_SESSION['user_name'] ?? 'Admin';
$adminInitial = strtoupper(substr(trim((string) $adminName), 0, 1)) ?: 'A';
$pendingReviewCount = class_exists('Product') ? Product::pendingReviewCount() : 0;
$openTicketCount = class_exists('SupportTicket') ? SupportTicket::openCount() : 0;
$navBadges = [
    'admin/reviews' => $pendingReviewCount,
    'admin/support' => $openTicketCount,
];
?>
<div class="admin-shell" data-admin-shell>
    <div class="admin-sidebar-backdrop" data-admin-backdrop></div>

    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand-mark">VS</span>
            <span class="admin-brand-text">VTU Shopping Admin</span>
        </div>

        <nav class="admin-nav">
            <?php foreach (admin_nav_groups() as $groupLabel => $items): ?>
                <div class="admin-nav-group">
                    <div class="admin-nav-group-label"><?= htmlspecialchars($groupLabel) ?></div>
                    <?php foreach ($items as $item): ?>
                        <?php $badge = $navBadges[$item['route']] ?? 0; ?>
                        <a class="admin-nav-link<?= $currentRoute === $item['route'] ? ' is-active' : '' ?>" href="<?= app_url($item['route']) ?>">
                            <?= admin_icon($item['icon']) ?>
                            <span class="admin-nav-label"><?= htmlspecialchars($item['label']) ?></span>
                            <?php if ($badge > 0): ?>
                                <span class="admin-nav-badge"><?= $badge > 9 ? '9+' : $badge ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </nav>

        <div class="admin-sidebar-foot">
            <a class="admin-nav-link" href="<?= app_url('admin/logout') ?>">
                <?= admin_icon('logout') ?>
                <span class="admin-nav-label">Logout</span>
            </a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="admin-icon-btn" type="button" data-admin-mobile-toggle aria-label="Open menu" style="display:none">
                <?= admin_icon('menu') ?>
            </button>
            <button class="admin-icon-btn" type="button" data-admin-collapse aria-label="Toggle sidebar">
                <?= admin_icon('collapse') ?>
            </button>
            <span class="admin-topbar-title"><?= htmlspecialchars($title ?? 'Admin') ?></span>
            <span class="admin-topbar-spacer"></span>
            <a class="admin-topbar-link" href="<?= app_url('products') ?>" target="_blank" rel="noopener">
                <?= admin_icon('store') ?> View store
            </a>
            <span class="admin-avatar"><?= htmlspecialchars($adminInitial) ?></span>
        </header>

        <div class="admin-body-scroll">
            <?php $flash = flash_get(); ?>
            <?php if ($flash): ?>
                <div class="admin-flash <?= htmlspecialchars($flash['type']) ?>" data-admin-flash>
                    <?= admin_icon($flash['type'] === 'success' ? 'check' : ($flash['type'] === 'error' ? 'alert' : 'notifications')) ?>
                    <span><?= htmlspecialchars($flash['message']) ?></span>
                </div>
            <?php endif; ?>
