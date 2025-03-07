<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../connection.php';

$db = new Database();
$pdo = $db->getConnection();
 
$errors = [];

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $errors['general'] = "Invalid user ID. Please check the URL.";
} else {
    $user_id = $_GET['id'];

 
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors['general'] = "User not found. Please check the user ID.";
    }
}

 
$rooms_query = "SELECT id, name FROM rooms ORDER BY id ASC";
$rooms_stmt = $pdo->prepare($rooms_query);
$rooms_stmt->execute();
$rooms = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $room_id = !empty($_POST['room_no']) ? $_POST['room_no'] : NULL;

 
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
 
    if ($room_id !== NULL) {
        $check_room = $pdo->prepare("SELECT id FROM rooms WHERE id = :room_id");
        $check_room->execute([':room_id' => $room_id]);

        if ($check_room->rowCount() == 0) {
            $room_id = NULL;  
        }
    }

    
    $check_email = $pdo->prepare("SELECT * FROM users WHERE email = :email AND id != :user_id");
    $check_email->execute([':email' => $email, ':user_id' => $user_id]);
    if ($check_email->rowCount() > 0) {
        $errors['email'] = "Email already in use.";
    }

  
    $check_username = $pdo->prepare("SELECT * FROM users WHERE username = :username AND id != :user_id");
    $check_username->execute([':username' => $name, ':user_id' => $user_id]);
    if ($check_username->rowCount() > 0) {
        $errors['name'] = "Username already in use.";
    }

     
    $image_url = $user['image_url']; 
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = __DIR__ . "/../assets/images/";  
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $errors['image'] = "File is not an image.";
        }

        
        if ($_FILES["image"]["size"] > 5000000) {
            $errors['image'] = "File is too large (max 5MB).";
        }

     
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $errors['image'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }

       
        if (empty($errors['image'])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = basename($_FILES["image"]["name"]);
            } else {
                $errors['image'] = "Failed to upload image.";
            }
        }
    }

 
    if (empty($errors)) {
        try {
            $query = "UPDATE users 
                      SET username = :name, email = :email, password = :password, room_id = :room_id, image_url = :image_url 
                      WHERE id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password_hash,
                ':room_id' => $room_id,
                ':image_url' => $image_url,
                ':user_id' => $user_id
            ]);

            $_SESSION['success_message'] = "User updated successfully!";
            header("Location: Allusers.php");
            exit();
        } catch (PDOException $e) {
            $errors['database'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../styles/edit-user.css">
</head>

<body>
    <style>
    body {
        position: relative;
        background: url("../assets/images/background.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;

    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);

        z-index: 1;
    }

    body>* {
        position: relative;
        z-index: 2;
    }

    .sidebar {
        width: 250px;
        height: 100vh;
        background: #343a40;
        color: white;
        padding: 20px;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .admin-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .admin-section img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        margin-bottom: 10px;
    }

    .nav-links ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .nav-links li {
        margin: 10px 0;
    }

    .nav-links a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: white;
        background: #495057;
        border-radius: 5px;
        text-align: center;
        transition: 0.3s;
    }

    .nav-links a:hover {
        background: #17a2b8;
    }

    .cart-section {
        margin-top: auto;
        text-align: center;
        padding-bottom: 20px;
    }

    .cart-icon {
        font-size: 24px;
        cursor: pointer;
        position: relative;
    }

    .cart-counter {
        position: absolute;
        top: -5px;
        right: -10px;
        background: red;
        color: white;
        font-size: 14px;
        border-radius: 50%;
        padding: 2px 6px;
    }

    .table {
        width: 90%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table thead {
        background: #343a40;
        color: white;
        text-align: center;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    .table img {
        border-radius: 5px;
        object-fit: cover;
    }

    #logout-btn {
        width: 100%;
        margin-top: 10px;
        background: #dc3545;
        color: white;
        border: none;
    }

    #logout-btn:hover {
        background: #c82333;
    }
    </style>

    <div class="sidebar">
        <div class="admin-section">
            <?php if (isset($_SESSION['admin_photo']) && isset($_SESSION['admin_name'])): ?>
            <img src="../assets/images/<?php echo $_SESSION['admin_photo']; ?>" alt="Admin Photo">
            <span><?php echo $_SESSION['admin_name']; ?></span>
            <?php else: ?>
            <span>Admin</span>
            <?php endif; ?>
        </div>

        <nav class="nav-links">
            <ul>
                <li><a href="admin.php">Make Order</a></li>
                <li><a href="addproduct.php">Add Product</a></li>
                <li><a href="showproduct.php">Show Products</a></li>
                <li><a href="Allusers.php">Show Users</a></li>
                <li><a href="add-user.php">Add User</a></li>
                <li><a href="admin_orders.php">Show Orders</a></li>
            </ul>
        </nav>

        <!-- <div class="cart-section">
            <div class="cart-icon" id="cart-icon">
                🛒
                <span class="cart-counter" id="cart-counter">0</span>
            </div>
        </div> -->

        <button id="logout-btn" class="btn">Logout</button>
    </div>
    <div class="container">
        <h2>Edit User</h2>
        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= $errors['general'] ?></div>
        <?php endif; ?>
        <?php if (!empty($errors['database'])): ?>
        <div class="alert alert-danger"><?= $errors['database'] ?></div>
        <?php endif; ?>
        <form action="edit-user.php?id=<?= htmlspecialchars($user_id) ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <input required type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    id="name" name="name" value="<?= htmlspecialchars($user['username']) ?>" required>
                <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input required type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input required type="password" class="form-control" id="password" name="password">
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input required type="password"
                    class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                    id="confirm_password" name="confirm_password">
                <?php if (isset($errors['confirm_password'])): ?>
                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="room_no" class="form-label">Room No</label>
                <select class="form-control" id="room_no" name="room_no">
                    <option value="" disabled>Select Room</option>
                    <?php foreach ($rooms as $room): ?>
                    <option value="<?= htmlspecialchars($room['id']) ?>"
                        <?= $room['id'] == $user['room_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($room['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="image-container">
                <label class="form-label">Current Picture</label>
                <img src="../assets/images/<?= htmlspecialchars($user['image_url']) ?>" alt="User Image">
            </div>

            <div class="form-group">
                <label for="image" class="form-label">Upload New Picture</label>
                <input type="file" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" id="image"
                    name="image" accept="image/*">
                <?php if (isset($errors['image'])): ?>
                <div class="invalid-feedback"><?= $errors['image'] ?></div>
                <?php endif; ?>
            </div>

            <div class="d-flex">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-secondary"
                    onclick="window.location.href='Allusers.php'">Cancel</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('logout-btn').addEventListener('click', () => {
        fetch('logout.php', {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'login.php';
                } else {
                    showAlert('Logout failed. Please try again.', 'danger');
                }
            })
            .catch(error => console.error('Error logging out:', error));
    });
    </script>
</body>

</html>