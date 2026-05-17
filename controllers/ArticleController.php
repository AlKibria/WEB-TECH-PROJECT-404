<?php
require_once 'config/db.php';
require_once 'models/User.php';

function showRegister() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        if (registerUser($name, $email, $password)) {
            header("Location: index.php?page=login&success=1");
            exit;
        } else {
            $error = "Registration failed. Email may already exist.";
        }
    }
    require 'views/auth/register.php';
}

function showLogin() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = loginUser($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php?page=home");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
    require 'views/auth/login.php';
}

function logout() {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}
?>