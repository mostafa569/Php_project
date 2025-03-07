<?php

if(isset($_POST)){
    require("../connection2.php");
    $connection = new db();
     
    $img= $connection->getImgProduct($_POST['id']);
    $imgdata = $img->fetch(pdo::FETCH_ASSOC);
   
    $old_image = $imgdata['image_url'];

    if (!empty($_FILES['image_url']['name'])) {
        $target_dir = "../assets/images/";
        $target_file = $target_dir . basename($_FILES["image_url"]["name"]);

        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
            $new_image = $_FILES["image_url"]["name"]; 
        } else {
            echo "Error uploading file.";
            exit; 
        }
    } else {
        $new_image = $old_image; 
    }

    $connection->UpdateProduct($_POST['name'],$_POST['description'],$_POST['price'],$_POST['category_id'],$new_image,$_POST['id']);
    header("Location:showproduct.php");
}