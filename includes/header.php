<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechGear Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-laptop"></i> TechGear</a>
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
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
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
</body>
</html> 