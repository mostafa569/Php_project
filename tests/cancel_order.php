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

// Get the order ID from the URL
$order_id = $_GET['order_id'];

// Check if the order is still processing
$sql = "SELECT status FROM orders WHERE id = :order_id AND user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['order_id' => $order_id, 'user_id' => $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if ($order && $order['status'] == 'Processing') {
    // Delete the order from the database
    $delete_sql = "DELETE FROM orders WHERE id = :order_id";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute(['order_id' => $order_id]);

    // Redirect back to the orders page with a success message
    
    header("Location: show_order.php");
    exit;
} else {
    // Redirect back to the orders page with an error message
    header("Location: show_order.php");
    exit;
}
?>