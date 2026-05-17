<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/SeriesModel.php';
require_once __DIR__ . '/../models/ArticleModel.php';

class AuthorSeriesController {
    private $seriesModel;
    private $articleModel;

    public function __construct() {
        requireAuthor();
        $this->seriesModel  = new SeriesModel();
        $this->articleModel = new ArticleModel();
    }

    public function index() {
        $author_id  = $_SESSION['user_id'];
        $seriesList = $this->seriesModel->getByAuthor($author_id);
        require __DIR__ . '/../views/series/index.php';
    }

    public function create() {
        require __DIR__ . '/../views/series/create.php';
    }

    public function store() {
        $author_id   = $_SESSION['user_id'];
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (empty($title)) { setFlash('error','Title required.'); redirect(BASE_URL.'/index.php?page=author_series&action=create'); }
        $cover = uploadImage($_FILES['cover_image'] ?? [], 'series');
        $this->seriesModel->create($author_id, $title, $description, $cover);
        setFlash('success','Series created!');
        redirect(BASE_URL.'/index.php?page=author_series');
    }

    public function edit() {
        $id        = intval($_GET['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $series    = $this->seriesModel->getById($id, $author_id);
        if (!$series) { setFlash('error','Not found.'); redirect(BASE_URL.'/index.php?page=author_series'); }
        $articles    = $this->seriesModel->getArticles($id, $author_id);
        $available   = $this->seriesModel->getPublishedArticlesNotInSeries($author_id, $id);
        require __DIR__ . '/../views/series/edit.php';
    }

    public function update() {
        $id          = intval($_POST['id'] ?? 0);
        $author_id   = $_SESSION['user_id'];
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $cover       = uploadImage($_FILES['cover_image'] ?? [], 'series');
        $this->seriesModel->update($id, $author_id, $title, $description, $cover ?: null);
        setFlash('success','Series updated!');
        redirect(BASE_URL.'/index.php?page=author_series&action=edit&id='.$id);
    }

    public function delete() {
        $id        = intval($_POST['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $this->seriesModel->delete($id, $author_id);
        setFlash('success','Series deleted.');
        redirect(BASE_URL.'/index.php?page=author_series');
    }

    public function addArticle() {
        header('Content-Type: application/json');
        $series_id  = intval($_POST['series_id'] ?? 0);
        $article_id = intval($_POST['article_id'] ?? 0);
        $order      = intval($_POST['order'] ?? 0);
        $author_id  = $_SESSION['user_id'];
        $ok = $this->seriesModel->addArticleToSeries($series_id, $article_id, $author_id, $order);
        echo json_encode(['success'=>$ok]);
        exit;
    }

    public function removeArticle() {
        header('Content-Type: application/json');
        $article_id = intval($_POST['article_id'] ?? 0);
        $author_id  = $_SESSION['user_id'];
        $ok = $this->seriesModel->removeArticleFromSeries($article_id, $author_id);
        echo json_encode(['success'=>$ok]);
        exit;
    }

    public function updateOrder() {
        header('Content-Type: application/json');
        $series_id  = intval($_POST['series_id'] ?? 0);
        $article_id = intval($_POST['article_id'] ?? 0);
        $order      = intval($_POST['order'] ?? 0);
        $author_id  = $_SESSION['user_id'];
        $ok = $this->seriesModel->updateOrder($series_id, $article_id, $author_id, $order);
        echo json_encode(['success'=>$ok]);
        exit;
    }
}
