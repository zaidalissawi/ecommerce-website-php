<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    switch($action) {
        case 'add':
            $quantity = $_POST['quantity'] ?? 1;
            addToCart($product_id, $quantity);
            echo json_encode([
                'success' => true,
                'cartCount' => array_sum($_SESSION['cart']),
                'cartTotal' => number_format(getCartTotal(), 2)
            ]);
            break;
            
        case 'remove':
            removeFromCart($product_id);
            echo json_encode([
                'success' => true,
                'cartCount' => array_sum($_SESSION['cart']),
                'cartTotal' => number_format(getCartTotal(), 2)
            ]);
            break;
            
        case 'update':
            $quantity = $_POST['quantity'] ?? 0;
            updateCartQuantity($product_id, $quantity);
            echo json_encode([
                'success' => true,
                'cartCount' => array_sum($_SESSION['cart']),
                'cartTotal' => number_format(getCartTotal(), 2)
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 