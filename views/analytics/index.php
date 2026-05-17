<?php $pageTitle = 'Analytics'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Analytics</h1>
        <p class="page-sub">Track your content performance and audience growth.</p>
    </div>
</div>

<!-- Overview stats -->
<div class="stats-grid">
    <div class="stat-card"><div class="stat-number"><?= $stats['total_published'] ?></div><div class="stat-label">Published Articles</div></div>
    <div class="stat-card"><div class="stat-number"><?= number_format($stats['total_views']) ?></div><div class="stat-label">Total Views</div></div>
    <div class="stat-card"><div class="stat-number"><?= $stats['total_followers'] ?></div><div class="stat-label">Followers</div></div>
    <div class="stat-card"><div class="stat-number"><?= $stats['total_likes'] ?></div><div class="stat-label">Total Likes</div></div>
    <div class="stat-card"><div class="stat-number"><?= $stats['total_comments'] ?></div><div class="stat-label">Total Comments</div></div>
</div>

<div class="two-col" style="margin-top:1.5rem;">
    <!-- Top articles table -->
    <div class="card">
        <div class="card-header"><h3>Published Articles</h3></div>
        <?php if (empty($articles)): ?>
            <p class="empty-msg">No published articles yet.</p>
        <?php else: ?>
        <table class="articles-table">
            <thead><tr><th>Title</th><th>Views</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= htmlspecialchars(mb_substr($a['title'],0,40)) ?><?= strlen($a['title'])>40?'…':'' ?></td>
                <td><?= number_format($a['view_count']) ?></td>
                <td><a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $a['id'] ?>" class="btn btn-xs btn-outline">Details</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Top performers -->
    <div>
        <?php if ($mostRead): ?>
        <div class="card card-highlight" style="margin-bottom:1rem;">
            <div class="highlight-label">📖 Most Read</div>
            <div class="highlight-title"><?= htmlspecialchars($mostRead['title']) ?></div>
            <div class="highlight-stat"><?= number_format($mostRead['view_count']) ?> views</div>
            <a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $mostRead['id'] ?>" class="btn btn-sm btn-outline">View Details</a>
        </div>
        <?php endif; ?>
        <?php if ($mostLiked): ?>
        <div class="card card-highlight">
            <div class="highlight-label">❤️ Most Liked</div>
            <div class="highlight-title"><?= htmlspecialchars($mostLiked['title']) ?></div>
            <div class="highlight-stat"><?= $mostLiked['like_count'] ?> likes</div>
            <a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $mostLiked['id'] ?>" class="btn btn-sm btn-outline">View Details</a>
        </div>
        <?php endif; ?>

        <!-- Follower growth chart -->
        <?php if (!empty($followerGrowth)): ?>
        <div class="card" style="margin-top:1rem;">
            <div class="card-header"><h3>Follower Growth (30d)</h3></div>
            <canvas id="followerChart" height="160"></canvas>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($followerGrowth)): ?>
<script>
const fLabels = <?= json_encode(array_column($followerGrowth,'date')) ?>;
const fData   = <?= json_encode(array_column($followerGrowth,'count')) ?>;
renderBarChart('followerChart', fLabels, fData, 'New Followers');
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
