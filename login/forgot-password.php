<?php

include("../general/bootstrap.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Cafeteria</title>
</head>

<body style="background: linear-gradient(to right, #fcb175, #e57373) !important;">
    <section class="container bg-white shadow p-0 w-75">
        
        <section class="form-col d-flex justify-content-center align-items-center flex-column">
            <form class="mb-4 w-75" action="server.php" method="post">
                <h3>Forgot Password</h3>
                <p class="mb-5">Remember your password ? <a href="login.php">Login</a></p>
                <div class="mb-4">
                    <label for="exampleInputEmail1" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <button type="submit" class="btn btn-danger px-3" name="send-code">Send Verification Code</button>
            </form>
            <div style="height:100px;">
            <?php
            if (isset($_GET['wrongEmailMessage'])) {
                echo "<p class='alert alert-danger p-2'>{$_GET['wrongEmailMessage']}</p>";
            }
            if (isset($_GET['successMessage'])) {
                echo "<p class='alert alert-success p-2 text-center'>{$_GET['successMessage']}</p>";
            }
            if (isset($_GET['failMessage'])) {
                echo "<p class='alert alert-danger p-2'>{$_GET['failMessage']}</p>";
            }
            ?>
            </div>
        </section>
    </section>

</body>

</html>