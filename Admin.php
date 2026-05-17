<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Demo Admin Credentials
    $admin_user = "admin";
    $admin_pass = "12345";

    if ($username === $admin_user && $password === $admin_pass) {

        $_SESSION['admin'] = $username;

        header("Location: admin_dashboard.php");
        exit();

    } else {
        $error = "Invalid Username or Password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="login-box">

        <h1>ADMIN PANEL</h1>

        <p class="subtitle">Secure System Access</p>

        <?php
        if ($error != "") {
            echo "<div class='error'>$error</div>";
        }
        ?>

        <form method="POST" onsubmit="return validateForm()">

            <div class="input-group">
                <label>Username</label>
                <input type="text" id="username" name="username">
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" id="password" name="password">
            </div>

            <button type="submit">LOGIN</button>

        </form>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>