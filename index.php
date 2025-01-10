<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechGear Store</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">TechGear Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="/categories.php">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="/deals.php">Special Deals</a></li>
                </ul>
                <form class="d-flex me-3" action="/search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search products...">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
                <div class="d-flex">
                    <a href="/cart.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="badge bg-danger"><?php echo getCartCount(); ?></span>
                    </a>
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="/login.php" class="btn btn-primary">Login</a>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Account
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile.php">My Profile</a></li>
                                <li><a class="dropdown-item" href="/orders.php">My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <!-- Featured Products Carousel -->
        <div id="featuredCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="assets/images/featured1.jpg" class="d-block w-100" alt="Featured Product 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/images/featured2.jpg" class="d-block w-100" alt="Featured Product 2">
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <h2 class="mb-4">Featured Products</h2>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            // Vulnerable SQL query (intentionally unsafe)
            $query = "SELECT * FROM products WHERE featured = 1 LIMIT 8";
            $result = $conn->query($query);
            while($product = $result->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="col">
                <div class="card h-100">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                        <p class="card-text"><?php echo $product['description']; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">$<?php echo number_format($product['price'], 2); ?></span>
                            <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About TechGear</h5>
                    <p>Your one-stop shop for all tech needs</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/about.php" class="text-light">About Us</a></li>
                        <li><a href="/contact.php" class="text-light">Contact</a></li>
                        <li><a href="/shipping.php" class="text-light">Shipping Info</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Newsletter</h5>
                    <form action="/subscribe.php" method="POST">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html> 