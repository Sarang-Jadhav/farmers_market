<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'Farmer Dashboard - Farmers Market';
requireFarmer();

$userId = getUserId();
$userDetails = getUserDetails();

// Get farmer statistics
$productsResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE farmer_id = $userId");
$productsCount = $productsResult->fetch_assoc()['count'];

$ordersResult = $conn->query("SELECT COUNT(DISTINCT oi.order_id) as count FROM order_items oi 
                              JOIN products p ON oi.product_id = p.id 
                              WHERE p.farmer_id = $userId");
$ordersCount = $ordersResult->fetch_assoc()['count'];

$revenueResult = $conn->query("SELECT SUM(oi.quantity * oi.price) as total FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id 
                               WHERE p.farmer_id = $userId");
$revenue = $revenueResult->fetch_assoc()['total'] ?? 0;

// Get recent products
$recentProducts = $conn->query("SELECT id, name, price_per_kg, created_at FROM products 
                                WHERE farmer_id = $userId 
                                ORDER BY created_at DESC LIMIT 5");

// Get recent orders
$recentOrders = $conn->query("SELECT DISTINCT oi.order_id, o.order_date, o.total_price, u.name 
                              FROM order_items oi 
                              JOIN orders o ON oi.order_id = o.id 
                              JOIN products p ON oi.product_id = p.id 
                              JOIN users u ON o.user_id = u.id 
                              WHERE p.farmer_id = $userId 
                              ORDER BY o.order_date DESC LIMIT 5");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-tachometer-alt"></i> Farmer Dashboard
            </h1>
            <p class="text-muted">Welcome, <?php echo htmlspecialchars($userDetails['name']); ?>!</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-box" style="font-size: 40px; color: #2c5f2d;"></i>
                    <h3 class="mt-3 fw-bold"><?php echo $productsCount; ?></h3>
                    <p class="text-muted">Products Listed</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-bag" style="font-size: 40px; color: #f4a642;"></i>
                    <h3 class="mt-3 fw-bold"><?php echo $ordersCount; ?></h3>
                    <p class="text-muted">Orders Received</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign" style="font-size: 40px; color: #28a745;"></i>
                    <h3 class="mt-3 fw-bold">₹<?php echo number_format($revenue, 2); ?></h3>
                    <p class="text-muted">Total Revenue</p>
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
                <a href="add_product.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
                <a href="manage_products.php" class="btn btn-info btn-lg">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="view_orders.php" class="btn btn-warning btn-lg">
                    <i class="fas fa-shopping-bag"></i> View Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Recent Products</h5>
                </div>
                <div class="card-body">
                    <?php if ($recentProducts->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Price/kg</th>
                                        <th>Date Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $recentProducts->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>₹<?php echo number_format($product['price_per_kg'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No products yet. <a href="add_product.php">Add your first product</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Recent Orders</h5>
                </div>
                <div class="card-body">
                    <?php if ($recentOrders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $recentOrders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['name']); ?></td>
                                            <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No orders yet. <a href="view_orders.php">View orders page</a></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
