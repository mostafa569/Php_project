<?php
    require("connection.php");
    $connection = new db();
    $data=$connection->getCategory();
    $categories = $data->fetchAll(PDO::FETCH_ASSOC);
    $dataPro = $connection->getOneProduct($_GET['id']);
    $product = $dataPro->fetch(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($product['name']);
    // echo "</pre>";
    // var_dump($categories[0]['id']);
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
    <h1 class="h1 text-center pt-5">Edit Product</h1>
    <div class="form container py-5 d-flex justify-content-center ">
        <form action="update.php" method="post" enctype="multipart/form-data" class="w-75">
            <input type="hidden" name="id" value="<?=$product['id']?>">
            <div class="mb-3">
                <label for="name" class="form-label">Product</label>
                <input type="text" class="form-control" name="name" value="<?=$product['name']?>"
                    placeholder="Prodact Name">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" value="<?=$product['description']?>"
                    placeholder="Prodact Name">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" name="price" value="<?=$product['price']?>"
                    placeholder="Prodact Name">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" aria-label="Default select example" name="category_id">
                    <option disabled>Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']); ?>"
                        <?= ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['name']); ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ($product['image_url']): ?>
            <div class="mb-2">
                <img src="assets/images/<?= $product['image_url']; ?>" alt="Product Image" width="150">
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="image_url" class="form-label">Product picture</label>
                <input class="form-control" type="file" name="image_url" value="<?=$product['image_url']?>">
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <!-- <button type="reset" class="btn btn-danger">Cancel</button> -->
        </form>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>