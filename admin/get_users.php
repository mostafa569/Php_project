<?php
include '../connection.php';

header('Content-Type: application/json');

try {
     
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->query("SELECT id, username, room_id FROM users WHERE role = 'user'");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()]);
}
?>