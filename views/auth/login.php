<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Login — InkPress</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="login-body">
<div class="login-container">
    <div class="login-brand">
        <span class="brand-ink">Ink</span><span class="brand-press">Press</span>
        <div class="login-tagline">Author Portal</div>
    </div>

    <?php $flash = getFlash(); if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>" style="margin-bottom:1rem;">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?= BASE_URL ?>/index.php?page=login">
        <input type="hidden" name="_route" value="login">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="you@example.com" class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary btn-full">Sign In to Author Portal</button>
    </form>

    <p class="login-note">Only approved authors can access this portal.</p>
    <p class="login-note" style="margin-top:.5rem;opacity:.5;font-size:.75rem;">
        Demo: alex@example.com / password123
    </p>
</div>
</body>
</html>
