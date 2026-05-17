<?php $pageTitle = 'Edit Article'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Article</h1>
        <div class="autosave-status" id="autosaveStatus"></div>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles" class="btn btn-outline">← Back</a>
</div>

<?php if ($article['status'] === 'revision_requested' && $article['editor_feedback']): ?>
<div class="alert-box alert-warning" style="margin-bottom:1.5rem;">
    <strong>📝 Editor Feedback:</strong>
    <p style="margin:.5rem 0 0;"><?= nl2br(htmlspecialchars($article['editor_feedback'])) ?></p>
</div>
<?php endif; ?>

<?php $canEdit = in_array($article['status'], ['draft','revision_requested']); ?>

<form method="POST" action="<?= BASE_URL ?>/index.php?page=author_articles" enctype="multipart/form-data" id="articleForm">
    <input type="hidden" name="_route" value="author_articles_update">
    <input type="hidden" name="id" value="<?= $article['id'] ?>">

    <div class="editor-layout">
        <div class="editor-main">
            <div class="form-group">
                <input type="text" name="title" id="articleTitle" class="title-input"
                    placeholder="Article title…" required
                    value="<?= htmlspecialchars($article['title']) ?>"
                    <?= !$canEdit?'readonly':'' ?>>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="body" id="articleBody" class="editor-textarea" rows="20"
                    <?= !$canEdit?'readonly':'' ?>><?= htmlspecialchars($article['body']) ?></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Excerpt</label>
                <textarea name="excerpt" class="form-control" rows="3" <?= !$canEdit?'readonly':'' ?>><?= htmlspecialchars($article['excerpt']) ?></textarea>
            </div>
        </div>

        <div class="editor-sidebar">
            <div class="card">
                <div class="card-header"><h4>Status: <span class="badge badge-<?= $article['status'] ?>"><?= ucfirst(str_replace('_',' ',$article['status'])) ?></span></h4></div>
                <?php if ($canEdit): ?>
                <div class="publish-actions">
                    <button type="submit" name="action" value="draft" class="btn btn-outline btn-full">Save Draft</button>
                    <button type="submit" name="action" value="submit" class="btn btn-primary btn-full">Submit for Review</button>
                </div>
                <?php elseif ($article['status'] === 'published'): ?>
                <form method="POST" onsubmit="return confirm('Unpublish this article?')">
                    <input type="hidden" name="_route" value="author_articles_unpublish">
                    <input type="hidden" name="id" value="<?= $article['id'] ?>">
                    <button class="btn btn-danger btn-full">Unpublish Article</button>
                </form>
                <?php else: ?>
                <p class="empty-msg">This article cannot be edited in its current status.</p>
                <?php endif; ?>
            </div>

            <div class="card" style="margin-top:1rem;">
                <div class="card-header"><h4>Settings</h4></div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-control" <?= !$canEdit?'disabled':'' ?>>
                        <option value="">— Select —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $article['category_id']==$cat['id']?'selected':'' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" class="form-control"
                        value="<?= htmlspecialchars(implode(', ', array_column($tags,'name'))) ?>"
                        <?= !$canEdit?'readonly':'' ?>>
                </div>
                <div class="form-group">
                    <label class="form-label">Series</label>
                    <select name="series_id" class="form-control" <?= !$canEdit?'disabled':'' ?>>
                        <option value="">— None —</option>
                        <?php foreach ($seriesList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $article['series_id']==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Series Order</label>
                    <input type="number" name="series_order" class="form-control" value="<?= $article['series_order'] ?>" <?= !$canEdit?'readonly':'' ?>>
                </div>
                <div class="form-group">
                    <label class="form-label">Featured Image</label>
                    <?php if ($article['featured_image_path']): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($article['featured_image_path']) ?>" style="width:100%;border-radius:6px;margin-bottom:.5rem;">
                    <?php endif; ?>
                    <?php if ($canEdit): ?>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Article stats -->
            <div class="card" style="margin-top:1rem;">
                <div class="card-header"><h4>Quick Stats</h4></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                    <div class="mini-stat"><div class="mini-stat-num"><?= $article['view_count'] ?></div><div class="mini-stat-label">Views</div></div>
                    <div class="mini-stat"><a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $article['id'] ?>" class="btn btn-sm btn-outline" style="width:100%;">Analytics</a></div>
                </div>
            </div>

            <!-- Revision history -->
            <?php if (!empty($revisions)): ?>
            <div class="card" style="margin-top:1rem;">
                <div class="card-header"><h4>Revision History</h4></div>
                <div class="revision-list">
                    <?php foreach ($revisions as $rev): ?>
                    <div class="revision-item">
                        <div class="revision-date"><?= date('M d, H:i', strtotime($rev['saved_at'])) ?></div>
                        <?php if ($canEdit): ?>
                        <form method="POST">
                            <input type="hidden" name="_route" value="author_articles_restorerevision">
                            <input type="hidden" name="revision_id" value="<?= $rev['id'] ?>">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <button class="btn btn-xs btn-outline" onclick="return confirm('Restore this revision?')">Restore</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if ($canEdit): ?>
<script>
// AJAX Autosave every 30 seconds
let autosaveTimer = null;
const statusEl = document.getElementById('autosaveStatus');
const articleId = <?= $article['id'] ?>;

function triggerAutosave() {
    const title = document.getElementById('articleTitle').value;
    const body  = document.getElementById('articleBody').value;
    statusEl.textContent = 'Saving…';
    statusEl.className = 'autosave-status saving';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= BASE_URL ?>/index.php?page=author_articles&action=autosave');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            statusEl.textContent = data.message || 'Saved';
            statusEl.className = 'autosave-status saved';
        }
    };
    xhr.send('_route=articles_autosave&id=' + articleId + '&title=' + encodeURIComponent(title) + '&body=' + encodeURIComponent(body));
}

document.getElementById('articleBody').addEventListener('input', function() {
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(triggerAutosave, 5000);
});

// Also autosave every 30 seconds
setInterval(triggerAutosave, 30000);
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
