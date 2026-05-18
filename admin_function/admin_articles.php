<?php
session_start();

// Guard: only allow logged-in admins
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/db_connect.php';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article_id = intval($_POST['article_id'] ?? 0);
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'change_status') {
            $new_status = $_POST['status'];
            $allowed = ['draft', 'pending', 'published', 'rejected'];
            if (in_array($new_status, $allowed)) {
                $stmt = $conn->prepare("UPDATE articles SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $new_status, $article_id);
                $stmt->execute();
            }
        } 
        elseif ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->bind_param("i", $article_id);
            $stmt->execute();
            
            // Optional: Delete related comments
            // $stmt = $conn->prepare("DELETE FROM comments WHERE article_id = ?");
            // $stmt->bind_param("i", $article_id);
            // $stmt->execute();
        }
    }
    header("Location: admin_articles.php");
    exit();
}

// Search & Filters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT a.id, a.title, a.status, a.created_at, a.views,
                 u.name as author_name, u.username 
          FROM articles a 
          JOIN users u ON a.author_id = u.id 
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (a.title LIKE ? OR u.name LIKE ?)";
    $like = "%$search%";
    $params = [$like, $like];
    $types .= "ss";
}

if (!empty($status_filter)) {
    $query .= " AND a.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY a.created_at DESC";

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
    <title>Manage Articles - Admin Panel</title>
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
            align-items: center;
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
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status.published { background: #22c55e; color: black; }
        .status.pending   { background: #eab308; color: black; }
        .status.draft     { background: #6b7280; color: white; }
        .status.rejected  { background: #ef4444; color: white; }

        .title { font-weight: 600; max-width: 380px; }
        .action-btn {
            padding: 6px 12px;
            margin: 0 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .view-btn   { background: #3b82f6; }
        .delete-btn { background: #ef4444; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>📄 All Articles</h1>
    <a href="admin.php" style="color:#00ffcc; text-decoration:none;">← Back to Dashboard</a>
</div>

<div class="main">

    <div class="controls">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <input type="text" name="search" placeholder="Search article title or author..." 
                   value="<?= htmlspecialchars($search) ?>" style="width:320px;">

            <select name="status">
                <option value="">All Status</option>
                <option value="published" <?= $status_filter=='published'?'selected':'' ?>>Published</option>
                <option value="pending" <?= $status_filter=='pending'?'selected':'' ?>>Pending</option>
                <option value="draft" <?= $status_filter=='draft'?'selected':'' ?>>Draft</option>
                <option value="rejected" <?= $status_filter=='rejected'?'selected':'' ?>>Rejected</option>
            </select>

            <button type="submit">Filter</button>
            <a href="admin_articles.php"><button type="button">Reset</button></a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Views</th>
                <th>Published Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($article = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $article['id'] ?></td>
                <td class="title"><?= htmlspecialchars($article['title']) ?></td>
                <td><?= htmlspecialchars($article['author_name']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <input type="hidden" name="action" value="change_status">
                        <select name="status" onchange="this.form.submit()" style="background:#374151; color:white; padding:5px 8px; border-radius:4px;">
                            <option value="draft"     <?= $article['status']=='draft'?'selected':'' ?>>Draft</option>
                            <option value="pending"   <?= $article['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="published" <?= $article['status']=='published'?'selected':'' ?>>Published</option>
                            <option value="rejected"  <?= $article['status']=='rejected'?'selected':'' ?>>Rejected</option>
                        </select>
                    </form>
                </td>
                <td><?= number_format($article['views'] ?? 0) ?></td>
                <td><?= date('d M Y', strtotime($article['created_at'])) ?></td>
                <td>
                    <a href="../article.php?id=<?= $article['id'] ?>" target="_blank" class="action-btn view-btn">View</a>
                    <button onclick="if(confirm('Delete this article permanently?')) { 
                        window.location.href='admin_articles.php?delete=<?= $article['id'] ?>'; 
                    }" class="action-btn delete-btn">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php if ($result->num_rows === 0): ?>
        <p style="text-align:center; margin-top:40px; color:#9ca3af;">No articles found.</p>
    <?php endif; ?>

</div>

</body>
</html>