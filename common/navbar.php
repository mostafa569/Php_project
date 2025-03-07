<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand">Cafeteria</a>


        <div class="d-flex align-items-center gap-3">

            <a href="../user/orders.php" class="btn btn-outline-light position-relative" id="order-link">
                Orders
            </a>
            <button class="btn btn-outline-light position-relative" id="cart-icon">
                🛒 Cart
                <span id="cart-counter"
                    class="badge bg-danger position-absolute top-0 start-100 translate-middle">0</span>
            </button>
            <div class="d-flex align-items-center gap-2">
                <img id="user-image" src="../assets/default-user.png" alt="User  Image" class="rounded-circle"
                    width="40" height="40">
                <span id="user-name" class="text-light">User Name</span>
            </div>
            <button id="logout-btn" class="btn btn-outline-light">Logout</button>
        </div>
    </div>
</nav>