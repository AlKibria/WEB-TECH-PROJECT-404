<?php $pageTitle = 'Article Analytics'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Article Analytics</h1>
        <p class="page-sub"><?= htmlspecialchars($article['title']) ?></p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_analytics" class="btn btn-outline">← Back</a>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-number"><?= number_format($articleStats['views']) ?></div><div class="stat-label">Total Views</div></div>
    <div class="stat-card"><div class="stat-number"><?= $articleStats['likes'] ?></div><div class="stat-label">Likes</div></div>
    <div class="stat-card"><div class="stat-number"><?= $articleStats['comments'] ?></div><div class="stat-label">Comments</div></div>
    <div class="stat-card"><div class="stat-number"><?= formatDate($article['published_at'] ?? $article['created_at']) ?></div><div class="stat-label">Published</div></div>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header"><h3>Daily Reads (Last 30 Days)</h3></div>
    <?php if (empty($viewsOverTime)): ?>
        <p class="empty-msg">No read data yet.</p>
    <?php else: ?>
    <canvas id="viewsChart" height="100"></canvas>
    <script>
    const vLabels = <?= json_encode(array_column($viewsOverTime,'date')) ?>;
    const vData   = <?= json_encode(array_column($viewsOverTime,'reads')) ?>;
    renderBarChart('viewsChart', vLabels, vData, 'Reads per Day');
    </script>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
