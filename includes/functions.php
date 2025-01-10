<?php
function getCartCount() {
    if(isset($_SESSION['cart'])) {
        return array_sum($_SESSION['cart']);
    }
    return 0;
}

function addToCart($product_id) {
    // Vulnerable to CSRF
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
}

function searchProducts($query) {
    global $conn;
    // Vulnerable to SQL Injection
    $sql = "SELECT * FROM products WHERE name LIKE '%$query%' OR description LIKE '%$query%'";
    return $conn->query($sql);
}

function getUserProfile($user_id) {
    global $conn;
    // Vulnerable to SQL Injection
    $sql = "SELECT * FROM users WHERE id = " . $user_id;
    $result = $conn->query($sql);
    return $result->fetch(PDO::FETCH_ASSOC);
}

function processOrder($user_id, $cart) {
    global $conn;
    // Vulnerable to Race Conditions
    $total = calculateTotal($cart);
    $sql = "INSERT INTO orders (user_id, total_amount, status) VALUES ($user_id, $total, 'pending')";
    $conn->query($sql);
    return $conn->lastInsertId();
} 