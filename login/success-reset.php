<?php

require('../connection.php');
include('../general/bootstrap.php');

?>
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href="../styles/login.css">
    <title>Cafeteria</title>
</head>

<body style=" background: linear-gradient(to right, #fcb175, #e57373) !important;">
    <section class='container bg-white shadow p-0 w-75'>
        <section class='form-col d-flex justify-content-center align-items-center flex-column'>
            <form class='mb-4 w-75'>
                <p class="mb-5">Remember your password ? <a href="login.php">Login</a></p>
                <h3>Reset Password</h3>
                <div class='mb-4'>
                    <label for='exampleInputPassword1' class='form-label'>Password</label>
                    <input type='password' name='password1' class='form-control' id='exampleInputPassword1'
                        aria-describedby='passwordHelp' disabled>
                </div>
                <div class=''>
                    <label for='exampleInputPassword2' class='form-label'>Re-enter Password</label>
                    <input type='password' name='password2' class='form-control' id='exampleInputPassword2'
                        aria-describedby='rePasswordHelp' disabled>
                </div>
                <div id='emailHelp' class='form-text mb-4'>Your password must be 8-20 characters long, include at least
                    one uppercase letter and one number.</div>
                <button type='submit' class='btn btn-danger px-3' name='reset-pass' disabled>Reset Password</button>
            </form>
            <div style='height:100px;'>
                <p class='alert alert-success p-2 text-center'>Password Updated Successfully :)<a
                        style="color:red;font-size:20px" href='login.php'>Login
                        Now
                    </a>

                </p>
            </div>
        </section>
    </section>';


</body>

</html>