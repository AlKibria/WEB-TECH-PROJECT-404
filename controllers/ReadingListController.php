<?php
require_once 'config/db.php';
require_once 'models/ReadingList.php';

function showReadingList() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $articles = getReadingList($_SESSION['user_id']);
    require 'views/reader/reading_list.php';
}

function handleSaveArticle() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $article_id = (int)$_GET['article_id'];
    saveArticle($_SESSION['user_id'], $article_id);
    header("Location: index.php?page=article&id=" . $article_id);
    exit;
}

function handleRemoveArticle() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
    $article_id = (int)$_GET['article_id'];
    removeArticle($_SESSION['user_id'], $article_id);
    header("Location: index.php?page=reading_list");
    exit;
}
?>