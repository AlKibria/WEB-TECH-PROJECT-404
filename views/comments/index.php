<?php $pageTitle = 'Comments Inbox'; require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1 class="page-title">Comments Inbox</h1>
    <p class="page-sub">All reader comments across your articles.</p>
</div>

<?php if (empty($comments)): ?>
<div class="empty-state">
    <div class="empty-icon">💬</div>
    <h3>No comments yet</h3>
    <p>Comments from readers will appear here.</p>
</div>
<?php else: ?>
<div class="comments-inbox">
    <?php foreach ($comments as $c): ?>
    <div class="inbox-comment" id="comment-<?= $c['id'] ?>">
        <div class="inbox-comment-header">
            <div class="comment-avatar"><?= strtoupper(substr($c['commenter_name'],0,1)) ?></div>
            <div>
                <div class="comment-meta">
                    <strong><?= htmlspecialchars($c['commenter_name']) ?></strong>
                    <span class="comment-on"> on </span>
                    <em><?= htmlspecialchars($c['article_title']) ?></em>
                </div>
                <div class="comment-time"><?= timeAgo($c['created_at']) ?></div>
            </div>
            <button class="btn btn-xs btn-danger ml-auto" onclick="deleteComment(<?= $c['id'] ?>)">Delete</button>
        </div>
        <div class="inbox-comment-body"><?= nl2br(htmlspecialchars($c['body'])) ?></div>

        <!-- Reply form -->
        <div class="reply-form-wrap">
            <form method="POST" action="<?= BASE_URL ?>/index.php?page=author_comments&action=reply">
                <input type="hidden" name="_route" value="author_comments_reply">
                <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                <input type="hidden" name="article_id" value="<?= $c['article_id'] ?>">
                <div style="display:flex;gap:.5rem;margin-top:.5rem;">
                    <input type="text" name="body" class="form-control" placeholder="Reply to <?= htmlspecialchars($c['commenter_name']) ?>…" required>
                    <button type="submit" class="btn btn-sm btn-primary">Reply</button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function deleteComment(id) {
    if (!confirm('Delete this comment?')) return;
    const xhr = new XMLHttpRequest();
    xhr.open('POST','<?= BASE_URL ?>/index.php?page=author_comments&action=delete');
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.onload = function() {
        const data = JSON.parse(xhr.responseText);
        if (data.success) {
            const el = document.getElementById('comment-'+id);
            el.style.opacity='0';
            el.style.transform='translateX(-20px)';
            el.style.transition='all .3s';
            setTimeout(()=>el.remove(),300);
        }
    };
    xhr.send('_route=comments_delete&comment_id='+id);
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
