<?php

date_default_timezone_set("Africa/Cairo");

require("../general/connection.php");

require('../PHPMailer-master/src/Exception.php');
require('../PHPMailer-master/src/PHPMailer.php');
require('../PHPMailer-master/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $encPassword = md5($password);
    $query = $pdo->prepare("select * from users where email=:email and password=:password");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->bindParam(":password", $encPassword, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        session_start();
        $_SESSION['loginId'] = $result['id'];
        header("location:home.php");
        exit;
    } else {
        header("location:login.php?wrongMessage=Wrong Email or Password*");
        exit;
    }
}

if (isset($_POST['send-code'])) {
    $email = $_POST['email'];
    $query = $pdo->prepare("select * from users where email=:email");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $encPassword = md5($result['password']);
        $verification_code = bin2hex(random_bytes(4));
        // 3600s = 1hour
        // date() : get the current date and time
        $verification_expiry = date("Y-m-d H:i:s", time() + 3600);
        $id = $result['id'];
        $query = $pdo->prepare("update users set verification_code=? , verification_expiry=? where id=?");
        $query->execute([$verification_code, $verification_expiry, $id]);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yasminaymanzin@gmail.com';
        $mail->Password = "udmy ymwv gljx nlqi";
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('support@cafeteria.com', 'Cafeteria');
        $mail->addAddress($result['email']);
        $mail->Subject = 'Reset Password Request';
        $mail->isHTML(true);
        $resetLink = "http://localhost/Project/login/reset-password.php?id={$id}&vrfy={$verification_code}";
        $mail->Body = "<div style='font-family: Arial, sans-serif; color: #333;'>
        <h2>Hello,</h2>
        <p>Click the link to reset your password : <a href='{$resetLink}'>Reset Password</a></p>
        <p>If you did not request to reset your password, please ignore this email.</p>
        <p>Thank you,</p>
        <p><strong>Cafeteria</strong></p>
    </div>";
        if ($mail->send()) {
            header('location:forgot-password.php?successMessage=Email Sent Successfully');
            exit;
        } else {
            header('location:forgot-password.php?failMessage=Failed to send email*');
            exit;
        }
    } else {
        header("location:forgot-password.php?wrongEmailMessage=Wrong Email*");
        exit;
    }
}

if (isset($_POST['verify-code'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $encPassword = md5($password);
    $query = $pdo->prepare("select * from users where email=:email and password=:password");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->bindParam(":password", $encPassword, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        session_start();
        $_SESSION['loginId'] = $result['id'];
        header("location:home.php");
        exit;
    } else {
        header("location:login.php?wrongMessage=Wrong Email or Password*");
        exit;
    }
}

if (isset($_POST['reset-pass'])) {
    $vrfy = $_POST['vrfy'];
    $id = $_POST['id'];
    if ($_POST['password1'] == $_POST['password2']) {
        $passPattern = "/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d@#$_!%*?&]{8,20}$/";
        $password = $_POST['password1'];
        if (preg_match($passPattern, $password)) {
            $encPassword = md5($password);
            $query = $pdo->prepare("update users set password=? where id=?");
            $result = $query->execute([$encPassword, $id]);
            if ($result) {
                header("location:success-reset.php");
                exit;
            } else {
                header("location:reset-password.php?id={$id}&vrfy={$vrfy}&failMessage=Failed to update password");
                exit;
            }
        } else {
            header("location:reset-password.php?id={$id}&vrfy={$vrfy}&pregMatchMessage=Password does not match the pattern");
            exit;
        }
    } else {
        header("location:reset-password.php?id={$id}&vrfy={$vrfy}&notEqualMessage=Passwords do not match");
        exit;
    }
}
