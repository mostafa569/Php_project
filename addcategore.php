<?php
    if(isset($_POST)){
        require("connection.php");
        $connection = new db();
        $connection->addCategory($_POST['category_name']);
        header("Location:addproduct.php");
    }
?>