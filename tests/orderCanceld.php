<?php
include 'connection.php';
session_start();

header('Content-Type: application/json');

// check userid
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Must be login']);
    exit;
}
//check request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $user_id = $_SESSION['user_id'];

    try {
    
        $stmt = $connection->prepare("SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id AND status = 'Processing'");
        $stmt->execute(['order_id' => $order_id, 'user_id' => $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the order is not found or not status Processing
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order Not Found']);
            exit;
        }
             
        // Delete the order from the database
        $stmt = $connection->prepare("DELETE FROM orders WHERE id = :order_id");
        $stmt->execute(['order_id' => $order_id]);

        echo json_encode(['success' => true, 'message' => 'Order Canceled']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'eroor can not canceled']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'order not founded']);
}
?>
