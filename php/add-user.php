<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


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

$query = "SELECT id FROM rooms ORDER BY id ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $room_no = !empty($_POST['room_no']) ? $_POST['room_no'] : NULL;


    if ($room_no !== NULL) {
        $check_room = $pdo->prepare("SELECT id FROM rooms WHERE id = :room_id");
        $check_room->execute([':room_id' => $room_no]);

        if ($check_room->rowCount() == 0) {
            $room_no = NULL;
        }
    }


    $check_email = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $check_email->execute([':email' => $email]);
    if ($check_email->rowCount() > 0) {
        die("<p style='color:red;'> email already in use</p>");
    }


    $image_url = "assets/images/default-avatar.png";
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = __DIR__ . "/assets/images/";


        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "assets/images/" . basename($_FILES["image"]["name"]);
        } else {
            die("<p style='color:red;'>Failed to upload image" . $_FILES["image"]["error"] . "</p>");
        }
    }


    try {
        $query = "INSERT INTO users (username, email, password, room_id, image_url) 
                  VALUES (:name, :email, :password, :room_no, :image_url)";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':room_no' => $room_no,
            ':image_url' => $image_url
        ]);

        echo "<p style='color:green;'> user added successfully!</p>";


        header("Location: Allusers.php");
        exit();
    } catch (PDOException $e) {
        die("<p style='color:red;'> error: " . $e->getMessage() . "</p>");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="add-user.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>Add User</h2>
            <form action="add-user.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group select-container">
                    <label for="room_no" class="form-label">Room No</label>
                    <select class="form-control" id="room_no" name="room_no" required>
                        <option value="" disabled selected>Select Room</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= htmlspecialchars($room['id']) ?>">
                                <?= htmlspecialchars($room['id']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Picture</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="d-flex">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>