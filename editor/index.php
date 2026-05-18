<?php
include '../includes/auth.php';
include '../includes/db.php';
?>

<!DOCTYPE html>
<html>
<head>
<title><h3>Welcome to Editor Dashboard</h3></title>
<p style="color:#888">Today: <?php echo date('D, d M Y'); ?></p>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
}
.card{
background-color: white;
padding: 20px;
width: 180px;
display: inline-block;
margin: 10px;
text-align: center;
border: 1px solid #ddd;
}
a{
display: inline-block;
margin: 5px;
padding: 8px 15px;
background-color: #2c3e50;
color: white;
text-decoration: none;
}
</style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<h1>Welcome <?php echo $_SESSION['name']; ?></h1>
<h3>Editor Dashboard</h3>

<?php
//count pending articles
$q1 = "SELECT COUNT(*) as total FROM articles WHERE status='submitted'";
$r1 = mysqli_query($conn, $q1);
$d1 = mysqli_fetch_assoc($r1);

//count published articles
$q2 = "SELECT COUNT(*) as total FROM articles WHERE status='published'";
$r2 = mysqli_query($conn, $q2);
$d2 = mysqli_fetch_assoc($r2);

//count reported comments
$q3 = "SELECT COUNT(*) as total FROM comment_reports WHERE status='pending'";
$r3 = mysqli_query($conn, $q3);
$d3 = mysqli_fetch_assoc($r3);
?>

<div class="card">
<h2><?php echo $d1['total']; ?></h2>
<p>Pending Review</p>
</div>

<div class="card">
<h2><?php echo $d2['total']; ?></h2>
<p>Published</p>
</div>

<div class="card">
<h2><?php echo $d3['total']; ?></h2>
<p>Reported Comments</p>
</div>

<br><br>
<a href="profile.php">My Profile</a>
<a href="categories.php">Categories</a>
<a href="publish.php">Publish Articles</a>
<a href="queue.php">Article Queue</a>
<a href="calendar.php">Calendar</a>
<a href="comments.php">Comments</a>
<a href="../logout.php">Logout</a>

</body>
</html>