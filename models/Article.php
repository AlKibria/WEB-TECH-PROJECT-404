<?php
require_once 'config/db.php';

function getAllArticles($category_id = null) {
    global $conn;
    if ($category_id) {
        $stmt = $conn->prepare("SELECT articles.*, users.name as author_name, categories.name as category_name 
            FROM articles 
            JOIN users ON articles.author_id = users.id 
            JOIN categories ON articles.category_id = categories.id
            WHERE articles.status = 'published' AND articles.category_id = ?
            ORDER BY articles.created_at DESC");
        $stmt->bind_param("i", $category_id);
    } else {
        $stmt = $conn->prepare("SELECT articles.*, users.name as author_name, categories.name as category_name 
            FROM articles 
            JOIN users ON articles.author_id = users.id 
            JOIN categories ON articles.category_id = categories.id
            WHERE articles.status = 'published'
            ORDER BY articles.created_at DESC");
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getArticleById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT articles.*, users.name as author_name, users.bio as author_bio, categories.name as category_name 
        FROM articles 
        JOIN users ON articles.author_id = users.id 
        JOIN categories ON articles.category_id = categories.id
        WHERE articles.id = ? AND articles.status = 'published'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getLikeCount($article_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE article_id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function isLikedByUser($article_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getAllCategories() {
    global $conn;
    $result = $conn->query("SELECT * FROM categories");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function incrementViewCount($article_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
}
?>