<?php
require_once 'config/db.php';

function registerUser($name, $email, $password) {
    global $conn;
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $username = explode('@', $email)[0];
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, password_hash, role) VALUES (?, ?, ?, ?, 'reader')");
    $stmt->bind_param("ssss", $name, $username, $email, $hashed);
    return $stmt->execute();
}

function loginUser($email, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}
?>