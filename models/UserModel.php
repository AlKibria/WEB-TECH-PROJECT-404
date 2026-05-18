<?php
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $db;
  
    public function __construct() {
        $this->db = getDB();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProfile($id, $name, $bio, $social_links, $profile_pic = null) {
        if ($profile_pic) {
            $stmt = $this->db->prepare("UPDATE users SET name=?, bio=?, social_links=?, profile_pic=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $bio, $social_links, $profile_pic, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name=?, bio=?, social_links=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $bio, $social_links, $id);
        }
        return $stmt->execute();
    }

    public function updatePassword($id, $hash) {
        $stmt = $this->db->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $stmt->bind_param("si", $hash, $id);
        return $stmt->execute();
    }

    public function getFollowerCount($author_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM follows WHERE followed_author_id=?");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['cnt'];
    }

    public function getFollowers($author_id) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.username, u.profile_pic, f.created_at
            FROM follows f JOIN users u ON u.id = f.follower_id
            WHERE f.followed_author_id = ? ORDER BY f.created_at DESC
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getFollowerGrowth($author_id) {
        $stmt = $this->db->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM follows WHERE followed_author_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at) ORDER BY date ASC
        ");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
