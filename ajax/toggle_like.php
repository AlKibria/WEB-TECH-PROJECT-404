<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$article_id = (int)$data['article_id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ?");
$stmt->bind_param("ii", $article_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM likes WHERE article_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $liked = false;
} else {
    $stmt = $conn->prepare("INSERT INTO likes (article_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $liked = true;
}

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM likes WHERE article_id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$count = $stmt->get_result()->fetch_assoc()['total'];

echo json_encode(['liked' => $liked, 'count' => $count]);
?>