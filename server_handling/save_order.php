<?php
include '../connection.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["cart"]) || empty($data["cart"])) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

if (!isset($data["room_id"]) || empty($data["room_id"])) {
    echo json_encode(["success" => false, "message" => "Room ID is required"]);
    exit;
}

$room_id = $data["room_id"];

try {
    $conn->beginTransaction();
    
    foreach ($data["cart"] as $product_id => $item) {
        $quantity = $item["quantity"];
        $total_price = $item["price"] * $quantity;

        $stmt = $conn->prepare("INSERT INTO orders (user_id, room_id, product_id, quantity, total_price, created_at) 
                                VALUES (:user_id, :room_id, :product_id, :quantity, :total_price, NOW())");
        $stmt->execute([
            "user_id" => $user_id,
            "room_id" => $room_id,
            "product_id" => $product_id,
            "quantity" => $quantity,
            "total_price" => $total_price
        ]);
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Order saved successfully"]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["success" => false, "message" => "Error saving order: " . $e->getMessage()]);
}
?>