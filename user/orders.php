<?php
include '../connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
}

$user_id = $_SESSION['user_id'];

class Orders {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function fetchOrders($user_id, $startDate = null, $endDate = null) {
        $whereCon = "WHERE orders.user_id = :user_id";
        $params = ['user_id' => $user_id];

        if ($startDate && $endDate) {
            $startDate = date('Y-m-d 00:00:00', strtotime($startDate));
            $endDate = date('Y-m-d 23:59:59', strtotime($endDate));
            $whereCon .= " AND orders.created_at BETWEEN :startDate AND :endDate";
            $params['startDate'] = $startDate;
            $params['endDate'] = $endDate;
        }

        $query = "SELECT orders.id, orders.created_at, rooms.name AS room_name, 
                         products.name AS product_name, orders.total_price, orders.status, orders.quantity
                  FROM orders
                  JOIN rooms ON orders.room_id = rooms.id
                  JOIN products ON orders.product_id = products.id
                  $whereCon
                  ORDER BY orders.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new Database();
$conn = $db->getConnection();
$orderObj = new Orders($conn);

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$orders = $orderObj->fetchOrders($user_id, $startDate, $endDate);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/styles.css">
    <link rel="stylesheet" href="../styles/order.css">
</head>

<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand">Cafeteria</a>
            <div class="d-flex align-items-center gap-3">
                <a href="user.php" class="btn btn-outline-light" id="order-link">Home</a>
                <div class="d-flex align-items-center gap-2">
                    <img id="user-image" src="../assets/default-user.png" class="rounded-circle" width="40" height="40">
                    <span id="user-name" class="text-light">User Name</span>
                </div>
                <button id="logout-btn" class="btn btn-outline-light">Logout</button>
            </div>
        </div>
    </nav>
    <br><br>
    <h2 style="color:white">My Orders</h2>
    <form method="GET" action="">
        <label>Date From</label>
        <input class="date" type="date" name="start_date" value="<?php echo $startDate; ?>" required>
        <label>Date To</label>
        <input class="date" type="date" name="end_date" value="<?php echo $endDate; ?>" required>
        <button type="submit">Search</button>
        <a href="?" class="btn btn-secondary">Reset</a>
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>Order Id</th>
                <th>Order Date</th>
                <th>Room</th>
                <th>Product</th>
                <th>Total</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalAmount = 0;
            $totalPrice = 0; 
            foreach ($orders as $order) {
                $totalAmount += $order['quantity'];  
                $totalPrice += $order['total_price'];  
            }
            foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td><?php echo $order['created_at']; ?></td>
                <td><?php echo $order['room_name']; ?></td>
                <td><?php echo $order['product_name']; ?></td>
                <td><?php echo $order['total_price']; ?> EGP</td>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td>
                    <?php if ($order['status'] === 'Processing'): ?>
                    <form id="cancel-form-<?php echo $order['id']; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <button type="button" onclick="cancelOrder(<?php echo $order['id']; ?>)">Cancel</button>
                    </form>
                    <?php else: ?>
                    <button disabled>Cancel</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p style="color:white"><strong>Total Price:</strong> <?php echo $totalPrice; ?> EGP</p>
    <p style="color:white"><strong>Total Quantity:</strong> <?php echo $totalAmount; ?> items</p>
    <script>
    function cancelOrder(orderId) {
        if (confirm("Are you sure you want to cancel this order?")) {
            let formData = new FormData();
            formData.append("order_id", orderId);
            fetch("orderCanceld.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Order cancelled successfully!");
                        location.reload();
                    } else {
                        alert("Failed to cancel the order.");
                    }
                })
                .catch(error => console.error("Error:", error));
        }
    }
    </script>
    <script src="../scripts/script.js"></script>
</body>

</html>