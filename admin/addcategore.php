<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit;
}

require("../connection2.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];

    $connection = new db();
    $connection->addCategory($category_name);

    
    header("Location: addproduct.php");
    exit;
}
?>