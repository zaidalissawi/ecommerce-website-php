<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vulnerable to SQL injection
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = $product_id";
$result = $conn->query($query);
$product = $result->fetch(PDO::FETCH_ASSOC);

// Get reviews
$reviews_query = "SELECT r.*, u.username 
                 FROM reviews r 
                 JOIN users u ON r.user_id = u.id 
                 WHERE r.product_id = $product_id 
                 ORDER BY r.created_at DESC";
$reviews = $conn->query($reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/products.php">Products</a></li>
                <li class="breadcrumb-item"><a href="/products.php?category=<?php echo $product['category_id']; ?>"><?php echo $product['category_name']; ?></a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         class="card-img-top product-detail-image" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="my-4">
                    <h2 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h2>
                    <p class="text-muted">Stock: <?php echo $product['stock']; ?> units</p>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['id']; ?>)">
                        Add to Cart
                    </button>
                    <button class="btn btn-outline-primary btn-lg" onclick="addToWishlist(<?php echo $product['id']; ?>)">
                        Add to Wishlist
                    </button>
                </div>

                <!-- Reviews Section -->
                <div class="mt-5">
                    <h3>Customer Reviews</h3>
                    <?php while($review = $reviews->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title"><?php echo htmlspecialchars($review['username']); ?></h5>
                                    <div class="text-warning">
                                        <?php for($i = 0; $i < $review['rating']; $i++): ?>★<?php endfor; ?>
                                    </div>
                                </div>
                                <p class="card-text"><?php echo $review['comment']; ?></p>
                                <small class="text-muted">
                                    Posted on <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="add_review.php" method="POST" class="mt-4">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Your Rating</label>
                                <select class="form-select" name="rating" id="rating" required>
                                    <option value="5">★★★★★ (5)</option>
                                    <option value="4">★★★★☆ (4)</option>
                                    <option value="3">★★★☆☆ (3)</option>
                                    <option value="2">★★☆☆☆ (2)</option>
                                    <option value="1">★☆☆☆☆ (1)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Your Review</label>
                                <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Please <a href="/login.php">login</a> to write a review.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 