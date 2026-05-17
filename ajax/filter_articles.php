<?php
require_once '../config/db.php';
require_once '../models/Article.php';

$category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;

$articles = getAllArticles($category_id);

header('Content-Type: application/json');
echo json_encode($articles);
?>