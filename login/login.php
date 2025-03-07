<?php

include("../general/bootstrap.php");
include("../general/fontawesome.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Cafeteria</title>
</head>

<h3 class="position-absolute title">Cafeteria</h3>
<body style="background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);">
    <section class="container bg-white shadow m-4">
        <section class="row">
            <section class="col-lg-7 col-md-12 col-sm-12 form-col d-flex justify-content-center align-items-center flex-column pt-5">
                <form class="mb-4 w-75" action="server.php" method="post">
                    <h2 class="mb-5">Please Login to your account</h2>
                    <div class="mb-4">
                        <label for="exampleInputEmail1" class="form-label">Email address</label>
                        <input type="email" name='email' class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                        <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                    </div>
                    <div class="mb-4">
                        <label for="exampleInputPassword1" class="form-label">Password</label>
                        <input type="password" name='password' class="form-control" id="exampleInputPassword1" required>
                    </div>
                    <button type="submit" class="btn btn-danger px-4" name='login'>Login</button>
                </form>
                <a href="forgot-password.php" class="mb-3">Forgot password?</a>
                <div style="height:100px;">
                    <?php
                    if (isset($_GET['wrongMessage'])) {
                        echo "<p class='alert alert-danger p-2'>{$_GET['wrongMessage']}</p>";
                    }
                    ?>
                </div>
            </section>
            <section class="col-lg-5 d-none d-lg-flex desc-col justify-content-center align-items-center flex-column p-0" style="overflow: hidden;">
                <div id="carouselExampleCaptions" class="carousel slide">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="../assets/images/pexels-olenkabohovyk-3323682.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="text-dark">Relax & Unwind</h5>
                                <p class="text-dark">Experience the perfect ambiance for work, study, or a casual meet-up.</p>
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
                            <img src="../assets/images/pexels-viktoria-alipatova-1083711-2668512.jpg" class="d-block w-100" alt="...">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Delight in Every Bite</h5>
                                <p>Savor our selection of freshly baked pastries, made with love and care.</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </section>
            <div class="text-center pt-4"><i class="fa-brands fa-x-twitter pe-5 fs-5"></i><i class="fa-brands fa-facebook pe-5 fs-5"></i><i class="fa-brands fa-instagram fs-5"></i></div>
        </section>
    </section>
</body>

</html>