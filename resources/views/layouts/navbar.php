<header class="site-header">
    <div class="promo-strip">
        <span>Free nationwide delivery on orders above ₦200,000</span>
    </div>
    <div class="header-inner">
        <a class="brand" href="<?= app_url('products') ?>" aria-label="VTU Shopping Store home">
            <span class="brand-mark">VS</span>
            <span class="brand-name">VTU Shopping</span>
        </a>

        <nav class="mega-nav" aria-label="Primary navigation">
            <a href="<?= app_url('products?collection=women') ?>">Fashion</a>
            <a href="<?= app_url('products?collection=beauty-skincare') ?>">Beauty</a>
            <a href="<?= app_url('products?collection=deals') ?>">Wedding</a>
            <a href="<?= app_url('products?collection=women') ?>">Jewelry</a>
            <a href="<?= app_url('products?collection=men') ?>">Accessories</a>
        </nav>

        <form class="search-form" action="<?= app_url('products') ?>" method="get">
            <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="m21 21-5.2-5.2m2.2-5.3a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>
            </svg>
            <input type="search" name="q" placeholder="Search" aria-label="Search products">
        </form>

        <nav class="header-actions" aria-label="Account and cart">
            <a class="icon-link badge-link" href="<?= app_url('notifications') ?>" aria-label="Notifications">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                    <path d="M10 21h4"/>
                </svg>
                <span class="nav-badge" data-notification-count>1</span>
            </a>
            <a class="icon-link badge-link" href="<?= app_url('wishlist') ?>" aria-label="Wishlist">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.8 4.6a5.3 5.3 0 0 0-7.5 0L12 5.9l-1.3-1.3a5.3 5.3 0 1 0-7.5 7.5L12 21l8.8-8.9a5.3 5.3 0 0 0 0-7.5Z"/>
                </svg>
            </a>
            <a class="icon-link badge-link" href="<?= app_url('cart') ?>" aria-label="Cart" data-cart-open>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 3h2l2.1 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 1.9-1.4L21 7H6"/>
                    <circle cx="9" cy="20" r="1.5"/>
                    <circle cx="18" cy="20" r="1.5"/>
                </svg>
                <span class="nav-badge" data-cart-count>0</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="account-menu">
                    <button class="icon-link account-link" type="button" data-account-toggle aria-label="Account" aria-expanded="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                    </button>
                    <div class="account-dropdown">
                        <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?></strong>
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <a href="<?= app_url('admin/dashboard') ?>">Admin dashboard</a>
                            <a href="<?= app_url('admin/products') ?>">Manage products</a>
                            <a href="<?= app_url('admin/orders') ?>">Manage orders</a>
                        <?php else: ?>
                            <a href="<?= app_url('user/dashboard') ?>">My account</a>
                            <a href="<?= app_url('user/orders') ?>">Orders</a>
                            <a href="<?= app_url('wishlist') ?>">Wishlist</a>
                        <?php endif; ?>
                        <a href="<?= app_url('logout') ?>">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="profile-avatar" href="<?= app_url('login') ?>" aria-label="Login">U</a>
            <?php endif; ?>
        </nav>
    </div>
    <div class="cart-drawer-backdrop" data-cart-backdrop></div>
    <aside class="cart-drawer" aria-label="Shopping cart" aria-hidden="true" data-cart-drawer>
        <div class="cart-drawer-head">
            <div>
                <span>Cart</span>
                <h2>Your cart</h2>
            </div>
            <button type="button" aria-label="Close cart" data-cart-close>×</button>
        </div>
        <div class="cart-drawer-empty" data-cart-empty>
            <p>Your cart is currently empty.</p>
            <span>Not sure where to start?</span>
            <div>
                <a href="<?= app_url('products?collection=women') ?>">Women's Fashion</a>
                <a href="<?= app_url('products?collection=men') ?>">Men's Fashion</a>
                <a href="<?= app_url('products?collection=beauty-skincare') ?>">Beauty & Skincare</a>
                <a href="<?= app_url('products?collection=deals') ?>">Deals</a>
            </div>
        </div>
        <div class="cart-drawer-items" data-cart-drawer-items></div>
        <div class="cart-drawer-tools">
            <label>
                Order note
                <textarea rows="3" placeholder="Order special instructions" data-cart-note></textarea>
            </label>
            <label>
                Discount code
                <span class="discount-entry">
                    <input type="text" placeholder="Discount code" data-discount-code>
                    <button type="button" data-discount-apply>Apply</button>
                </span>
                <small class="discount-note" data-discount-note></small>
            </label>
        </div>
        <div class="cart-drawer-summary">
            <p data-shipping-message>Spend ₦200,000.00 more to reach free shipping!</p>
            <div class="shipping-progress"><span data-shipping-progress></span></div>
            <div class="summary-row discount-row" data-cart-discount-row hidden>
                <span data-cart-discount-label>Discount</span>
                <strong data-cart-discount>-₦0.00</strong>
            </div>
            <div class="summary-row">
                <span>Total</span>
                <strong data-cart-subtotal>₦0.00 NGN</strong>
            </div>
            <small>Taxes and shipping calculated at checkout</small>
            <a class="checkout-button" href="<?= app_url('checkout') ?>">Check out</a>
            <a class="view-cart-link" href="<?= app_url('cart') ?>">View cart</a>
        </div>
    </aside>
</header>
