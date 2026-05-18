<?php
include '../includes/auth.php';
include '../includes/db.php';

//delete action
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $q = "UPDATE comments SET is_deleted=1 WHERE id='$id'";
    mysqli_query($conn, $q);

    $q2 = "UPDATE comment_reports SET status='resolved' WHERE comment_id='$id'";
    mysqli_query($conn, $q2);

    header('Location: comments.php');
}

//dismiss action
if(isset($_GET['dismiss'])){
    $id = $_GET['dismiss'];
    $q = "UPDATE comment_reports SET status='dismissed' WHERE comment_id='$id'";
    mysqli_query($conn, $q);

    header('Location: comments.php');
}

//reported comments load with article title
$sql = "SELECT comment_reports.*, comments.body as comment_body, 
        users.name as reporter_name, articles.title as article_title
        FROM comment_reports
        JOIN comments ON comment_reports.comment_id = comments.id
        JOIN users ON comment_reports.reporter_id = users.id
        JOIN articles ON comments.article_id = articles.id
        WHERE comment_reports.status='pending'
        ORDER BY comment_reports.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Comment Moderation</title>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
}
table{
width: 100%;
border-collapse: collapse;
background-color: white;
}
th, td{
padding: 10px;
border: 1px solid #ddd;
text-align: left;
}
th{
background-color: #2c3e50;
color: white;
}
.btn-delete{
background-color: red;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
.btn-dismiss{
background-color: gray;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
a.back{
display: inline-block;
margin-bottom: 15px;
color: #2c3e50;
text-decoration: none;
}
</style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<br>
<a class="back" href="index.php">← Back to Dashboard</a>
<h1>Comment Moderation Panel</h1>

<table>
    <tr>
        <th>Article</th>
        <th>Comment</th>
        <th>Reported By</th>
        <th>Reason</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['article_title']; ?></td>
        <td><?php echo $row['comment_body']; ?></td>
        <td><?php echo $row['reporter_name']; ?></td>
        <td><?php echo $row['reason']; ?></td>
        <td>
            <a class="btn-delete" href="comments.php?delete=<?php echo $row['comment_id']; ?>">Delete</a>
            &nbsp;
            <a class="btn-dismiss" href="comments.php?dismiss=<?php echo $row['comment_id']; ?>">Dismiss</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>