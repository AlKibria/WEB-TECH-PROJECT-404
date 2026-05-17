<?php require_once __DIR__ . '/../../config/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — InkPress</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="login-body">
<div class="login-container" style="max-width:480px;">
    <div class="login-brand">
        <span class="brand-ink">Ink</span><span class="brand-press">Press</span>
        <div class="login-tagline">Create an Account</div>
    </div>

    <?php $flash = getFlash(); if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?= BASE_URL ?>/index.php?page=register">
        <input type="hidden" name="_route" value="register">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Your full name" class="form-control"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="e.g. alexj" class="form-control"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            <small style="color:var(--text-muted);font-size:.78rem;">Letters, numbers, underscores only</small>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="you@example.com" class="form-control"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Min. 6 characters" class="form-control">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required placeholder="Repeat password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary btn-full">Create Account</button>
    </form>

    <p class="login-note" style="margin-top:1.2rem;">
        Already have an account? <a href="<?= BASE_URL ?>/index.php?page=login" style="color:var(--accent);">Sign in</a>
    </p>
</div>
</body>
</html>
