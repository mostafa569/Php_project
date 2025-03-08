<?php
    require("connection.php");
    $connection = new db();
    $data=$connection->getCategory();
    $categories = $data->fetchAll(PDO::FETCH_ASSOC);
    // echo "<pre>";
    // var_dump($categories);
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
    <h1 class="h1 text-center pt-5">Add Product</h1>

    <div class="form container py-5 d-flex justify-content-center ">
        <form action="add-pro.php" method="post" enctype="multipart/form-data" class="w-75">
            <div class="mb-3">
                <label for="name" class="form-label">Product</label>
                <input type="text" class="form-control" name="name" placeholder="Prodact Name">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" name="description" placeholder="Prodact Name">
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" name="price" placeholder="Prodact Name">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" aria-label="Default select example" name="category_id">
                    <option selected disabled>Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']); ?>">
                        <?= htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    Add Category
                </button>
            </div>


            <div class="mb-3">
                <label for="image_url" class="form-label">Product picture</label>
                <input class="form-control" type="file" name="image_url">
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </form>
    </div>



    <!-- modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="addcategore.php" method="post" id="modalForm">
                        <div class="mb-3">
                            <label for="category-name" class="col-form-label">Category Name</label>
                            <input type="text" class="form-control" name="category_name" id="category-name">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button> <!-- زر الإرسال -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>