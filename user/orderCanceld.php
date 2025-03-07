<?php
include '../connection.php';
session_start();

header('Content-Type: application/json');
 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Must be logged in']);
    exit;
}

 
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

 
    $stmt = $conn->prepare("SELECT id FROM orders WHERE id = :order_id AND user_id = :user_id AND status = 'Processing'");
    $stmt->execute(['order_id' => $order_id, 'user_id' => $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found or cannot be canceled']);
        exit;
    }
        
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = :order_id");
    $stmt->execute(['order_id' => $order_id]);

    echo json_encode(['success' => true, 'message' => 'Order Canceled']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: Cannot cancel order. ' . $e->getMessage()]);
}
?>