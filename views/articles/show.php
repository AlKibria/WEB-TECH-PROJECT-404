<?php require 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-md-8">
        <!-- Article -->
        <div class="card mb-4">
            <div class="card-body">
                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                <h2><?= htmlspecialchars($article['title']) ?></h2>
                <p class="text-muted">By <?= htmlspecialchars($article['author_name']) ?> | 
                <?= date('M d, Y', strtotime($article['created_at'])) ?> | 
                👁 <?= $article['view_count'] ?> views</p>
                <hr>
                <div><?= nl2br(htmlspecialchars($article['body'])) ?></div>
                <hr>

                <!-- Like Button -->
                <button id="like-btn" 
                    class="btn <?= $is_liked ? 'btn-danger' : 'btn-outline-danger' ?>"
                    data-article-id="<?= $article['id'] ?>"
                    <?= !isset($_SESSION['user_id']) ? 'disabled' : '' ?>>
                    ❤️ <span id="like-count"><?= $like_count ?></span> Likes
                </button>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <small class="text-muted ms-2">Login to like</small>
                <?php endif; ?>
            </div>
        </div>

        <!-- Save to Reading List -->
<?php if (isset($_SESSION['user_id'])): ?>
    <?php
    require_once 'models/ReadingList.php';
    $is_saved = isArticleSaved($_SESSION['user_id'], $article['id']);
    ?>
    <a href="index.php?page=save_article&article_id=<?= $article['id'] ?>" 
        class="btn <?= $is_saved ? 'btn-success' : 'btn-outline-success' ?> ms-2">
        <?= $is_saved ? '✅ Saved' : '🔖 Save Article' ?>
    </a>
<?php endif; ?>

        <!-- Comments -->
        <div class="card mb-4">
            <div class="card-body">
                <h5>Comments (<?= count($comments) ?>)</h5>

                <?php if (isset($_SESSION['user_id'])): ?>
                <form action="index.php?page=comment" method="POST" class="mb-4">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <div class="mb-2">
                        <textarea name="body" class="form-control" rows="3" 
                            placeholder="Write a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Post Comment</button>
                </form>
                <?php else: ?>
                    <p><a href="index.php?page=login">Login</a> to comment.</p>
                <?php endif; ?>

                <?php foreach ($comments as $comment): ?>
                <div class="border-bottom pb-3 mb-3">
                    <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                    <small class="text-muted ms-2"><?= date('M d, Y', strtotime($comment['created_at'])) ?></small>
                    <p class="mt-1 mb-1"><?= htmlspecialchars($comment['body']) ?></p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                        <a href="index.php?page=delete_comment&comment_id=<?= $comment['id'] ?>&article_id=<?= $article['id'] ?>" 
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Delete this comment?')">Delete</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6>About the Author</h6>
                <p><strong><?= htmlspecialchars($article['author_name']) ?></strong></p>
                <p class="text-muted small"><?= htmlspecialchars($article['author_bio'] ?? 'No bio available.') ?></p>
            </div>
        </div>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>