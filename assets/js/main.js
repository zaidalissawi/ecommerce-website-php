function addToCart(productId, event) {
    if(event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    fetch('/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            updateCartCount(data.cartCount);
            showMessage('Product added to cart!', 'success');
        } else {
            showMessage('Failed to add product to cart', 'error');
        }
    });
}

function updateCartCount(count) {
    document.querySelector('.cart-count').textContent = count;
}

function showMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    setTimeout(() => alertDiv.remove(), 3000);
}

function addToWishlist(productId) {
    // Vulnerable to CSRF
    fetch('/add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showMessage('Product added to wishlist!', 'success');
        } else {
            showMessage(data.message || 'Failed to add to wishlist', 'error');
        }
    });
} 