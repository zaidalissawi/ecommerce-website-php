<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vulnerable to SQL injection
$category_id = isset($_GET['category']) ? $_GET['category'] : null;
$query = "SELECT * FROM products";
if($category_id) {
    $query .= " WHERE category_id = $category_id";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <div class="list-group">
                            <?php
                            $cat_query = "SELECT * FROM categories";
                            $categories = $conn->query($cat_query);
                            while($category = $categories->fetch(PDO::FETCH_ASSOC)):
                            ?>
                            <a href="?category=<?php echo $category['id']; ?>" 
                               class="list-group-item list-group-item-action">
                                <?php echo $category['name']; ?>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-md-9">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php while($product = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="col">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-card">
                            <div class="card h-100">
                                <div class="product-image-container">
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.src='assets/images/placeholder.jpg'">
                                    <?php if($product['stock'] <= 5): ?>
                                        <span class="stock-badge <?php echo $product['stock'] == 0 ? 'out-of-stock' : 'low-stock'; ?>">
                                            <?php echo $product['stock'] == 0 ? 'Out of Stock' : 'Low Stock'; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title text-truncate"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text description-truncate"><?php echo htmlspecialchars($product['description']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <button class="btn btn-primary" 
                                                onclick="event.preventDefault(); addToCart(<?php echo $product['id']; ?>)"
                                                <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                            <?php echo $product['stock'] == 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 