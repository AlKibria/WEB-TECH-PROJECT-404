<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CommentModel.php';

class AuthorDashboardController {
    private $articleModel;
    private $userModel;
    private $commentModel;

    public function __construct() {
        requireAuthor();
        $this->articleModel = new ArticleModel();
        $this->userModel    = new UserModel();
        $this->commentModel = new CommentModel();
    }

    public function index() {
        $author_id    = $_SESSION['user_id'];
        $stats        = $this->articleModel->getAuthorStats($author_id);
        $statusCounts = $this->articleModel->getStatusCounts($author_id);
        $mostRead     = $this->articleModel->getMostRead($author_id);
        $mostLiked    = $this->articleModel->getMostLiked($author_id);
        $recentArticles = $this->articleModel->getByAuthor($author_id);
        $recentArticles = array_slice($recentArticles, 0, 5);
        $recentComments = $this->commentModel->getByAuthorArticles($author_id, 5);
        $newComments    = $this->commentModel->getUnreadCount($author_id);
        require __DIR__ . '/../views/dashboard/index.php';
    }
}
