<?php


session_start();

// Guard: only allow logged-in admins
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once 'config/db.php';


$stats = [];

$result = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
while ($row = mysqli_fetch_assoc($result)) {
    $stats['users'][$row['role']] = $row['count'];
}

// Total published articles
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM articles WHERE status = 'published'");
$stats['published_articles'] = mysqli_fetch_assoc($result)['count'];

// Total comments
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM comments");
$stats['total_comments'] = mysqli_fetch_assoc($result)['count'];

// New registrations this week
$result = mysqli_query($conn, "
    SELECT COUNT(*) as count FROM users
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$stats['new_this_week'] = mysqli_fetch_assoc($result)['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #111827;
            color: white;
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        
        .topbar {
            background: #1f2937;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #00ffcc;
        }
        .topbar h1 { color: #00ffcc; font-size: 1.4rem; }
        .topbar span { font-size: 0.9rem; color: #9ca3af; }
        .topbar a {
            text-decoration: none;
            background: #00ffcc;
            color: black;
            padding: 8px 18px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.85rem;
            transition: opacity 0.2s;
        }
        .topbar a:hover { opacity: 0.85; }

        .main { padding: 32px; }

        .welcome {
            margin-bottom: 28px;
            color: #9ca3af;
            font-size: 0.95rem;
        }
        .welcome strong { color: #00ffcc; }

        
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 36px;
        }
        .card {
            background: #1f2937;
            border-radius: 10px;
            padding: 22px 20px;
            border: 1px solid #374151;
        }
        .card .label {
            font-size: 0.78rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }
        .card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #00ffcc;
        }
        .card .sub {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 4px;
        }

       
        .nav-section h2 {
            font-size: 1rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
        }
        .nav-card {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 8px;
            padding: 16px 20px;
            text-decoration: none;
            color: white;
            transition: border-color 0.2s, background 0.2s;
        }
        .nav-card:hover {
            border-color: #00ffcc;
            background: #263244;
        }
        .nav-card .nav-title { font-weight: bold; margin-bottom: 4px; }
        .nav-card .nav-desc { font-size: 0.8rem; color: #9ca3af; }
    </style>
</head>
<body>

<div class="topbar">
    <h1>⚙ ADMIN PANEL</h1>
    <span>Logged in as <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></span>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <p class="welcome">
        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>. 
        Here is today's platform overview.
    </p>

    <!-- Stats -->
    <div class="cards">
        <div class="card">
            <div class="label">Total Users</div>
            <div class="value">
                <?php echo array_sum($stats['users'] ?? []); ?>
            </div>
            <div class="sub">All roles combined</div>
        </div>
        <div class="card">
            <div class="label">Readers</div>
            <div class="value"><?php echo $stats['users']['reader'] ?? 0; ?></div>
        </div>
        <div class="card">
            <div class="label">Authors</div>
            <div class="value"><?php echo $stats['users']['author'] ?? 0; ?></div>
        </div>
        <div class="card">
            <div class="label">Editors</div>
            <div class="value"><?php echo $stats['users']['editor'] ?? 0; ?></div>
        </div>
        <div class="card">
            <div class="label">Published Articles</div>
            <div class="value"><?php echo $stats['published_articles']; ?></div>
        </div>
        <div class="card">
            <div class="label">Total Comments</div>
            <div class="value"><?php echo $stats['total_comments']; ?></div>
        </div>
        <div class="card">
            <div class="label">New This Week</div>
            <div class="value"><?php echo $stats['new_this_week']; ?></div>
            <div class="sub">Registrations</div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="nav-section">
        <h2>Manage</h2>
        <div class="nav-grid">
            <a class="nav-card" href="admin_users.php">
                <div class="nav-title">👥 User Management</div>
                <div class="nav-desc">Search, activate, deactivate, promote users</div>
            </a>
            <a class="nav-card" href="admin_articles.php">
                <div class="nav-title">📄 All Articles</div>
                <div class="nav-desc">View, filter, delete articles across all statuses</div>
            </a>
            <a class="nav-card" href="admin_comments.php">
                <div class="nav-title">💬 Comments & Reports</div>
                <div class="nav-desc">Moderate comments and resolve reports</div>
            </a>
            <a class="nav-card" href="admin_applications.php">
                <div class="nav-title">✍ Author Applications</div>
                <div class="nav-desc">Approve or reject pending applications</div>
            </a>
            <a class="nav-card" href="admin_analytics.php">
                <div class="nav-title">📊 Analytics</div>
                <div class="nav-desc">Platform-wide stats and trends</div>
            </a>
            <a class="nav-card" href="admin_settings.php">
                <div class="nav-title">⚙ Platform Settings</div>
                <div class="nav-desc">Registration, comments, visibility settings</div>
            </a>
        </div>
    </div>

</div>

</body>
</html>