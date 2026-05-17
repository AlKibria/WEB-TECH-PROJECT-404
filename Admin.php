<?php
// admin_login.php

session_start();

// If already logged in as admin, redirect directly to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

require_once 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic server-side validation
    if ($username === "" || $password === "") {
        $error = "Username and password are required.";

    } elseif (strlen($password) < 5) {
        $error = "Password must be at least 5 characters.";

    } else {
        // Fetch admin user from database using a prepared statement
        $stmt = mysqli_prepare($conn, "
            SELECT id, name, username, password_hash, role, is_active
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            // Username not found — use a generic message to avoid username enumeration
            $error = "Invalid Username or Password.";

        } elseif ($user['role'] !== 'admin') {
            // User exists but is not an admin
            $error = "Access denied. Admin accounts only.";

        } elseif (!$user['is_active']) {
            // Account has been deactivated
            $error = "Your account has been deactivated. Please contact support.";

        } elseif (!password_verify($password, $user['password_hash'])) {
            // Wrong password
            $error = "Invalid Username or Password.";

        } else {
            // ✅ Successful login — store admin info in session
            session_regenerate_id(true); // Prevent session fixation

            $_SESSION['admin_id']       = $user['id'];
            $_SESSION['admin']          = $user['username'];
            $_SESSION['admin_name']     = $user['name'];
            $_SESSION['admin_role']     = $user['role'];

            header("Location: admin_dashboard.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="adminloginstyle.css">
</head>
<body>

<div class="container">
    <div class="login-box">
        <h1>ADMIN PANEL</h1>
        <p class="subtitle">Secure System Access</p>

        <?php if ($error !== ""): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       autocomplete="username">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password">
            </div>
            <button type="submit">LOGIN</button>
        </form>
    </div>
</div>

<script src="adminloginjs.js"></script>
</body>
</html>