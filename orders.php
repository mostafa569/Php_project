<?php
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("must be login");
}

$user_id = $_SESSION['user_id']; 

$whereCon = "WHERE orders.user_id = :user_id"; 
$params = ['user_id' => $user_id];

if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $startDate = date('Y-m-d 00:00:00', strtotime($_GET['start_date']));
    $endDate = date('Y-m-d 23:59:59', strtotime($_GET['end_date']));

    $whereCon .= " AND orders.created_at BETWEEN :startDate AND :endDate";
    $params['startDate'] = $startDate;
    $params['endDate'] = $endDate;
}


$querys = "SELECT orders.id, orders.created_at, rooms.name AS room_name, 
                  products.name AS product_name, orders.total_price, orders.status, orders.quantity
           FROM orders
           JOIN rooms ON orders.room_id = rooms.id
           JOIN products ON orders.product_id = products.id
           $whereCon
           ORDER BY orders.created_at DESC";


// all data 
// $querys = "SELECT orders.id, orders.created_at, users.username, rooms.name AS room_name, 
//                   products.name AS product_name, orders.total_price, orders.status, orders.quantity
//            FROM orders
//            JOIN users ON orders.user_id = users.id
//            JOIN rooms ON orders.room_id = rooms.id
//            JOIN products ON orders.product_id = products.id
//            $whereCon
//            ORDER BY orders.created_at DESC";

try {
    $stmt = $connection->prepare($querys);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// try {
    
//  $result=$connection->query($querys);
// $orders=$result->fetchAll(PDO::FETCH_ASSOC);

// } catch (PDOException $e) {
 
//     die("Database connection failed: " . $e->getMessage());
// }

// var_dump($orders);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
 <h2>My Orders</h2>
 <!-- Form date  -->
 <form method="GET" action="">
        <label>Date Form</label>
       <input  class="date" type="date" name="start_date" required value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
        <label>Date To</label>
        <input class="date" type="date" name="end_date" required value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
        <button type="submit">Search</button>
    </form>
<!-- Table  -->
<table border="1">
        <thead>
        <tr>
            <th>Order Id</th>
            <th>Order Date</th>
            <th>Room</th>
            <th>Product</th>
            <th>Total</th>
            <th>Status</th>
            <th>quantity</th>
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
        foreach ($orders as $order):  ?>
        
                <tr>
                <td><?php echo $order['id'];?></td>
                    <td><?php echo $order['created_at'];?></td>
                    <td><?php echo $order['room_name'];?></td>
                    <td><?php echo $order['product_name'];?></td>
                    <td><?php echo $order['total_price'];?></td>
                    <td><?php echo $order['status'];?></td>
                    <td><?php echo $order['quantity'];?></td>
                    <td>
                 <?php 
                        if ($order['status'] === 'Processing'): 
                        
                ?>
            <form id="cancel-form-<?php echo $order['id']; ?>">
    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
    <button type="button" onclick="cancelOrder(<?php echo $order['id']; ?>)">Cancel</button>
</form>

                        <?php else: ?>
                            <button disabled>Cancel</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php 
            endforeach; 
            ?>
        </tbody>
        </table>

    <p><strong>Total Price:</strong> <?php echo $totalPrice; ?> EGP</p>
<p><strong>Total Quantity:</strong> <?php echo $totalAmount; ?> items</p>


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
            console.log(data); 
            if (data.success) {
                alert("Order cancelled successfully!");
                document.getElementById("cancel-form-" + orderId).remove();
                location.reload();
            } else {
                alert("Failed to cancel the order.");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}


</script>

</body>
</html>