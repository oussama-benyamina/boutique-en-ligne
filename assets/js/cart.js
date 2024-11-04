document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.cart-btn.add-to-cart');
    const cartOverlay = document.getElementById('cart-overlay');
    const closeCartButton = document.getElementById('close-cart');
    const checkoutButton = document.getElementById('checkout-button');
    const giftWrapCheckbox = document.getElementById('gift-wrap');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });

    closeCartButton.addEventListener('click', function() {
        cartOverlay.classList.remove('active');
    });

    checkoutButton.addEventListener('click', function() {
        alert('Proceeding to checkout...');
    });

    if (giftWrapCheckbox) {
        giftWrapCheckbox.addEventListener('change', updateCartTotal);
    }

    function addToCart(productId) {
        fetch('cart.php?action=add&id=' + productId, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.items) {
                updateCartDisplay(data);
                cartOverlay.classList.add('active');
            } else {
                console.error('Invalid response:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the item to the cart. Please try again.');
        });
    }

    function updateCartDisplay(cartData) {
        const cartItems = document.getElementById('cart-items');
        const cartSubtotal = document.getElementById('cart-subtotal');

        cartItems.innerHTML = ''; // Clear existing cart items
        cartData.items.forEach(item => {
            cartItems.innerHTML += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-img">
                    <div class="detail-box">
                        <div class="cart-food-title">${item.name}</div>
                        <div class="price-box">
                            <div class="cart-price">€${item.price}</div>
                            <div class="cart-amt">€${(item.price * item.quantity).toFixed(2)}</div>
                        </div>
                        <input type="number" value="${item.quantity}" class="cart-quantity" data-product-id="${item.id}">
                    </div>
                    <ion-icon name="trash" class="cart-remove" data-product-id="${item.id}"></ion-icon>
                </div>
            `;
        });

        cartSubtotal.innerHTML = `<p>SOUS-TOTAL <span>€${cartData.subtotal.toFixed(2)}</span></p>`;
        updateCartTotal();

        // Add event listeners for quantity changes and remove buttons
        addCartItemListeners();
    }

    function updateCartTotal() {
        const subtotalElement = document.querySelector('#cart-subtotal span');
        const totalElement = document.getElementById('cart-total');
        const giftWrapPrice = 1.95;

        if (subtotalElement) {
            let subtotal = parseFloat(subtotalElement.textContent.replace('€', ''));
            let total = subtotal;

            if (giftWrapCheckbox && giftWrapCheckbox.checked) {
                total += giftWrapPrice;
            }

            if (totalElement) {
                totalElement.innerHTML = `<p>TOTAL <span>€${total.toFixed(2)}</span></p>`;
            }
        }
    }

    function addCartItemListeners() {
        const quantityInputs = document.querySelectorAll('.cart-quantity');
        const removeButtons = document.querySelectorAll('.cart-remove');

        quantityInputs.forEach(input => {
            input.addEventListener('change', updateItemQuantity);
        });

        removeButtons.forEach(button => {
            button.addEventListener('click', removeCartItem);
        });
    }

    function updateItemQuantity() {
        const productId = this.dataset.productId;
        const newQuantity = this.value;

        fetch(`cart.php?action=update&id=${productId}&quantity=${newQuantity}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.items) {
                updateCartDisplay(data);
            } else {
                console.error('Invalid response:', data);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function removeCartItem() {
        const productId = this.dataset.productId;

        fetch(`cart.php?action=remove&id=${productId}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.items) {
                updateCartDisplay(data);
            } else {
                console.error('Invalid response:', data);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
