<?php

/**
 * Returns a small inline SVG icon (20x20, stroke-based, currentColor) by name.
 * Kept centralised so every admin view/sidebar uses the same icon set.
 */
function admin_icon(string $name): string
{
    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'products' => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="M3 8l9 5 9-5"/><path d="M12 13v8"/>',
        'categories' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z"/>',
        'banners' => '<rect x="3" y="4" width="18" height="14" rx="2"/><path d="m3 15 5-5 4 4 5-6 4 5"/>',
        'orders' => '<path d="M6 3h9l3 4v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        'payments' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
        'delivery' => '<path d="M3 7h11v9H3z"/><path d="M14 10h4l3 3v3h-7z"/><circle cx="7.5" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M2.5 19a6.5 6.5 0 0 1 13 0"/><path d="M16 8.2a3.2 3.2 0 1 1 3 4.6"/><path d="M15.5 13.3c2.7.4 4.9 2.5 5 5.7"/>',
        'reviews' => '<path d="m12 3 2.6 5.7 6.2.6-4.7 4.2 1.4 6.1L12 16.9l-5.5 2.7 1.4-6.1-4.7-4.2 6.2-.6L12 3Z"/>',
        'support' => '<path d="M21 11.5a8.5 8.5 0 0 1-12.9 7.3L3 20l1.3-4.9A8.5 8.5 0 1 1 21 11.5Z"/>',
        'reports' => '<path d="M4 20V10M12 20V4M20 20v-7"/><path d="M2 20h20"/>',
        'notifications' => '<path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
        'security' => '<path d="M12 3 4 6v6c0 5 3.4 8.4 8 9 4.6-.6 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>',
        'store' => '<path d="M14 3h7v7"/><path d="M21 3 10 14"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'collapse' => '<path d="M15 18l-6-6 6-6"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'trash' => '<path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0-.9 13.1A2 2 0 0 1 15.1 21H8.9a2 2 0 0 1-2-1.9L6 6"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'alert' => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L14.1 3.9a2 2 0 0 0-3.8 0Z"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/>',
        'key' => '<circle cx="8" cy="15" r="4"/><path d="m10.8 12.2 8.6-8.6M15 8l2 2M18 5l2 2"/>',
        'box-check' => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="M8.5 12.2l2 2 4-4"/>',
    ];

    $paths = $icons[$name] ?? $icons['dashboard'];

    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
}

/**
 * The admin sidebar navigation, grouped. Central source of truth so the
 * active-state highlighting and menu stay identical on every admin page.
 */
function admin_nav_groups(): array
{
    return [
        'Overview' => [
            ['route' => 'admin/dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ],
        'Catalog' => [
            ['route' => 'admin/products', 'label' => 'Products', 'icon' => 'products'],
            ['route' => 'admin/categories', 'label' => 'Categories', 'icon' => 'categories'],
            ['route' => 'admin/banners', 'label' => 'Banners', 'icon' => 'banners'],
        ],
        'Sales' => [
            ['route' => 'admin/orders', 'label' => 'Orders', 'icon' => 'orders'],
            ['route' => 'admin/payments', 'label' => 'Payments', 'icon' => 'payments'],
            ['route' => 'admin/delivery', 'label' => 'Delivery', 'icon' => 'delivery'],
        ],
        'Customers' => [
            ['route' => 'admin/users', 'label' => 'Users', 'icon' => 'users'],
            ['route' => 'admin/reviews', 'label' => 'Reviews', 'icon' => 'reviews'],
            ['route' => 'admin/support', 'label' => 'Support', 'icon' => 'support'],
        ],
        'Insights' => [
            ['route' => 'admin/reports', 'label' => 'Reports', 'icon' => 'reports'],
            ['route' => 'admin/notifications', 'label' => 'Notifications', 'icon' => 'notifications'],
        ],
        'System' => [
            ['route' => 'admin/settings', 'label' => 'Settings', 'icon' => 'settings'],
            ['route' => 'admin/security', 'label' => 'Security', 'icon' => 'security'],
        ],
    ];
}
