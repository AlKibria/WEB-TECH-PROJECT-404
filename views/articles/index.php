<?php $pageTitle = 'My Articles'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">My Articles</h1>
        <p class="page-sub">Manage all your drafts and published content.</p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles&action=create" class="btn btn-primary">+ New Article</a>
</div>

<!-- Filter tabs -->
<div class="filter-tabs" id="filterTabs">
    <?php $curStatus = $_GET['status'] ?? 'all'; ?>
    <button class="filter-tab <?= $curStatus==='all'?'active':'' ?>" data-status="all">
        All <span class="tab-count"><?= array_sum($statusCounts) ?></span>
    </button>
    <?php foreach (['draft','submitted','revision_requested','approved','published','unpublished'] as $s): ?>
    <?php if ($statusCounts[$s] > 0): ?>
    <button class="filter-tab <?= $curStatus===$s?'active':'' ?>" data-status="<?= $s ?>">
        <?= ucfirst(str_replace('_',' ',$s)) ?> <span class="tab-count"><?= $statusCounts[$s] ?></span>
    </button>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<div id="articlesContainer">
    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <div class="empty-icon">📝</div>
            <h3>No articles found</h3>
            <p>Start writing your first article!</p>
            <a href="<?= BASE_URL ?>/index.php?page=author_articles&action=create" class="btn btn-primary">Create Article</a>
        </div>
    <?php else: ?>
    <div class="articles-table-wrap">
        <table class="articles-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="articlesTableBody">
                <?php foreach ($articles as $a): ?>
                <tr>
                    <td>
                        <div class="art-title"><?= htmlspecialchars($a['title']) ?></div>
                        <?php if ($a['status']==='revision_requested' && $a['editor_feedback']): ?>
                        <div class="feedback-snip">💬 <?= htmlspecialchars(mb_substr($a['editor_feedback'],0,60)) ?>…</div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($a['category_name'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span></td>
                    <td><?= number_format($a['view_count']) ?></td>
                    <td><?= formatDate($a['updated_at']) ?></td>
                    <td class="actions-cell">
                        <?php if (in_array($a['status'],['draft','revision_requested'])): ?>
                        <a href="<?= BASE_URL ?>/index.php?page=author_articles&action=edit&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                        <?php endif; ?>
                        <?php if ($a['status']==='published'): ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Unpublish this article?')">
                            <input type="hidden" name="_route" value="author_articles_unpublish">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button class="btn btn-sm btn-danger">Unpublish</button>
                        </form>
                        <?php endif; ?>
                        <?php if (in_array($a['status'],['draft','revision_requested'])): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="_route" value="author_articles_submit">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <button class="btn btn-sm btn-success">Submit</button>
                        </form>
                        <?php endif; ?>
                        <?php if ($a['status']==='published'): ?>
                        <a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline">Analytics</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
// AJAX filter tabs
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const status = this.dataset.status;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= BASE_URL ?>/index.php?page=author_articles&action=ajaxlist&status=' + status);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                renderArticles(data.articles);
            }
        };
        xhr.send();
    });
});

function renderArticles(articles) {
    const container = document.getElementById('articlesContainer');
    if (!articles.length) {
        container.innerHTML = '<div class="empty-state"><div class="empty-icon">📝</div><h3>No articles found</h3></div>';
        return;
    }
    let rows = articles.map(a => {
        const statusLabel = a.status.replace('_',' ').replace(/\b\w/g,c=>c.toUpperCase());
        const editBtn = ['draft','revision_requested'].includes(a.status)
            ? `<a href="<?= BASE_URL ?>/index.php?page=author_articles&action=edit&id=${a.id}" class="btn btn-sm btn-outline">Edit</a>` : '';
        return `<tr>
            <td><div class="art-title">${escHtml(a.title)}</div></td>
            <td>${escHtml(a.category_name || '—')}</td>
            <td><span class="badge badge-${a.status}">${statusLabel}</span></td>
            <td>${Number(a.view_count).toLocaleString()}</td>
            <td>${a.updated_at ? a.updated_at.split(' ')[0] : ''}</td>
            <td class="actions-cell">${editBtn}</td>
        </tr>`;
    }).join('');
    container.innerHTML = `<div class="articles-table-wrap"><table class="articles-table"><thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Updated</th><th>Actions</th></tr></thead><tbody>${rows}</tbody></table></div>`;
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
