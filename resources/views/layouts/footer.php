<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <a class="brand footer-brand" href="<?= app_url('products') ?>" aria-label="VTU Shopping Store home">
                    <span class="brand-mark">VTU</span>
                    <span class="brand-name">Shopping Store</span>
                </a>
                <p>Luxury fashion, beauty, jewelry, and wedding accessories for modern Nigerian women.</p>
            </div>
            <div>
                <h2>Shop</h2>
                <a href="<?= app_url('products?collection=women') ?>">Fashion</a>
                <a href="<?= app_url('products?collection=beauty-skincare') ?>">Beauty</a>
                <a href="<?= app_url('products?collection=women') ?>">Jewelry</a>
                <a href="<?= app_url('products?collection=deals') ?>">Wedding</a>
            </div>
            <div>
                <h2>Support</h2>
                <a href="<?= app_url('contact') ?>">Contact Us</a>
                <a href="<?= app_url('faq') ?>">Shipping info</a>
                <a href="<?= app_url('faq') ?>">Returns</a>
                <a href="<?= app_url('faq') ?>">FAQ</a>
            </div>
            <div>
                <h2>Stay Connected</h2>
                <p>Be first to know about exclusive drops and boutique offers.</p>
                <div class="social-links">
                    <a href="#" aria-label="Instagram">◎</a>
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Twitter">t</a>
                    <a href="#" aria-label="Email">@</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 VTU Shopping Store. All right reserved.</p>
            <div>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of services</a>
                <a href="#">Payment icons</a>
            </div>
        </div>
    </div>
</footer>
</div>
<script src="<?= asset('js/store.js') ?>"></script>
</body>
</html>
