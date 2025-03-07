<!-- header.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Admin Dashboard</title>
    <style>
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
    }
    </style>
</head>