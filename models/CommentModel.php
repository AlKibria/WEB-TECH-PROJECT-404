<?php
require_once __DIR__ . '/../config/database.php';

class CommentModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getByAuthorArticles($author_id, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as commenter_name, u.username as commenter_username, u.profile_pic,
                   a.title as article_title, a.id as article_id, a.slug as article_slug
            FROM comments c
            JOIN users u ON u.id = c.user_id
            JOIN articles a ON a.id = c.article_id
            WHERE a.author_id = ? ORDER BY c.created_at DESC LIMIT ?
        ");
        $stmt->bind_param("ii", $author_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getByArticle($article_id, $author_id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as commenter_name, u.username as commenter_username, u.profile_pic
            FROM comments c JOIN users u ON u.id = c.user_id
            JOIN articles a ON a.id = c.article_id
            WHERE c.article_id = ? AND a.author_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->bind_param("ii", $article_id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function reply($article_id, $user_id, $body, $parent_id) {
        $stmt = $this->db->prepare("INSERT INTO comments (article_id, user_id, body, parent_id) VALUES (?,?,?,?)");
        $stmt->bind_param("iisi", $article_id, $user_id, $body, $parent_id);
        return $stmt->execute();
    }

    public function delete($comment_id, $author_id) {
        // Author can delete comments on their own articles
        $stmt = $this->db->prepare("
            DELETE c FROM comments c
            JOIN articles a ON a.id = c.article_id
            WHERE c.id = ? AND a.author_id = ?
        ");
        $stmt->bind_param("ii", $comment_id, $author_id);
        return $stmt->execute();
    }

    public function getUnreadCount($author_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM comments c
            JOIN articles a ON a.id = c.article_id
            WHERE a.author_id = ? AND c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['cnt'];
    }
}
