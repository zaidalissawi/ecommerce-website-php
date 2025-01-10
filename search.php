<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

$search_query = $_GET['q'] ?? '';
$category_id = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';

// Build query
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = [];

if ($search_query) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_term = "%$search_query%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}

if ($min_price !== '') {
    $query .= " AND p.price >= ?";
    $params[] = $min_price;
}

if ($max_price !== '') {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY p.price DESC";
        break;
    case 'name_desc':
        $query .= " ORDER BY p.name DESC";
        break;
    default:
        $query .= " ORDER BY p.name ASC";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Filters</h4>
                        <form action="search.php" method="GET">
                            <?php if ($search_query): ?>
                                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price Range</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="min_price" class="form-control" 
                                           placeholder="Min" value="<?php echo $min_price; ?>">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="max_price" class="form-control" 
                                           placeholder="Max" value="<?php echo $max_price; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort By</label>
                                <select name="sort" class="form-select">
                                    <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                                    <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Search Results -->
            <div class="col-md-9">
                <?php if ($search_query): ?>
                    <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
                <?php endif; ?>

                <div class="mb-3">
                    Found <?php echo count($products); ?> products
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
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
                    <?php endforeach; ?>
                </div>

                <?php if (empty($products)): ?>
                    <div class="alert alert-info">
                        No products found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 