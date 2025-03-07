<?php
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


$query = "SELECT id, username, email, role, room_id, image_url FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="container">
        <h2>Users Management</h2>
        <div class="button-container">
            <button class="button" onclick="window.location.href='add-user.php'">Add User</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Room</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= htmlspecialchars($user['room_id'] ?? '') ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($user['image_url'] ?? 'default-avatar.png') ?>" alt="User Image">
                        </td>
                        <td>
                            <a href="edit-user.php?id=<?= $user['id'] ?>" class="edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="#" class="delete" data-id="<?= $user['id'] ?>">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="./main.js"></script>
</body>

</html>