<?php require 'views/layout/header.php'; ?>

<h2 class="mb-4">Latest Articles</h2>

<!-- Category Filter -->
<div class="mb-4">
    <button class="btn btn-dark me-2 category-btn" data-id="">All</button>
    <?php foreach ($categories as $cat): ?>
        <button class="btn btn-outline-dark me-2 category-btn" data-id="<?= $cat['id'] ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </button>
    <?php endforeach; ?>
</div>

<!-- Articles List -->
<div id="articles-container">
    <?php foreach ($articles as $article): ?>
        <div class="card mb-3">
            <div class="card-body">
                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($article['category_name']) ?></span>
                <h5 class="card-title">
                    <a href="index.php?page=article&id=<?= $article['id'] ?>">
                        <?= htmlspecialchars($article['title']) ?>
                    </a>
                </h5>
                <p class="card-text text-muted"><?= htmlspecialchars($article['excerpt']) ?></p>
                <small class="text-muted">By <?= htmlspecialchars($article['author_name']) ?> | 
                <?= date('M d, Y', strtotime($article['created_at'])) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.category-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('btn-dark'));
        this.classList.add('btn-dark');
        const categoryId = this.getAttribute('data-id');
        fetch('ajax/filter_articles.php?category_id=' + categoryId)
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.length === 0) {
                    html = '<p>No articles found.</p>';
                }
                data.forEach(article => {
                    html += `
                    <div class="card mb-3">
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2">${article.category_name}</span>
                            <h5 class="card-title">
                                <a href="index.php?page=article&id=${article.id}">${article.title}</a>
                            </h5>
                            <p class="card-text text-muted">${article.excerpt}</p>
                            <small class="text-muted">By ${article.author_name}</small>
                        </div>
                    </div>`;
                });
                document.getElementById('articles-container').innerHTML = html;
            });
    });
});
</script>

<?php require 'views/layout/footer.php'; ?>