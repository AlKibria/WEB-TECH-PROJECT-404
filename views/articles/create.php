<?php $pageTitle = 'New Article'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">New Article</h1>
        <p class="page-sub">Write, save drafts, and submit for editorial review.</p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles" class="btn btn-outline">← Back</a>
</div>

<form method="POST" action="<?= BASE_URL ?>/index.php?page=author_articles" enctype="multipart/form-data" id="articleForm">
    <input type="hidden" name="_route" value="author_articles_store">

    <div class="editor-layout">
        <!-- Main editor -->
        <div class="editor-main">
            <div class="form-group">
                <input type="text" name="title" id="articleTitle" class="title-input" placeholder="Article title…" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="body" id="articleBody" class="editor-textarea" placeholder="Start writing your article…" rows="20"><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Excerpt <span class="label-hint">(short summary shown in listings)</span></label>
                <textarea name="excerpt" class="form-control" rows="3" placeholder="Brief summary of your article…"><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Sidebar options -->
        <div class="editor-sidebar">
            <div class="card">
                <div class="card-header"><h4>Publish</h4></div>
                <div class="publish-actions">
                    <button type="submit" name="action" value="draft" class="btn btn-outline btn-full">Save Draft</button>
                    <button type="submit" name="action" value="submit" class="btn btn-primary btn-full">Submit for Review</button>
                </div>
            </div>

            <div class="card" style="margin-top:1rem;">
                <div class="card-header"><h4>Settings</h4></div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">— Select category —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? '')==$cat['id']?'selected':'' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tags <span class="label-hint">(comma separated)</span></label>
                    <input type="text" name="tags" class="form-control" placeholder="PHP, Web Dev, Tutorial" value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Series</label>
                    <select name="series_id" class="form-control">
                        <option value="">— None —</option>
                        <?php foreach ($seriesList as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Series Order</label>
                    <input type="number" name="series_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                </div>
            </div>
        </div>
    </div>
</form>

<?php require __DIR__ . '/../layout/footer.php'; ?>
