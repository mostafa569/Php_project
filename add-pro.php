<?php
if(isset($_POST)){
    require("connection.php");
    $connection = new db();
    // echo "<pre>";
    // var_dump($_FILES);
    // echo "</pre>";
    $img= $_FILES['image_url']['name'];
    // echo $_POST['category_id'];
    $connection->insertProduct($_POST['name'],$_POST['description'],$_POST['price'],$_POST['category_id'],$img);
    move_uploaded_file($_FILES['image_url']['tmp_name'],'assets/images/'.$_FILES['image_url']['name']);
    header("Location:addproduct.php");
}

?>