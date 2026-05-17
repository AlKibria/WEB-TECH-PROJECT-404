<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/ArticleModel.php';

class AuthorCommentController {
    private $commentModel;
    private $articleModel;

    public function __construct() {
        requireAuthor();
        $this->commentModel = new CommentModel();
        $this->articleModel = new ArticleModel();
    }

    public function index() {
        $author_id = $_SESSION['user_id'];
        $comments  = $this->commentModel->getByAuthorArticles($author_id);
        require __DIR__ . '/../views/comments/index.php';
    }

    public function reply() {
        $author_id  = $_SESSION['user_id'];
        $comment_id = intval($_POST['comment_id'] ?? 0);
        $article_id = intval($_POST['article_id'] ?? 0);
        $body       = trim($_POST['body'] ?? '');
        if (empty($body)) { setFlash('error','Reply cannot be empty.'); redirect(BASE_URL.'/index.php?page=author_comments'); }
        $this->commentModel->reply($article_id, $author_id, $body, $comment_id);
        setFlash('success','Reply posted!');
        redirect(BASE_URL.'/index.php?page=author_comments');
    }

    public function delete() {
        header('Content-Type: application/json');
        $author_id  = $_SESSION['user_id'];
        $comment_id = intval($_POST['comment_id'] ?? 0);
        $ok = $this->commentModel->delete($comment_id, $author_id);
        echo json_encode(['success'=>$ok]);
        exit;
    }
}
