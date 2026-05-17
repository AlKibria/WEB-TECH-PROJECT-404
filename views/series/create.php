<?php $pageTitle = 'New Series'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
    <h1 class="page-title">New Series</h1>
    <a href="<?= BASE_URL ?>/index.php?page=author_series" class="btn btn-outline">← Back</a>
</div>
<div class="form-page">
    <form method="POST" action="<?= BASE_URL ?>/index.php?page=author_series" enctype="multipart/form-data">
        <input type="hidden" name="_route" value="author_series_store">
        <div class="card">
            <div class="form-group">
                <label class="form-label">Series Title *</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Modern Web Development">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="What is this series about?"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Cover Image</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Create Series</button>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
