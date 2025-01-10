<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Vulnerable to SQL injection
$cart_items = array();
if(!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($query);
    while($product = $result->fetch(PDO::FETCH_ASSOC)) {
        $cart_items[] = array(
            'product' => $product,
            'quantity' => $_SESSION['cart'][$product['id']]
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <h2>Shopping Cart</h2>
        <?php if(empty($cart_items)): ?>
            <div class="alert alert-info">Your cart is empty</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach($cart_items as $item): 
                            $subtotal = $item['product']['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $item['product']['image_url']; ?>" 
                                         alt="<?php echo $item['product']['name']; ?>"
                                         style="width: 50px; margin-right: 10px;">
                                    <?php echo $item['product']['name']; ?>
                                </div>
                            </td>
                            <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                            <td>
                                <input type="number" 
                                       value="<?php echo $item['quantity']; ?>"
                                       min="1"
                                       onchange="updateQuantity(<?php echo $item['product']['id']; ?>, this.value)">
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                        onclick="removeFromCart(<?php echo $item['product']['id']; ?>)">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 