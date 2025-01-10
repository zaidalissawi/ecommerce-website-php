<?php
// Cart Functions
function addToCart($product_id, $quantity = 1) {
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function removeFromCart($product_id) {
    if(isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function updateCartQuantity($product_id, $quantity) {
    if($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        removeFromCart($product_id);
    }
}

function getCartTotal() {
    global $conn;
    $total = 0;
    
    if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        $ids_str = implode(',', $ids);
        
        $stmt = $conn->prepare("SELECT id, price FROM products WHERE id IN ($ids_str)");
        $stmt->execute();
        
        while($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $total += $product['price'] * $_SESSION['cart'][$product['id']];
        }
    }
    
    return $total;
}

// Product Functions
function getProduct($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getFeaturedProducts($limit = 8) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE featured = 1 LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductsByCategory($category_id, $limit = null) {
    global $conn;
    $query = "SELECT * FROM products WHERE category_id = ?";
    if($limit) {
        $query .= " LIMIT " . (int)$limit;
    }
    $stmt = $conn->prepare($query);
    $stmt->execute([$category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// User Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    global $conn;
    if(!isLoggedIn()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Review Functions
function getProductReviews($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT r.*, u.username 
                           FROM reviews r 
                           JOIN users u ON r.user_id = u.id 
                           WHERE r.product_id = ? 
                           ORDER BY r.created_at DESC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addReview($product_id, $rating, $comment) {
    global $conn;
    if(!isLoggedIn()) return false;
    
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) 
                           VALUES (?, ?, ?, ?)");
    return $stmt->execute([
        $product_id,
        $_SESSION['user_id'],
        $rating,
        $comment
    ]);
}

// Search Function
function searchProducts($query) {
    global $conn;
    $search = "%$query%";
    $stmt = $conn->prepare("SELECT * FROM products 
                           WHERE name LIKE ? 
                           OR description LIKE ?");
    $stmt->execute([$search, $search]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 