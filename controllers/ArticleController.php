<?php
require_once 'config/db.php';
require_once 'models/Article.php';

function showHomepage() {
    $categories = getAllCategories();
    $articles = getAllArticles();
    require 'views/articles/index.php';
}

function showArticle() {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $article = getArticleById($id);
    if (!$article) {
        echo "Article not found.";
        return;
    }
    incrementViewCount($id);
    $like_count = getLikeCount($id);
    $is_liked = isset($_SESSION['user_id']) ? isLikedByUser($id, $_SESSION['user_id']) : false;
    require_once 'models/Comment.php';
    $comments = getCommentsByArticle($id);
    require 'views/articles/show.php';
}
?>