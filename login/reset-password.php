<?php

require('../general/connection.php');
include('../general/bootstrap.php');

?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='style.css'>
    <title>Cafeteria</title>
</head>

<body style="background: linear-gradient(to right, #fcb175, #e57373) !important;">
    <?php
    $verification_code = $_GET['vrfy'];
    $id = $_GET['id'];
    $query = $pdo->prepare('select * from users where id=? and verification_code=?');
    $query->execute([$id, $verification_code]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if (isset($_GET['vrfy']) && isset($_GET['id']) && $result || isset($_GET['failMessage']) || isset($_GET['notEqualMessage']) || isset($_GET['pregMatchMessage'])) {
        $verification_expiry = $result['verification_expiry'];
        $now = $verification_expiry = date('Y-m-d H:i:s');
        $query = $pdo->prepare('select * from users where id=? and verification_expiry > ?');
        $query->execute([$id, $now]);
        $expiryResult = $query->fetch(PDO::FETCH_ASSOC);
        if ($expiryResult) {
            echo "<section class='container bg-white shadow p-0 w-75'>
            <section class='form-col d-flex justify-content-center align-items-center flex-column'>
                <form class='mb-4 w-75' action='server.php' method='post'>
                    <h3>Reset Password</h3>
                    <div class='mb-4'>
                        <label for='exampleInputPassword1' class='form-label'>Password</label>
                        <input type='password' name='password1' class='form-control' id='exampleInputPassword1' aria-describedby='passwordHelp'>
                    </div>
                    <div class=''>
                        <label for='exampleInputPassword2' class='form-label'>Re-enter Password</label>
                        <input type='password' name='password2' class='form-control' id='exampleInputPassword2' aria-describedby='rePasswordHelp'>
                    </div>
                    <div id='emailHelp' class='form-text mb-4'>Your password must be 8-20 characters long, include at least one uppercase letter and one number.</div>
                    <input type='hidden' name='id' value='{$_GET['id']}'>
                    <input type='hidden' name='vrfy' value='{$_GET['vrfy']}'>
                    <button type='submit' class='btn btn-danger px-3' name='reset-pass'>Reset Password</button>
                </form>
                <div style='height:100px;'>";
            if (isset($_GET['notEqualMessage'])) {
                echo "<p class='alert alert-danger p-2'>{$_GET['notEqualMessage']}</p>";
            }
            if (isset($_GET['pregMatchMessage'])) {
                echo "<p class='alert alert-danger p-2'>{$_GET['pregMatchMessage']}</p>";
            }
            if (isset($_GET['successMessage'])) {
                echo "<p class='alert alert-success p-2 text-center'>{$_GET['successMessage']}</p>";
            }
            if (isset($_GET['failMessage'])) {
                echo "<p class='alert alert-danger p-2'>{$_GET['failMessage']}</p>";
            }
            echo '</div>
            </section>
        </section>';
        } else {
            echo '<h1>Session Expired</h1>';
        }
    } else {
        header('location:page-not-found.php');
        exit;
    }
    ?>


</body>

</html>