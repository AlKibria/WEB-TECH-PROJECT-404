<?php
include '../includes/auth.php';
include '../includes/db.php';

//schedule action
if(isset($_POST['schedule'])){
    $article_id = $_POST['article_id'];
    $date = $_POST['date'];

    //editorial calendar e add korbo
    $sql = "INSERT INTO editorial_calendar (article_id, scheduled_date, created_by) 
            VALUES ('$article_id', '$date', '".$_SESSION['user_id']."')";
    mysqli_query($conn, $sql);

    //article scheduled at update korbo
    $sql2 = "UPDATE articles SET scheduled_at='$date' WHERE id='$article_id'";
    mysqli_query($conn, $sql2);

    header('Location: calendar.php');
}

//delete schedule action
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "DELETE FROM editorial_calendar WHERE id='$id'";
    mysqli_query($conn, $sql);
    header('Location: calendar.php');
}

//shob approved article load korbo
$sql = "SELECT * FROM articles WHERE status='approved' OR status='published'";
$articles = mysqli_query($conn, $sql);

//shob scheduled article load korbo
$sql2 = "SELECT editorial_calendar.*, articles.title 
         FROM editorial_calendar 
         JOIN articles ON editorial_calendar.article_id = articles.id
         ORDER BY scheduled_date ASC";
$calendar = mysqli_query($conn, $sql2);
?>

<!DOCTYPE html>
<html>
<head>
<title>Editorial Calendar</title>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
margin-bottom: 20px;
}
.box{
background-color: white;
padding: 20px;
border-radius: 8px;
border: 1px solid #ddd;
margin-bottom: 20px;
width: 450px;
}
select, input[type=datetime-local]{
width: 100%;
padding: 8px;
margin: 8px 0 15px;
border: 1px solid #ddd;
border-radius: 4px;
}
input[type=submit]{
background-color: #2c3e50;
color: white;
border: none;
cursor: pointer;
padding: 8px 15px;
border-radius: 4px;
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
<h1>Editorial Calendar</h1>

<!-- schedule form -->
<div class="box">
    <h3>Schedule an Article</h3>
    <form method="POST">
        Select Article: <br>
        <select name="article_id">
            <?php while($a = mysqli_fetch_assoc($articles)){ ?>
            <option value="<?php echo $a['id']; ?>"><?php echo $a['title']; ?></option>
            <?php } ?>
        </select>

        Publish Date: <br>
        <input type="datetime-local" name="date">

        <input type="submit" name="schedule" value="Schedule">
    </form>
</div>

<!-- scheduled articles -->
<h2>Scheduled Articles</h2>
<table>
    <tr>
        <th>Title</th>
        <th>Scheduled Date</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($calendar)){ ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['scheduled_date']; ?></td>
        <td>
            <a class="btn-delete" href="calendar.php?delete=<?php echo $row['id']; ?>">Remove</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>