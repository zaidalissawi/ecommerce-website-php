<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? 0;
    
    try {
        switch($action) {
            case 'add':
                $stmt = $conn->prepare("
                    INSERT IGNORE INTO wishlist (user_id, product_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                echo json_encode(['success' => true]);
                break;
                
            case 'remove':
                $stmt = $conn->prepare("
                    DELETE FROM wishlist 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                echo json_encode(['success' => true]);
                break;
                
            case 'check':
                $stmt = $conn->prepare("
                    SELECT 1 FROM wishlist 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                echo json_encode([
                    'success' => true,
                    'inWishlist' => $stmt->rowCount() > 0
                ]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 