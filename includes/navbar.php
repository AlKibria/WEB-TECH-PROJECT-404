<style>
.navbar{
background-color: #2c3e50;
padding: 10px 20px;
display: flex;
justify-content: space-between;
align-items: center;
}
.navbar a{
color: white;
text-decoration: none;
margin-right: 15px;
font-size: 14px;
}
.navbar a:hover{
color: #f39c12;
}
.navbar .brand{
color: white;
font-size: 18px;
font-weight: bold;
}
.navbar .right{
color: #f39c12;
font-size: 14px;
}
</style>

<div class="navbar">
    <div>
        <span class="brand">📝 Blog Platform</span>
        &nbsp;&nbsp;
        <a href="/blog_platform/editor/index.php">Dashboard</a>
        <a href="/blog_platform/editor/queue.php">Queue</a>
        <a href="/blog_platform/editor/publish.php">Publish</a>
        <a href="/blog_platform/editor/categories.php">Categories</a>
        <a href="/blog_platform/editor/calendar.php">Calendar</a>
        <a href="/blog_platform/editor/comments.php">Comments</a>
        <a href="/blog_platform/editor/profile.php">Profile</a>
    </div>
    <div class="right">
        👤 <?php echo $_SESSION['name']; ?> &nbsp;
        <a href="/blog_platform/logout.php" style="color:white">Logout</a>
    </div>
</div>