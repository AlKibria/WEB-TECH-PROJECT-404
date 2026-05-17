<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page === 'home') {
    require 'controllers/ArticleController.php';
    showHomepage();

} elseif ($page === 'article') {
    require 'controllers/ArticleController.php';
    showArticle();

} elseif ($page === 'register') {
    require 'controllers/AuthController.php';
    showRegister();

} elseif ($page === 'login') {
    require 'controllers/AuthController.php';
    showLogin();

} elseif ($page === 'logout') {
    require 'controllers/AuthController.php';
    logout();

} elseif ($page === 'comment') {
    require 'controllers/CommentController.php';
    addComment();

} else {
    echo "Page not found.";
}
?>