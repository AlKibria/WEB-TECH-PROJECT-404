<?php
session_start();

// Guard: only allow logged-in admins
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/db_connect.php';

// Handle Actions (Update Role, Toggle Status, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'toggle_status') {
            $new_status = $_POST['status'] === 'active' ? 'inactive' : 'active';
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $user_id);
            $stmt->execute();
        } 
        elseif ($_POST['action'] === 'update_role') {
            $new_role = $_POST['role'];
            $allowed_roles = ['reader', 'author', 'editor', 'admin'];
            if (in_array($new_role, $allowed_roles)) {
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->bind_param("si", $new_role, $user_id);
                $stmt->execute();
            }
        } 
        elseif ($_POST['action'] === 'delete') {
            // Soft delete or hard delete (recommended: soft delete)
            $stmt = $conn->prepare("UPDATE users SET status = 'deleted' WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
    }
    header("Location: admin_users.php");
    exit();
}

// Search & Filter
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT id, name, email, username, role, status, created_at 
          FROM users WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR username LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if (!empty($role_filter)) {
    $query .= " AND role = ?";
    $params[] = $role_filter;
    $types .= "s";
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
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
        .topbar h1 { color: #00ffcc; }
        .main { padding: 32px; }

        .controls {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        input, select, button {
            padding: 10px 14px;
            border: none;
            border-radius: 6px;
            background: #1f2937;
            color: white;
        }
        button {
            background: #00ffcc;
            color: black;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { opacity: 0.9; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #1f2937;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #374151;
        }
        th {
            background: #111827;
            color: #00ffcc;
        }
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .status.active { background: #22c55e; color: black; }
        .status.inactive { background: #ef4444; color: white; }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        .edit-btn { background: #3b82f6; }
        .delete-btn { background: #ef4444; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>👥 User Management</h1>
    <a href="admin.php" style="color:#00ffcc; text-decoration:none;">← Back to Dashboard</a>
</div>

<div class="main">

    <div class="controls">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
            <input type="text" name="search" placeholder="Search name, email or username..." 
                   value="<?= htmlspecialchars($search) ?>" style="width:280px;">
            
            <select name="role">
                <option value="">All Roles</option>
                <option value="reader" <?= $role_filter=='reader'?'selected':'' ?>>Reader</option>
                <option value="author" <?= $role_filter=='author'?'selected':'' ?>>Author</option>
                <option value="editor" <?= $role_filter=='editor'?'selected':'' ?>>Editor</option>
                <option value="admin" <?= $role_filter=='admin'?'selected':'' ?>>Admin</option>
            </select>

            <select name="status">
                <option value="">All Status</option>
                <option value="active" <?= $status_filter=='active'?'selected':'' ?>>Active</option>
                <option value="inactive" <?= $status_filter=='inactive'?'selected':'' ?>>Inactive</option>
            </select>

            <button type="submit">Filter</button>
            <a href="admin_users.php"><button type="button">Reset</button></a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="action" value="update_role">
                        <select name="role" onchange="this.form.submit()" style="background:#374151; color:white; padding:4px 8px;">
                            <option value="reader" <?= $user['role']=='reader'?'selected':'' ?>>Reader</option>
                            <option value="author" <?= $user['role']=='author'?'selected':'' ?>>Author</option>
                            <option value="editor" <?= $user['role']=='editor'?'selected':'' ?>>Editor</option>
                            <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                        </select>
                    </form>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="action" value="toggle_status">
                        <input type="hidden" name="status" value="<?= $user['status'] ?>">
                        <span class="status <?= $user['status'] === 'active' ? 'active' : 'inactive' ?>">
                            <?= ucfirst($user['status']) ?>
                        </span>
                        <button type="submit" class="action-btn" style="margin-left:8px;">
                            <?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </form>
                </td>
                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                <td>
                    <button onclick="if(confirm('Delete this user?')) { 
                        window.location.href='admin_users.php?delete=<?= $user['id'] ?>'; 
                    }" class="action-btn delete-btn">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($result->num_rows === 0): ?>
        <p style="text-align:center; margin-top:30px; color:#9ca3af;">No users found.</p>
    <?php endif; ?>

</div>

</body>
</html>