<?php
session_start();

// Guard: only allow logged-in admins
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/db_connect.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example: Save settings to a settings table or config file
    // For simplicity, we'll save into a `settings` table (key-value pair)

    $settings = [
        'site_name'              => $_POST['site_name'] ?? '',
        'site_description'       => $_POST['site_description'] ?? '',
        'allow_registration'     => isset($_POST['allow_registration']) ? '1' : '0',
        'default_user_role'      => $_POST['default_user_role'] ?? 'reader',
        'require_email_verification' => isset($_POST['require_email_verification']) ? '1' : '0',
        'allow_comments'         => isset($_POST['allow_comments']) ? '1' : '0',
        'comments_moderation'    => isset($_POST['comments_moderation']) ? '1' : '0',
        'articles_require_approval' => isset($_POST['articles_require_approval']) ? '1' : '0',
        'items_per_page'         => intval($_POST['items_per_page'] ?? 10),
        'site_email'             => $_POST['site_email'] ?? '',
    ];

    foreach ($settings as $key => $value) {
        $stmt = $conn->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE `value` = ?");
        $stmt->bind_param("sss", $key, $value, $value);
        $stmt->execute();
    }

    $success = "Settings saved successfully!";
}

// Fetch Current Settings
$settings = [];
$result = mysqli_query($conn, "SELECT `key`, `value` FROM settings");
while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Settings - Admin Panel</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #111827;
            color: white;
            font-family: Arial, sans-serif;
        }
        .topbar {
            background: #1f2937;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #00ffcc;
        }
        .main { padding: 32px; max-width: 1000px; }

        .card {
            background: #1f2937;
            border-radius: 10px;
            padding: 28px;
            margin-bottom: 25px;
            border: 1px solid #374151;
        }
        h2 {
            color: #00ffcc;
            margin-bottom: 20px;
            border-bottom: 1px solid #374151;
            padding-bottom: 10px;
        }
        label {
            display: block;
            margin: 15px 0 6px;
            color: #9ca3af;
        }
        input[type="text"], input[type="email"], select, textarea {
            width: 100%;
            padding: 12px;
            background: #111827;
            border: 1px solid #374151;
            border-radius: 6px;
            color: white;
        }
        .checkbox-group {
            margin: 15px 0;
        }
        .success {
            background: #22c55e;
            color: black;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        button {
            background: #00ffcc;
            color: black;
            padding: 12px 28px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover { opacity: 0.9; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>⚙ Platform Settings</h1>
    <a href="admin.php" style="color:#00ffcc; text-decoration:none;">← Back to Dashboard</a>
</div>

<div class="main">

    <?php if (isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        
        <!-- General Settings -->
        <div class="card">
            <h2>General Settings</h2>
            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'My Blog') ?>">

            <label>Site Description</label>
            <textarea name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>

            <label>Site Email</label>
            <input type="email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
        </div>

        <!-- User Registration -->
        <div class="card">
            <h2>User Registration</h2>
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="allow_registration" <?= ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' ?>>
                    Allow new user registrations
                </label>
            </div>
            
            <label>Default Role for New Users</label>
            <select name="default_user_role">
                <option value="reader" <?= ($settings['default_user_role'] ?? '') == 'reader' ? 'selected' : '' ?>>Reader</option>
                <option value="author" <?= ($settings['default_user_role'] ?? '') == 'author' ? 'selected' : '' ?>>Author</option>
            </select>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="require_email_verification" <?= ($settings['require_email_verification'] ?? '0') == '1' ? 'checked' : '' ?>>
                    Require email verification
                </label>
            </div>
        </div>

        <!-- Content Settings -->
        <div class="card">
            <h2>Content & Moderation</h2>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="allow_comments" <?= ($settings['allow_comments'] ?? '1') == '1' ? 'checked' : '' ?>>
                    Allow comments on articles
                </label>
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="comments_moderation" <?= ($settings['comments_moderation'] ?? '1') == '1' ? 'checked' : '' ?>>
                    Require admin approval for comments
                </label>
            </div>

            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="articles_require_approval" <?= ($settings['articles_require_approval'] ?? '1') == '1' ? 'checked' : '' ?>>
                    Require admin approval for new articles
                </label>
            </div>

            <label>Items per Page (Articles/Users)</label>
            <input type="number" name="items_per_page" value="<?= htmlspecialchars($settings['items_per_page'] ?? '10') ?>" style="width:150px;">
        </div>

        <button type="submit">Save All Settings</button>
    </form>

</div>

</body>
</html>