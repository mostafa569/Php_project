<?php

if(isset($_GET['id'])){
    require("../connection2.php");
    $connection = new db();
    $connection->deleteProduct($_GET['id']);
    header("Location:showproduct.php");
}