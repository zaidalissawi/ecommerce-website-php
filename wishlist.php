<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get wishlist items
$stmt = $conn->prepare("
    SELECT p.*, w.created_at as added_date
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <h2>My Wishlist</h2>

        <?php if ($wishlist_items): ?>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     class="card-img-top product-image" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                        onclick="removeFromWishlist(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                <p class="card-text description-truncate">
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <button class="btn btn-primary" 
                                            onclick="addToCart(<?php echo $item['id']; ?>)"
                                            <?php echo $item['stock'] == 0 ? 'disabled' : ''; ?>>
                                        <?php echo $item['stock'] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                    </button>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                Added <?php echo date('M d, Y', strtotime($item['added_date'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
            <a href="products.php" class="btn btn-primary">Browse Products</a>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    function removeFromWishlist(productId) {
        if (confirm('Are you sure you want to remove this item from your wishlist?')) {
            fetch('wishlist_actions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to remove item from wishlist');
                }
            });
        }
    }
    </script>
</body>
</html> 