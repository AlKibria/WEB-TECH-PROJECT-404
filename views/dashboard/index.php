<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?> ✍️</h1>
        <p class="page-sub">Here's what's happening with your content today.</p>
    </div>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles&action=create" class="btn btn-primary">+ New Article</a>
</div>

<!-- Stats cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total_published'] ?></div>
        <div class="stat-label">Published Articles</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= number_format($stats['total_views']) ?></div>
        <div class="stat-label">Total Views</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total_followers'] ?></div>
        <div class="stat-label">Followers</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total_likes'] ?></div>
        <div class="stat-label">Total Likes</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['total_comments'] ?></div>
        <div class="stat-label">Comments</div>
    </div>
    <?php if ($newComments > 0): ?>
    <div class="stat-card stat-card-alert">
        <div class="stat-number"><?= $newComments ?></div>
        <div class="stat-label">New Comments (7d)</div>
    </div>
    <?php endif; ?>
</div>

<!-- Status overview -->
<div class="section-title">Article Status Overview</div>
<div class="status-bar-row">
    <?php $statuses = ['draft'=>'Draft','submitted'=>'Submitted','revision_requested'=>'Needs Revision','approved'=>'Approved','published'=>'Published','unpublished'=>'Unpublished']; ?>
    <?php foreach ($statuses as $key => $label): ?>
    <?php if ($statusCounts[$key] > 0): ?>
    <a href="<?= BASE_URL ?>/index.php?page=author_articles&status=<?= $key ?>" class="status-pill status-<?= $key ?>">
        <?= $label ?>: <strong><?= $statusCounts[$key] ?></strong>
    </a>
    <?php endif; ?>
    <?php endforeach; ?>
</div>

<!-- Revision requested alert -->
<?php $revCount = $statusCounts['revision_requested']; ?>
<?php if ($revCount > 0): ?>
<div class="alert-box alert-warning">
    <strong>⚠ Action Required:</strong> You have <?= $revCount ?> article<?= $revCount > 1 ? 's' : '' ?> needing revision.
    <a href="<?= BASE_URL ?>/index.php?page=author_articles&status=revision_requested">View now →</a>
</div>
<?php endif; ?>

<div class="two-col">
    <!-- Recent Articles -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Articles</h3>
            <a href="<?= BASE_URL ?>/index.php?page=author_articles" class="card-link">View all</a>
        </div>
        <?php if (empty($recentArticles)): ?>
            <p class="empty-msg">No articles yet. <a href="<?= BASE_URL ?>/index.php?page=author_articles&action=create">Create your first one!</a></p>
        <?php else: ?>
        <div class="article-list">
            <?php foreach ($recentArticles as $a): ?>
            <div class="article-item">
                <div class="article-item-info">
                    <div class="article-item-title"><?= htmlspecialchars($a['title']) ?></div>
                    <div class="article-item-meta"><?= formatDate($a['updated_at']) ?></div>
                </div>
                <span class="badge badge-<?= $a['status'] ?>"><?= ucfirst(str_replace('_',' ',$a['status'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Comments -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Comments</h3>
            <a href="<?= BASE_URL ?>/index.php?page=author_comments" class="card-link">View all</a>
        </div>
        <?php if (empty($recentComments)): ?>
            <p class="empty-msg">No comments yet.</p>
        <?php else: ?>
        <div class="comment-list">
            <?php foreach ($recentComments as $c): ?>
            <div class="comment-item">
                <div class="comment-avatar"><?= strtoupper(substr($c['commenter_name'],0,1)) ?></div>
                <div class="comment-body-wrap">
                    <div class="comment-meta"><strong><?= htmlspecialchars($c['commenter_name']) ?></strong> on <em><?= htmlspecialchars($c['article_title']) ?></em></div>
                    <div class="comment-text"><?= htmlspecialchars(mb_substr($c['body'],0,80)) ?><?= strlen($c['body'])>80?'…':'' ?></div>
                    <div class="comment-time"><?= timeAgo($c['created_at']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Top articles -->
<?php if ($mostRead || $mostLiked): ?>
<div class="section-title" style="margin-top:2rem;">Top Performers</div>
<div class="two-col">
    <?php if ($mostRead): ?>
    <div class="card card-highlight">
        <div class="highlight-label">📖 Most Read</div>
        <div class="highlight-title"><?= htmlspecialchars($mostRead['title']) ?></div>
        <div class="highlight-stat"><?= number_format($mostRead['view_count']) ?> views</div>
        <a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $mostRead['id'] ?>" class="btn btn-sm btn-outline">View Analytics</a>
    </div>
    <?php endif; ?>
    <?php if ($mostLiked): ?>
    <div class="card card-highlight">
        <div class="highlight-label">❤️ Most Liked</div>
        <div class="highlight-title"><?= htmlspecialchars($mostLiked['title']) ?></div>
        <div class="highlight-stat"><?= $mostLiked['like_count'] ?> likes</div>
        <a href="<?= BASE_URL ?>/index.php?page=author_analytics&action=article&id=<?= $mostLiked['id'] ?>" class="btn btn-sm btn-outline">View Analytics</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
