<?php
require_once '../db.php';
require_once '../includes/auth.php';

$pageTitle = 'View Orders - Farmers Market';
requireFarmer();

$userId = getUserId();

// Get all orders that contain products from this farmer
$orders = $conn->query("
    SELECT DISTINCT 
        o.id as order_id, 
        o.order_date, 
        o.total_price,
        u.name as customer_name,
        u.email as customer_email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.farmer_id = $userId
    ORDER BY o.order_date DESC
");
?>

<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-12">
            <h1 class="display-5" style="color: #2c5f2d;">
                <i class="fas fa-shopping-bag"></i> Orders
            </h1>
            <p class="text-muted">View all orders placed for your products</p>
        </div>
    </div>

    <?php if ($orders->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Order Date</th>
                        <th>Total Price</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                            <td><strong>$<?php echo number_format($order['total_price'], 2); ?></strong></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                        data-bs-target="#itemsModal<?php echo $order['order_id']; ?>">
                                    <i class="fas fa-eye"></i> View Items
                                </button>
                            </td>
                        </tr>

                        <!-- Items Modal -->
                        <div class="modal fade" id="itemsModal<?php echo $order['order_id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Order Items - Order #<?php echo $order['order_id']; ?></h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php 
                                        $itemsResult = $conn->query("
                                            SELECT oi.product_id, p.name, oi.quantity, oi.price
                                            FROM order_items oi
                                            JOIN products p ON oi.product_id = p.id
                                            WHERE oi.order_id = {$order['order_id']} AND p.farmer_id = $userId
                                        ");
                                        ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($item = $itemsResult->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                            <td><?php echo number_format($item['quantity'], 2); ?> kg</td>
                                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                            <td><strong>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></strong></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No orders yet. Add products and wait for customers to order.
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
