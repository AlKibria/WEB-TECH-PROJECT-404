<?php
// Git update: Added documentation comment for ArticleModel
require_once __DIR__ . '/../config/database.php';

class ArticleModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }
 
    public function getByAuthor($author_id, $status = null) {
        if ($status && $status !== 'all') {
            $stmt = $this->db->prepare("
                SELECT a.*, c.name as category_name
                FROM articles a LEFT JOIN categories c ON c.id = a.category_id
                WHERE a.author_id = ? AND a.status = ? ORDER BY a.updated_at DESC
            ");
            $stmt->bind_param("is", $author_id, $status);
        } else {
            $stmt = $this->db->prepare("
                SELECT a.*, c.name as category_name
                FROM articles a LEFT JOIN categories c ON c.id = a.category_id
                WHERE a.author_id = ? ORDER BY a.updated_at DESC
            ");
            $stmt->bind_param("i", $author_id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id, $author_id = null) {
        if ($author_id) {
            $stmt = $this->db->prepare("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.id = ? AND a.author_id = ?");
            $stmt->bind_param("ii", $id, $author_id);
        } else {
            $stmt = $this->db->prepare("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE a.id = ?");
            $stmt->bind_param("i", $id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO articles (author_id, category_id, series_id, series_order, title, slug, body, excerpt, featured_image_path, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiisssssss",
            $data['author_id'],
            $data['category_id'],
            $data['series_id'],
            $data['series_order'],
            $data['title'],
            $data['slug'],
            $data['body'],
            $data['excerpt'],
            $data['featured_image_path'],
            $data['status']
        );
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function update($id, $author_id, $data) {
        $stmt = $this->db->prepare("
            UPDATE articles SET title=?, slug=?, body=?, excerpt=?, category_id=?, series_id=?,
                series_order=?, featured_image_path=COALESCE(?, featured_image_path), status=?, updated_at=NOW()
            WHERE id=? AND author_id=?
        ");
        $stmt->bind_param(
            "ssssiissii",
            $data['title'], $data['slug'], $data['body'], $data['excerpt'],
            $data['category_id'], $data['series_id'], $data['series_order'],
            $data['featured_image_path'], $data['status'], $id, $author_id
        );
        return $stmt->execute();
    }

    public function updateStatus($id, $author_id, $status) {
        $stmt = $this->db->prepare("UPDATE articles SET status=?, updated_at=NOW() WHERE id=? AND author_id=?");
        $stmt->bind_param("sii", $status, $id, $author_id);
        return $stmt->execute();
    }

    public function saveRevision($article_id, $author_id, $body) {
        $stmt = $this->db->prepare("INSERT INTO article_revisions (article_id, author_id, body_snapshot) VALUES (?,?,?)");
        $stmt->bind_param("iis", $article_id, $author_id, $body);
        return $stmt->execute();
    }

    public function getRevisions($article_id, $author_id) {
        $stmt = $this->db->prepare("SELECT * FROM article_revisions WHERE article_id=? AND author_id=? ORDER BY saved_at DESC");
        $stmt->bind_param("ii", $article_id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getRevisionById($revision_id) {
        $stmt = $this->db->prepare("SELECT * FROM article_revisions WHERE id=?");
        $stmt->bind_param("i", $revision_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getTags($article_id) {
        $stmt = $this->db->prepare("SELECT t.* FROM tags t JOIN article_tags at ON at.tag_id=t.id WHERE at.article_id=?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function syncTags($article_id, $author_id, $tag_names) {
        $stmt = $this->db->prepare("DELETE FROM article_tags WHERE article_id=?");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        foreach ($tag_names as $name) {
            $name = trim($name);
            if (empty($name)) continue;
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $stmt = $this->db->prepare("INSERT INTO tags (name, slug, created_by) VALUES (?,?,?) ON DUPLICATE KEY UPDATE usage_count=usage_count+1");
            $stmt->bind_param("ssi", $name, $slug, $author_id);
            $stmt->execute();
            $stmt2 = $this->db->prepare("SELECT id FROM tags WHERE slug=?");
            $stmt2->bind_param("s", $slug);
            $stmt2->execute();
            $tag = $stmt2->get_result()->fetch_assoc();
            if ($tag) {
                $stmt3 = $this->db->prepare("INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?,?)");
                $stmt3->bind_param("ii", $article_id, $tag['id']);
                $stmt3->execute();
            }
        }
    }

    public function getStats($article_id, $author_id) {
        $stmt = $this->db->prepare("
            SELECT
                (SELECT COUNT(*) FROM likes WHERE article_id=?) as likes,
                (SELECT COUNT(*) FROM comments WHERE article_id=?) as comments,
                (SELECT view_count FROM articles WHERE id=? AND author_id=?) as views
        ");
        $stmt->bind_param("iiii", $article_id, $article_id, $article_id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getViewsOverTime($article_id) {
        $stmt = $this->db->prepare("
            SELECT DATE(read_at) as date, COUNT(*) as reads
            FROM reading_history WHERE article_id=?
            AND read_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(read_at) ORDER BY date ASC
        ");
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAuthorStats($author_id) {
        $stmt = $this->db->prepare("
            SELECT
                (SELECT COUNT(*) FROM articles WHERE author_id=? AND status='published') as total_published,
                (SELECT COALESCE(SUM(view_count),0) FROM articles WHERE author_id=?) as total_views,
                (SELECT COUNT(*) FROM follows WHERE followed_author_id=?) as total_followers,
                (SELECT COUNT(*) FROM likes l JOIN articles a ON a.id=l.article_id WHERE a.author_id=?) as total_likes,
                (SELECT COUNT(*) FROM comments c JOIN articles a ON a.id=c.article_id WHERE a.author_id=?) as total_comments
        ");
        $stmt->bind_param("iiiii", $author_id, $author_id, $author_id, $author_id, $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getMostRead($author_id) {
        $stmt = $this->db->prepare("SELECT id, title, view_count FROM articles WHERE author_id=? AND status='published' ORDER BY view_count DESC LIMIT 1");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getMostLiked($author_id) {
        $stmt = $this->db->prepare("
            SELECT a.id, a.title, COUNT(l.id) as like_count
            FROM articles a LEFT JOIN likes l ON l.article_id=a.id
            WHERE a.author_id=? AND a.status='published'
            GROUP BY a.id ORDER BY like_count DESC LIMIT 1
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStatusCounts($author_id) {
        $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM articles WHERE author_id=? GROUP BY status");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $counts = ['draft'=>0,'submitted'=>0,'revision_requested'=>0,'approved'=>0,'published'=>0,'unpublished'=>0];
        foreach ($rows as $r) {
            if (isset($counts[$r['status']])) $counts[$r['status']] = $r['count'];
        }
        return $counts;
    }

    public function getAllCategories() {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getEditorialCalendar($author_id) {
        $stmt = $this->db->prepare("
            SELECT ec.*, a.title as article_title, a.status as article_status, u.name as editor_name
            FROM editorial_calendar ec
            JOIN articles a ON a.id = ec.article_id
            JOIN users u ON u.id = ec.editor_id
            WHERE a.author_id = ?
            ORDER BY ec.scheduled_date ASC
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
