<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLogin() {
        include __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($email) || empty($password)) {
            setFlash('error', 'Please fill in all fields.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        $user = $this->userModel->findByEmail($email);

        // Wrong email or password
        if (!$user || !password_verify($password, $user['password_hash'])) {
            setFlash('error', 'Invalid email or password.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        // Account inactive
        if (!$user['is_active']) {
            setFlash('error', 'Your account has been deactivated. Contact admin.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        // Author specific check — must be approved
        if ($user['role'] === 'author' && !$user['is_author_approved']) {
            setFlash('error', 'Your author application is pending approval by admin.');
            redirect(BASE_URL . '/index.php?page=login');
        }

        // Set session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['username']   = $user['username'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['profile_pic']= $user['profile_pic'];

        // Redirect based on role — merge friendly
        switch ($user['role']) {
            case 'author':
                redirect(BASE_URL . '/index.php?page=author_dashboard');
                break;
            case 'reader':
                redirect(BASE_URL . '/index.php?page=reader_dashboard');
                break;
            case 'editor':
                redirect(BASE_URL . '/index.php?page=editor_dashboard');
                break;
            case 'admin':
                redirect(BASE_URL . '/index.php?page=admin_dashboard');
                break;
            default:
                redirect(BASE_URL . '/index.php?page=login');
        }
    }

    public function logout() {
        session_destroy();
        redirect(BASE_URL . '/index.php?page=login');
    }
}
