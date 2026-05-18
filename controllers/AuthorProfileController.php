
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthorProfileController {
    private $userModel;
 
    public function __construct() {
        requireAuthor();
        $this->userModel = new UserModel();
    }

    public function index() {
        $user      = $this->userModel->findById($_SESSION['user_id']);
        $followers = $this->userModel->getFollowers($_SESSION['user_id']);
        require __DIR__ . '/../views/profile/index.php';
    }

    public function update() {
        $author_id = $_SESSION['user_id'];
        $name      = trim($_POST['name'] ?? '');
        $bio       = trim($_POST['bio'] ?? '');
        $twitter   = trim($_POST['twitter'] ?? '');
        $linkedin  = trim($_POST['linkedin'] ?? '');
        $github    = trim($_POST['github'] ?? '');

        $social_links = json_encode(['twitter'=>$twitter,'linkedin'=>$linkedin,'github'=>$github]);
        $pic = uploadImage($_FILES['profile_pic'] ?? [], 'profile');

        $this->userModel->updateProfile($author_id, $name, $bio, $social_links, $pic ?: null);

        // Update session name
        $_SESSION['user_name'] = $name;
        if ($pic) $_SESSION['profile_pic'] = $pic;

        setFlash('success','Profile updated successfully!');
        redirect(BASE_URL.'/index.php?page=author_profile');
    }

    public function changePassword() {
        $author_id   = $_SESSION['user_id'];
        $current     = $_POST['current_password'] ?? '';
        $new_pass    = $_POST['new_password'] ?? '';
        $confirm     = $_POST['confirm_password'] ?? '';
        $user        = $this->userModel->findById($author_id);

        if (!password_verify($current, $user['password_hash'])) {
            setFlash('error','Current password is incorrect.');
            redirect(BASE_URL.'/index.php?page=author_profile');
        }
        if (strlen($new_pass) < 6) {
            setFlash('error','Password must be at least 6 characters.');
            redirect(BASE_URL.'/index.php?page=author_profile');
        }
        if ($new_pass !== $confirm) {
            setFlash('error','Passwords do not match.');
            redirect(BASE_URL.'/index.php?page=author_profile');
        }
        $this->userModel->updatePassword($author_id, password_hash($new_pass, PASSWORD_DEFAULT));
        setFlash('success','Password changed successfully!');
        redirect(BASE_URL.'/index.php?page=author_profile');
    }
}
