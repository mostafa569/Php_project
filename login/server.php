<?php
 

date_default_timezone_set("Africa/Cairo");
require_once("../connection.php");

require('../PHPMailer-master/src/Exception.php');
require('../PHPMailer-master/src/PHPMailer.php');
require('../PHPMailer-master/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $query = $pdo->prepare("SELECT id, password FROM users WHERE email = :email AND role = 'user'");
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: ../user/user.php");
            exit;
        } else {
            header("Location: login.php?wrongMessage=Wrong Email or Password*");
            exit;
        }
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

if (isset($_POST['send-code'])) {
    $email = $_POST['email'];
    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $verification_code = bin2hex(random_bytes(4));
        $verification_expiry = date("Y-m-d H:i:s", time() + 3600);
        $id = $result['id'];
        $query = $pdo->prepare("UPDATE users SET verification_code = ?, verification_expiry = ? WHERE id = ?");
        $query->execute([$verification_code, $verification_expiry, $id]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mostafa.mohamed.aboali76@gmail.com';
            $mail->Password = "rmcx jlgh fdmd jaxj";
            $mail->Port = 587;
            $mail->setFrom('support@cafeteria.com', 'Cafeteria');
            $mail->addAddress($result['email']);
            $mail->Subject = 'Reset Password Request';
            $mail->isHTML(true);
            $resetLink = "http://localhost/project/login/reset-password.php?id={$id}&vrfy={$verification_code}";
            $mail->Body = "<div style='font-family: Arial, sans-serif; color: #333;'>
                <h2>Hello,</h2>
                <p>Click the link to reset your password: <a href='{$resetLink}'>Reset Password</a></p>
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
        } catch (Exception $e) {
            die("Mailer Error: " . $mail->ErrorInfo);
        }
    } else {
        header("location:forgot-password.php?wrongEmailMessage=Wrong Email*");
        exit;
    }
}

if (isset($_POST['verify-code'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result && password_verify($password, $result['password'])) {
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
            $encPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
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
?>