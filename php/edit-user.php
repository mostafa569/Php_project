<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

var_dump($_GET);


$host = "localhost";
$dbname = "cafeteria";
$username = "root";
$password = "Sanaa@123";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:red;'>Invalid user ID.  check the URL.</p>");
}

$user_id = $_GET['id'];


$query = "SELECT * FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("<p style='color:red;'> User not found. check user ID.</p>");
}

$rooms_query = "SELECT id FROM rooms ORDER BY id ASC";
$rooms_stmt = $pdo->prepare($rooms_query);
$rooms_stmt->execute();
$rooms = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $room_no = !empty($_POST['room_no']) ? $_POST['room_no'] : NULL;

    $image_url = $user['image_url'];
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = __DIR__ . "/assets/images/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "assets/images/" . basename($_FILES["image"]["name"]);
        } else {
            echo "<p style='color:red;'> Failed to upload image. Error Code: " . $_FILES["image"]["error"] . "</p>";
        }
    }

    try {
        $update_query = "UPDATE users SET username = :name, email = :email, room_id = :room_no, image_url = :image_url WHERE id = :id";
        $stmt = $pdo->prepare($update_query);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':room_no' => $room_no,
            ':image_url' => $image_url,
            ':id' => $user_id
        ]);

        header("Location: Allusers.php");
        exit();
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error updating user: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="edit-user.css">
</head>

<body>
    <div class="container">
        <h2>Edit User</h2>
        <form action="edit-user.php?id=<?= htmlspecialchars($user_id) ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="room_no" class="form-label">Room No</label>
                <select class="form-control" id="room_no" name="room_no">
                    <option value="" disabled>Select Room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= htmlspecialchars($room['id']) ?>" <?= $room['id'] == $user['room_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="image-container">
                <label class="form-label">Current Picture</label>
                <img src="<?= htmlspecialchars($user['image_url']) ?>" alt="User Image">
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Upload New Picture</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='Allusers.php'">Cancel</button>
            </div>
        </form>
    </div>
</body>

</html>