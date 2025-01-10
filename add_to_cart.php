<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    
    echo json_encode([
        'success' => true,
        'cartCount' => array_sum($_SESSION['cart'])
    ]);
} else {
    echo json_encode(['success' => false]);
} 