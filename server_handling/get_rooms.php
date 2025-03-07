<?php
include '../connection.php';  

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->query("SELECT id, name FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'rooms' => $rooms]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching rooms: ' . $e->getMessage()]);
}
?>