<?php
// Include the database connection file
include '../connection.php';

// Start the session
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch orders for the logged-in user
$user_id = $_SESSION['user_id'];

// Initialize date filter variables
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

$sql = "SELECT orders.*, products.name AS product_name 
        FROM orders 
        JOIN products ON orders.product_id = products.id 
        WHERE orders.user_id = :user_id";

$params = ['user_id' => $user_id];

if ($start_date && $end_date) {
    $sql .= " AND orders.created_at BETWEEN :start_date AND :end_date";
    $params['start_date'] = $start_date;
    $params['end_date'] = $end_date;
}

$sql .= " ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .container {
        margin-top: 50px;
    }

    h2 {
        color: #343a40;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .no-orders {
        color: #6c757d;
        font-style: italic;
    }

    .filter-form {
        margin-bottom: 20px;
    }

    .filter-form input[type="date"] {
        margin-right: 10px;
        padding: 5px;
    }

    .filter-form button {
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .filter-form button:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>My Orders</h2>

        <!-- Date Filter Form -->
        <form method="GET" class="filter-form" id="filterForm">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">

            <button type="submit">Filter</button>
            <button type="button" id="clearFilters" class="btn btn-secondary btn-sm">Clear Filters</button>
        </form>

        <?php if (count($orders) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td> <!-- Product Name -->
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($order['total_price']); ?> EGP</td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <?php if ($order['status'] == 'Processing'): ?>
                        <a href="cancel_order.php?order_id=<?php echo $order['id']; ?>"
                            class="btn btn-danger btn-sm">Cancel</a>
                        <?php else: ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php else: ?>
        <p class="no-orders">No orders found.</p>
        <?php endif; ?>
    </div>
    <script>
    document.getElementById('clearFilters').addEventListener('click', function() {
        // Clear the input fields
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';

        // Optionally, submit the form to apply the cleared filters
        document.getElementById('filterForm').submit();
    });
    </script>
</body>

</html>