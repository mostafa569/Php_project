<?php
$host = "localhost";
$user = "root";
$password = "root";
$database = "cafeteria";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
