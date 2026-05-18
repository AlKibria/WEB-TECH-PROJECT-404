<?php
include '../includes/auth.php';
include '../includes/db.php';

//editor er info load korbo
$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

//update action
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];

    //database update korbo
    $uq = "UPDATE users SET name='$name', email='$email' WHERE id='$id'";
    mysqli_query($conn, $uq);

    //session update
    $_SESSION['name'] = $name;

    header('Location: profile.php');
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Editor Profile</title>
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
padding: 30px;
width: 400px;
border-radius: 10px;
border: 1px solid #ddd;
}
input{
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
padding: 10px;
}
a.back{
display: inline-block;
margin-bottom: 15px;
color: #2c3e50;
text-decoration: none;
}
.info{
color: #888;
font-size: 13px;
}
</style>
</head>
<body>

<a class="back" href="index.php">← Back to Dashboard</a>
<h1>My Profile</h1>

<div class="box">
    <p class="info">Role: <?php echo $user['role']; ?></p>
    <p class="info">Member since: <?php echo $user['created_at']; ?></p>
    <br>

    <form method="POST">
        Name: <br>
        <input type="text" name="name" value="<?php echo $user['name']; ?>">

        Email: <br>
        <input type="email" name="email" value="<?php echo $user['email']; ?>">

        <input type="submit" name="update" value="Update Profile">
    </form>
</div>

</body>
</html>