<?php
session_start();
include '../connection.php';
include("../general/bootstrap.php");
include("../general/fontawesome.php");
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        try {
          
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT id, password, username, image_url FROM users WHERE email = :email AND role = 'admin'");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['username'];
                $_SESSION['admin_photo'] = $user['image_url'];

                header("Location: admin.php");
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/login.css">
    <style>
    body {
        background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
    }

    .container {
        height: 80vh;
    }

    .form-col {
        height: 80vh;
    }

    .desc-col {
        height: 80vh;
    }

    .carousel .carousel-caption {
        bottom: 13rem !important;
    }

    .carousel .carousel-indicators {
        bottom: 10rem !important;
    }

    .carousel h5,
    .carousel p {
        -webkit-text-stroke: 0.1px black;
    }

    i {
        color: rgba(255, 255, 255, 0.6);
    }

    .title {
        color: rgba(255, 255, 255, 0.6);
        top: 1.1rem;
        left: 7rem;
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
    }
    </style>
</head>

<body>
    <h3 class="position-absolute title">Cafeteria</h3>
    <section class="container bg-white shadow m-4">
        <section class="row">
            <section
                class="col-lg-7 col-md-12 col-sm-12 form-col d-flex justify-content-center align-items-center flex-column pt-5">
                <form class="mb-4 w-75" method="POST">
                    <h2 class="mb-5">Admin Login</h2>
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger px-4">Login</button>
                </form>

            </section>
            <section
                class="col-lg-5 d-none d-lg-flex desc-col justify-content-center align-items-center flex-column p-0"
                style="overflow: hidden;">
                <div id="carouselExampleCaptions" class="carousel slide">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="../assets/images/pexels-olenkabohovyk-3323682.jpg" class="d-block w-100"
                                alt="...">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="text-dark">Relax & Unwind</h5>
                                <p class="text-dark">Experience the perfect ambiance for work, study, or a casual
                                    meet-up.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/images/pexels-chevanon-312418.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Start Your Day Right</h5>
                                <p> Enjoy a fresh cup of coffee brewed to perfection, just the way you love it.</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img src="../assets/images/pexels-viktoria-alipatova-1083711-2668512.jpg"
                                class="d-block w-100" alt="...">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Delight in Every Bite</h5>
                                <p>Savor our selection of freshly baked pastries, made with love and care.</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </section>
            <div class="text-center pt-4"><i class="fa-brands fa-x-twitter pe-5 fs-5"></i><i
                    class="fa-brands fa-facebook pe-5 fs-5"></i><i class="fa-brands fa-instagram fs-5"></i></div>
        </section>
    </section>
</body>

</html>