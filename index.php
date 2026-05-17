<?php
require_once __DIR__ . '/config/app.php';

$page   = $_GET['page']   ?? 'login';
$action = $_GET['action'] ?? 'index';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================================
// SHARED ROUTES — login/logout used by all roles
// ============================================================
$routes = [
    'login'  => ['index' => ['AuthController', 'showLogin']],
    'logout' => ['index' => ['AuthController', 'logout']],
];

// ============================================================
// AUTHOR ROUTES
// All prefixed with author_ — no conflict with other roles
// ============================================================
$routes['author_dashboard'] = ['index'           => ['AuthorDashboardController', 'index']];
$routes['author_articles']  = [
    'index'          => ['AuthorArticleController', 'index'],
    'create'         => ['AuthorArticleController', 'create'],
    'edit'           => ['AuthorArticleController', 'edit'],
    'autosave'       => ['AuthorArticleController', 'autosave'],
    'ajaxlist'       => ['AuthorArticleController', 'ajaxList'],
    'restorerevision'=> ['AuthorArticleController', 'restoreRevision'],
];
$routes['author_series']    = [
    'index'         => ['AuthorSeriesController', 'index'],
    'create'        => ['AuthorSeriesController', 'create'],
    'edit'          => ['AuthorSeriesController', 'edit'],
    'addarticle'    => ['AuthorSeriesController', 'addArticle'],
    'removearticle' => ['AuthorSeriesController', 'removeArticle'],
    'updateorder'   => ['AuthorSeriesController', 'updateOrder'],
];
$routes['author_analytics'] = [
    'index'   => ['AuthorAnalyticsController', 'index'],
    'article' => ['AuthorAnalyticsController', 'article'],
];
$routes['author_comments']  = [
    'index'  => ['AuthorCommentController', 'index'],
    'reply'  => ['AuthorCommentController', 'reply'],
    'delete' => ['AuthorCommentController', 'delete'],
];
$routes['author_profile']   = [
    'index'          => ['AuthorProfileController', 'index'],
    'changepassword' => ['AuthorProfileController', 'changePassword'],
];
$routes['author_calendar']  = [
    'index' => ['AuthorCalendarController', 'index'],
];

// ============================================================
// POST ROUTES
// ============================================================
$postRoutes = [
    'login'                               => ['AuthController',          'login'],
    'author_articles_store'               => ['AuthorArticleController', 'store'],
    'author_articles_update'              => ['AuthorArticleController', 'update'],
    'author_articles_submit'              => ['AuthorArticleController', 'submit'],
    'author_articles_unpublish'           => ['AuthorArticleController', 'unpublish'],
    'author_articles_restorerevision'     => ['AuthorArticleController', 'restoreRevision'],
    'author_articles_autosave'            => ['AuthorArticleController', 'autosave'],
    'author_series_store'                 => ['AuthorSeriesController',  'store'],
    'author_series_update'                => ['AuthorSeriesController',  'update'],
    'author_series_delete'                => ['AuthorSeriesController',  'delete'],
    'author_series_addarticle'            => ['AuthorSeriesController',  'addArticle'],
    'author_series_removearticle'         => ['AuthorSeriesController',  'removeArticle'],
    'author_series_updateorder'           => ['AuthorSeriesController',  'updateOrder'],
    'author_comments_reply'               => ['AuthorCommentController', 'reply'],
    'author_comments_delete'              => ['AuthorCommentController', 'delete'],
    'author_profile_update'               => ['AuthorProfileController', 'update'],
    'author_profile_changepassword'       => ['AuthorProfileController', 'changePassword'],
];

// ============================================================
// DISPATCHER
// ============================================================
if ($method === 'POST') {
    $postKey = $_POST['_route'] ?? '';
    if (isset($postRoutes[$postKey])) {
        [$controllerName, $methodName] = $postRoutes[$postKey];
        require_once __DIR__ . '/controllers/' . $controllerName . '.php';
        $controller = new $controllerName();
        $controller->$methodName();
        exit;
    }
}

$actionKey = strtolower($action);
if (isset($routes[$page][$actionKey])) {
    [$controllerName, $methodName] = $routes[$page][$actionKey];
    require_once __DIR__ . '/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    if (isLoggedIn()) {
        $role = $_SESSION['role'] ?? 'author';
        redirect(BASE_URL . '/index.php?page=' . $role . '_dashboard');
    } else {
        redirect(BASE_URL . '/index.php?page=login');
    }
}
