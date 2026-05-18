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

}  elseif ($page === 'comment') {
    require 'controllers/CommentController.php';
    handleAddComment();

}  elseif ($page === 'reading_list') {
    require 'controllers/ReadingListController.php';
    showReadingList();

} elseif ($page === 'save_article') {
    require 'controllers/ReadingListController.php';
    handleSaveArticle();

} elseif ($page === 'remove_article') {
    require 'controllers/ReadingListController.php';
    handleRemoveArticle();

} elseif ($page === 'delete_comment') {
    require 'controllers/CommentController.php';
    handleDeleteComment();

} else {
    echo "Page not found.";
}
?>