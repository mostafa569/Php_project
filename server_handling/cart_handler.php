<?php
include '../connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;

   
    if ($action === 'add' && $id) {
        $stmt = $conn->prepare("SELECT id FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo json_encode(['error' => 'Product not found']);
            exit;
        }
    }

    echo json_encode(['success' => true]);  
}
?>