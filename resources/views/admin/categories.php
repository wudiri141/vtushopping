<div class="admin-page-head">
    <div>
        <p class="eyebrow">Catalog</p>
        <h1>Categories</h1>
        <p class="admin-page-sub">Organize how products are grouped in the store</p>
    </div>
</div>

<div class="admin-grid-2">
    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Create category</h2>
                <p>Name should match how it's typed on products</p>
            </div>
        </div>
        <div class="admin-panel-body">
            <form action="<?= app_url('admin/categories/store') ?>" method="post">
                <div class="admin-form-grid">
                    <label class="admin-field wide">Category name
                        <input type="text" name="name" placeholder="Women's Jewelry" required>
                    </label>
                    <label class="admin-field wide">Description
                        <textarea name="description" rows="2" placeholder="Optional short description"></textarea>
                    </label>
                    <label class="admin-field">Sort order
                        <input type="number" name="sort_order" value="0" min="0">
                    </label>
                    <div class="admin-form-actions">
                        <button class="admin-btn primary" type="submit"><?= admin_icon('plus') ?> Create category</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Existing categories</h2>
                <p><?= count($categories ?? []) ?> categor(y/ies)</p>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <tr><th>Name</th><th>Products</th><th></th></tr>
                <?php foreach (($categories ?? []) as $category): ?>
                    <tr>
                        <td>
                            <form action="<?= app_url('admin/categories/update') ?>" method="post" class="admin-inline-form">
                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" style="border:1px solid var(--admin-border-strong);border-radius:8px;padding:6px 8px;font-size:13px;">
                                <input type="hidden" name="sort_order" value="<?= (int) $category['sort_order'] ?>">
                                <button class="admin-btn sm" type="submit">Save</button>
                            </form>
                        </td>
                        <td><span class="admin-badge neutral"><?= (int) $category['product_count'] ?> product(s)</span></td>
                        <td>
                            <form action="<?= app_url('admin/categories/delete') ?>" method="post" onsubmit="return confirm('Delete this category?');">
                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                <button class="admin-btn sm danger" type="submit"><?= admin_icon('trash') ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php if (empty($categories)): ?>
                <div class="admin-table-empty">No categories yet — create one to help organize the catalog.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
