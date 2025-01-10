<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = '/checkout.php';
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$user = getCurrentUser();
$cart_total = getCartTotal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process checkout
    try {
        $conn->beginTransaction();
        
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, status)
            VALUES (?, ?, 'pending')
        ");
        $stmt->execute([$_SESSION['user_id'], $cart_total]);
        $order_id = $conn->lastInsertId();
        
        // Add order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProduct($product_id);
            $stmt->execute([
                $order_id,
                $product_id,
                $quantity,
                $product['price']
            ]);
            
            // Update stock
            $conn->prepare("
                UPDATE products 
                SET stock = stock - ? 
                WHERE id = ?
            ")->execute([$quantity, $product_id]);
        }
        
        $conn->commit();
        $_SESSION['cart'] = array();
        $_SESSION['success'] = 'Order placed successfully!';
        header('Location: order_confirmation.php?id=' . $order_id);
        exit;
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Failed to process order. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <h2>Checkout</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h3>Shipping Information</h3>
                        <form method="POST" id="checkout-form">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea class="form-control" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" required>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3>Order Summary</h3>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($cart_total, 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 