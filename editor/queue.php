<?php
include '../includes/auth.php';
include '../includes/db.php';

//approve action
if(isset($_GET['approve'])){
    $id = $_GET['approve'];

    //article publish korbo
    $q = "UPDATE articles SET status='published' WHERE id='$id'";
    mysqli_query($conn, $q);

    //revision table e rakhi
    $eq = "INSERT INTO article_revisions (article_id, editor_id, status) 
           VALUES ('$id', '".$_SESSION['user_id']."', 'approved')";
    mysqli_query($conn, $eq);

    header('Location: queue.php');
}

//revision request
if(isset($_GET['revision'])){
    $id = $_GET['revision'];

    //article draft e pathabo
    $q = "UPDATE articles SET status='draft' WHERE id='$id'";
    mysqli_query($conn, $q);

    //revision table e rakhi
    $eq = "INSERT INTO article_revisions (article_id, editor_id, notes, status) 
           VALUES ('$id', '".$_SESSION['user_id']."', 'Please revise your article', 'revision_requested')";
    mysqli_query($conn, $eq);

    header('Location: queue.php');
}

//submitted articles load
$sql = "SELECT articles.*, users.name as author_name 
        FROM articles 
        JOIN users ON articles.author_id = users.id
        WHERE articles.status='submitted'
        ORDER BY articles.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Article Queue</title>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
}
input{
padding: 8px;
width: 300px;
border: 1px solid #ddd;
border-radius: 4px;
margin-bottom: 15px;
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
.btn-approve{
background-color: green;
color: white;
padding: 5px 10px;
text-decoration: none;
border-radius: 4px;
}
.btn-revision{
background-color: orange;
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
<h1>Article Queue</h1>

<!-- AJAX search box -->
<input type="text" id="searchbox" placeholder="Search articles..." onkeyup="searchArticle()">

<!-- search result akhane ashbe -->
<table id="searchtable" style="display:none">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Status</th>
    </tr>
    <tbody id="searchresult"></tbody>
</table>

<!-- main article table -->
<table id="maintable">
    <tr>
        <th>Title</th>
        <th>Author</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['author_name']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
            <a class="btn-approve" href="queue.php?approve=<?php echo $row['id']; ?>">Approve</a>
            &nbsp;
            <a class="btn-revision" href="queue.php?revision=<?php echo $row['id']; ?>">Revision</a>
        </td>
    </tr>
    <?php } ?>

</table>

<script>
function searchArticle(){

    //search box theke value nibo
    var keyword = document.getElementById('searchbox').value;

    //keyword thakle search table dekhabo
    if(keyword.length > 0){
        document.getElementById('searchtable').style.display = 'table';
        document.getElementById('maintable').style.display = 'none';
    } else {
        document.getElementById('searchtable').style.display = 'none';
        document.getElementById('maintable').style.display = 'table';
        return;
    }

    //XMLHttpRequest diye search.php te pathabo
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?q='+keyword, true);

    xhr.onload = function(){
        if(xhr.status == 200){
            //result table e bosabo
            document.getElementById('searchresult').innerHTML = xhr.responseText;
        }
    }

    xhr.send();
}
</script>

</body>
</html>