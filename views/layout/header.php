<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Author Portal' ?> — InkPress</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-ink">Ink</span><span class="brand-press">Press</span>
            <div class="brand-sub">Author Portal</div>
        </div>

        <div class="sidebar-user">
            <?php $pic = $_SESSION['profile_pic'] ?? null; ?>
            <?php if ($pic): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($pic) ?>" alt="avatar" class="user-avatar">
            <?php else: ?>
                <div class="user-avatar user-avatar-placeholder"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
            <?php endif; ?>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></div>
                <div class="user-role">Author</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php $cur = $_GET['page'] ?? 'author_dashboard'; ?>
            <a href="<?= BASE_URL ?>/index.php?page=author_dashboard" class="nav-item <?= 'author_dashboard'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                Dashboard
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_articles" class="nav-item <?= 'author_articles'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                Articles
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_series" class="nav-item <?= 'author_series'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12zM10 9h8v2h-8zm0 3h4v2h-4zm0-6h8v2h-8z"/></svg>
                Series
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_analytics" class="nav-item <?= 'author_analytics'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L21 8l-9 9z"/></svg>
                Analytics
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_comments" class="nav-item <?= 'author_comments'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M21 6.5c0-1.38-1.12-2.5-2.5-2.5h-13C4.12 4 3 5.12 3 6.5v9C3 16.88 4.12 18 5.5 18H8l4 4 4-4h2.5c1.38 0 2.5-1.12 2.5-2.5v-9z"/></svg>
                Comments
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_calendar" class="nav-item <?= 'author_calendar'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                Calendar
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=author_profile" class="nav-item <?= 'author_profile'===$cur?'active':'' ?>">
                <svg viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                Profile
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/index.php?page=logout" class="nav-item logout-link">
                <svg viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="flash flash-<?= $flash['type'] ?>">
            <?= htmlspecialchars($flash['message']) ?>
            <button onclick="this.parentElement.remove()" class="flash-close">×</button>
        </div>
        <?php endif; ?>
