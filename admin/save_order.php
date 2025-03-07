<?php
include '../connection.php';
session_start();

header('Content-Type: application/json');
 
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["success" => false, "message" => "Admin not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
 
if (empty($data["cart"])) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

if (empty($data["room_id"])) {
    echo json_encode(["success" => false, "message" => "Room ID is required"]);
    exit;
}

$room_id = $data["room_id"];

try {
 
    $db = new Database();
    $conn = $db->getConnection();

 
    $conn->beginTransaction();

    $stmt = $conn->prepare("INSERT INTO orders (user_id, room_id, product_id, quantity, total_price, created_at) 
                            VALUES (:user_id, :room_id, :product_id, :quantity, :total_price, NOW())");

    foreach ($data["cart"] as $product_id => $item) {
        $stmt->execute([
            "user_id" => $user_id,
            "room_id" => $room_id,
            "product_id" => $product_id,
            "quantity" => $item["quantity"],
            "total_price" => $item["price"] * $item["quantity"]
        ]);
    }
 
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Order saved successfully"]);
} catch (Exception $e) {
 
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => "Error saving order: " . $e->getMessage()]);
}
?>