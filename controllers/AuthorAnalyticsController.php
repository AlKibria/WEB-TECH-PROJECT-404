<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthorAnalyticsController {
    private $articleModel;
    private $userModel;

    public function __construct() {
        requireAuthor();
        $this->articleModel = new ArticleModel();
        $this->userModel    = new UserModel();
    }

    public function index() {
        $author_id      = $_SESSION['user_id'];
        $stats          = $this->articleModel->getAuthorStats($author_id);
        $mostRead       = $this->articleModel->getMostRead($author_id);
        $mostLiked      = $this->articleModel->getMostLiked($author_id);
        $followerGrowth = $this->userModel->getFollowerGrowth($author_id);
        $articles       = $this->articleModel->getByAuthor($author_id, 'published');
        require __DIR__ . '/../views/analytics/index.php';
    }

    public function article() {
        $id        = intval($_GET['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $article   = $this->articleModel->getById($id, $author_id);
        if (!$article) { setFlash('error','Not found.'); redirect(BASE_URL.'/index.php?page=author_analytics'); }
        $articleStats  = $this->articleModel->getStats($id, $author_id);
        $viewsOverTime = $this->articleModel->getViewsOverTime($id);
        require __DIR__ . '/../views/analytics/article.php';
    }
}
