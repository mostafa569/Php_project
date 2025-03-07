<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "cafeteria";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);

    $updateSQL = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    
    if ($conn->query($updateSQL) === TRUE) {
        header("Location: admin_orders.php"); \]
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
