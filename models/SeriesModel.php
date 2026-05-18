<?php
require_once __DIR__ . '/../config/database.php';

class SeriesModel {
    private $db;
 
    public function __construct() {
        $this->db = getDB();
    }

    public function getByAuthor($author_id) {
        $stmt = $this->db->prepare("
            SELECT s.*, COUNT(a.id) as article_count
            FROM series s LEFT JOIN articles a ON a.series_id = s.id
            WHERE s.author_id = ? GROUP BY s.id ORDER BY s.created_at DESC
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id, $author_id) {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE id=? AND author_id=?");
        $stmt->bind_param("ii", $id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($author_id, $title, $description, $cover_image_path) {
        $stmt = $this->db->prepare("INSERT INTO series (author_id, title, description, cover_image_path) VALUES (?,?,?,?)");
        $stmt->bind_param("isss", $author_id, $title, $description, $cover_image_path);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function update($id, $author_id, $title, $description, $cover_image_path = null) {
        if ($cover_image_path) {
            $stmt = $this->db->prepare("UPDATE series SET title=?, description=?, cover_image_path=? WHERE id=? AND author_id=?");
            $stmt->bind_param("sssii", $title, $description, $cover_image_path, $id, $author_id);
        } else {
            $stmt = $this->db->prepare("UPDATE series SET title=?, description=? WHERE id=? AND author_id=?");
            $stmt->bind_param("ssii", $title, $description, $id, $author_id);
        }
        return $stmt->execute();
    }

    public function delete($id, $author_id) {
        // Remove series link from articles first
        $stmt = $this->db->prepare("UPDATE articles SET series_id=NULL WHERE series_id=? AND author_id=?");
        $stmt->bind_param("ii", $id, $author_id);
        $stmt->execute();
        $stmt = $this->db->prepare("DELETE FROM series WHERE id=? AND author_id=?");
        $stmt->bind_param("ii", $id, $author_id);
        return $stmt->execute();
    }

    public function getArticles($series_id, $author_id) {
        $stmt = $this->db->prepare("
            SELECT id, title, status, series_order, published_at
            FROM articles WHERE series_id=? AND author_id=? ORDER BY series_order ASC, created_at ASC
        ");
        $stmt->bind_param("ii", $series_id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPublishedArticlesNotInSeries($author_id, $series_id) {
        $stmt = $this->db->prepare("
            SELECT id, title FROM articles
            WHERE author_id=? AND status='published' AND (series_id IS NULL OR series_id != ?)
            ORDER BY title
        ");
        $stmt->bind_param("ii", $author_id, $series_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function addArticleToSeries($series_id, $article_id, $author_id, $order) {
        $stmt = $this->db->prepare("UPDATE articles SET series_id=?, series_order=? WHERE id=? AND author_id=?");
        $stmt->bind_param("iiii", $series_id, $order, $article_id, $author_id);
        return $stmt->execute();
    }

    public function removeArticleFromSeries($article_id, $author_id) {
        $stmt = $this->db->prepare("UPDATE articles SET series_id=NULL, series_order=0 WHERE id=? AND author_id=?");
        $stmt->bind_param("ii", $article_id, $author_id);
        return $stmt->execute();
    }

    public function updateOrder($series_id, $article_id, $author_id, $order) {
        $stmt = $this->db->prepare("UPDATE articles SET series_order=? WHERE series_id=? AND id=? AND author_id=?");
        $stmt->bind_param("iiii", $order, $series_id, $article_id, $author_id);
        return $stmt->execute();
    }
}
