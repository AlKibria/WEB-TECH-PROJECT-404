<?php
require_once 'config/db.php';

function saveArticle($user_id, $article_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT IGNORE INTO reading_lists (user_id, article_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $article_id);
    return $stmt->execute();
}

function removeArticle($user_id, $article_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM reading_lists WHERE user_id = ? AND article_id = ?");
    $stmt->bind_param("ii", $user_id, $article_id);
    return $stmt->execute();
}

function getReadingList($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT articles.*, users.name as author_name, categories.name as category_name 
        FROM reading_lists 
        JOIN articles ON reading_lists.article_id = articles.id
        JOIN users ON articles.author_id = users.id
        JOIN categories ON articles.category_id = categories.id
        WHERE reading_lists.user_id = ?
        ORDER BY reading_lists.saved_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function isArticleSaved($user_id, $article_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM reading_lists WHERE user_id = ? AND article_id = ?");
    $stmt->bind_param("ii", $user_id, $article_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}
?>