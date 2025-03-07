<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../connection.php';

$db = new Database();
$pdo = $db->getConnection();
 
$query = "SELECT id, name FROM rooms ORDER BY id ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
$errors = [];

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

    
    $check_email = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $check_email->execute([':email' => $email]);
    if ($check_email->rowCount() > 0) {
        $errors['email'] = "Email already in use.";
    }
 
    $check_username = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $check_username->execute([':username' => $name]);
    if ($check_username->rowCount() > 0) {
        $errors['name'] = "Username already in use.";
    }
 
    $image_url = "assets/images/default-avatar.png";  
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
            $query = "INSERT INTO users (username, email, password, room_id, image_url) 
                      VALUES (:name, :email, :password, :room_id, :image_url)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password_hash,
                ':room_id' => $room_id,
                ':image_url' => $image_url
            ]);

            $_SESSION['success_message'] = "User added successfully!";
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
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/add-user.css">
</head>
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
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100%;
    background-color: #343a40;
    padding: 20px;
    color: #fff;
    z-index: 1000;
}

.sidebar .brand {
    font-size: 24px;
    margin-bottom: 20px;
}

.cart-section {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.cart-icon {
    position: relative;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    margin-right: 10px;

}

.cart-counter {
    background-color: red;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
}

.admin-section {
    display: flex;
    align-items: center;
    margin-top: 20px;
}

.admin-section img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.admin-section span {
    color: #fff;
    font-size: 16px;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    position: relative;
    z-index: 1;

}

#cart-sidebar {
    width: 350px;
    background-image: url("../assets/images/cart.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: fixed;
    top: 0;
    right: -350px;
    height: 100vh;
    box-shadow: -4px 0 15px rgba(0, 0, 0, 0.3);
    padding: 25px;
    display: flex;
    flex-direction: column;
    transition: 0.4s ease-in-out;
    border-radius: 15px 0 0 15px;
    color: white;
    overflow: hidden;
    z-index: 2000;

}

#cart-sidebar::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.89);

    z-index: 1;
    border-radius: 15px 0 0 15px;
}

#cart-sidebar>* {
    position: relative;
    z-index: 2;
}

#cart-sidebar.open {
    right: 0;
}

#cart-items {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    flex-grow: 1;
    overflow-y: auto;
}

#cart-items li {
    background: rgba(255, 255, 255, 0.1);
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

#cart-items li:hover {
    background: rgba(255, 255, 255, 0.2);
}

#total-price {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 20px 0;
    color: #ecf0f1;
}

#payment-alert {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: #2ecc71;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    z-index: 9999;
    animation: slideIn 0.5s ease, fadeOut 2s 2s ease forwards;
}


#logout-btn,
#clear-cart,
#close-cart {
    background: #e74c3c;
    border: none;
    border-radius: 25px;
    padding: 8px 20px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

#logout-btn:hover,
#clear-cart:hover,
#close-cart:hover {
    background: #c0392b;
    transform: scale(1.05);
}



#payment-alert {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    display: none;
}

.user-selection {
    margin-left: 500px;
    background-color: #f4f4f4;
    padding: 15px;

    border-radius: 8px;
}

.user-select-container {
    width: auto;
}


#user-select {
    background-color: #ffffff;

    color: #333333;

    border: 2px solid #007bff;

    border-radius: 4px;

    padding: 8px 12px;

    font-size: 16px;

}


#user-select:focus {
    border-color: #0056b3;

    outline: none;

    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);

}


#user-select option {
    background-color: #ffffff;

    color: #333333;

}

#room-id {
    background-color: transparent;

    color: #333333;

}


.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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
</head>

<body>


    <body>

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
            <div class="form-container">
                <h2>Add User</h2>

                <?php if (!empty($errors['database'])): ?>
                <div class="alert alert-danger"><?= $errors['database'] ?></div>
                <?php endif; ?>

                <form action="add-user.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                            id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                        <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password"
                            class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                            id="confirm_password" name="confirm_password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="room_no" class="form-label">Room No</label>
                        <select class="form-select" id="room_no" name="room_no" required>
                            <option value="" disabled selected>Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                            <option value="<?= htmlspecialchars($room['id']) ?>"
                                <?= (isset($room_id) && $room_id == $room['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Picture</label>
                        <input type="file" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                            id="image" name="image" accept="image/*">
                        <?php if (isset($errors['image'])): ?>
                        <div class="invalid-feedback"><?= $errors['image'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="btn-container">
                        <button type="reset" class="btn btn-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
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