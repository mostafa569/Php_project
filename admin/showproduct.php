<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit;
}

require("../connection2.php");
$connection = new db();
$data = $connection->getProducts();
$products = $data->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<body>
    <style>
    body {
        position: relative;
        background: url("../assets/images/background.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;

    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);

        z-index: 1;
    }

    body>* {
        position: relative;
        z-index: 2;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background: #343a40;
        color: white;
        padding: 20px;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .admin-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .admin-section img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        margin-bottom: 10px;
    }

    .nav-links ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .nav-links li {
        margin: 10px 0;
    }

    .nav-links a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: white;
        background: #495057;
        border-radius: 5px;
        text-align: center;
        transition: 0.3s;
    }

    .nav-links a:hover {
        background: #17a2b8;
    }


    .table {
        width: 90%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead {
        background: #343a40;
        color: white;
        text-align: center;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    .table img {
        border-radius: 5px;
        object-fit: cover;
    }

    #logout-btn {
        width: 100%;
        margin-top: 10px;
        background: #dc3545;
        color: white;
        border: none;
    }

    #logout-btn:hover {
        background: #c82333;
    }
    </style>

    <div class="sidebar">
        <div class="admin-section">
            <?php if (isset($_SESSION['admin_photo']) && isset($_SESSION['admin_name'])): ?>
            <img src="../assets/images/<?php echo $_SESSION['admin_photo']; ?>" alt="Admin Photo">
            <span><?php echo $_SESSION['admin_name']; ?></span>
            <?php else: ?>
            <span>Admin</span>
            <?php endif; ?>
        </div>

        <nav class="nav-links">
            <ul>
                <li><a href="admin.php">Make Order</a></li>
                <li><a href="addproduct.php">Add Product</a></li>
                <li><a href="showproduct.php">Show Products</a></li>
                <li><a href="Allusers.php">Show Users</a></li>
                <li><a href="add-user.php">Add User</a></li>
                <li><a href="admin_orders.php">Show Orders</a></li>
            </ul>
        </nav>

        <!-- <div class="cart-section">
            <div class="cart-icon" id="cart-icon">
                🛒
                <span class="cart-counter" id="cart-counter">0</span>
            </div>
        </div> -->

        <button id="logout-btn" class="btn">Logout</button>
    </div>


    <div class="main-content">
        <h1 style="color:white" class="h1 text-center">All Products</h1>
        <div class="container d-flex justify-content-center my-5">
            <table class="table w-75">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Price</th>
                        <th scope="col">Category Name</th>
                        <th scope="col">Product Image</th>
                        <th scope="col">Edit Product</th>
                        <th scope="col">Delete Product</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td><?= htmlspecialchars($product['description']); ?></td>
                        <td><?= htmlspecialchars($product['price']); ?></td>
                        <td><?= htmlspecialchars($product['cateName']); ?></td>
                        <td><img src='../assets/images/<?= htmlspecialchars($product['image_url']); ?>' width='100'
                                height='100'></td>
                        <td><a href='editproduct.php?id=<?= htmlspecialchars($product['id']); ?>' type='button'
                                class='btn btn-secondary'>Edit</a></td>
                        <td><a href='deleteproduct.php?id=<?= htmlspecialchars($product['id']); ?>' type='button'
                                class='btn btn-danger'>Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
    document.getElementById('logout-btn').addEventListener('click', () => {
        fetch('logout.php', {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'login.php';
                } else {
                    showAlert('Logout failed. Please try again.', 'danger');
                }
            })
            .catch(error => console.error('Error logging out:', error));
    });
    </script>
</body>

</html>