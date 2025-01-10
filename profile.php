<?php
session_start();
require_once 'config/database.php';
require_once 'includes/core_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    try {
        $conn->beginTransaction();
        
        // Check if email is already taken by another user
        if ($email !== $user['email']) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception('Email is already taken');
            }
        }
        
        // If changing password, verify current password
        if ($new_password) {
            if ($current_password !== $user['password']) { // Vulnerable: plain text password comparison
                throw new Exception('Current password is incorrect');
            }
            $password_to_save = $new_password; // Vulnerable: storing plain text password
        } else {
            $password_to_save = $user['password'];
        }
        
        // Update user profile
        $stmt = $conn->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, password = ?
            WHERE id = ?
        ");
        $stmt->execute([$full_name, $email, $password_to_save, $_SESSION['user_id']]);
        
        $conn->commit();
        $success = 'Profile updated successfully';
        $user = getCurrentUser(); // Refresh user data
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

// Get user's recent orders
$stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - TechGear Store</title>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="https://www.gravatar.com/avatar/<?php echo md5(strtolower(trim($user['email']))); ?>?s=150" 
                                 class="rounded-circle" alt="Profile Picture">
                            <h4 class="mt-2"><?php echo htmlspecialchars($user['full_name']); ?></h4>
                        </div>
                        <div class="list-group">
                            <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">Profile</a>
                            <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="list">Orders</a>
                            <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">Security</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="col-md-9">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Profile</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="full_name" 
                                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Orders</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($recent_orders): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Items</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_orders as $order): ?>
                                                    <tr>
                                                        <td><?php echo $order['id']; ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td><?php echo $order['item_count']; ?> items</td>
                                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="orders.php" class="btn btn-outline-primary">View All Orders</a>
                                <?php else: ?>
                                    <div class="alert alert-info">You haven't placed any orders yet.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Change Password</h3>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle tab switching
        const triggerTabList = [].slice.call(document.querySelectorAll('.list-group-item'))
        triggerTabList.forEach(function(triggerEl) {
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault()
                triggerTabList.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                
                const tabId = this.getAttribute('href');
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
                document.querySelector(tabId).classList.add('show', 'active');
            })
        })
    });
    </script>
</body>
</html> 