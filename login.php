<?php
session_start();
include 'includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password_hash='$password' AND role='editor'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header('Location: editor/index.php');
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editor Login</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        input[type=submit] {
            background: #2c3e50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }
        .err {
            color: red;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Editor Login</h2>

    <?php if($error != "") { ?>
        <p class="err"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">
        Email: <br>
        <input type="email" name="email"> <br>

        Password: <br>
        <input type="password" name="password"> <br>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>