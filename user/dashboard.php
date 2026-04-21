<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Customer Dashboard - Farmers Market';
requireCustomer();

$userId = getUserId();
$userDetails = getUserDetails();

// Get customer statistics
$cartResult = $conn->query("SELECT COUNT(*) as count FROM cart WHERE user_id = $userId");
$cartCount = $cartResult->fetch_assoc()['count'];

$ordersResult = $conn->query("SELECT COUNT(*) as count FROM orders WHERE user_id = $userId");
$ordersCount = $ordersResult->fetch_assoc()['count'];

$spentResult = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE user_id = $userId");
$totalSpent = $spentResult->fetch_assoc()['total'] ?? 0;

// Get recent products
$recentProducts = $conn->query("SELECT id, name, price_per_kg FROM products ORDER BY created_at DESC LIMIT 4");

// Get recent orders
$recentOrders = $conn->query("SELECT id, order_date, total_price FROM orders WHERE user_id = $userId 
                              ORDER BY order_date DESC LIMIT 5");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-tachometer-alt"></i> Customer Dashboard
            </h1>
            <p class="text-muted">Welcome, <?php echo htmlspecialchars($userDetails['name']); ?>!</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart" style="font-size: 40px; color: #2c5f2d;"></i>
                    <h3 class="mt-3 fw-bold"><?php echo $cartCount; ?></h3>
                    <p class="text-muted">Items in Cart</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-box" style="font-size: 40px; color: #f4a642;"></i>
                    <h3 class="mt-3 fw-bold"><?php echo $ordersCount; ?></h3>
                    <p class="text-muted">Total Orders</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-rupee-sign" style="font-size: 40px; color: #28a745;"></i>
                    <h3 class="mt-3 fw-bold">₹<?php echo number_format($totalSpent, 2); ?></h3>
                    <p class="text-muted">Total Spent</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle" style="font-size: 40px; color: #007bff;"></i>
                    <h3 class="mt-3 fw-bold"><?php echo htmlspecialchars($userDetails['role']); ?></h3>
                    <p class="text-muted">Account Type</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-sm-flex">
                <a href="view_products.php" class="btn btn-success btn-lg">
                    <i class="fas fa-apple-alt"></i> Browse Products
                </a>
                <a href="cart.php" class="btn btn-info btn-lg">
                    <i class="fas fa-shopping-cart"></i> View Cart (<?php echo $cartCount; ?>)
                </a>
                <a href="orders.php" class="btn btn-warning btn-lg">
                    <i class="fas fa-history"></i> Order History
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h3 style="color: #2c5f2d;"><i class="fas fa-star"></i> Featured Products</h3>
        </div>
        <?php if ($recentProducts->num_rows > 0): ?>
            <?php while ($product = $recentProducts->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text text-muted">Price: ₹<?php echo number_format($product['price_per_kg'], 2); ?>/kg</p>
                            <a href="view_products.php#product<?php echo $product['id']; ?>" class="btn btn-sm btn-success">
                                <i class="fas fa-cart-plus"></i> View & Order
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php if ($recentOrders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Order Date</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <a href="orders.php" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No orders yet. <a href="view_products.php">Start shopping</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
