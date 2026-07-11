<div class="admin-page-head">
    <div>
        <p class="eyebrow">Catalog</p>
        <h1>Products</h1>
        <p class="admin-page-sub"><?= count($products ?? []) ?> product(s) in your catalog</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Upload product</h2>
            <p>Add a new item to the storefront</p>
        </div>
    </div>
    <div class="admin-panel-body">
        <form action="<?= app_url('admin/products/store') ?>" method="post" enctype="multipart/form-data">
            <div class="admin-form-grid">
                <label class="admin-field">Product name
                    <input type="text" name="name" placeholder="Personalized Necklace" required>
                </label>
                <label class="admin-field">Short name
                    <input type="text" name="short_name" placeholder="Personalised necklace">
                </label>
                <label class="admin-field">Category
                    <input type="text" name="category" placeholder="Women's Jewelry" required>
                </label>
                <label class="admin-field">Collection
                    <select name="collection" required>
                        <option>Women's Fashion</option>
                        <option>Men's Fashion</option>
                        <option>Beauty &amp; Skincare</option>
                        <option>Makeup &amp; Cosmetics</option>
                        <option>Deals</option>
                    </select>
                </label>
                <label class="admin-field">Price (₦)
                    <input type="number" name="price" placeholder="50000" min="0" step="0.01" required>
                </label>
                <label class="admin-field">Original price (₦)
                    <input type="number" name="original_price" placeholder="65000" min="0" step="0.01">
                </label>
                <label class="admin-field">Discount %
                    <input type="number" name="discount_percent" placeholder="23" min="0" max="100">
                </label>
                <label class="admin-field">Stock
                    <input type="number" name="stock" placeholder="20" min="0" required>
                </label>
                <label class="admin-field">Rating
                    <input type="number" name="rating" placeholder="3.5" min="0" max="5" step="0.1">
                </label>
                <label class="admin-field">Reviews count
                    <input type="number" name="reviews_count" placeholder="12" min="0">
                </label>
                <label class="admin-field wide">Description
                    <textarea name="description" rows="3" placeholder="Product description"></textarea>
                </label>
                <label class="admin-field wide">Product images (1 to 5)
                    <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple>
                    <small>Clear JPG, PNG, or WEBP. Square photos work best. Only the first 5 files are saved.</small>
                </label>
                <div class="admin-form-actions">
                    <button class="admin-btn primary" type="submit"><?= admin_icon('plus') ?> Upload product</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Current products</h2>
            <p>Click a product to edit or delete it</p>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Rating</th><th></th></tr>
            <?php foreach (($products ?? []) as $product): ?>
                <?php $adminImages = Product::images((int) $product['id'], $product['image'] ?? null); ?>
                <tr>
                    <td>
                        <div class="admin-row-flex">
                            <img class="admin-table-media" src="<?= media_url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div>
                                <span class="admin-cell-title"><?= htmlspecialchars($product['name']) ?></span>
                                <span class="admin-cell-sub"><?= htmlspecialchars($product['collection'] ?? '') ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td>₦<?= number_format((float) $product['price'], 2) ?></td>
                    <td>
                        <?php $stock = (int) ($product['stock'] ?? 0); ?>
                        <span class="admin-badge <?= $stock <= 5 ? 'warning' : 'neutral' ?>"><?= $stock ?> in stock</span>
                    </td>
                    <td><span class="admin-rating-stars">★</span> <?= number_format((float) ($product['rating'] ?? 0), 1) ?> <span class="admin-cell-sub">(<?= (int) ($product['reviews_count'] ?? 0) ?>)</span></td>
                    <td>
                        <div class="admin-btn-row">
                            <button class="admin-btn sm" type="button" data-toggle-panel="edit-product-<?= (int) $product['id'] ?>"><?= admin_icon('edit') ?> Edit</button>
                            <form action="<?= app_url('admin/products/delete') ?>" method="post" onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                <button class="admin-btn sm danger" type="submit"><?= admin_icon('trash') ?> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr id="edit-product-<?= (int) $product['id'] ?>" style="display:none;">
                    <td colspan="6" style="background:var(--admin-bg);">
                        <form action="<?= app_url('admin/products/update') ?>" method="post" enctype="multipart/form-data" style="padding:16px 4px;">
                            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                            <div class="admin-form-grid">
                                <label class="admin-field">Name <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required></label>
                                <label class="admin-field">Short name <input type="text" name="short_name" value="<?= htmlspecialchars($product['short_name'] ?? '') ?>"></label>
                                <label class="admin-field">Category <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required></label>
                                <label class="admin-field">Collection <input type="text" name="collection" value="<?= htmlspecialchars($product['collection'] ?? '') ?>" required></label>
                                <label class="admin-field">Price <input type="number" name="price" value="<?= (float) $product['price'] ?>" min="0" step="0.01" required></label>
                                <label class="admin-field">Original price <input type="number" name="original_price" value="<?= (float) ($product['original_price'] ?? 0) ?>" min="0" step="0.01"></label>
                                <label class="admin-field">Discount % <input type="number" name="discount_percent" value="<?= (int) ($product['discount_percent'] ?? 0) ?>" min="0" max="100"></label>
                                <label class="admin-field">Stock <input type="number" name="stock" value="<?= (int) ($product['stock'] ?? 0) ?>" min="0" required></label>
                                <label class="admin-field">Rating <input type="number" name="rating" value="<?= (float) ($product['rating'] ?? 3.5) ?>" min="0" max="5" step="0.1"></label>
                                <label class="admin-field">Reviews count <input type="number" name="reviews_count" value="<?= (int) ($product['reviews_count'] ?? 0) ?>" min="0"></label>
                                <label class="admin-field wide">Description <textarea name="description" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea></label>
                                <label class="admin-field wide">
                                    Current images
                                    <span class="admin-row-flex" style="margin-top:4px;">
                                        <?php foreach ($adminImages as $image): ?>
                                            <img class="admin-table-media" src="<?= media_url($image) ?>" alt="">
                                        <?php endforeach; ?>
                                    </span>
                                </label>
                                <label class="admin-field wide">Replace images (1 to 5) <input type="file" name="images[]" accept="image/png,image/jpeg,image/webp" multiple><small>Leave empty to keep the current images.</small></label>
                                <div class="admin-form-actions">
                                    <button class="admin-btn primary" type="submit"><?= admin_icon('check') ?> Save changes</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($products)): ?>
            <div class="admin-table-empty">No products yet. Upload your first product above.</div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('[data-toggle-panel]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var row = document.getElementById(btn.getAttribute('data-toggle-panel'));
        if (row) row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    });
});
</script>
