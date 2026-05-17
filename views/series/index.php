<?php $pageTitle = 'My Series'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">My Series</h1>
        <p class="page-sub">Group related articles into series for your readers.</p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_series&action=create" class="btn btn-primary">+ New Series</a>
</div>

<?php if (empty($seriesList)): ?>
<div class="empty-state">
    <div class="empty-icon">📚</div>
    <h3>No series yet</h3>
    <p>Create a series to group related articles together.</p>
    <a href="<?= BASE_URL ?>/index.php?page=author_series&action=create" class="btn btn-primary">Create Series</a>
</div>
<?php else: ?>
<div class="series-grid">
    <?php foreach ($seriesList as $s): ?>
    <div class="series-card">
        <?php if ($s['cover_image_path']): ?>
        <img src="<?= UPLOAD_URL . htmlspecialchars($s['cover_image_path']) ?>" class="series-cover" alt="cover">
        <?php else: ?>
        <div class="series-cover-placeholder">📖</div>
        <?php endif; ?>
        <div class="series-info">
            <h3 class="series-title"><?= htmlspecialchars($s['title']) ?></h3>
            <p class="series-desc"><?= htmlspecialchars(mb_substr($s['description'] ?? '',0,100)) ?><?= strlen($s['description'] ?? '')>100?'…':'' ?></p>
            <div class="series-meta"><?= $s['article_count'] ?> article<?= $s['article_count']!=1?'s':'' ?></div>
            <div class="series-actions">
                <a href="<?= BASE_URL ?>/index.php?page=author_series&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this series?')">
                    <input type="hidden" name="_route" value="author_series_delete">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
