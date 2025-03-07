<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        position: relative;
        background: url("../assets/images/background.jpg") no-repeat center center fixed;
        background-size: cover;
        margin: 0;
        height: 100vh;
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;

    }

    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);

        z-index: 1;
    }

    body>* {
        position: relative;
        z-index: 2;
    }

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
        position: relative;
        z-index: 1;

    }

    #cart-sidebar {
        width: 350px;
        background-image: url("../assets/images/cart.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: fixed;
        top: 0;
        right: -350px;
        height: 100vh;
        box-shadow: -4px 0 15px rgba(0, 0, 0, 0.3);
        padding: 25px;
        display: flex;
        flex-direction: column;
        transition: 0.4s ease-in-out;
        border-radius: 15px 0 0 15px;
        color: white;
        overflow: hidden;
        z-index: 2000;

    }

    #cart-sidebar::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.89);

        z-index: 1;
        border-radius: 15px 0 0 15px;
    }

    #cart-sidebar>* {
        position: relative;
        z-index: 2;
    }

    #cart-sidebar.open {
        right: 0;
    }

    #cart-items {
        list-style: none;
        padding: 0;
        margin: 20px 0;
        flex-grow: 1;
        overflow-y: auto;
    }

    #cart-items li {
        background: rgba(255, 255, 255, 0.1);
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s ease;
    }

    #cart-items li:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    #total-price {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 20px 0;
        color: #ecf0f1;
    }

    #payment-alert {
        position: fixed;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: #2ecc71;
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        z-index: 9999;
        animation: slideIn 0.5s ease, fadeOut 2s 2s ease forwards;
    }


    #logout-btn,
    #clear-cart,
    #close-cart {
        background: #e74c3c;
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    #logout-btn:hover,
    #clear-cart:hover,
    #close-cart:hover {
        background: #c0392b;
        transform: scale(1.05);
    }



    #payment-alert {
        position: fixed;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        display: none;
    }

    .user-selection {
        margin-left: 500px;
        background-color: #f4f4f4;
        padding: 15px;

        border-radius: 8px;
    }

    .user-select-container {
        width: auto;
    }


    #user-select {
        background-color: #ffffff;

        color: #333333;

        border: 2px solid #007bff;

        border-radius: 4px;

        padding: 8px 12px;

        font-size: 16px;

    }


    #user-select:focus {
        border-color: #0056b3;

        outline: none;

        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);

    }


    #user-select option {
        background-color: #ffffff;

        color: #333333;

    }

    #room-id {
        background-color: transparent;

        color: #333333;

    }


    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    </style>
</head>

<body>

    <style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background: #343a40;
        color: white;
        padding: 20px;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .admin-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .admin-section img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        margin-bottom: 10px;
    }

    .nav-links ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .nav-links li {
        margin: 10px 0;
    }

    .nav-links a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: white;
        background: #495057;
        border-radius: 5px;
        text-align: center;
        transition: 0.3s;
    }

    .nav-links a:hover {
        background: #17a2b8;
    }

    .cart-section {
        margin-top: auto;
        text-align: center;
        padding-bottom: 20px;
    }

    .cart-icon {
        font-size: 24px;
        cursor: pointer;
        position: relative;
    }

    .cart-counter {
        position: absolute;
        top: -5px;
        right: -10px;
        background: red;
        color: white;
        font-size: 14px;
        border-radius: 50%;
        padding: 2px 6px;
    }

    #logout-btn {
        width: 100%;
        margin-top: 10px;
        background: #dc3545;
        color: white;
        border: none;
    }

    #logout-btn:hover {
        background: #c82333;
    }
    </style>

    <div class="sidebar">
        <div class="admin-section">
            <?php if (isset($_SESSION['admin_photo']) && isset($_SESSION['admin_name'])): ?>
            <img src="../assets/images/<?php echo $_SESSION['admin_photo']; ?>" alt="Admin Photo">
            <span><?php echo $_SESSION['admin_name']; ?></span>
            <?php else: ?>
            <span>Admin</span>
            <?php endif; ?>
        </div>

        <nav class="nav-links">
            <ul>
                <li><a href="admin.php">Make Order</a></li>
                <li><a href="addproduct.php">Add Product</a></li>
                <li><a href="showproduct.php">Show Products</a></li>
                <li><a href="Allusers.php">Show Users</a></li>
                <li><a href="add-user.php">Add User</a></li>
                <li><a href="admin_orders.php">Show Orders</a></li>

            </ul>
        </nav>

        <div class="cart-section">
            <div class="cart-icon" id="cart-icon">
                🛒
                <span class="cart-counter" id="cart-counter">0</span>
            </div>
        </div>

        <button id="logout-btn" class="btn">Logout</button>
    </div>


    <div id="cart-sidebar">
        <button id="close-cart">✖</button>
        <h4>Your Cart</h4>
        <ul id="cart-items"></ul>
        <h5 id="total-price">Total: 0 EGP</h5>
        <button class="btn btn-warning" id="clear-cart">Clear Cart</button>
        <button class="btn btn-primary" id="pay-btn">Place Order</button>
    </div>


    <div id="payment-alert" class="alert alert-success">Payment Successful!</div>

    <div class="main-content">

        <div class="row mb-4">
            <div class="col-md-4 user-selection">
                <div class="user-select-container">
                    <label for="user-select" class="sr-only">Select User</label>
                    <select id="user-select" class="form-control">
                        <option value="">Select a user</option>

                    </select>
                </div>
                <input type="hidden" id="room-id" value="">
            </div>
        </div>

        <h3 style="color:white;text-align:center">Products</h3>
        <div class="row" id="products-list">

        </div>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartIcon = document.getElementById('cart-icon');
        const cartSidebar = document.getElementById('cart-sidebar');
        const closeCart = document.getElementById('close-cart');
        const cartItemsList = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');
        const payButton = document.getElementById('pay-btn');
        const clearCartButton = document.getElementById('clear-cart');
        const paymentAlert = document.getElementById('payment-alert');
        const cartCounter = document.getElementById('cart-counter');
        const userSelect = document.getElementById('user-select');
        const productsList = document.getElementById('products-list');
        const roomIdInput = document.getElementById('room-id');

        let cart = {};
        let selectedUserId = null;
        let selectedRoomId = null;
        document.getElementById('logout-btn').addEventListener('click', () => {
            fetch('logout.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'login.php';
                    } else {
                        showAlert('Logout failed. Please try again.', 'danger');
                    }
                })
                .catch(error => console.error('Error logging out:', error));
        });

        cartIcon.addEventListener('click', () => {
            cartSidebar.classList.toggle('open');
        });

        closeCart.addEventListener('click', () => {
            cartSidebar.classList.remove('open');
        });


        fetch('get_users.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.username;
                        option.dataset.roomId = user.room_id;
                        userSelect.appendChild(option);
                    });
                } else {
                    console.error('Failed to fetch users:', data.message);
                }
            })
            .catch(error => console.error('Error fetching users:', error));


        fetch('get_products.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.products.forEach(product => {
                        const productCard = `
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img style="height:200px" src="../assets/images/${product.image_url}" class="card-img-top">
                                <div class="card-body text-center">
                                    <h5>${product.name}</h5>
                                    <p>${product.price} EGP</p>
                                    <button class="btn btn-success add-to-cart" data-id="${product.id}"
                                        data-name="${product.name}" data-price="${product.price}" disabled>
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                        productsList.innerHTML += productCard;
                    });

                    document.querySelectorAll('.add-to-cart').forEach(button => {
                        button.addEventListener('click', function() {
                            if (!selectedUserId) {
                                showAlert('Please select a user first.', 'danger');
                                return;
                            }

                            const id = this.dataset.id;
                            const name = this.dataset.name;
                            const price = parseFloat(this.dataset.price);
                            const image = this.closest('.card').querySelector('img')
                                .src;

                            addToCart(id, name, price, image);
                        });
                    });
                } else {
                    console.error('Failed to fetch products:', data.message);
                }
            })
            .catch(error => console.error('Error fetching products:', error));

        userSelect.addEventListener('change', function() {
            selectedUserId = this.value;
            selectedRoomId = this.options[this.selectedIndex].dataset
                .roomId;
            roomIdInput.value = selectedRoomId;

            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.disabled = !selectedUserId;
            });

            if (!selectedUserId) {
                showAlert('Please select a user first.', 'danger');
            }
        });

        clearCartButton.addEventListener('click', () => {
            clearCart();
        });

        payButton.addEventListener('click', () => {
            if (!selectedUserId) {
                showAlert('Please select a user first.', 'danger');
                return;
            }

            if (Object.keys(cart).length === 0) {
                showAlert('Cart is empty! Add items before placing the order.', 'danger');
                return;
            }


            const orderData = {
                cart: cart,
                user_id: selectedUserId,
                room_id: selectedRoomId,
            };


            console.log('Sending order data:', orderData);


            fetch('save_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Server response:', data);
                    if (data.success) {
                        showAlert('Order placed successfully!', 'success');
                        clearCart();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while placing the order.', 'danger');
                });
        });


        cartItemsList.addEventListener('click', function(event) {
            if (event.target.classList.contains('increase-item')) {
                const id = event.target.dataset.id;
                updateCartItem(id, 'increase');
            } else if (event.target.classList.contains('decrease-item')) {
                const id = event.target.dataset.id;
                updateCartItem(id, 'decrease');
            }
        });


        function addToCart(id, name, price, image) {
            if (!cart[id]) {
                cart[id] = {
                    name,
                    price,
                    quantity: 1,
                    image
                };
            } else {
                cart[id].quantity += 1;
            }

            updateCartUI();
            updateCartCounter();
            showAlert(`${name} added to cart!`, 'success');
        }

        function updateCartItem(id, action) {
            if (cart[id]) {
                if (action === 'increase') {
                    cart[id].quantity += 1;
                } else if (action === 'decrease') {
                    cart[id].quantity -= 1;
                    if (cart[id].quantity <= 0) {
                        delete cart[id];
                    }
                }

                updateCartUI();
                updateCartCounter();
            }
        }

        function updateCartUI() {
            cartItemsList.innerHTML = '';
            let totalPrice = 0;

            for (const [id, item] of Object.entries(cart)) {
                const itemTotal = item.price * item.quantity;
                totalPrice += itemTotal;

                const li = document.createElement('li');
                li.className = 'cart-item d-flex align-items-center';
                li.innerHTML = `
                <img src="${item.image}" class="cart-img me-2" width="50">
                <span class="flex-grow-1">${item.name} - ${item.quantity} x ${item.price} EGP</span>
                <button class="btn btn-sm btn-success increase-item" data-id="${id}">+</button>
                <button class="btn btn-sm btn-danger decrease-item" data-id="${id}">-</button>
            `;
                cartItemsList.appendChild(li);
            }

            totalPriceElement.textContent = `Total: ${totalPrice} EGP`;
        }


        function clearCart() {
            cart = {};
            updateCartUI();
            updateCartCounter();
        }


        function updateCartCounter() {
            const totalItems = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
            cartCounter.textContent = totalItems;
        }


        function showAlert(message, type) {
            paymentAlert.textContent = message;
            paymentAlert.className = `alert alert-${type}`;
            paymentAlert.style.display = 'block';
            setTimeout(() => {
                paymentAlert.style.display = 'none';
            }, 3000);
        }
    });
    </script>
</body>

</html>