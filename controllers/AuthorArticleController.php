<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/ArticleModel.php';
require_once __DIR__ . '/../models/SeriesModel.php';

class AuthorArticleController {
    private $articleModel;
    private $seriesModel;

    public function __construct() {
        requireAuthor();
        $this->articleModel = new ArticleModel();
        $this->seriesModel  = new SeriesModel();
    }

    public function index() {
        $author_id = $_SESSION['user_id'];
        $status    = $_GET['status'] ?? 'all';
        $articles  = $this->articleModel->getByAuthor($author_id, $status);
        $statusCounts = $this->articleModel->getStatusCounts($author_id);
        require __DIR__ . '/../views/articles/index.php';
    }

    public function create() {
        $categories = $this->articleModel->getAllCategories();
        $seriesList = $this->seriesModel->getByAuthor($_SESSION['user_id']);
        require __DIR__ . '/../views/articles/create.php';
    }

    public function store() {
        $author_id = $_SESSION['user_id'];
        $title     = trim($_POST['title'] ?? '');
        $body      = $_POST['body'] ?? '';
        $excerpt   = trim($_POST['excerpt'] ?? '');
        $category_id  = intval($_POST['category_id'] ?? 0) ?: null;
        $series_id    = intval($_POST['series_id'] ?? 0) ?: null;
        $series_order = intval($_POST['series_order'] ?? 0);
        $tags_raw     = $_POST['tags'] ?? '';
        $action       = $_POST['action'] ?? 'draft'; // draft or submit

        if (empty($title) || empty($body)) {
            setFlash('error', 'Title and body are required.');
            redirect(BASE_URL . '/index.php?page=author_articles&action=create');
        }

        $slug  = generateSlug($title);
        $image = uploadImage($_FILES['featured_image'] ?? [], 'article');
        $status = ($action === 'submit') ? 'submitted' : 'draft';

        $data = compact('author_id','category_id','series_id','series_order','title','slug','body','excerpt','status');
        $data['featured_image_path'] = $image;

        $article_id = $this->articleModel->create($data);

        if ($article_id) {
            // Save revision
            $this->articleModel->saveRevision($article_id, $author_id, $body);
            // Sync tags
            if (!empty($tags_raw)) {
                $tags = explode(',', $tags_raw);
                $this->articleModel->syncTags($article_id, $author_id, $tags);
            }
            setFlash('success', $status === 'submitted' ? 'Article submitted for review!' : 'Draft saved successfully!');
            redirect(BASE_URL . '/index.php?page=author_articles&action=edit&id=' . $article_id);
        } else {
            setFlash('error', 'Failed to create article.');
            redirect(BASE_URL . '/index.php?page=author_articles&action=create');
        }
    }

    public function edit() {
        $id        = intval($_GET['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $article   = $this->articleModel->getById($id, $author_id);
        if (!$article) { setFlash('error', 'Article not found.'); redirect(BASE_URL . '/index.php?page=author_articles'); }

        $tags       = $this->articleModel->getTags($id);
        $revisions  = $this->articleModel->getRevisions($id, $author_id);
        $categories = $this->articleModel->getAllCategories();
        $seriesList = $this->seriesModel->getByAuthor($author_id);
        require __DIR__ . '/../views/articles/edit.php';
    }

    public function update() {
        $id        = intval($_POST['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $article   = $this->articleModel->getById($id, $author_id);
        if (!$article) { setFlash('error', 'Article not found.'); redirect(BASE_URL . '/index.php?page=author_articles'); }

        // Only allow editing drafts and revision_requested articles
        if (!in_array($article['status'], ['draft', 'revision_requested'])) {
            setFlash('error', 'You cannot edit this article in its current status.');
            redirect(BASE_URL . '/index.php?page=author_articles&action=edit&id=' . $id);
        }

        $title        = trim($_POST['title'] ?? '');
        $body         = $_POST['body'] ?? '';
        $excerpt      = trim($_POST['excerpt'] ?? '');
        $category_id  = intval($_POST['category_id'] ?? 0) ?: null;
        $series_id    = intval($_POST['series_id'] ?? 0) ?: null;
        $series_order = intval($_POST['series_order'] ?? 0);
        $tags_raw     = $_POST['tags'] ?? '';
        $action       = $_POST['action'] ?? 'draft';

        $slug   = generateSlug($title);
        $image  = uploadImage($_FILES['featured_image'] ?? [], 'article');
        $status = ($action === 'submit') ? 'submitted' : 'draft';

        $data = compact('title','slug','body','excerpt','category_id','series_id','series_order','status');
        $data['featured_image_path'] = $image;

        $this->articleModel->update($id, $author_id, $data);
        $this->articleModel->saveRevision($id, $author_id, $body);

        if (!empty($tags_raw)) {
            $this->articleModel->syncTags($id, $author_id, explode(',', $tags_raw));
        }

        setFlash('success', $status === 'submitted' ? 'Article submitted for review!' : 'Draft saved!');
        redirect(BASE_URL . '/index.php?page=author_articles&action=edit&id=' . $id);
    }

    public function submit() {
        $id        = intval($_POST['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $article   = $this->articleModel->getById($id, $author_id);
        if (!$article || !in_array($article['status'], ['draft','revision_requested'])) {
            setFlash('error', 'Cannot submit this article.'); redirect(BASE_URL . '/index.php?page=author_articles');
        }
        $this->articleModel->updateStatus($id, $author_id, 'submitted');
        setFlash('success', 'Article submitted for editorial review!');
        redirect(BASE_URL . '/index.php?page=author_articles');
    }

    public function unpublish() {
        $id        = intval($_POST['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $article   = $this->articleModel->getById($id, $author_id);
        if (!$article || $article['status'] !== 'published') {
            setFlash('error', 'Article not found or not published.'); redirect(BASE_URL . '/index.php?page=author_articles');
        }
        $this->articleModel->updateStatus($id, $author_id, 'unpublished');
        setFlash('success', 'Article unpublished.');
        redirect(BASE_URL . '/index.php?page=author_articles');
    }

    public function restoreRevision() {
        $revision_id = intval($_POST['revision_id'] ?? 0);
        $article_id  = intval($_POST['article_id'] ?? 0);
        $author_id   = $_SESSION['user_id'];
        $revision    = $this->articleModel->getRevisionById($revision_id);
        $article     = $this->articleModel->getById($article_id, $author_id);

        if (!$revision || !$article) { setFlash('error', 'Not found.'); redirect(BASE_URL . '/index.php?page=author_articles'); }

        // Save current as a revision before restoring
        $this->articleModel->saveRevision($article_id, $author_id, $article['body']);

        $data = ['title'=>$article['title'],'slug'=>$article['slug'],'body'=>$revision['body_snapshot'],
                 'excerpt'=>$article['excerpt'],'category_id'=>$article['category_id'],
                 'series_id'=>$article['series_id'],'series_order'=>$article['series_order'],
                 'status'=>$article['status'],'featured_image_path'=>null];
        $this->articleModel->update($article_id, $author_id, $data);

        setFlash('success', 'Revision restored successfully!');
        redirect(BASE_URL . '/index.php?page=author_articles&action=edit&id=' . $article_id);
    }

    // AJAX: Auto-save draft
    public function autosave() {
        header('Content-Type: application/json');
        requireAuthor();
        $id        = intval($_POST['id'] ?? 0);
        $author_id = $_SESSION['user_id'];
        $body      = $_POST['body'] ?? '';
        $title     = trim($_POST['title'] ?? '');

        if (!$id || !$body) { echo json_encode(['success'=>false,'message'=>'Missing data']); exit; }

        $article = $this->articleModel->getById($id, $author_id);
        if (!$article || !in_array($article['status'], ['draft','revision_requested'])) {
            echo json_encode(['success'=>false,'message'=>'Cannot autosave']); exit;
        }

        $slug = $title ? generateSlug($title) : $article['slug'];
        $data = ['title'=>$title ?: $article['title'],'slug'=>$slug,'body'=>$body,
                 'excerpt'=>$article['excerpt'],'category_id'=>$article['category_id'],
                 'series_id'=>$article['series_id'],'series_order'=>$article['series_order'],
                 'status'=>$article['status'],'featured_image_path'=>null];
        $this->articleModel->update($id, $author_id, $data);

        echo json_encode(['success'=>true,'message'=>'Saved at ' . date('H:i:s')]);
        exit;
    }

    // AJAX: Filter articles by status
    public function ajaxList() {
        header('Content-Type: application/json');
        requireAuthor();
        $author_id = $_SESSION['user_id'];
        $status    = $_GET['status'] ?? 'all';
        $articles  = $this->articleModel->getByAuthor($author_id, $status);
        echo json_encode(['success'=>true,'articles'=>$articles]);
        exit;
    }
}
