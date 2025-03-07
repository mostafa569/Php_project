<?php
$host = "localhost:3306";
$dbname = "cafeteria";
$username = "root";
$password = "MYSQLSERVER";

try {

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    //   echo "Connected successfully!";
} catch (PDOException $e) {

    // die("Database connection failed: " . $e->getMessage());
}
