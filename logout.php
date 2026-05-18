<?php
session_start();

//session destroy korbo
session_destroy();

//login page e pathabo
header('Location: login.php');
?>