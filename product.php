<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vulnerable to SQL injection
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;
$query = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($query);
$product = $result->fetch(PDO::FETCH_ASSOC);

// Vulnerable to XSS in comments
$comments_query = "SELECT r.*, u.username FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE product_id = $product_id 
                  ORDER BY created_at DESC";
$comments = $conn->query($comments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - TechGear Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $product['image_url']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
            </div>
            <div class="col-md-6">
                <h1><?php echo $product['name']; ?></h1>
                <p class="lead"><?php echo $product['description']; ?></p>
                <h2 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h2>
                
                <div class="my-4">
                    <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['id']; ?>)">
                        Add to Cart
                    </button>
                </div>

                <!-- Reviews Section -->
                <h3 class="mt-5">Customer Reviews</h3>
                <?php while($comment = $comments->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <!-- Vulnerable to XSS -->
                            <h5 class="card-title"><?php echo $comment['username']; ?></h5>
                            <p class="card-text"><?php echo $comment['comment']; ?></p>
                            <div class="text-warning">
                                <?php for($i = 0; $i < $comment['rating']; $i++): ?>
                                    ★
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

                <!-- Add Review Form -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="add_review.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-control" name="rating" id="rating">
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 