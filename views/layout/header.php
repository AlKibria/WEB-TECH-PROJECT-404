<?php $current = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">📰 BlogPlatform</a>
        <div class="ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-light me-3">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="index.php?page=reading_list" class="btn btn-outline-light btn-sm me-2">📚 Reading List</a>
                <a href="index.php?page=logout" class="btn btn-outline-light btn-sm">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="index.php?page=register" class="btn btn-light btn-sm">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">