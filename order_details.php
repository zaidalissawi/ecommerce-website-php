<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;

// Get order details with user info
$stmt = $conn->prepare("
    SELECT o.*, u.email, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Get order items with product details
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image_url, p.description
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order_id; ?> - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="profile.php">My Profile</a></li>
                <li class="breadcrumb-item"><a href="orders.php">Orders</a></li>
                <li class="breadcrumb-item active">Order #<?php echo $order_id; ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Order Items</h3>
                    </div>
                    <div class="card-body">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex mb-3 border-bottom pb-3">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     style="width: 100px; height: 100px; object-fit: contain;">
                                <div class="ms-3">
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p class="text-muted"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <div class="d-flex justify-content-between">
                                        <span>Quantity: <?php echo $item['quantity']; ?></span>
                                        <span>Price: $<?php echo number_format($item['price'], 2); ?></span>
                                        <span>Total: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Order Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Order Number:</span>
                            <strong>#<?php echo $order_id; ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Date:</span>
                            <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Status:</span>
                            <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Shipping Information</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <?php if (isset($order['shipping_address'])): ?>
                            <p><strong>Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 