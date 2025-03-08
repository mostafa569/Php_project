<?php

if(isset($_POST)){
    require("connection.php");
    $connection = new db();
    //     echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";
    $img= $connection->getImgProduct($_POST['id']);
    $imgdata = $img->fetch(pdo::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($imgdata['image_url']);
    // echo "</pre>";
    // var_dump($imgdata);
    $old_image = $imgdata['image_url'];

    if (!empty($_FILES['image_url']['name'])) {
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES["image_url"]["name"]);

        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
            $new_image = $_FILES["image_url"]["name"]; // Use new image name
        } else {
            echo "Error uploading file.";
            exit; // Stop execution if upload fails
        }
    } else {
        $new_image = $old_image; // Keep the old image if no new file is uploaded
    }

    $connection->UpdateProduct($_POST['name'],$_POST['description'],$_POST['price'],$_POST['category_id'],$new_image,$_POST['id']);
    header("Location:showproduct.php");
}