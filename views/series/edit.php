<?php $pageTitle = 'Edit Series'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
    <h1 class="page-title">Edit Series: <?= htmlspecialchars($series['title']) ?></h1>
    <a href="<?= BASE_URL ?>/index.php?page=author_series" class="btn btn-outline">← Back</a>
</div>

<div class="two-col">
    <!-- Edit form -->
    <div>
        <div class="card">
            <div class="card-header"><h3>Series Details</h3></div>
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=author_series&action=edit&id=<?= $series['id'] ?>" enctype="multipart/form-data">
                <input type="hidden" name="_route" value="author_series_update">
                <input type="hidden" name="id" value="<?= $series['id'] ?>">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($series['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($series['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Cover Image</label>
                    <?php if ($series['cover_image_path']): ?>
                    <img src="<?= UPLOAD_URL . htmlspecialchars($series['cover_image_path']) ?>" style="width:100%;border-radius:6px;margin-bottom:.5rem;">
                    <?php endif; ?>
                    <input type="file" name="cover_image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Update Series</button>
            </form>
        </div>

        <!-- Add article to series -->
        <?php if (!empty($available)): ?>
        <div class="card" style="margin-top:1rem;">
            <div class="card-header"><h3>Add Article to Series</h3></div>
            <div class="form-group" style="display:flex;gap:.5rem;">
                <select id="addArticleSelect" class="form-control">
                    <option value="">— Choose article —</option>
                    <?php foreach ($available as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" id="addOrder" class="form-control" placeholder="Order" value="<?= count($articles)+1 ?>" style="width:90px;">
                <button class="btn btn-primary" onclick="addArticleToSeries()">Add</button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Articles in series -->
    <div class="card">
        <div class="card-header"><h3>Articles in This Series</h3></div>
        <?php if (empty($articles)): ?>
            <p class="empty-msg">No articles in this series yet.</p>
        <?php else: ?>
        <div class="series-articles-list" id="seriesArticleList">
            <?php foreach ($articles as $a): ?>
            <div class="series-art-item" data-id="<?= $a['id'] ?>">
                <div class="series-art-order">
                    <input type="number" class="order-input" value="<?= $a['series_order'] ?>"
                        onchange="updateOrder(<?= $series['id'] ?>, <?= $a['id'] ?>, this.value)" style="width:55px;">
                </div>
                <div class="series-art-info">
                    <div><?= htmlspecialchars($a['title']) ?></div>
                    <span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span>
                </div>
                <button class="btn btn-xs btn-danger" onclick="removeFromSeries(<?= $a['id'] ?>)">Remove</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const seriesId = <?= $series['id'] ?>;

function addArticleToSeries() {
    const articleId = document.getElementById('addArticleSelect').value;
    const order = document.getElementById('addOrder').value;
    if (!articleId) return alert('Select an article first.');
    ajax('<?= BASE_URL ?>/index.php?page=author_series&action=addarticle',
        '_route=series_addarticle&series_id='+seriesId+'&article_id='+articleId+'&order='+order,
        () => location.reload());
}

function removeFromSeries(articleId) {
    if (!confirm('Remove from series?')) return;
    ajax('<?= BASE_URL ?>/index.php?page=author_series&action=removearticle',
        '_route=series_removearticle&article_id='+articleId,
        () => location.reload());
}

function updateOrder(seriesId, articleId, order) {
    ajax('<?= BASE_URL ?>/index.php?page=author_series&action=updateorder',
        '_route=series_updateorder&series_id='+seriesId+'&article_id='+articleId+'&order='+order,
        () => {});
}

function ajax(url, body, cb) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.onload = function() { if(xhr.status===200) cb(JSON.parse(xhr.responseText)); };
    xhr.send(body);
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
