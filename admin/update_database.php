<?php
require_once '../config/database.php';

class DatabaseUpdater {
    private $conn;
    private $products = [
        // Array of products with their details
        [
            'name' => 'ASUS ROG Gaming Laptop',
            'description' => 'ASUS ROG Strix G16 Gaming Laptop with NVIDIA RTX 4060, Intel i9-13980HX, 16GB DDR5, 1TB PCIe SSD',
            'price' => 1499.99,
            'image_url' => 'https://m.media-amazon.com/images/I/71nz3cIcFOL._AC_SL1500_.jpg',
            'category' => 'Laptops',
            'stock' => 15,
            'featured' => 1
        ],
        // يمكنك إضافة المزيد من المنتجات هنا
    ];

    private $categories = [
        'Laptops' => 'High-performance gaming and professional laptops',
        'Peripherals' => 'Gaming mice, keyboards, and headsets',
        'Components' => 'PC parts and hardware upgrades',
        'Monitors' => 'Gaming and professional displays',
        'Accessories' => 'Cables, adapters, and other accessories'
    ];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function update() {
        try {
            $this->conn->beginTransaction();

            $this->updateCategories();
            $this->updateProducts();
            $this->updateImages();
            $this->checkStock();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Database updated successfully'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function updateCategories() {
        foreach ($this->categories as $name => $description) {
            $stmt = $this->conn->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
        }
    }

    private function updateProducts() {
        foreach ($this->products as $product) {
            // Get category ID
            $stmt = $this->conn->prepare("SELECT id FROM categories WHERE name = ?");
            $stmt->execute([$product['category']]);
            $category_id = $stmt->fetchColumn();

            // Check if product exists
            $stmt = $this->conn->prepare("SELECT id FROM products WHERE name = ?");
            $stmt->execute([$product['name']]);
            $product_id = $stmt->fetchColumn();

            if ($product_id) {
                // Update existing product
                $stmt = $this->conn->prepare("
                    UPDATE products 
                    SET description = ?, price = ?, image_url = ?, 
                        category_id = ?, stock = ?, featured = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $product['description'],
                    $product['price'],
                    $product['image_url'],
                    $category_id,
                    $product['stock'],
                    $product['featured'],
                    $product_id
                ]);
            } else {
                // Insert new product
                $stmt = $this->conn->prepare("
                    INSERT INTO products (name, description, price, image_url, 
                                        category_id, stock, featured)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['image_url'],
                    $category_id,
                    $product['stock'],
                    $product['featured']
                ]);
            }
        }
    }

    private function updateImages() {
        // تحديث الصور التي لا تعمل
        $stmt = $this->conn->prepare("
            SELECT id, image_url FROM products 
            WHERE image_url IS NOT NULL
        ");
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $headers = @get_headers($row['image_url']);
            if (!$headers || $headers[0] == 'HTTP/1.1 404 Not Found') {
                // تحديث الصورة بصورة بديلة
                $stmt2 = $this->conn->prepare("
                    UPDATE products 
                    SET image_url = 'assets/images/placeholder.jpg'
                    WHERE id = ?
                ");
                $stmt2->execute([$row['id']]);
            }
        }
    }

    private function checkStock() {
        // تحديث حالة المخزون للمنتجات
        $stmt = $this->conn->prepare("
            UPDATE products 
            SET stock = 0 
            WHERE stock < 0
        ");
        $stmt->execute();
    }
}

// واجهة المستخدم البسيطة للتحديث
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $updater = new DatabaseUpdater($conn);
    $result = $updater->update();
    $message = $result['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Updater</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Database Updater</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)): ?>
                            <div class="alert alert-<?php echo $result['success'] ? 'success' : 'danger'; ?>">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="d-grid">
                                <button type="submit" name="update" class="btn btn-primary">
                                    Update Database
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 