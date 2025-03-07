document.addEventListener('DOMContentLoaded', function () {
  
    fetch('../server_handling/get_user.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                console.log(user.image_url);
                document.getElementById('user-name').textContent = user.username;
                document.getElementById('user-image').src = user.image_url ? `../assets/images/${user.image_url}` : '../assets/images/default-user.png';
            } else {
                console.error('Failed to fetch user details:', data.message);
            }
        })
        .catch(error => console.error('Error fetching user details:', error));
 
    document.getElementById('logout-btn').addEventListener('click', () => {
        fetch('../user/logout.php', {
            method: 'POST',
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../login/login.php';  
                } else {
                    showAlert('Logout failed. Please try again.', 'danger');
                }
            })
            .catch(error => console.error('Error logging out:', error));
    });

     
    localStorage.removeItem('cart');

    const cartIcon = document.getElementById('cart-icon');
    const cartSidebar = document.getElementById('cart-sidebar');
    const closeCart = document.getElementById('close-cart');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const clearCartButton = document.getElementById('clear-cart');
    const payButton = document.getElementById('pay-btn');
    const cartItemsList = document.getElementById('cart-items');
    const totalPriceElement = document.getElementById('total-price');
    const paymentAlert = document.getElementById('payment-alert');
    const cartCounter = document.getElementById('cart-counter');  

     const roomSelectionModal = document.getElementById('room-selection-modal');
    const closeModalButton = document.querySelector('.close-modal');
    const confirmRoomButton = document.getElementById('confirm-room');
    const roomSelect = document.getElementById('room-select');

    let cart = {};  
    let selectedRoomId = null; 

   
    function updateCartCounter() {
        const totalItems = Object.values(cart).reduce((sum, item) => sum + item.quantity, 0);
        cartCounter.textContent = totalItems; 
    }

    cartIcon.addEventListener('click', () => {
        cartSidebar.classList.toggle('open');
    });

    closeCart.addEventListener('click', () => {
        cartSidebar.classList.remove('open');
    });
 
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const image = this.closest('.card').querySelector('img').src;

            addToCart(id, name, price, image);
        });
    });

    
    clearCartButton.addEventListener('click', () => {
        clearCart();
    });

    
    payButton.addEventListener('click', () => {
        console.log('Pay button clicked');
        if (Object.keys(cart).length === 0) {
            showAlert('Cart is empty! Add items before paying.', 'danger');
        } else {
           
            fetchRooms();
            roomSelectionModal.style.display = 'block';
        }
    });

     
    closeModalButton.addEventListener('click', () => {
        roomSelectionModal.style.display = 'none';
    });

   
    window.addEventListener('click', (event) => {
        if (event.target === roomSelectionModal) {
            roomSelectionModal.style.display = 'none';
        }
    });

   
    confirmRoomButton.addEventListener('click', () => {
        selectedRoomId = roomSelect.value;
        if (!selectedRoomId) {
            showAlert('Please select a room.', 'danger');
            return;
        }

        roomSelectionModal.style.display = 'none';  
        proceedToPayment(selectedRoomId); 
    });

    
    cartItemsList.addEventListener('click', function (event) {
        if (event.target.classList.contains('increase-item')) {
            const id = event.target.dataset.id;
            updateCartItem(id, 'add');
        } else if (event.target.classList.contains('decrease-item')) {
            const id = event.target.dataset.id;
            updateCartItem(id, 'remove');
        }
    });

   
    function addToCart(id, name, price, image) {
        if (!cart[id]) {
            cart[id] = { name, price, quantity: 1, image };
        } else {
            cart[id].quantity += 1;
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        updateCartCounter();  
        showAlert(`${name} added to cart!`, 'success');
    }

 
    function updateCartItem(id, action) {
        if (cart[id]) {
            if (action === 'add') {
                cart[id].quantity += 1;
            } else if (action === 'remove') {
                cart[id].quantity -= 1;
                if (cart[id].quantity <= 0) {
                    delete cart[id];
                }
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
            updateCartCounter();  
        }
    }

   
    function clearCart() {
        localStorage.removeItem('cart');
        cart = {};
        updateCartUI();
        updateCartCounter();  
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

    
    function showAlert(message, type) {
        paymentAlert.textContent = message;
        paymentAlert.classList.remove('d-none', 'alert-success', 'alert-danger');

      
        paymentAlert.style.backgroundColor = '';
        paymentAlert.style.color = 'white';
        paymentAlert.style.padding = '12px';
        paymentAlert.style.textAlign = 'center';
        paymentAlert.style.borderRadius = '5px';
        paymentAlert.style.fontWeight = 'bold';
        paymentAlert.style.marginBottom = '10px';

        if (type === 'success') {
            paymentAlert.classList.add('alert-success');
            paymentAlert.style.backgroundColor = 'green'; 
        } else if (type === 'danger') {
            paymentAlert.classList.add('alert-danger');
            paymentAlert.style.backgroundColor = 'red';  
        }

       
        paymentAlert.style.position = 'fixed';
        paymentAlert.style.top = '10px'; 
        paymentAlert.style.left = '50%';
        paymentAlert.style.transform = 'translateX(-50%)';
        paymentAlert.style.width = '40%';
        paymentAlert.style.zIndex = '1000';

        setTimeout(() => paymentAlert.classList.add('d-none'), 3000);
    }

    
    function fetchRooms() {
        fetch('../server_handling/get_rooms.php') 
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateRoomSelect(data.rooms); 
                } else {
                    showAlert('Failed to fetch rooms.', 'danger');
                }
            })
            .catch(error => console.error('Error fetching rooms:', error));
    }

    function populateRoomSelect(rooms) {
        roomSelect.innerHTML = '';
        rooms.forEach(room => {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = room.name;
            roomSelect.appendChild(option);
        });
    }

    
    function proceedToPayment(roomId) {
        console.log('Proceeding to payment with room ID:', roomId);
        console.log('Sending cart data to server:', cart);

        fetch('../server_handling/save_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cart, room_id: roomId }), 
        })
            .then(response => response.json())
            .then(data => {
                console.log('Server Response:', data);
                if (data.success) {
                    showAlert('Payment Successful!', 'success');
                    clearCart();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => console.error('Fetch Error:', error));
    }

 
    updateCartUI();
    updateCartCounter();  
});