<?php
session_start();
unset($_SESSION['admin_id']);  
echo json_encode(['success' => true]);

?>