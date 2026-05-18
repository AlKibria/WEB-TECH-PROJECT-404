<?php
include '../includes/db.php';

//search box theke keyword nibo
$keyword = $_GET['q'];

//database theke article khujbo title diye
$sql = "SELECT articles.title, articles.status, users.name 
        FROM articles 
        JOIN users ON articles.author_id = users.id
        WHERE articles.title LIKE '%$keyword%'";

$result = mysqli_query($conn, $sql);

//koto article paisi count korbo
$total = mysqli_num_rows($result);

//result thakle print korbo
if($total > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<tr>";
        echo "<td>".$row['title']."</td>";
        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['status']."</td>";
        echo "</tr>";
    }
} else {
    //kono result na paile ei message dekhabo
    echo "<tr>";
    echo "<td colspan='3'>No articles found for: ".$keyword."</td>";
    echo "</tr>";
}
?>