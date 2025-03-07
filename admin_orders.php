<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "cafeteria";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$startDateFilter = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDateFilter = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $updateSQL = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    
    if ($conn->query($updateSQL) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?start_date=$startDateFilter&end_date=$endDateFilter&user=$userFilter");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$sql = "SELECT orders.id, users.username, rooms.name AS room, products.name AS product, 
        orders.quantity, orders.total_price, orders.status, orders.created_at 
        FROM orders 
        JOIN users ON orders.user_id = users.id
        JOIN rooms ON orders.room_id = rooms.id
        JOIN products ON orders.product_id = products.id
        WHERE 1";

if (!empty($startDateFilter) && !empty($endDateFilter)) {
    $sql .= " AND DATE(orders.created_at) BETWEEN '$startDateFilter' AND '$endDateFilter'";
}

if (!empty($userFilter)) {
    $sql .= " AND orders.user_id = '$userFilter'";
}

$sql .= " ORDER BY orders.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

<h2>Admin Orders</h2>

<form method="GET">
    <label for="start_date">From:</label>
    <input type="date" name="start_date" value="<?= htmlspecialchars($startDateFilter) ?>">

    <label for="end_date">To:</label>
    <input type="date" name="end_date" value="<?= htmlspecialchars($endDateFilter) ?>">

    <label for="user">Select User:</label>
    <select name="user">
        <option value="">All Users</option>
        <?php
        $users = $conn->query("SELECT id, username FROM users");
        while ($user = $users->fetch_assoc()) {
            $selected = ($userFilter == $user['id']) ? 'selected' : '';
            echo "<option value='{$user['id']}' $selected>{$user['username']}</option>";
        }
        ?>
    </select>

    <button type="submit">Filter</button>
</form>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Room</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Update Status</th>
    </tr>
    
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['room']) ?></td>
            <td><?= htmlspecialchars($row['product']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>$<?= number_format($row['total_price'], 2) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDateFilter) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDateFilter) ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($userFilter) ?>">
                    <select name="status">
                        <option value="Processing" <?= $row['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="Out for Delivery" <?= $row['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                        <option value="Done" <?= $row['status'] == 'Done' ? 'selected' : '' ?>>Done</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
