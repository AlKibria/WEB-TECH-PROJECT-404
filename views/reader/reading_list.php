<?php require 'views/layout/header.php'; ?>

<h2 class="mb-4">My Reading List</h2>

<?php if (empty($articles)): ?>
    <p>No saved articles yet. <a href="index.php">Browse articles</a></p>
<?php else: ?>
    <?php foreach ($articles as $article): ?>
        <div class="card mb-3">
            <div class="card-body">
                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                <h5 class="card-title">
                    <a href="index.php?page=article&id=<?= $article['id'] ?>">
                        <?= htmlspecialchars($article['title']) ?>
                    </a>
                </h5>
                <p class="text-muted"><?= htmlspecialchars($article['excerpt']) ?></p>
                <a href="index.php?page=remove_article&article_id=<?= $article['id'] ?>" 
                    class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Remove from reading list?')">Remove</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require 'views/layout/footer.php'; ?>