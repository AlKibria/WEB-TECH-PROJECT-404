<?php
include '../includes/auth.php';
include '../includes/db.php';

//publish action
if(isset($_GET['publish'])){
    $id = $_GET['publish'];

    //article publish korbo
    $sql = "UPDATE articles SET status='published' WHERE id='$id'";
    mysqli_query($conn, $sql);

    header('Location: publish.php');
}

//unpublish action
if(isset($_GET['unpublish'])){
    $id = $_GET['unpublish'];

    //article unpublish korbo
    $sql = "UPDATE articles SET status='unpublished' WHERE id='$id'";
    mysqli_query($conn, $sql);

    header('Location: publish.php');
}

//shob approved article load korbo
$sql = "SELECT articles.*, users.name as author_name 
        FROM articles 
        JOIN users ON articles.author_id = users.id
        WHERE articles.status='approved' OR articles.status='published' OR articles.status='unpublished'
        ORDER BY articles.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Publish Articles</title>
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
.btn-publish{
background-color: green;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
.btn-unpublish{
background-color: red;
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

<a class="back" href="index.php">← Back to Dashboard</a>
<h1>Publish Articles</h1>

<table>
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['author_name']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php if($row['status'] == 'approved' || $row['status'] == 'unpublished'){ ?>
                <a class="btn-publish" href="publish.php?publish=<?php echo $row['id']; ?>">Publish</a>
            <?php } ?>

            <?php if($row['status'] == 'published'){ ?>
                <a class="btn-unpublish" href="publish.php?unpublish=<?php echo $row['id']; ?>">Unpublish</a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>