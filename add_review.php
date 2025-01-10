<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    // Vulnerable to SQL injection
    $query = "INSERT INTO reviews (product_id, user_id, rating, comment) 
              VALUES ($product_id, $user_id, $rating, '$comment')";
    
    try {
        $conn->query($query);
        header('Location: product_details.php?id=' . $product_id);
        exit;
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']); 