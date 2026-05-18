<?php
require_once 'config/db.php';
require_once 'models/Comment.php';

function addComment() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $article_id = (int)$_POST['article_id'];
        $user_id = $_SESSION['user_id'];
        $body = trim($_POST['body']);
        if (!empty($body)) {
            addComment($article_id, $user_id, $body);
        }
        header("Location: index.php?page=article&id=" . $article_id);
        exit;
    }
}

function handleDeleteComment() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $comment_id = (int)$_GET['comment_id'];
    $article_id = (int)$_GET['article_id'];
    deleteComment($comment_id, $_SESSION['user_id']);
    header("Location: index.php?page=article&id=" . $article_id);
    exit;
}
?>