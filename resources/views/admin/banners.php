<div class="admin-page-head">
    <div>
        <p class="eyebrow">Catalog</p>
        <h1>Banners</h1>
        <p class="admin-page-sub">Manage homepage hero and promo banners</p>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Create banner</h2>
            <p>Appears on the storefront homepage</p>
        </div>
    </div>
    <div class="admin-panel-body">
        <form action="<?= app_url('admin/banners/store') ?>" method="post" enctype="multipart/form-data">
            <div class="admin-form-grid">
                <label class="admin-field wide">Title
                    <input type="text" name="title" placeholder="Luxury Fashion & Beauty for Modern Women" required>
                </label>
                <label class="admin-field">Placement
                    <select name="placement">
                        <option value="hero">Homepage hero</option>
                        <option value="wedding">Wedding promo</option>
                    </select>
                </label>
                <label class="admin-field">Sort order
                    <input type="number" name="sort_order" value="0" min="0">
                </label>
                <label class="admin-field">Button text
                    <input type="text" name="button_text" placeholder="Shop new arrivals">
                </label>
                <label class="admin-field wide">Link URL
                    <input type="text" name="link_url" placeholder="<?= app_url('products') ?>">
                </label>
                <label class="admin-field wide">Subtitle
                    <textarea name="subtitle" rows="2" placeholder="Discover curated styles and beauty essentials."></textarea>
                </label>
                <label class="admin-field wide">Banner image
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp">
                    <small>Recommended: 1600 x 1000 JPG, PNG, or WEBP for a sharp homepage hero.</small>
                </label>
                <label class="admin-check-field"><input type="checkbox" name="is_active" checked> Active</label>
                <div class="admin-form-actions">
                    <button class="admin-btn primary" type="submit"><?= admin_icon('plus') ?> Save banner</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel-head">
        <div>
            <h2>Existing banners</h2>
            <p><?= count($banners ?? []) ?> banner(s)</p>
        </div>
    </div>
    <div class="admin-panel-body">
        <?php if (empty($banners)): ?>
            <div class="admin-empty-state">
                <?= admin_icon('banners') ?>
                <h3>No banners yet</h3>
                <p>Create your first homepage banner above.</p>
            </div>
        <?php else: ?>
            <?php foreach ($banners as $banner): ?>
                <div class="admin-panel" style="margin-bottom:14px;box-shadow:none;">
                    <div class="admin-panel-head">
                        <div class="admin-row-flex">
                            <?php if (!empty($banner['image'])): ?>
                                <img class="admin-table-media" src="<?= media_url($banner['image']) ?>" alt="<?= htmlspecialchars($banner['title']) ?>">
                            <?php endif; ?>
                            <div>
                                <strong><?= htmlspecialchars($banner['title']) ?></strong>
                                <div class="admin-cell-sub"><?= htmlspecialchars(ucfirst($banner['placement'])) ?></div>
                            </div>
                        </div>
                        <span class="admin-badge <?= !empty($banner['is_active']) ? 'success' : 'neutral' ?>"><?= !empty($banner['is_active']) ? 'Active' : 'Inactive' ?></span>
                    </div>
                    <div class="admin-panel-body">
                        <form action="<?= app_url('admin/banners/update') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                            <div class="admin-form-grid">
                                <label class="admin-field">Title <input type="text" name="title" value="<?= htmlspecialchars($banner['title']) ?>" required></label>
                                <label class="admin-field">Placement
                                    <select name="placement">
                                        <option value="hero" <?= $banner['placement'] === 'hero' ? 'selected' : '' ?>>Homepage hero</option>
                                        <option value="wedding" <?= $banner['placement'] === 'wedding' ? 'selected' : '' ?>>Wedding promo</option>
                                    </select>
                                </label>
                                <label class="admin-field">Button text <input type="text" name="button_text" value="<?= htmlspecialchars($banner['button_text'] ?? '') ?>"></label>
                                <label class="admin-field">Sort order <input type="number" name="sort_order" value="<?= (int) ($banner['sort_order'] ?? 0) ?>"></label>
                                <label class="admin-field wide">Subtitle <textarea name="subtitle" rows="2"><?= htmlspecialchars($banner['subtitle'] ?? '') ?></textarea></label>
                                <label class="admin-field wide">Link URL <input type="text" name="link_url" value="<?= htmlspecialchars($banner['link_url'] ?? '') ?>"></label>
                                <label class="admin-field wide">Replace image <input type="file" name="image" accept="image/png,image/jpeg,image/webp"><small>Leave empty to keep the current image.</small></label>
                                <label class="admin-check-field"><input type="checkbox" name="is_active" <?= !empty($banner['is_active']) ? 'checked' : '' ?>> Active</label>
                                <div class="admin-form-actions">
                                    <button class="admin-btn primary" type="submit"><?= admin_icon('check') ?> Update banner</button>
                                </div>
                            </div>
                        </form>
                        <form action="<?= app_url('admin/banners/delete') ?>" method="post" onsubmit="return confirm('Delete this banner?');" style="margin-top:10px;">
                            <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                            <button class="admin-btn sm danger" type="submit"><?= admin_icon('trash') ?> Delete banner</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
