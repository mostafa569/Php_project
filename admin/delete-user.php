<?php
header("Content-Type: application/json");

require_once '../connection.php';

$db = new Database();
$pdo = $db->getConnection();

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(["success" => false, "message" => "User ID is missing."]);
    exit();
}

$id = $data['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "User deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}