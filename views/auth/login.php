<?php require 'views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <h2 class="mb-4">Login</h2>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Registered successfully! Please login.</div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <p class="mt-3 text-center">No account? <a href="index.php?page=register">Register</a></p>
        </form>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>