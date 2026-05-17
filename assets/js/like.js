document.addEventListener('DOMContentLoaded', function() {
    const likeBtn = document.getElementById('like-btn');
    if (!likeBtn) return;

    likeBtn.addEventListener('click', function() {
        const articleId = this.getAttribute('data-article-id');

        fetch('ajax/toggle_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ article_id: articleId })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('like-count').textContent = data.count;
            if (data.liked) {
                likeBtn.classList.remove('btn-outline-danger');
                likeBtn.classList.add('btn-danger');
            } else {
                likeBtn.classList.remove('btn-danger');
                likeBtn.classList.add('btn-outline-danger');
            }
        });
    });
});
