<?php
require_once 'config/db.php';

function getCommentsByArticle($article_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT comments.*, users.name as user_name 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.article_id = ?
        ORDER BY comments.created_at DESC");
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function addComment($article_id, $user_id, $body) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, body) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $article_id, $user_id, $body);
    return $stmt->execute();
}

function deleteComment($comment_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    return $stmt->execute();
}
?>