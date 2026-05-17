<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/ArticleModel.php';

class AuthorCalendarController {
    private $articleModel;

    public function __construct() {
        requireAuthor();
        $this->articleModel = new ArticleModel();
    }

    public function index() {
        $author_id = $_SESSION['user_id'];
        $calendar  = $this->articleModel->getEditorialCalendar($author_id);
        require __DIR__ . '/../views/calendar/index.php';
    }
}
