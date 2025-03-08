<?php
    require("connection.php");
    $connection = new db();
    $data = $connection->getProducts();
    $products = $data->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($products);
    // echo"</pre>";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <h1 class="h1 text-center">All Products</h1>
    <div class="container d-flex justify-content-center my-5">
        <table class="table w-75">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Product Name</th>
                    <th scope="col">Description</th>
                    <th scope="col">Price</th>
                    <th scope="col">Category Name</th>
                    <th scope="col">Prodcut Image</th>
                    <th scope="col">Edit Product</th>
                    <th scope="col">Delete Prodcut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($products as $product){
                        echo "<tr>";
                        echo "<td>".$product['name']."</td>";
                        echo "<td>".$product['description']."</td>";
                        echo "<td>".$product['price']."</td>";
                        echo "<td>".$product['cateName']."</td>";
                        echo "<td> <img src='assets/images/".$product['image_url']."' width ='100' height ='100'></td>";
                        echo "<td><a href='editproduct.php?id=".$product['id']."' type='button' class='btn btn-secondary'>Edit</a> </td>";
                        echo "<td><a href='deleteproduct.php?id=".$product['id']."' type='button' class='btn btn-danger'>Delete</a> </td>";
                        
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>