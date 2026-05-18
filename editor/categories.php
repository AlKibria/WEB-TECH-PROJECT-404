<?php
include '../includes/auth.php';
include '../includes/db.php';

//category add action
if(isset($_POST['add'])){
    $name = $_POST['name'];

    //database e add korbo
  //already ache kina check korbo
$check = "SELECT * FROM categories WHERE name='$name'";
$cr = mysqli_query($conn, $check);

if(mysqli_num_rows($cr) > 0){
    $error = "Category already exists!";
} else {
    $sql = "INSERT INTO categories (name) VALUES ('$name')";
    mysqli_query($conn, $sql);
    header('Location: categories.php');
}

    header('Location: categories.php');
}

//category delete action
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    //database theke delete korbo
    $sql = "DELETE FROM categories WHERE id='$id'";
    mysqli_query($conn, $sql);

    header('Location: categories.php');
}

//shob category load korbo
$sql = "SELECT * FROM categories ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Category Management</title>
<style>
body{
font-family: arial;
background-color: #f0f2f5;
padding: 20px;
}
h1{
color: #2c3e50;
}
.box{
background-color: white;
padding: 20px;
border-radius: 10px;
border: 1px solid #ddd;
margin-bottom: 20px;
width: 400px;
}
input[type=text]{
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
<h1>Category Management</h1>

<!-- category add form -->
<div class="box">
    <?php if(isset($error)){ ?>
    <p style="color:red"><?php echo $error; ?></p>
<?php } ?>
    <form method="POST">
        Category Name: <br>
        <input type="text" name="name" placeholder="Enter category name">
        <input type="submit" name="add" value="Add Category">
    </form>
</div>

<!-- shob category dekhabo -->
<table>
    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
            <a class="btn-delete" href="categories.php?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>