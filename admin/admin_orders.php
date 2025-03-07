<?php

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit;
}
require '../connection.php';
$db = new Database();
$pdo = $db->getConnection();

$startDateFilter = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDateFilter = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$userFilter = isset($_GET['user']) ? $_GET['user'] : '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    
    $updateSQL = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $pdo->prepare($updateSQL);
    $stmt->execute(['status' => $status, 'order_id' => $order_id]);
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?start_date=$startDateFilter&end_date=$endDateFilter&user=$userFilter");
    exit();
}

$sql = "SELECT orders.id, users.username, rooms.name AS room, products.name AS product, 
        orders.quantity, orders.total_price, orders.status, orders.created_at 
        FROM orders 
        JOIN users ON orders.user_id = users.id
        JOIN rooms ON orders.room_id = rooms.id
        JOIN products ON orders.product_id = products.id
        WHERE 1";

if (!empty($startDateFilter) && !empty($endDateFilter)) {
    $sql .= " AND DATE(orders.created_at) BETWEEN :start_date AND :end_date";
}

if (!empty($userFilter)) {
    $sql .= " AND orders.user_id = :user_id";
}

$sql .= " ORDER BY orders.created_at DESC";

$stmt = $pdo->prepare($sql);

if (!empty($startDateFilter) && !empty($endDateFilter)) {
    $stmt->bindValue(':start_date', $startDateFilter);
    $stmt->bindValue(':end_date', $endDateFilter);
}

if (!empty($userFilter)) {
    $stmt->bindValue(':user_id', $userFilter);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <!-- <link rel="stylesheet" type="text/css" href="style.css"> -->
</head>

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
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        background-color: #343a40;
        padding: 20px;
        color: #fff;
        z-index: 1000;
    }

    .admin-section {
        display: flex;
        align-items: center;
        margin-top: 20px;
    }

    .admin-section img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .admin-section span {
        color: #fff;
        font-size: 16px;
    }

    .main-content {
        margin-left: 250px;
        padding: 20px;
        position: relative;
        z-index: 1;

    }



    .sidebar .brand {
        font-size: 24px;
        margin-bottom: 20px;
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




    h2 {
        text-align: center;
        color: white;
    }

    form {
        text-align: center;
        margin-bottom: 20px;

    }

    table {
        width: 75%;
        border-collapse: collapse;
        margin-left: 310px;
        /* margin: 0 auto; */
        background-color: #ffffff;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    th {
        background-color: rgb(49, 68, 97);
        color: white;
        padding: 15px;
        text-align: left;
    }

    td {
        padding: 15px;
        text-align: left;
    }


    tr:nth-child(even) {
        background-color: #f2f2f2;


    }

    tr:nth-child(odd) {

        background-color: rgb(248, 248, 248);

    }


    th,
    td,
    tr {
        border: none;
    }


    tr:hover {
        background-color: #c2d8ff;
        transition: 0.3s;
    }

    button {
        background-color: rgb(22, 90, 192);
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
    }

    input {
        height: 30px
    }

    select {
        padding: 5px;
        border-radius: 5px;
    }

    label {
        color: rgb(241, 241, 241);
        font-size: large;
        font-weight: 800;
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



        <button id="logout-btn" class="btn">Logout</button>
    </div>
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
            $users = $pdo->query("SELECT id, username FROM users where role='user'");
            while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
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

        <?php foreach ($result as $row): ?>
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
                        <option value="Processing" <?= $row['status'] == 'Processing' ? 'selected' : '' ?>>Processing
                        </option>
                        <option value="Out for Delivery" <?= $row['status'] == 'Out for Delivery' ? 'selected' : '' ?>>
                            Out for Delivery</option>
                        <option value="Done" <?= $row['status'] == 'Done' ? 'selected' : '' ?>>Done</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
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