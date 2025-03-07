<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];

$latestOrdersQuery = "SELECT o.*, p.name AS product_name 
                      FROM orders o
                      JOIN products p ON o.product_id = p.id
                      WHERE o.user_id = :user_id
                      ORDER BY o.created_at DESC
                      LIMIT 6";
$stmt = $conn->prepare($latestOrdersQuery);
$stmt->execute(['user_id' => $user_id]);
$latestOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$productsQuery = "SELECT * FROM products";
$products = $conn->query($productsQuery)->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css">
</head>

<body>


    <?php include '../common/navbar.php'?>
    <div class="container mt-4">

        <h3 style="color:white">Latest Orders</h3>
        <div class="row" id="latest-orders">
            <?php foreach ($latestOrders as $order) { ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($order['product_name']); ?></h5>
                        <p class="card-text">
                            <strong>Quantity:</strong> <?php echo $order['quantity']; ?><br>
                            <strong>Total Price:</strong> <?php echo $order['total_price']; ?> EGP<br>
                            <strong>Order Status:</strong> <?php echo $order['status']; ?><br>
                            <strong>Order Date:</strong>
                            <?php echo date('M j, Y H:i', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <hr>

        <h3 style="color:white">Products</h3>
        <div class="row" id="products-list">
            <?php foreach ($products as $product) { ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image_url']); ?>"
                        class="card-img-top">
                    <div class="card-body text-center">
                        <h5><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p><?php echo $product['price']; ?> EGP</p>
                        <button class="btn btn-success add-to-cart" data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                            data-price="<?php echo $product['price']; ?>">
                            Add
                        </button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <div id="cart-sidebar">
        <button id="close-cart">✖</button>
        <h4>Your Cart</h4>
        <ul id="cart-items"></ul>
        <h5 id="total-price">Total: 0 EGP</h5>
        <button class="btn btn-warning" id="clear-cart">Clear Cart</button>
        <button class="btn btn-primary" id="pay-btn">Pay</button>
    </div>

    <div id="payment-alert" class="alert alert-success d-none text-center">Payment Successful!</div>
    <div id="room-selection-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h4>Select Your Room</h4>
            <select id="room-select"></select>
            <button id="confirm-room" class="btn btn-primary mt-3">Confirm</button>
        </div>
    </div>

    <script src="../scripts/script.js"></script>
</body>

</html>