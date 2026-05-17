<?php
// ============================================================
// Merge করার সময় BASE_URL automatically detect হবে
// আলাদা করে change করতে হবে না
// ============================================================
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$script   = str_replace('\\', '/', $script);
$script   = rtrim($script, '/');
define('BASE_URL', $protocol . '://' . $host . $script);

define('UPLOAD_PATH', __DIR__ . '/../public/uploads/images/');
define('UPLOAD_URL',  BASE_URL . '/public/uploads/images/');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireAuthor() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'author') {
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
}

function currentUser() {
    return $_SESSION ?? [];
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function generateSlug($title) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    return $slug . '-' . time();
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function timeAgo($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' year'   . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month'  . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day'    . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour'   . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

function uploadImage($file, $prefix = 'img') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return null;
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null;
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . uniqid() . '.' . strtolower($ext);
    $dest     = UPLOAD_PATH . $filename;
    if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $dest)) return $filename;
    return null;
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
