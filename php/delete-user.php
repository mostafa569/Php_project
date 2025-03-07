<?php
header("Content-Type: application/json");

$host = "localhost";
$dbname = "cafeteria";
$username = "root";
$password = "Sanaa@123";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}


file_put_contents("debug.log", "Request Method: " . $_SERVER["REQUEST_METHOD"] . PHP_EOL, FILE_APPEND);


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method. You sent: " . $_SERVER["REQUEST_METHOD"]]);
    exit();
}


$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);


file_put_contents("debug.log", "Raw Input: " . $rawInput . PHP_EOL, FILE_APPEND);

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
